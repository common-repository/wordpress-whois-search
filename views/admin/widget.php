<p>
	<label for="<?php echo $this -> pre; ?>_widget_<?php echo $number; ?>_title">
		<?php _e('Title', $this -> plugin_name); ?> :
		<input class="widefat" type="text" name="<?php echo $this -> pre; ?>-widget[<?php echo $number; ?>][title]" value="<?php echo $options[$number]['title']; ?>" id="<?php echo $this -> pre; ?>_widget_<?php echo $number; ?>_title" />
	</label>
</p>

<p>
	<label for="<?php echo $this -> pre; ?>_widget_<?php echo $number; ?>_reflinkN"><?php _e('Show Link when FREE', $this -> plugin_name); ?> :</label>
		<label><input onclick="jQuery('#reflinkdiv<?php echo $number; ?>').show();" <?php echo ($options[$number]['reflink'] == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="<?php echo $this -> pre; ?>-widget[<?php echo $number; ?>][reflink]" value="Y" id="<?php echo $this -> pre; ?>_widget_<?php echo $number; ?>_reflinkY" /> <?php _e('Yes', $this -> plugin_name); ?></label>
		<label><input onclick="jQuery('#reflinkdiv<?php echo $number; ?>').hide();" <?php echo (empty($options[$number]['reflink']) || $options[$number]['reflink'] == "N") ? 'checked="checked"' : ''; ?> type="radio" name="<?php echo $this -> pre; ?>-widget[<?php echo $number; ?>][reflink]" value="N" id="<?php echo $this -> pre; ?>_widget_<?php echo $number; ?>_reflinkN" /> <?php _e('No', $this -> plugin_name); ?></label>
		<span class="howto"><small><?php _e('Link will be shown when domain is available', $this -> plugin_name); ?></small></span>
	
</p>

<div id="reflinkdiv<?php echo $number; ?>" style="display:<?php echo ($options[$number]['reflink'] == "Y") ? 'block' : 'none'; ?>;">
	<p>
		<label for="<?php echo $this -> pre; ?>_widget_<?php echo $number; ?>_reflinktitle">
			<?php _e('Referral Link Title', $this -> plugin_name); ?>
			<input type="text" class="widefat" name="<?php echo $this -> pre; ?>-widget[<?php echo $number; ?>][reflinktitle]" value="<?php echo $options[$number]['reflinktitle']; ?>" id="<?php echo $this -> pre; ?>_widget_<?php echo $number; ?>_reflinktitle" />
			<span class="howto"><small><?php _e('anchor text for the referral link', $this -> plugin_name); ?></small></span>
		</label>
	</p>
	
	<p>
		<label for="<?php echo $this -> pre; ?>_widget_<?php echo $number; ?>_reflinkurl">
			<?php _e('Referral Link URL', $this -> plugin_name); ?> :
			<input class="widefat" type="text" name="<?php echo $this -> pre; ?>-widget[<?php echo $number; ?>][reflinkurl]" value="<?php echo $options[$number]['reflinkurl']; ?>" id="<?php echo $this -> pre; ?>_widget_<?php echo $number; ?>_reflinkurl" />
			<span class="howto"><small><?php _e('URL of referral link to show', $this -> plugin_name); ?></small></span>
		</label>
	</p>
	
	<p>
		<label for="<?php echo $this -> pre; ?>_widget_<?php echo $number; ?>_reflinktargetself">
			<?php _e('Link Target', $this -> plugin_name); ?> :</label>
			<label><input <?php echo (empty($options[$number]['reflinktarget']) || $options[$number]['reflinktarget'] == "_self") ? 'checked="checked"' : ''; ?> type="radio" name="<?php echo $this -> pre; ?>-widget[<?php echo $number; ?>][reflinktarget]" value="_self" id="<?php echo $this -> pre; ?>_widget_<?php echo $number; ?>_reflinktargetself" /> <?php _e('Same Window', $this -> plugin_name); ?></label>
			<label><input <?php echo ($options[$number]['reflinktarget'] == "_blank") ? 'checked="checked"' : ''; ?> type="radio" name="<?php echo $this -> pre; ?>-widget[<?php echo $number; ?>][reflinktarget]" value="_blank" id="<?php echo $this -> pre; ?>_widget_<?php echo $number; ?>_reflinktargetblank" /> <?php _e('New Window', $this -> plugin_name); ?></label>
			<span class="howto"><small><?php _e('let the link open in the same or a new window', $this -> plugin_name); ?></small></span>
		
	</p>
</div>