<?php

/* WordPress WHOIS plugin Ajax */

if (!defined('DS')) { define('DS', DIRECTORY_SEPARATOR); }

$root = __FILE__;
for ($i = 0; $i < 4; $i++) $root = dirname($root);
for ($i = 0; $i < 3; $i++) $rootup = dirname($root);

if (file_exists($root . DS . 'wp-config.php')) {
	require_once($root . DS . 'wp-config.php');
} else {
	require_once($rootup . DS . 'wp-config.php');
}

include_once(ABSPATH . 'wp-admin' . DS . 'admin-functions.php');

class wpWhoisAjax extends wpWhoisPlugin {

	var $safecommands = array();

	function wpWhoisAjax($cmd) {
		$this -> register_plugin('wp-whois', __FILE__);
	
		if (!empty($cmd)) {
			if ((!empty($this -> safecommands) && in_array($cmd, $this -> safecommands)) || current_user_can('edit_plugins')) {			
				if (method_exists($this, $cmd)) {
					$this -> $cmd();
				}
			}
		}
	}
}

$cmd = $_GET['cmd'];
$wpWhoisAjax = new wpWhoisAjax($cmd);

?>