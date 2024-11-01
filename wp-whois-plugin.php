<?php

class wpWhoisPlugin {

	var $url = '';
	var $pre = 'wpwhois';
	var $plugin_name;
	var $plugin_base;
	var $samswhoisinc;
	var $whois_ptrn = "/\{whois\}/i";
	
	var $debugging = false;
	var $debug_level = 2;
	
	var $version = '1.4.2.4';
	
	var $sections = array('welcome' => "whois");

	function register_plugin($name = null, $base = null) {
		$this -> plugin_name = $name;
		$this -> plugin_base = rtrim(dirname($base), DS);
		$this -> sections = (object) $this -> sections;
		$this -> samswhoisinc = $this -> plugin_base . DS . 'vendors' . DS . 'samswhois' . DS . 'samswhois.inc.php';
		
		global $wpdb;
		if ($this -> debugging == true) {
			$wpdb -> show_errors();
			
			if ($this -> debug_level == 2) {
				error_reporting(E_ALL ^ E_NOTICE);
				@ini_set('display_errors', 1);
			}
		} else {
			$wpdb -> hide_errors();
			error_reporting(0);
			@ini_set('display_errors', 0);
		}
		
		return true;
	}
	
	function plugin_is_active($plugin = 'really-simple-captcha/really-simple-captcha.php') {
		require_once ABSPATH . 'wp-admin' . DS . 'includes' . DS . 'plugin.php';
		
		if (is_plugin_active(plugin_basename($plugin))) {
			return true;
		}
		
		return false;
	}
	
	function print_scripts() {
		$this -> enqueue_scripts();
	}
	
	function print_styles() {
		$this -> enqueue_styles();
	}
	
	function enqueue_scripts() {	
		wp_enqueue_script('jquery');
		wp_enqueue_script('common');
		wp_enqueue_script('jquery-color');
		wp_enqueue_script('schedule');
		add_thickbox();
		wp_enqueue_script('thickbox');
		
		wp_enqueue_script('wp-whois', '/wp-content/plugins/wp-whois/js/wp-whois.js', array('jquery'));

		if (is_admin()) {		
			if (eregi("admin.php", $this -> url) || eregi("tools.php", $this -> url)) {
				wp_enqueue_script('common');
				wp_enqueue_script('wp-lists');
				wp_enqueue_script('jquery-ui-sortable');
				wp_enqueue_script('postbox');
				
				if ($_GET['page'] == $this -> sections -> welcome) { wp_enqueue_script('whois-editor', WP_PLUGIN_URL . '/' . $this -> plugin_name . '/js/editors/whois.js', array('jquery')); }
			}
		}
		
		return true;
	}
	
	function enqueue_styles() {
		wp_enqueue_style('wp-whois', WP_PLUGIN_URL . '/' . $this -> plugin_name . '/vendors/samswhois/swstyles.css', false, $this -> version, "all");
		
		return true;
	}
	
	function addquicktags() {
		?>
        
        <script type="text/javascript">
		function WHOISButtonClick() {			
			var tag = "[whois]";
			var list = jQuery('#list').val();
			
			if (window.tinyMCE && tag != "") {
				window.tinyMCE.execInstanceCommand('content', 'mceInsertContent', false, tag);
			}
			return;
		}
		</script>
        
        <?php	
	}
	
	function plugin_base() {
		return rtrim(dirname(__FILE__), '/');
	}
	
	function url() {
		return rtrim(get_bloginfo('wpurl'), '/') . '/' . substr(preg_replace("/\\" . DS . "/si", "/", $this -> plugin_base()), strlen(ABSPATH));
	}
	
	function debug($var = array()) {
		if ($this -> debugging == true) {
			echo '<pre>' . print_r($var, true) . '</pre>';
			return true;
		}

		return false;
	}
	
	function add_action($action, $function = null, $priority = 10, $params = 1) {
		if (add_action($action, array($this, (empty($function)) ? $action : $function), $priority, $params)) {
			return true;
		}
		
		return false;
	}
	
	function add_filter($filter, $function = null, $priority = 10, $params = 1) {
		if (add_filter($filter, array($this, (empty($function)) ? $filter : $function), $priority, $params)) {
			return true;
		}
		
		return false;
	}
	
	function retainquery($add = null) {
		$url = $_SERVER['REQUEST_URI'];
	
		if (($urls = @explode("?", $url)) !== false) {				
			if (!empty($urls[1])) {			
				if (!empty($add)) {				
					if (($adds = explode("&", str_replace("&amp;", "&", $add))) !== false) {					
						foreach ($adds as $qstring) {						
							if (($qparts = @explode("=", $qstring)) !== false) {							
								if (!empty($qparts[0])) {								
									if (preg_match("/\&?" . $qparts[0] . "\=([0-9a-z]*)/i", $urls[1], $matches)) {
										$urls[1] = preg_replace("/\&?" . $qparts[0] . "\=([0-9a-z]*)/i", "", $urls[1]);
									}									
								}
							}
						}
					}
				}
			}
		}
		
		$urls[1] = preg_replace("/\&?" . $this -> pre . "message\=([0-9a-z+]*)/i", "", $urls[1]);
		$url = $urls[0];
		$url .= '?';
		$url .= (empty($urls[1])) ? '' : $urls[1] . '&amp;';
		$url .= $add;
				
		return $url;
	}
	
	function add_option($name = null, $value = null) {
		if (add_option($this -> pre . $name, $value)) {
			return true;
		}
		
		return false;
	}
	
	function update_option($name = null, $value = null) {
		if (update_option($this -> pre . $name, $value)) {
			return true;
		}
		
		return false;
	}
	
	function get_option($name = null, $stripslashes = true) {
		if ($option = get_option($this -> pre . $name)) {
			if (@unserialize($option) !== false) {
				return unserialize($option);
			}
			
			if ($stripslashes == true) {
				$option = stripslashes_deep($option);
			}
			
			return $option;
		}
		
		return false;
	}
	
	function initialize_options() {
		ob_start();
		include($this -> samswhoisinc);
		ob_get_clean();
		
		$tlds = array();
		if (!empty($whois -> m_usetlds)) {
			foreach ($whois -> m_usetlds as $t => $i) {
				$tlds[] = $t;
			}
			
			$this -> add_option('alltlds', $tlds);
			$this -> add_option('usetlds', $tlds);
		}
		
		$this -> add_option('ajax', "Y");
		$this -> add_option('captcha', "human");
		$this -> add_option('captchacolor', "#FFFFFF");
		$this -> add_option('output', "full");
		$this -> add_option('highlight', "Y");
		$this -> add_option('cleanoutput', "Y");
		$this -> add_option('redirect', "N");
		$this -> add_option('redirecturl', "http://www.domain.com/?domain={domain}&tld={tld}");
		$this -> add_option('redirecttarget', "sam");
		$this -> add_option('customcss', "N");
		$this -> add_option('cache', "N");
		$this -> add_option('tinymcebutton', "Y");
		
		$cssname = "swstyles.css";
		$csspath = $this -> plugin_base . DS . 'vendors' . DS . 'samswhois' . DS;
		$cssfull = $csspath . $cssname;

		if (file_exists($cssfull) && is_readable($cssfull)) {		
			if ($fh = fopen($cssfull, "r")) {
				$css = '';
				
				while (!feof($fh)) {
					$css .= fread($fh, 1024);
				}
				
				$this -> add_option('css', $css);
			}
		}
		
		if ($cur_version = $this -> get_option('version')) {		
			if ($this -> version > $cur_version) {			
				$new_version = $cur_version;
			
				if (version_compare("1.3.4", $cur_version) === 1) {				
					$this -> update_option('alltlds', $tlds);					
					$new_version = "1.3.4";
				} elseif (version_compare("1.4", $cur_version) === 1) {
					$this -> update_option('alltlds', $tlds);
					$new_version = "1.4";
				} elseif (version_compare("1.4.2", $cur_version) === 1) {
					$new_version = "1.4.2";	
				}
				
				//update the version number
				$this -> update_option('version', $new_version);
			}
		} else {
			$this -> add_option('version', "1.0");
		}
	
		return true;
	}
	
	function hexrgb($hexstr) {
		$int = hexdec($hexstr);	
		return array("r" => 0xFF & ($int >> 0x10), "g" => 0xFF & ($int >> 0x8), "b" => 0xFF & $int);
	}

	
	function render_msg($message = null) {
		$this -> render('msg-top', array('message' => $message), true, 'admin');
	}
	
	function render_err($message = null) {
		$this -> render('err-top', array('message' => $message), true, 'admin');
	}
	
	function render($file = null, $params = array(), $output = true, $folder = 'admin') {
		if (!empty($file)) {
			$filename = $file . '.php';
			$filepath = $this -> plugin_base . DS . 'views' . DS . $folder . DS;
			$filefull = $filepath . $filename;
			
			if (file_exists($filefull)) {
				if (!empty($params) && (is_array($params) || is_object($params))) {
					foreach ($params as $pkey => $pval) {
						${$pkey} = $pval;
					}
				}
			
				if ($output == false) {
					ob_start();
				}
				
				include($filefull);
				
				if ($output == false) {
					$content = ob_get_clean();
					return $content;
				} else {
					return true;
				}
			}
		}
		
		return false;
	}
	
	function replace_whois($matches = array()) {
		if (!empty($matches[0])) {
			ob_start();
			$this -> whois();
			$whois = ob_get_clean();
			return $whois;
		}
		
		return false;
	}
	
	function whois() {
		//start output buffering
		ob_start();
		
		//generate a unique, random string/number
		$number = substr(md5(rand(1, 999)), 0, 6);
		
		?>
		
		<div id="<?php echo $this -> pre; ?>-<?php echo $number; ?>" class="swPositioner">
        	<div id="<?php echo $this -> pre; ?>inside-<?php echo $number; ?>">
				<?php
                
                $swWidget = false;
                include($this -> samswhoisinc);
                
                ?>
			</div>
		</div>
		
		<?php
		
		$whois = ob_get_clean();
		print $whois;
	}
	
	function metabox_settingssubmit() {
		$this -> render('metaboxes' . DS . 'settings-submit', false, true, 'admin');
		return true;
	}
	
	function metabox_settingsotheractions() {
		$this -> render('metaboxes' . DS . 'settings-otheractions', false, true, 'admin');
		return true;
	}
	
	function metabox_settingscredits() {
		$this -> render('metaboxes' . DS . 'settings-credits', false, true, 'admin');
		return true;
	}
	
	function metabox_settingsgeneral() {
		$this -> render('metaboxes' . DS . 'settings-general', false, true, 'admin');
		return true;
	}
	
	function metabox_settingstlds() {
		$this -> render('metaboxes' . DS . 'settings-tlds', false, true, 'admin');
		return true;
	}
	
	function metabox_settingscss() {
		$this -> render('metaboxes' . DS . 'settings-css', false, true, 'admin');
		return true;
	}
	
	//determines whether an ad link should be shown
	var $adversion = true;
}

?>