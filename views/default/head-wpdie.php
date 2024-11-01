<script type="text/javascript" src="<?php echo get_option('siteurl'); ?>/wp-includes/js/scriptaculous/prototype.js"></script>
<script type="text/javascript" src="<?php echo get_option('siteurl'); ?>/wp-includes/js/scriptaculous/scriptaculous.js?load=effects"></script>
<script type="text/javascript" src="<?php echo get_option('siteurl'); ?>/wp-includes/js/jquery/jquery.js"></script>

<div align="center">
	<h1 id="logo"><?php echo $title; ?></h1>
</div>

<link rel="stylesheet" href="<?php echo $this -> url(); ?>/css/default/<?php echo $this -> plugin_name; ?>-wpdie.css" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php echo $this -> url(); ?>/vendors/samswhois/swstyles.css" type="text/css" media="screen" />

<script type="text/javascript">
var wpwhoisAjax = "<?php echo rtrim($this -> url(), '/'); ?>/<?php echo $this -> plugin_name; ?>-ajax.php";
var wpwhoisUrl = "<?php echo $this -> url(); ?>";
$<?php echo $this -> pre; ?> = jQuery.noConflict();
</script>

<script type="text/javascript" src="<?php echo $this -> url(); ?>/js/<?php echo $this -> plugin_name; ?>-wpdie.js"></script>
<script type="text/javascript" src="<?php echo $this -> url(); ?>/js/<?php echo $this -> plugin_name; ?>.js"></script>