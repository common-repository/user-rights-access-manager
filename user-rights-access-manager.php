<?php

/*
Plugin Name: User Rights Access Manager
Plugin URI: https://www.prismitworks.com/
Description: Using this plugin you can restrict admin menus, admin submenus and posttype by user accessibility. You can add restriction for single user as well as user role.
Version: 1.1.3
Author: Prism IT Systems
Author URI: https://www.prismitsystems.com
Text Domain: user-rights-access-manager
License: GPL-2.0+
License URI: https://www.gnu.org/licenses/gpl-2.0.txt
 */

if (!defined('ABSPATH')) {
	exit;
}

//Define all constant here
define('URAM_URL', plugin_dir_url(__FILE__));
define('URAM_PATH', plugin_dir_path(__FILE__));
define('URAM_BASENAME', plugin_basename(__FILE__));
define('URAM_INC', URAM_PATH . 'inc/');
define('URAM_TEMP', URAM_PATH . 'template/');
define('URAM_CLASS', URAM_PATH . 'class/');
define('URAM_ADMIN', URAM_PATH . 'admin/');
define('URAM_METABOX', URAM_PATH . 'metabox/');
define('URAM_JS', URAM_URL . 'assets/js/');
define('URAM_CSS', URAM_URL . 'assets/css/');
define('URAM_IMG', URAM_URL . 'assets/img/');

//include init file
include URAM_INC . 'init.php';
include URAM_INC . 'functions.php';
add_action('plugins_loaded', 'uram_init');

if (!function_exists('uram_init')) {

	function uram_init() {
		$locale = is_admin() && function_exists('get_user_locale') ? get_user_locale() : get_locale();
		$locale = apply_filters('plugin_locale', $locale, 'user-rights-access-manager');
		unload_textdomain('user-rights-access-manager');
		load_textdomain('user-rights-access-manager', URAM_PATH . 'languages/' . "user-rights-access-manager-" . $locale . '.mo');
		load_plugin_textdomain('user-rights-access-manager', false, URAM_PATH . 'languages');
	}

}
?>