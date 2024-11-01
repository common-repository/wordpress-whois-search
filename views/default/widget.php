<?php if (empty($isajax) || $isajax == false) : ?>
	<?php echo $args['before_title']; ?><?php echo $options['title']; ?><?php echo $args['after_title']; ?>
	<div id="<?php echo $this -> pre; ?>inside-<?php echo $options['number']; ?>">
<?php endif; ?>
    
<?php

ob_start();
$swWidget = true;
$swOptions = $options;
include($this -> samswhoisinc);
$whois = ob_get_clean();
echo $whois;

?>

<?php if (empty($isajax) || $isajax == false) : ?>
</div>
<?php endif; ?>