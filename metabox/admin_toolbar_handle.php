<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of admin_toolbar_handle
 * @see 'wcm_admin_toolbar_handle'
 * @author prism
 */

// class exist or not!.
if (!class_exists('wpres_wcm_admin_toolbar_handle')) {
	// class admin tool bar handle
	class wpres_wcm_admin_toolbar_handle {

		function __construct() {
			add_action('wp_before_admin_bar_render', array($this, 'uram_remove_toolbar_node'), 9999); // admin bar hide/show
		}

		// remove admin bar notification and icon
		public function uram_remove_toolbar_node() {
			global $uram_user_role, $wp_admin_bar;
			if (is_user_logged_in()) {

				$user_id = get_current_user_ID();
				if ($user_id) {
					$user_restrict = unserialize(get_user_meta($user_id, 'user_restriction_option', true));
					if ($user_restrict) {
						if (isset($user_restrict['admin_bar_notification'])) {
							$get_admin_bar = $user_restrict['admin_bar_notification'];
							if ($get_admin_bar == 'on' && $get_admin_bar != 1) {
								$nodes = $wp_admin_bar->get_nodes();
								add_filter( 'screen_options_show_screen', array( $this, 'remove_screen_options_show' ) );
								foreach ($nodes as $key => $value) {
									$allow_array = array('menu-toggle', 'wp-logo', 'site-name', 'top-secondary', 'my-account', 'user-actions',
										'user-info', 'edit-profile', 'logout', 'view-site');
									if (!in_array($key, $allow_array)) {
										$wp_admin_bar->remove_node($key);
									}
								}
							}
						}
					} else {
						$user = wp_get_current_user();
						$role = (array) $user->roles;
						$user_restrict = unserialize(get_option('user_restriction_option'));
						if(is_array($user_restrict))
						{
							$user_role_restrict = isset($user_restrict['system_role']) ? $user_restrict['system_role'] :array();
						}
						else
							$user_role_restrict = array();

						if (isset($user_role_restrict[$role[0]])) {
							$get_admin_bar = $user_role_restrict[$role[0]]['admin_bar_notification'];

							if ($get_admin_bar == 'on' && $get_admin_bar != 1) {
								$nodes = $wp_admin_bar->get_nodes();
								add_filter( 'screen_options_show_screen', array( $this, 'remove_screen_options_show' ) );
								foreach ($nodes as $key => $value) {
									$allow_array = array('menu-toggle', 'wp-logo', 'site-name', 'top-secondary', 'my-account', 'user-actions',
										'user-info', 'edit-profile', 'logout', 'view-site');

									if (!in_array($key, $allow_array)) {
										$wp_admin_bar->remove_node($key);
									}
								}
							}
						}
					}
				}
			}
		}

		function remove_screen_options_show( $data ){
			return false;
		}
	}
	// called class toolbar handle
	new wpres_wcm_admin_toolbar_handle();
}
