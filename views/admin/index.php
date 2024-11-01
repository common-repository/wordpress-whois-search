<?php

global $user_ID, $post, $post_ID, $wp_meta_boxes;
$post_ID = 1;

wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false);
wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false); 

?>

<div class="wrap">
	<h2><?php _e('WHOIS', $this -> plugin_name); ?> <?php echo $this -> version; ?></h2>
	<form action="<?php echo $this -> url; ?>" method="post">
    	<?php wp_nonce_field($this -> sections -> welcome); ?>
    
		<div id="poststuff" class="metabox-holder has-right-sidebar">
			<div id="side-info-column" class="inner-sidebar">
            	<?php do_action('submitpage_box'); ?>
				<?php $side_meta_boxes = do_meta_boxes("tools_page_" . $this -> sections -> welcome, 'side', $post); ?>
			</div>
			<div id="post-body" class="has-sidebar">
				<div id="post-body-content" class="has-sidebar-content">
					<?php do_meta_boxes("tools_page_" . $this -> sections -> welcome, 'normal', $post); ?>
					<?php do_meta_boxes("tools_page_" . $this -> sections -> welcome, 'advanced', $post); ?>
				</div>
			</div>
			<br class="clear" />
		</div>
	</form>
</div>