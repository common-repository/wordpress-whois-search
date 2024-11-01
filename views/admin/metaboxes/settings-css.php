<table class="form-table">
	<tbody>
		<tr>
			<th><label for="customcssN"><?php _e('Custom CSS', $this -> plugin_name); ?></label></th>
			<td>
				<label><input onclick="jQuery('#cssdiv').show();" <?php echo ($this -> get_option('customcss') == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="customcss" value="Y" id="customcssY" /> <?php _e('Yes', $this -> plugin_name); ?></label>
				<label><input onclick="jQuery('#cssdiv').hide();" <?php echo ($this -> get_option('customcss') == "N") ? 'checked="checked"' : ''; ?> type="radio" name="customcss" value="N" id="customcssN" /> <?php _e('No', $this -> plugin_name); ?></label>
				<span class="howto"><?php _e('use custom CSS for the front-end', $this -> plugin_name); ?></span>
			</td>
		</tr>
	</tbody>
</table>

<div id="cssdiv" style="display:<?php echo ($this -> get_option('customcss') == "Y") ? 'block' : 'none'; ?>;">
	<?php
	
	$cssname = "swstyles.css";
	$csspath = $this -> plugin_base . DS . 'vendors' . DS . 'samswhois' . DS;
	$cssfull = $csspath . $cssname;
	
	if (!is_writable($cssfull)) {
		?>
		
		<p class="<?php echo $this -> pre; ?>error"><?php _e('Stylesheet wp-whois/vendors/samswhois/' . $cssname . ' is not writable. Please CHMOD to 0777.', $this -> plugin_name); ?></p>
		
		<?php
	}
	
	?>

	<textarea class="widefat" name="css" style="width:100%;" rows="10"><?php echo htmlentities($this -> get_option('css')); ?></textarea>
</div>