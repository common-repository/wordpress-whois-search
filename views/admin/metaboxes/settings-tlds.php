<?php $alltlds = $this -> get_option('alltlds'); ?>
<?php $usetlds = $this -> get_option('usetlds'); ?>

<?php if (!empty($alltlds)) : ?>
	<label style="font-weight:bold;"><input type="checkbox" name="checkboxall" value="checkboxall" id="checkboxall" /> <?php _e('Check All', $this -> plugin_name); ?></label><br/>
	<label style="font-weight:bold;"><input type="checkbox" name="checkinvert" value="checkinvert" id="checkinvert" /> <?php _e('Inverse Selection', $this -> plugin_name); ?></label><br class="clear" />

	<div style="margin:10px 0 0 0; overflow:auto; max-height:200px;">	
		<ul id="tldslist">
			<?php foreach ($alltlds as $tld) : ?>
				<li class="dragitem" id="item_<?php echo $tld; ?>">
					<span class="draghandle"><img src="<?php echo $this -> url(); ?>/images/drag_handle.gif" alt="drag" /></span>
					<label><input <?php echo (!empty($usetlds) && in_array($tld, $usetlds)) ? 'checked="checked"' : ''; ?> type="checkbox" name="usetlds[]" value="<?php echo $tld; ?>" id="checklist<?php echo $tld; ?>" /> .<?php echo $tld; ?></label>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
    
    <div id="<?php echo $this -> pre; ?>message" style="display:none;"></div>
	
	<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery("ul#tldslist").sortable({
			start: function(request) {
				jQuery("#<?php echo $this -> pre; ?>message").slideUp();
			},
			stop: function(request) {					
				jQuery("#<?php echo $this -> pre; ?>message").load(ajaxurl + "?action=wpwhoisordertlds", jQuery("ul#tldslist").sortable('serialize')).slideDown("slow");
			},
			axis: "y",
		});
	});
	</script>
	
	<style type="text/css">
	.dragitem {
		list-style: none;
		margin: 3px 0px;
		padding: 2px 5px 2px 5px;
		background-color: #F9F9F9;
		border:1px solid #B2B2B2;
		vertical-align: middle !important;
		display: block;
	}
	
	span.draghandle {
		cursor: move;
		vertical-align: middle;
	}
	</style>
<?php else : ?>
	<p class="<?php echo $this -> pre; ?>error"><?php _e('No domain TLDs were found. Please check your configuration file', $this -> plugin_name); ?></p>
<?php endif; ?>