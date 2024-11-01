<table class="form-table">
	<tbody>
		<tr>
			<th><label for="ajaxY"><?php _e('Search Form Method', $this -> plugin_name); ?></label></th>
			<td>
				<label><input <?php echo ($this -> get_option('ajax') == "N") ? 'checked="checked"' : ''; ?> type="radio" name="ajax" value="N" id="ajaxN" /> <?php _e('Regular POST', $this -> plugin_name); ?></label>
				<label><input <?php echo ($this -> get_option('ajax') == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="ajax" value="Y" id="ajaxY" /> <?php _e('Ajax Request', $this -> plugin_name); ?></label>
			</td>
		</tr>
		<tr>
			<th><label for="captchaoff"><?php _e('Security Check', $this -> plugin_name); ?></label></th>
			<td>
				<label><input onchange="change_captcha(this.value);" <?php echo ($this -> get_option('captcha') == "image") ? 'checked="checked"' : ''; ?> type="radio" name="captcha" value="image" /> <?php _e('Captcha Image', $this -> plugin_name); ?> <br/><small><?php _e('turing number image. requires <a href="http://wordpress.org/extend/plugins/really-simple-captcha/" target="_blank">Really Simple Captcha</a> plugin.', $this -> plugin_name); ?></small></label><br/>
				<label><input onchange="change_captcha(this.value);" <?php echo ($this -> get_option('captcha') == "human") ? 'checked="checked"' : ''; ?> type="radio" name="captcha" value="human" /> <?php _e('Human Check', $this -> plugin_name); ?> <br/><small><?php _e('mathematical calculation', $this -> plugin_name); ?></small></label><br/>
				<label><input onchange="change_captcha(this.value);" <?php echo (!$this -> get_option('captcha') || $this -> get_option('captcha') == "off") ? 'checked="checked"' : ''; ?> type="radio" name="captcha" value="off" id="captchaoff" /> <?php _e('OFF', $this -> plugin_name); ?></label>
				<span class="howto"><?php _e('displays an image or calculation for accurate human input recognition', $this -> plugin_name); ?></span>
                
                <script type="text/javascript">
				function change_captcha(captcha) {
					jQuery('#captchacolor_div').hide();
					
					if (captcha == "image") {
						jQuery('#captchacolor_div').show();	
					}
				}
				</script>
			</td>
		</tr>
    </tbody>
</table>

<div id="captchacolor_div" style="display:<?php echo ($this -> get_option('captcha') == "image") ? 'block' : 'none'; ?>;">
	<table class="form-table">
    	<tbody>
        	<tr>
            	<th><label for="captchacolor"><?php _e('Background Color', $this -> plugin_name); ?></label></th>
                <td>
                	<input type="text" name="captchacolor" value="<?php echo esc_attr(stripslashes($this -> get_option('captchacolor'))); ?>" id="captchacolor" />
                    <span class="howto"><?php _e('hexadecimal background color of the captcha image eg. #FFFFFF', $this -> plugin_name); ?></span>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<table class="form-table">
	<tbody>
		<tr>
			<th><label for="output_full"><?php _e('WHOIS Output', $this -> plugin_name); ?></label></th>
			<td>
				<label><input onclick="jQuery('#output_div').show();" <?php echo ($this -> get_option('output') == "full") ? 'checked="checked"' : ''; ?> type="radio" name="output" value="full" id="output_full" /> <?php _e('Full Information', $this -> plugin_name); ?></label><br/>
				<label><input onclick="jQuery('#output_div').hide();" <?php echo ($this -> get_option('output') == "short") ? 'checked="checked"' : ''; ?> type="radio" name="output" value="short" id="output_short" /> <?php _e('Short: Available/Not Available Only', $this -> plugin_name); ?></label>
			</td>
		</tr>
	</tbody>
</table>

<div id="output_div" style="display:<?php echo ($this -> get_option('output') == "full") ? 'block' : 'none'; ?>;">
	<table class="form-table">
		<tbody>
			<tr>
				<th><label for="highlightY"><?php _e('Highlight Fields', $this -> plugin_name); ?></label></th>
				<td>
					<label><input <?php echo ($this -> get_option('highlight') == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="highlight" value="Y" id="highlightY" /> <?php _e('Yes', $this -> plugin_name); ?></label>
					<label><input <?php echo ($this -> get_option('highlight') == "N") ? 'checked="checked"' : ''; ?> type="radio" name="highlight" value="N" /> <?php _e('No', $this -> plugin_name); ?></label>
					<span class="howto"><?php _e('highlight important fields in the whois output (eg. status, nameservers, etc)', $this -> plugin_name); ?></span>
				</td>
			</tr>
			<tr>
				<th><label for="cleanoutputY"><?php _e('Clean Output', $this -> plugin_name); ?></label></th>
				<td>
					<label><input <?php echo ($this -> get_option('cleanoutput') == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="cleanoutput" value="Y" id="cleanoutputY" /> <?php _e('Yes', $this -> plugin_name); ?></label>
					<label><input <?php echo ($this -> get_option('cleanoutput') == "N") ? 'checked="checked"' : ''; ?> type="radio" name="cleanoutput" value="N" /> <?php _e('No', $this -> plugin_name); ?></label>
					<span class="howto"><?php _e('"clean" the whois output of extraneous text', $this -> plugin_name); ?></span>
				</td>
			</tr>
		</tbody>
	</table>
</div>

<table class="form-table">
	<tbody>
		<tr>
			<th><label for="redirectN"><?php _e('Redirect When "Available"', $this -> plugin_name); ?></label></th>
			<td>
				<label><input <?php echo ($this -> get_option('redirect') == "Y") ? 'checked="checked"' : ''; ?> onclick="jQuery('#redirect_div').show();" type="radio" name="redirect" value="Y" id="redirectY" /> <?php _e('Yes', $this -> plugin_name); ?></label>
				<label><input <?php echo ($this -> get_option('redirect') == "N") ? 'checked="checked"' : ''; ?> onclick="jQuery('#redirect_div').hide();" type="radio" name="redirect" value="N" id="redirectN" /> <?php _e('No', $this -> plugin_name); ?></label>
				<span class="howto"><?php _e('when a domain searched for is available, the plugin can do a redirect', $this -> plugin_name); ?></span>
			</td>
		</tr>
	</tbody>
</table>

<div id="redirect_div" style="display:<?php echo ($this -> get_option('redirect') == "Y") ? 'block' : 'none'; ?>;">
	<table class="form-table">
		<tbody>
			<tr>
				<th><label for="redirecturl"><?php _e('Redirect URL', $this -> plugin_name); ?></label></th>
				<td>
					<input class="widefat" style="width:100%;" type="text" name="redirecturl" value="<?php echo $this -> get_option('redirecturl'); ?>" id="redirecturl" />
					<span class="howto">
						<?php _e('URL to redirect to when a domain is available (not registered)', $this -> plugin_name); ?><br/>
						<?php _e('you may use <code>{domain}</code> and <code>{tld}</code> in the URL where you want it to be replaced', $this -> plugin_name); ?>
					</span>
				</td>
			</tr>
			<tr>
				<th><label for="redirecttarget_sam"><?php _e('Redirect Target', $this -> plugin_name); ?></label></th>
				<td>
					<label><input <?php echo ($this -> get_option('redirecttarget') == "sam") ? 'checked="checked"' : ''; ?> type="radio" name="redirecttarget" value="sam" id="redirecttarget_sam" /> <?php _e('Same Window', $this -> plugin_name); ?></label>
					<label><input <?php echo ($this -> get_option('redirecttarget') == "new") ? 'checked="checked"' : ''; ?> type="radio" name="redirecttarget" value="new" id="redirecttarget_new" /> <?php _e('New Window', $this -> plugin_name); ?></label>
					<span class="howto"><?php _e('open "Redirect URL" above in the same or a new window', $this -> plugin_name); ?></span>
				</td>
			</tr>
		</tbody>
	</table>
</div>

<!-- Cache -->
<table class="form-table">
	<tbody>
		<tr>
			<th><label for="cache_N"><?php _e('Turn on Cache', $this -> plugin_name); ?></label></th>
			<td>
				<label><input <?php echo ($this -> get_option('cache') == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="cache" value="Y" id="cache_Y" /> <?php _e('On', $this -> plugin_name); ?></label>
				<label><input <?php echo ($this -> get_option('cache') == "N") ? 'checked="checked"' : ''; ?> type="radio" name="cache" value="N" id="cache_N" /> <?php _e('Off', $this -> plugin_name); ?></label>
				
				<?php 
				
				$cache = $this -> plugin_base() . DS . 'vendors' . DS . 'samswhois' . DS . 'cache';
				
				if (!is_writable($cache)) {
					?><p class="<?php echo $this -> pre; ?>error"><?php _e('Cache directory is NOT writable. Please CHMOD wp-whois/vendors/samswhois/cache to 0777.', $this -> plugin_name); ?></p><?php
				}
				
				?>
			</td>
		</tr>
        <tr>
        	<th><label for="tinymcebutton_Y"><?php _e('TinyMCE Button', $this -> plugin_name); ?></label></th>
            <td>
            	<label><input <?php echo ($this -> get_option('tinymcebutton') == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="tinymcebutton" value="Y" id="tinymcebutton_Y" /> <?php _e('On', $this -> plugin_name); ?></label>
                <label><input <?php echo ($this -> get_option('tinymcebutton') == "N") ? 'checked="checked"' : ''; ?> type="radio" name="tinymcebutton" value="N" id="tinymcebutton_N" /> <?php _e('Off', $this -> plugin_name); ?></label>
            	<span class="howto"><?php _e('turn this On for a TinyMCE button to quickly insert WHOIS form', $this -> plugin_name); ?></span>
            </td>
        </tr>
	</tbody>
</table>