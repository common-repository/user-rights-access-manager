<?php
/**
 * Description of uram_menu_restriction
 *
 * @see menu restriction user role wise
 * @author prism
 */

if (!class_exists('uram_menu_restriction')) {
	//create class if does not exist
	class uram_menu_restriction {
		function __construct() {
			add_filter('wp_nav_menu_items', array($this, 'uram_home_menu'), 10, 2);
		}
		function uram_home_menu($items, $args) {
			// if($args->theme_location != 'primary' )
			// {
			// 	return $items;
			// }
			if (is_user_logged_in()) {
				$user_id = get_current_user_ID();
				$user_restrict = unserialize(get_user_meta($user_id, 'user_restriction_option', true));
				if (isset($user_restrict['home_page_menus']) && (isset($user_restrict['home_page_menus']['is_on']))) {
					if (isset($user_restrict['home_page_menus']) && (isset($user_restrict['home_page_menus']['pages']))) {
						$new_menu = "";
						//print_r(($user_restrict['home_page_menus']['pages']));
						foreach ($user_restrict['home_page_menus']['pages'] as $key => $page_id) {
							$new_menu .= '<li id="menu-item-page- ' . $page_id . '" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-9 is-focused"><a href=" ' . get_the_permalink($page_id) . ' "> ' . get_the_title($page_id) . '</a></li>';
						}
						return $new_menu;
					}
				}
				$user_meta = get_userdata($user_id);
				$user_roles = $user_meta->roles;
				$get_role_data = get_option('user_restriction_option');
				if ($get_role_data) {
					$user_restrict = unserialize($get_role_data);
				} else {
					$get_role_data = array();
				}
				if ($user_roles) {
					foreach ($user_roles as $key => $role) {
						if (isset($user_restrict['system_role'][$role]['home_page_menus']['is_on'])) {
							if (isset($user_restrict['system_role'][$role]['home_page_menus']['pages'])) {
								$new_menu = "";
								foreach ($user_restrict['system_role'][$role]['home_page_menus']['pages'] as $key => $page_id) {
									$new_menu .= '<li id="menu-item-page- ' . $page_id . '" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-9 is-focused"><a href=" ' . get_the_permalink($page_id) . ' "> ' . get_the_title($page_id) . '</a></li>';
								}
								return $new_menu;
							}
						}
					}
				}
			}
			return $items;
		}
	}
	new uram_menu_restriction();
}