<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of dashboard_metabox
 * @see 'wcm_dashboard_metabox'
 * @author prism
 */
//clas check exist!
if (!class_exists('wpres_wcm_dashboard_metabox')) {
	// create dashboard metabox class
	class wpres_wcm_dashboard_metabox {
		// class construtor
		function __construct() {
			add_action('wp_dashboard_setup', array($this, 'uram_dashboard_widgets'), 9999); // admin dashboard hide/show
		}

		// dashboard in create metabox
		function uram_dashboard_widgets() {
			global $wp_meta_boxes;
			if (is_user_logged_in()) {
				$user_id = get_current_user_ID();
				$user_restrict = unserialize(get_user_meta($user_id, 'user_restriction_option', true));
				if ($user_restrict) {
					if (isset($user_restrict['dashboard_metaboxes'])) {
						$uram_dashboard = $user_restrict['dashboard_metaboxes'];
						if ($uram_dashboard == 'on' && $uram_dashboard != 1) {
							unset($wp_meta_boxes['dashboard']);
							update_user_meta(get_current_user_id(), 'show_welcome_panel', false);
						}
					}
				} else {
					$user = wp_get_current_user();
					$role = (array) $user->roles;
					$user_restrict = unserialize(get_option('user_restriction_option'));
					$user_role_restrict = isset($user_restrict['system_role']) ? $user_restrict['system_role'] : '';

					if (isset($user_role_restrict[$role[0]])) {
						$uram_dashboard = $user_role_restrict[$role[0]]['dashboard_metaboxes'];
						if ($uram_dashboard == 'on' && $uram_dashboard != 1) {
							unset($wp_meta_boxes['dashboard']);
							update_user_meta(get_current_user_id(), 'show_welcome_panel', false);
						}
					}
				}
				$user_data = get_userdata($user_id);
				if( !empty($user_data->first_name) && !empty($user_data->last_name) ){
					$name = $user_data->first_name . ' ' . $user_data->last_name;
				}else{
					$name = ucwords($user_data->data->display_name);
				}
				$user_name = __('Welcome: ', 'user-rights-access-manager') . $name;
				add_meta_box('add_widget_uram', $user_name, array($this, 'uram_new_dash_widget'), 'dashboard', 'normal', 'high');
			}
		}

		// metabox contents
		public function uram_new_dash_widget() {
			echo "<p>" . esc_html(__('Here you can access the site based on your job requirement. Let us know if you require some additional access.', 'user-rights-access-manager')) .
				"</p>";
			echo "<p>" . esc_html(__('Thanks', 'user-rights-access-manager') . ",") . "</p>";
			echo "<p>" . esc_html(__('Prism IT Systems', 'user-rights-access-manager'))."</p>";
		}

	}

}
// call dashboard metbox class
new wpres_wcm_dashboard_metabox();
