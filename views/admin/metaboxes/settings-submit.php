<div class="submitbox" id="submitpost">
	<div id="minor-publishing">
		<div id="misc-publishing-actions">
			<div class="misc-pub-section">
				<a href="<?php echo $this -> url; ?>&amp;method=reset" title="<?php _e('Restore all configuration settings to their defaults', $this -> plugin_name); ?>" onclick="if (!confirm('<?php _e('Are you sure you wish to reset all configuration settings to their defaults?', $this -> plugin_name); ?>')) { return false; }"><?php _e('Reset Configuration Settings', $this -> plugin_name); ?></a>
			</div>
            <div class="misc-pub-section misc-pub-section-last">
            	<a href="<?php echo $this -> url; ?>&amp;method=reloadtlds" title="<?php _e('Reload any changes to TLDs in the config file', $this -> plugin_name); ?>"><?php _e('Reload all TLDs from config file', $this -> plugin_name); ?></a>
            </div>
		</div>
	</div>
	<div id="major-publishing-actions">
		<div id="publishing-action">
			<input class="button-primary" type="submit" name="save" value="<?php _e('Save Configuration', $this -> plugin_name); ?>" />
		</div>
		<br class="clear" />
	</div>
</div>