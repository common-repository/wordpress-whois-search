<?php

/*
Plugin Name: WHOIS
Plugin URI: http://tribulant.com/plugins/view/12/wordpress-whois-plugin
Author: Tribulant Software
Author URI: http://tribulant.com
Description: Place a domain whois search form on your WordPress website for users to use do domain searches and get information on domains.
Version: 1.4.2.4
*/

//directory separator constant
if (!defined('DS')) { define('DS', DIRECTORY_SEPARATOR); }

//Include the 'wp-whois-plugin.php' file for the use of the wpWhoisPlugin class
require_once(dirname(__FILE__) . DS . 'wp-whois-plugin.php');

//wpWhois class
//extends to the wpWhoisPlugin helper class. 'wp-whois-plugin.php'

class wpWhois extends wpWhoisPlugin {

	//global name of the plugin
	var $name = 'wp-whois';

	function wpWhois() {
		$url = explode("&", $_SERVER['REQUEST_URI']);
		//current, absolute URL to the plugin folder/directory
		$this -> url = $url[0];
	
		//register the plugin $name & $base
		$this -> register_plugin($this -> name, __FILE__);
		//enqueue necessary JavaScript scripts
		$this -> enqueue_scripts();
		//initialize the plugin options
		$this -> initialize_options();
		
		//WordPress Action Hooks
		$this -> add_action('admin_menu');
		$this -> add_action('admin_head');
		$this -> add_action('wp_head');
		$this -> add_action('widgets_init', 'widget_register');
		$this -> add_action('admin_notices');
		$this -> add_action('init', 'init_getpost');
		$this -> add_action('init', 'init_textdomain');
		$this -> add_action('after_plugin_row_' . plugin_basename(__FILE__), 'after_plugin_row', 1, 2);
		
		/* Ajax hooks */
		add_action('wp_ajax_wpwhoisform', array($this, 'ajax_wpwhoisform'));
		add_action('wp_ajax_nopriv_wpwhoisform', array($this, 'ajax_wpwhoisform'));
		add_action('wp_ajax_wpwhoisordertlds', array($this, 'ajax_ordertlds'));
		
		$this -> add_action('wp_print_styles', 'print_styles');
		$this -> add_action('admin_print_styles', 'print_styles');
		$this -> add_action('wp_print_scripts', 'print_scripts');
		$this -> add_action('admin_print_scripts', 'print_scripts');
		
		if ($this -> get_option('tinymcebutton') == "Y") {
			$this -> add_action('admin_init', 'tinymce');
		}
		
		add_action('edit_form_advanced', array($this, 'addquicktags'));
		add_action('edit_page_form', array($this, 'addquicktags'));
		
		//WordPress Filter Hooks
		$this -> add_filter('the_content');
		
		//WordPress shortcodes
		add_shortcode('whois', array($this, 'sc_whois'));
		
		return true;
	}
	
	function ajax_wpwhoisform() {
		$isajax = true;
		
		if (!empty($_REQUEST['widget']) && $_REQUEST['widget'] == "Y") {
			global $wp_registered_sidebars;
			if (!empty($wp_registered_sidebars)) {
				foreach ($wp_registered_sidebars as $skey => $sidebar) {
					$args = $sidebar;
					break 1;
				}
			}
			
			$number = $_REQUEST['number'];
			$options = $this -> get_option('-widget');
			$options = $options[$number];
			$options['number'] = $number;
			
			$this -> render('widget', array('isajax' => $isajax, 'args' => $args, 'options' => $options), true, 'default');
		} else {
			ob_start();
			$swWidget = false;
			$number = $_REQUEST['ms'];
			include($this -> samswhoisinc);
			$whois = ob_get_clean();
			print $whois;
		}
			
		exit();
		die();
	}
	
	function ajax_ordertlds() {
		$tldslist = $_REQUEST['item'];
		
		if (!empty($tldslist)) {
			$this -> update_option('alltlds', $tldslist);
			_e('Selected TLDs and their order have been saved', $this -> plugin_name);
		} else {
			_e('TLDs list seems to be empty. Please debug.', $this -> plugin_name);	
		}
		
		exit();
		die();
	}
	
	function admin_menu() {
		$menus = array();
		
		//add a WordPress management page under "Tools" in the Dashboard
		$menus['whois'] = add_management_page(__('WHOIS', $this -> plugin_name), __('WHOIS', $this -> plugin_name), 10, $this -> sections -> welcome, array($this, 'admin'));
		
		$this -> debug($menus);
		
		add_action('admin_head-' . $menus['whois'], array($this, 'admin_head_whois'));
	}
	
	function admin_head() {
		$this -> render('head', false, true, 'admin');
	}
	
	function admin_head_whois() {		
		add_meta_box('submitdiv', __('Configuration Settings', $this -> plugin_name), array($this, 'metabox_settingssubmit'), "tools_page_" . $this -> sections -> welcome, 'side', 'core');
		add_meta_box('credits', __('Support &amp; Credits', $this -> plugin_name), array($this, 'metabox_settingscredits'), "tools_page_" . $this -> sections -> welcome, 'side', 'core');
		add_meta_box('general', __('General Settings', $this -> plugin_name), array($this, 'metabox_settingsgeneral'), "tools_page_" . $this -> sections -> welcome, 'normal', 'core');
		add_meta_box('tlds', __('TLD Settings', $this -> plugin_name), array($this, 'metabox_settingstlds'), "tools_page_" . $this -> sections -> welcome, 'normal', 'core');
		add_meta_box('css', __('CSS Configuration', $this -> plugin_name), array($this, 'metabox_settingscss'), "tools_page_" . $this -> sections -> welcome, 'normal', 'core');	
		
		do_action('do_meta_boxes', "tools_page_" . $this -> sections -> welcome, 'side');
		do_action('do_meta_boxes', "tools_page_" . $this -> sections -> welcome, 'normal');
		do_action('do_meta_boxes', "tools_page_" . $this -> sections -> welcome, 'advanced');
	}
	
	function wp_head() {
		$this -> render('head', false, true, 'default');
	}
	
	function widget_register() {		
		if (function_exists('register_sidebar_widget')) {
			if (!$options = get_option($this -> pre . '-widget')) {
				$options = array();
			}
		
			$widget_options = array('classname' => $this -> pre . '-widget', 'description' => __('Insert a domain whois search form into your sidebar(s)', $this -> plugin_name));	
			$control_options = array('id_base' => $this -> pre, 'width' => 350, 'height' => 300);	
			$name = __('WHOIS', $this -> plugin_name);
			
			if (!empty($options)) {
				foreach ($options as $okey => $oval) {
					$id = $this -> pre . '-' . $okey;
										
					wp_register_sidebar_widget($id, $name, array($this, $this -> pre . '_widget'), $widget_options, array('number' => $okey));
					wp_register_widget_control($id, $name, array($this, $this -> pre . '_widget_control'), $control_options, array('number' => $okey));
				}
			} else {
				$id = $this -> pre . '-1';
				wp_register_sidebar_widget($id, $name, array($this, $this -> pre . '_widget'), $widget_options, array('number' => -1));
				wp_register_widget_control($id, $name, array($this, $this -> pre . '_widget_control'), $control_options, array('number' => -1));
			}
		}
	}
	
	function admin_notices() {
		return true;
	}
	
	function tinymce() {
		if (!current_user_can('edit_posts') && !current_user_can('edit_pages')) return;

		// Add TinyMCE buttons when using rich editor
		if (get_user_option('rich_editing') == 'true') {
			add_filter('mce_buttons', array($this, 'mcebutton'));
			add_filter('mce_buttons_3', array($this, 'mcebutton3'));
			add_filter('mce_external_plugins', array($this, 'mceplugin'));
		}
	}
	
	function mcebutton($buttons) {	
		array_push($buttons, "WHOIS");		
		return $buttons;
	}
	
	function mcebutton3($buttons) {
		//Viper's Video Quicktags compatibility
		if (!empty($_GET['page']) && ($_GET['page'] == $this -> sections -> send || $_GET['page'] == $this -> sections -> templates_save)) {
			if (!empty($buttons)) {
				foreach ($buttons as $bkey => $bval) {
					if (preg_match("/\v\v\q(.*)?/si", $bval, $match)) {
						unset($buttons[$bkey]);
					}
				}
			}
		}
		
		return $buttons;
	}

	function mceplugin($plugins) {
		$url = $this -> url() . '/js/tinymce/editor_plugin.js';
		$plugins['WHOIS'] = $url;
		
		//Viper's Video Quicktags compatibility
		if (!empty($_GET['page']) && ($_GET['page'] == $this -> sections -> send || $_GET['page'] == $this -> sections -> templates_save)) {
			if (isset($plugins['vipersvideoquicktags'])) {
				unset($plugins['vipersvideoquicktags']);
			}
		}
		
		return $plugins;
	}
	
	function init_getpost() {		
		if (!empty($_REQUEST[$this -> pre . 'method'])) {
			switch ($_REQUEST[$this -> pre . 'method']) {
				case 'whois'		:
					$title = __('WHOIS', $this -> plugin_name) . " : " . $_REQUEST['domain'] . "." . $_REQUEST['tld'];
						
					ob_start();
					$this -> render('head-wpdie', array('wp_die' => true, 'title' => $title), true, 'default');					
					$content = ob_get_clean();
					
					ob_start();
					$swWidget = false;
					$swSecure = false;
					$swHuman = false;
					$swForm = false;
					include($this -> samswhoisinc);
					$content2 = ob_get_clean();
					$content .= $content2;
					
					$this -> render('wp-die', array('title' => $title, 'content' => $content), true, 'default');
					break;
			}
		}
		
		return true;
	}
	
	function init_textdomain() {		
		if (function_exists('load_plugin_textdomain')) {
			load_plugin_textdomain($this -> plugin_name, $this -> plugin_name . DS . 'languages', dirname(plugin_basename(__FILE__)) . DS . 'languages');
		}	
	}
	
	function after_plugin_row($file = "", $info = array()) {
		$columns = substr($wp_version, 0, 3) == "2.8" ? 3 : 5;
		
		if (!empty($this -> adversion) && $this -> adversion == true) {
			$adversionmsg = __('You are running the free, ad links version. Please ', $this -> plugin_name) . '<a href="http://tribulant.com/products/view/12/wordpress-whois-plugin" target="_blank" title="' . __('WordPress WHOIS Plugin', $this -> plugin_name) . '">' . __('purchase the WHOIS plugin', $this -> plugin_name) . '</a>' . __(' to remove all the ad links.', $this -> plugin_name);
		
			?>
			
			<tr class="plugin-update-tr">
				<td colspan="<?php echo $columns; ?>" class="plugin-update">
					<div class="update-message">
						<?php echo $adversionmsg; ?>
					</div>
				</td>
			</tr>
			
			<?php
		}
		
		return false;
	}
	
	function widget($display = 'cart', $args = array()) {
		if (!empty($display)) {
			foreach ($args as $akey => $aval) {
				$this -> update_option($display . '_' . $akey, $aval);
			}
			
			$number = substr(md5(rand(1, 999)), 0, 6);
			
			echo $this -> get_option($display . '_before_widget');
			$this -> render('widget-' . $display, array('args' => $args, 'options' => array('number' => $number, 'title' => $this -> get_option($display . '_title'))), true, 'default');
			echo $this -> get_option($display . '_after_widget');
		}
		
		return false;
	}
	
	function wpwhois_widget($args = array(), $widget_args = array()) {		
		extract($args, EXTR_SKIP);
		
		if (is_numeric($widget_args)) {
			$widget_args = array('number' => $widget_args);
		}
			
		$widget_args = wp_parse_args($widget_args, array('number' => -1));
		extract($widget_args, EXTR_SKIP);
	
		$options = get_option($this -> pre . '-widget');		
		if (empty($options[$number])) {
			return;
		}
		
		$options[$number]['number'] = $number;
		
		echo $args['before_widget'];
		$this -> render('widget', array('args' => $args, 'options' => $options[$number]), true, 'default');
		echo $args['after_widget'];
	}
	
	function wpwhois_widget_control($widget_args = array()) {
		global $wp_registered_widgets;
		static $updated = false;
		
		if (is_numeric($widget_args)) {
			$widget_args = array('number' => $widget_args);
		}
			
		$widget_args = wp_parse_args($widget_args, array('number' => -1));
		
		if (!empty($widget_args['number']) && is_array($widget_args['number'])) {
			extract($widget_args['number'], EXTR_SKIP);
		} else {
			extract($widget_args, EXTR_SKIP);
		}
		
		$options = get_option($this -> pre . '-widget');
		if (empty($options) || !is_array($options)) {
			$options = array();
		}
	
		if (!$updated && !empty($_POST['sidebar'])) {
			$sidebar = $_POST['sidebar'];
			$sidebars_widgets = wp_get_sidebars_widgets();
			
			if (!empty($sidebars_widgets[$sidebar])) {
				$this_sidebar = $sidebars_widgets[$sidebar];
			} else {
				$this_sidebar = array();
			}

			if (!empty($this_sidebar)) {			
				foreach ($this_sidebar as $_widget_id ) {
					if ($this -> pre . '_widget' == $wp_registered_widgets[$_widget_id]['callback'] && isset($wp_registered_widgets[$_widget_id]['params'][0]['number'])) {
						$widget_number = $wp_registered_widgets[$_widget_id]['params'][0]['number'];
						
						if (!in_array($this -> pre . "-" . $widget_number, $_POST['widget-id'])) {
							unset($options[$widget_number]);
						}
					}
				}
			}

			if (!empty($_POST[$this -> pre . '-widget'])) {					
				foreach ($_POST[$this -> pre . '-widget'] as $widget_number => $widget_values) {
					if (!isset($widget_values['title']) && isset($options[$widget_number])) {
						continue;
					}
					
					$title = strip_tags(stripslashes($widget_values['title']));	
					$reflink = $widget_values['reflink'];
					$reflinktitle = $widget_values['reflinktitle'];
					$reflinkurl = $widget_values['reflinkurl'];
					$reflinktarget = $widget_values['reflinktarget'];			
					$options[$widget_number] = compact('title', 'reflink', 'reflinktitle', 'reflinkurl', 'reflinktarget');
				}
			}
	
			update_option($this -> pre . '-widget', $options);
			$updated = true;
		}
		
		if (-1 == $number) {
			$number = '%i%';
		}
		
		if (empty($_POST)) {
			$this -> render('widget', array('options' => $options, 'number' => $number), true, 'admin');
		}
	}
	
	function the_content($content = '') {	
		if (!empty($content)) {				
			if (preg_match($this -> whois_ptrn, $content, $matches)) {
				$content = preg_replace_callback($this -> whois_ptrn, array($this, 'replace_whois'), $content);
			}
		
			return $content;
		}
		
		return false;
	}
	
	function sc_whois($atts = array(), $content = null) {	
		ob_start();
		$this -> whois();
		$whois = ob_get_clean();
		return $whois;
	}
	
	function admin() {
		global $wpdb;
	
		if (!empty($_POST) || !empty($_REQUEST['method'])) {
			unset($_POST['save']);
			
			switch ($_REQUEST['method']) {
				case 'reloadtlds'		:
					ob_start();
					include($this -> samswhoisinc);
					ob_get_clean();
					
					$tlds = array();
					if (!empty($whois -> m_usetlds)) {
						foreach ($whois -> m_usetlds as $t => $i) {
							$tlds[] = $t;
						}
					}
					
					$this -> update_option('alltlds', $tlds);
					
					$message = __('TLDs have been updated accordingly.', $this -> plugin_name);
					$this -> render_msg($message);
					break;
				case 'reset'			:
					$query = "DELETE FROM `" . $wpdb -> options . "` WHERE `option_name` LIKE '" . $this -> pre . "%'";
					
					if ($wpdb -> query($query)) {					
						$message = __('Configuration settings have been reset to their defaults', $this -> plugin_name);
						$this -> render_msg($message);
					} else {
						$message = __('Configuration settings could not be reset', $this -> plugin_name);
						$this -> render_err($message);
					}
					break;
				default					:			
					foreach ($_POST as $pkey => $pval) {
						if (!empty($pval) || $pval == "0") {
							$this -> update_option($pkey, $pval);
							
							//absolute path to the directory of the CSS file
							$csspath = $this -> plugin_base . DS . 'vendors' . DS . 'samswhois' . DS;
							
							switch ($pkey) {
								case 'customcss'	:						
									if (!empty($pval) && $pval == "N") {
										$cssdef = "swstyles.default.css";
										$cssdfull = $csspath . $cssdef;
										
										$cssnam = "swstyles.css";
										$cssfull = $csspath . $cssnam;
										
										if (is_readable($cssdfull)) {									
											if ($fh = fopen($cssdfull, "r")) {
												$cssdefault = '';
												
												while (!feof($fh)) {
													$cssdefault .= fread($fh, 1024);
												}
												
												//close the reading file handle
												fclose($fh);
												
												//update the "css" option
												$this -> update_option('css', $cssdefault);
												
												if (file_exists($cssfull)) {
													if (is_writable($cssfull)) {
														if (!empty($cssdefault)) {
															if ($fk = fopen($cssfull, "w")) {
																fwrite($fk, $cssdefault);
																fclose($fk);
																@chmod($cssfull, 0777);
															}
														}
													}
												}
											}
										}
									}
									break;
								case 'css'			:
									if (!empty($_POST['customcss']) && $_POST['customcss'] == "Y") {
										$cssname = "swstyles.css";
										$cssfull = $csspath . $cssname;
		
										if (file_exists($cssfull)) {																				
											if (is_writable($cssfull)) {
												if ($fh = fopen($cssfull, "w")) {										
													fwrite($fh, $pval);
													fclose($fh);
													@chmod($cssfull, 0777);
												} else {
													$this -> render_err(__('Stylesheet "/vendors/samswhois/' . $cssname . '" cannot be written', $this -> plugin_name));
												}
											}
										}
									} else {
										$this -> update_option('css', $cssdefault);
									}
									break;
							}
						}
					}
					
					$message = __('Configuration settings have been saved', $this -> plugin_name);
					$this -> render_msg($message);
					break;
			}
		}
		
		if (!empty($this -> adversion) && $this -> adversion == true) {
			$adversion = __('You are running the free, ad links version. Please ', $this -> plugin_name) . '<a href="http://tribulant.com/products/view/12/wordpress-whois-plugin" target="_blank" title="' . __('WordPress WHOIS Plugin', $this -> plugin_name) . '">' . __('purchase the WHOIS plugin', $this -> plugin_name) . '</a>' . __(' to remove all the ad links.', $this -> plugin_name);
			$this -> render_err($adversion);
		}
	
		//render the 'views/admin/index.php' file
		$this -> render('index', false, true, 'admin');
	}
}

//initialize a wpWhois class object
$wpWhois = new wpWhois();

?>