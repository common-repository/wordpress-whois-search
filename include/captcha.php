<?php

define('DS', DIRECTORY_SEPARATOR);
include_once('../../../../wp-config.php');
include_once(ABSPATH . 'wp-admin' . DS . 'admin-functions.php');

if (class_exists('ReallySimpleCaptcha')) {
	$captcha = new ReallySimpleCaptcha();
	$word = $captcha -> generate_random_word();
	$prefix = mt_rand();
    $filename = $captcha -> generate_image($prefix, $word);
	echo rtrim(get_bloginfo('wpurl'), '/') . '/wp-content/plugins/really-simple-captcha/tmp/' . $filename;
}

?>