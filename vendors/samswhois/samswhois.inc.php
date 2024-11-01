<?php
/*

File: samswhois.inc.php
Purpose: simple interface to using the samswhois.class.php file

Home Page: http://whois.samscripts.com/
Copyright: Copyright 2004 Sam Yapp

Licence: Free to use provided your site links back to http://whois.samscripts.com/ . You may not sell or otherwise
distribute any versions of this script without prior permission with the sole exception that distribution as part
of transfer of ownership of a functioning website is allowed, provided this copyright notice remains intact.

This script is distributed without any warranty whatsoever. Usage is at your own risk.

*/

/***********************************************
	Section 1 - Initialization...
************************************************/

/*
	include the samswhois class file. You can use this class directly in your scripts if you want
	more control...
*/

require_once(dirname(__FILE__) . DS . 'samswhois.class.php');

/* create a new samswhois object */



$whois = new SamsWhois();

/*
	initialize any of the variables that we use. 
	You can set any of these values in the script that includes this one to override
	the values below.
*/

if (!isset($swHilite)) $swHilite = ($this -> get_option('highlight') == "Y") ? 'yes' : 'no';	// hilight fields in the whois output (eg. status, nameservers, etc)
if (!isset($swClean)) $swClean = ($this -> get_option('cleanoutput') == "Y") ? 'yes' : 'no';	// "clean" the whois output of extraneous text
if (!isset($swAuth)) $swAuth = true;	// check the authoritative whois server for com & net
if (!isset($swOnlyShowAuth)) $swOnlyShowAuth = false;	// if checking authoritative, should we ignore the registry whois?
if (!isset($swSecure)) $swSecure = ($whois -> get_option('captcha') == "image") ? true : false;	// generate a security code for each whois lookup
if (!isset($swListTlds)) $swListTlds = false;	// list the supported tlds underneath the lookup form
if (!isset($swTldOptions)) $swTldOptions = true;	// let the user select the tld from a drop-down list
if (!isset($swAlphabeticalTlds)) $swAlphabeticalTlds = true;	// list tlds alphabetically?
if (!isset($swTlds)) $swTlds = '';	// limit the tlds supported to those in a comma separated list eg 'com,net,org'
//if( !isset($swDefaultTld) ) $swDefaultTld = 'com';	// the default tld to use / display in the form
if (!isset($swDefaultSld)) $swDefaultSld = 'domain';	// the default sld to display in the form
if (!isset($swCacheLifetime)) $swCacheLifetime = ($this -> get_option('cache') == "Y") ? 60 : 0; // the length of time in minutes to cache whois lookup results
if (!isset($swOnlyShowAvailability)) $swOnlyShowAvailability = false;	// only show availability, no whois data
$swAjax = $this -> get_option('ajax');

$swHumanMessage = __('For security reasons, please provide the answer to the given mathematical calculation', $whois -> plugin_name);

/*
	initialize any messages to display that aren't already set in the script that includes this one
*/

if( !isset($swSubmitLabel) ) $swSubmitLabel = __('Check Domain', $whois -> plugin_name);	// the submit button label for the whois form

if( !isset($swInstructions) ){		// instructions displayed under the form - differ slightly if the user can choose the tld
	if( $swTldOptions ){
		$swInstructions = __('Enter a domain name and select a tld from the box above', $whois -> plugin_name);
	}else{
		$swInstructions = __('Enter a domain name including extension in the box above', $whois -> plugin_name);
	}
}

if( !isset($swSecurityError) ){	// error message displayed if the user doesn't enter the security code when required
	$swSecurityError = __('For security reasons, you MUST enter the 4 digit code shown above.', $whois -> plugin_name);
}

if( !isset($swLookupError) ){	// error message displayed if a whois lookup query fails
	$swLookupError = __('Sorry, an error occurred.', $whois -> plugin_name);
}

if( !isset($swSecurityMessage) ){	// message displayed below the form when a security code is required
	$swSecurityMessage = __('For security purposes, please also enter the 4 digit code.', $whois -> plugin_name);
}

if( !isset($swTldError) ){	// message displayed if the user enters a tld that is not supported
	$swTldError = __('Sorry, that tld is not supported.', $whois -> plugin_name);
}

if( !isset($swHeadingText) ){ // displayed above the form - could replace with a logo image or the name of your site
	$swHeadingText = __('Whois Lookup', $whois -> plugin_name);
}

/*
	Set any variables in the SamsWhois class that have been set in the script that includes this one.
*/

$whois->SetCacheLifetime($swCacheLifetime);

if( isset($swAvailableMessage) ){	// the message displayed when $whois->GetStatusText() is called and the domain is available
	$whois->SetAvailableMessage($swAvailableMessage);
}

if( isset($swRegisteredMessage) ){ // message displayed when $whois->GetStatusText() is called and the domain is registered
	$whois->SetRegisteredMessage($swRegisteredMessage);
}

if( isset($swServerText) ){	// message displayed when $whois->GetServerText() is called - {server} is replaced by the server name
	$whois->SetServerText($swServerText);
}

if( isset($swAuth) && $swAuth == true ){ // tell the script whether to lookup com & net at the authoratitive server
	$whois -> m_redirectauth = true;
}

/*
	Security code image
	if we are using a secure image code, include the class and create a secureimagecode object for later use
*/

if($swSecure) {
	require_once(dirname(__FILE__) . DS . 'secureimagecode.class.php');
	$secure = new secureimagecode();
	
	//initialize Really Simple Captcha
	if ($whois -> plugin_is_active()) {
		$captcha = new ReallySimpleCaptcha();
	}
}

/*
	limit the tlds to use (if set in the script that includes this one)
	$swTlds should be in the form 'com,net,org' where the tlds are the ones to use.
*/

if(  $swTlds != '') $whois->SetTlds($swTlds);

/*
	Initialize some variables used in the rest of the script
*/

$tld = $swDefaultTld;	// the tld
$sld = $swDefaultSld;	// the sld
$domain = '';	// this will be displayed as the value of the domain <input> in the form - it is set later on
$nocode = false;	// will be set to true later if the user submits the form without a correct security code (if required)
$dolookup = false;	// set later if the lookup form has been submitted with a valid domain / tld.

/*
	Determine whether to automatically clean whois output. If $swClean = 'optional', checks if the user wants this.
*/

switch ($swClean) {
	case 'yes' : $sw_clean = true; break;
	case 'optional' : $sw_clean = isset($_REQUEST['clean']) ? true : false; break;
	default: $sw_clean = false; break;
}

/*
	Determine whether to hilight certain rows of the whois output. If $swHilite = 'optional', checks if the user wants this.
*/

switch( $swHilite ){
	case 'yes': $sw_hilite = true; break;
	case 'optional': $sw_hilite = isset($_REQUEST['hilite']) ? true : false; break;
	default: $sw_hilite = false; break;
}

/*
	Check if the user has submitted the lookup form
*/

if (isset($_REQUEST['domain'])) {
	$dn = trim($_REQUEST['domain']);

	if($dn != ''){

		// separate the sld and tld, checking for a submitted tld if $swTldOptions = true
		$dot = strpos($dn, '.');
		if ($dot !== false) {
			$sld = substr($dn, 0, $dot);
			$tld = substr($dn, $dot+1);
		} else {
			$sld = $dn;
			if ($swTldOptions && isset($_REQUEST['tld'])) $tld = trim($_REQUEST['tld']);
		}

		$domain = $sld . '.' . $tld;

		if ($whois -> ValidDomain($domain)) { // check that it is a valid domain
			$dolookup = true;

			if ($swSecure) {
				/*
				if(!$secure -> CheckCode($_REQUEST['code'])) {				
					$nocode = true;
					$swErrorMessage = $swSecurityError;
					$dolookup = false;
				}
				*/
				
				if ($whois -> plugin_is_active()) {
					if (!$captcha -> check($_REQUEST['captcha_prefix'], esc_attr(stripslashes($_REQUEST['captcha_code'])))) {
						$nocode = true;
						$swErrorMessage = $swSecurityError;
						$dolookup = false;
					}
				}
			} elseif ($swHuman !== false && $this -> get_option('captcha') == "human") {
				if (!empty($_REQUEST['total'])) {
					if (md5($_REQUEST['total']) !== $_REQUEST['hashtotal']) {
						$nocode = true;
						$swErrorMessage = __('The sum you filled in is incorrect', $whois -> plugin_name);
						$dolookup = false;
					}
				} else {
					$nocode = true;
					$swErrorMessage = __('Please fill in a sum for the mathematical calculation', $whois -> plugin_name);
					$dolookup = false;
				}
			}
		} else {
			$swErrorMessage = $swTldError;
			$dolookup = false;
		}
	}
}

/*
	Set the domain variable to the correct value (either with or without tld) for later output in the form
*/

if ($swTldOptions) {
	$domain = $sld;
} else {
	$domain = $sld . '.' . $tld;
}

/***********************************************
	Section 2 - Display the whois lookup form

	Depending on what options have been set in the calling script, the form may contain
	various messages, a drop-down box to select the tld, and checkboxes.

************************************************/

//generate a random number encrypted string
$swNumber = (empty($swWidget) || $swWidget == false) ? $number : $swOptions['number'];
$formid = $this -> pre . 'form' . $swNumber;

?>

<?php if ($swForm !== false) : ?>
	<div id="<?php echo $this -> pre; ?>div" class="swWrap">	
		<form id="<?php echo $formid; ?>" name="<?php echo $formid; ?>" <?php echo (!empty($swAjax) && $swAjax == "Y") ? 'onsubmit="wpwhoisform(\'' . $swNumber . '\'); return false;" action="#void"' : 'action="' . $_SERVER['REQUEST_URI'] . '#' . $this -> pre . 'div"'; ?> style="margin: 0px;" method="post">
		
			<?php if (!empty($swWidget) && $swWidget == true) : ?>
				<input type="hidden" name="widget" value="Y" id="<?php echo $this -> pre; ?>widget<?php echo $swNumber; ?>" />
				<input type="hidden" name="number" value="<?php echo $swOptions['number']; ?>" />
			<?php endif; ?>
		
			<div class="swForm">
				<input type="text" id="<?php echo $this -> pre; ?>domain<?php echo $swNumber; ?>" name="domain" class="swDomain" value="<?php echo $domain;?>" onFocus="this.select();" />
				<?php if ($swTldOptions) { ?>
					<b>.</b>
					<select class="swtld" name="tld"><?php echo $whois -> TldOptions($tld,$swAlphabeticalTlds); ?></select>
				<?php }
		
				if ($whois -> plugin_is_active() && $swSecure) { // should we get the user to enter a security code?
				$code = $secure -> GenerateCode($swNumber);
				
				$bg = $this -> hexrgb($this -> get_option('captchacolor'));
				$captcha -> bg = array($bg['r'], $bg['g'], $bg['b']);
				
				if ($whois -> plugin_is_active()) {
					$word = $captcha -> generate_random_word();
					$captcha_prefix = mt_rand();
					$filename = $captcha -> generate_image($captcha_prefix, $word);
					$captcha_file = rtrim(get_bloginfo('wpurl'), '/') . '/wp-content/plugins/really-simple-captcha/tmp/' . $filename;
				}
				
				?>
		
		<div class="<?php echo $whois -> pre; ?>secureimage">
		<input type="text" class="swSecureCode" name="captcha_code" value="" />
		<?php /*<img align="absmiddle" src="<?php echo $this -> url() . '/include/captcha.php?ms=' . $swNumber; ?>" class="swSecureImage" />*/ ?>
        <img src="<?php echo $captcha_file; ?>" />
        <input type="hidden" name="captcha_prefix" value="<?php echo $captcha_prefix; ?>" />
		<input type="hidden" name="hashcode" value="<?php echo md5($code); ?>" />
		</div>
		
		<?php
		
		/*$captcha = new ReallySimpleCaptcha();
		$word = $captcha -> generate_random_word();
		$prefix = mt_rand();
    	$captcha_image = $captcha_instance -> generate_image($prefix, $word);
		echo $captcha_image;*/
		
		} elseif ($this -> get_option('captcha') == "human") {
		$no1 = rand(1, 10);
		$no2 = rand(1, 10);
		$operators = array("+", "-", "/", "*");
		shuffle($operators);
		$operator = $operators[0];
		$total = $no1 + $no2;
		
		?>
		
		<div class="<?php echo $whois -> pre; ?>secureimage">
		<?php echo $no1; ?> <?php _e('+', $this -> plugin_name); ?> <?php echo $no2; ?> <?php _e('=', $this -> plugin_name); ?>
		<input type="text" name="total" style="width:25px; font-size:10px; font-weight:bold;" value="" />
		<input type="hidden" name="hashtotal" value="<?php echo md5($total); ?>" />
		</div>
		
		<?php
		}
		
		?>
		
		<input type="submit" name="lookup" value="<?php echo $swSubmitLabel;?>" class="swSubmit" />
		<div class="swInfo">
		<?php
		if( $swClean == 'optional' ){ // if cleaning is optional, give the user the option...
		?>
		<input type="checkbox" name="clean" value="1" <?php if( $sw_clean ) echo 'CHECKED';?> />
		<b><?php _e('Clean whois output?', $whois -> plugin_name); ?></b>
		<?php
		}
		
		if( $swHilite == 'optional' ){ // if whois output hilighting is optional, give the user the option...
			?>
			<input type="checkbox" name="hilite" value="1" <?php if( $sw_hilite ) echo 'CHECKED';?> />
			<b><?php _e('Highlight Important Fields?', $whois -> plugin_name); ?></b>
			<?php
		}
		
		if ($swHilite == 'optional' || $swClean == 'optional') {
			echo '<br/>';
			echo $swInstructions;
		}
		
		if ($swListTlds){	// list all supported tlds.
			echo '<br/>' . __('Supported Tlds: ', $whois -> plugin_name) . join(', ', $whois->GetTlds($swAlphabeticalTlds)).'.';
		}
		
		if ($swSecure && (empty($swWidget) || $swWidget == false)) {
			echo "<br/>" . $swSecurityMessage . "<br/>";
		}
		
		if ($this -> get_option('captcha') == "human" && (empty($swWidget) || $swWidget == false)) {
			echo "<br/>" . $swHumanMessage . "<br/>";
		}
		
		?>
		
		<?php
		
		if (!$dolookup && !empty($this -> adversion) && $this -> adversion == true) {
			echo '<div style="margin:10px 0; font-style:italic; color:#999999;">' . __('WHOIS powered by ', $this -> plugin_name) . ' : <a href="http://tribulant.com" target="_blank" title="Tribulant Software">Tribulant</a>' . "</div>";
		}
		
		?>
		
		</div>
		
		<?php
		
		if (isset($swErrorMessage )) {
		?><div class="swError"><?php echo $swErrorMessage;?></div><?php
		}
		
		?>
		
		</div>
		</form>
	</div>
<?php endif; ?>

<?php if (!empty($dolookup) && $dolookup == true) : ?>
	<?php if ($whois -> Lookup($sld . '.' . $tld)) : ?>
		<div id="<?php echo $this -> pre; ?>results<?php echo $swNumber; ?>" class="swResults">
			<?php if (empty($swWidget) || $swWidget == false) : ?>
				<?php if ((!empty($_GET[$this -> pre . 'method']) && $_GET[$this -> pre . 'method'] == "whois") || !$this -> get_option('output') || $this -> get_option('output') == "full") : ?>
					<table style="border:0px; width:100%;" align="center">
						<tbody>
							<tr>
								<td style="font-size: 10pt; font-family: verdana, arial;">
									<div class="swStatus">
										<?php echo $whois -> GetStatusText(); ?>
									</div>
									
									<?php
							
									if (!$swOnlyShowAvailability) {
										$data = $whois -> GetData(0, $swClean, $swHilite);
										
										if ($whois -> GetServerCount() == 2) {
											if ($swOnlyShowAuth) {
												$output = '<div class="swServer">'.$whois->GetServerText(1).'</div>'."\n";
												$output .='<div class="swData">'.nl2br($whois->GetData(1, $sw_clean, $sw_hilite)).'</div>'."\n";
											} else {
												$output = '<div class="swServer">' . $whois -> GetServerText(1).'</div>'."\n";
												$output .='<div class="swData">' . nl2br($whois->GetData(1, $sw_clean, $sw_hilite)).'</div>'."\n";
												$output .= '<div class="swServer">'.$whois->GetServerText(0).'</div>'."\n";
												$output .='<div class="swData">'.nl2br($whois->GetData(0, $sw_clean, $sw_hilite)).'</div>'."\n";
											}
										} else {
											$output = '<div class="swServer">'.$whois -> GetServerText(0).'</div>'."\n";
											$output .='<div class="swData">' . nl2br($whois -> GetData(0, $sw_clean, $sw_hilite)) . '</div>' . "\n";
										}
										
										echo $output;
									}
									
									?>							
								</td>
							</tr>
						</tbody>
					</table>
				<?php else : ?>
					<div class="swStatus">
						<?php echo $whois -> GetStatusText(); ?>
					</div>
				<?php endif; ?>
			<?php else : ?>
				<?php _e('The Domain Status Is', $this -> plugin_name); ?> :<br/>
			
				<?php
				
				$message = '<span style="color:red; font-weight:bold;">' . __('ERROR', $this -> plugin_name) . '</span>';
				
				switch ($whois -> m_status) {
					case 0			:
						$message = '<span style="color:red; font-weight:bold;">' . __('ERROR', $this -> plugin_name) . '</span>';
						break;
					case 1			:						
						$message = '<span style="color:green; font-weight:bold;">' . __('AVAILABLE', $this -> plugin_name) . '</span>';
						break;
					case 2			:
						$message = '<span style="color:red; font-weight:bold;">' . __('REGISTERED', $this -> plugin_name) . '</span>';
						break;
				}
				
				echo $message;
				
				?>
			<?php endif; ?>
			
			<?php
			
			if (!empty($whois -> m_status) && (!empty($swWidget) && $swWidget == true)) {
				if ($whois -> m_status == 2) {
					?>
					
					<p>
						<a href="#void" onclick="window.open('<?php echo $this -> get_option('siteurl'); ?>?<?php echo $this -> pre; ?>method=whois&lookup=Y&domain=<?php echo $sld; ?>&tld=<?php echo $tld; ?>','<?php echo $this -> pre; ?>window');"><?php _e('Domain WHOIS', $this -> plugin_name); ?></a>
					</p>
					
					<?php
				}
			}
	
			if (!empty($whois -> m_status) && $whois -> m_status == 1) {						
				if (!empty($swOptions['reflink']) && $swOptions['reflink'] == "Y" && (!empty($swWidget) && $swWidget == true)) {
					if (!empty($swOptions['reflinktitle']) && !empty($swOptions['reflinkurl'])) {
						?>
						
						<p>
							<a title="<?php echo $swOptions['reflinktitle']; ?>" href="<?php echo $swOptions['reflinkurl']; ?>" target="<?php echo $swOptions['reflinktarget']; ?>"><?php echo $swOptions['reflinktitle']; ?></a>
						</p>
						
						<?php
					}
				} else {
					if ($this -> get_option('redirect') == "Y") {
						if ($redirecturl = $this -> get_option('redirecturl')) {
							if (!empty($redirecturl)) {
								$matches = array("/\{domain\}/", "/\{tld\}/");
								$replace = array($sld, $tld);
								$redirecturl = preg_replace($matches, $replace, $redirecturl);
							
								?>
								
								<script type="text/javascript">
								<?php if (true) : ?>
									<?php if ($this -> get_option('redirecttarget') == "new") : ?>
										window.open("<?php echo $redirecturl; ?>","mywindow");
									<?php else : ?>
										window.location = "<?php echo $redirecturl; ?>";
									<?php endif; ?>
								<?php endif; ?>
								</script>
								
								<?php
								
								exit();
							}
						}
					}
				}
			}

			if ($dolookup && !empty($this -> adversion) && $this -> adversion == true) {
				echo '<div style="margin:10px 0; font-style:italic; color:#999999;">' . __('WHOIS powered by ', $this -> plugin_name) . ' : <a href="http://tribulant.com" target="_blank" title="Tribulant Software">Tribulant</a>' . "</div>";
			}
			
			?>
		</div>
	<?php else : ?>
		<div class="swError">
			<?php echo $swLookupError; ?>
		</div>
	<?php endif; ?>
<?php endif; ?>