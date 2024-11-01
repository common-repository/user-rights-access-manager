<?php
/**
 * @see 'admin_menu_handle'
 *
 */
// class check
if (!class_exists('wpres_admin_menu_handle')) {

// admin menu create
	class wpres_admin_menu_handle {

		function __construct() {
			add_action('add_meta_boxes', array($this, 'wpres_get_metabox_value'), 9999);
			add_action('pre_current_active_plugins', array($this, 'hide_current_plugin'));
		}
		public function wpres_get_metabox_value() {
			global $wp_meta_boxes;
			update_option('wp_meta_boxes_data', $wp_meta_boxes);
			include_once URAM_METABOX . "metaboxes_restriction.php";
		}
		public function wpres_metaboxes_add_remove_handle($wp_meta_boxes, $post_type, $get_metabox = false) {
			$remove_res = array('edit.php');
			$post_handle_type = $post_type;
			parse_str($post_type, $out);
			$post_type = '';
			if (isset($out['edit_php?post_type'])) {
				$post_type = $out['edit_php?post_type'];
			}
			$priority_arr = array('default', 'high', 'low');
			$context_arr = array('normal', 'side', 'advanced');
			$new_array = array();

			if (in_array($post_handle_type, $remove_res)) {
				$post_type = 'post';
			}

			if (isset($wp_meta_boxes[$post_type])) {
				?>
				<h3 class="metabox_header">
					<?php esc_html_e('Meta Boxes', 'user-rights-access-manager');?> (<span><?php echo esc_html($post_type); ?></span>)
				</h3>
				<?php
foreach ($wp_meta_boxes[$post_type] as $priority_key => $val_box) {
					if (isset($wp_meta_boxes[$post_type][$priority_key])) {
						if (isset($val_box[$priority_arr[0]])) {
							foreach ($val_box[$priority_arr[0]] as $metaboxe_dir => $meta_info) {
								if (isset($meta_info['id']) && !empty($meta_info['id'])) {
									$checked = '';
									if (isset($get_metabox[$post_type][$priority_key][$meta_info['id']])) {
										$checked = ($get_metabox[$post_type][$priority_key][$meta_info['id']] == 'on' ? 'checked' : '');
									}
									
									?>
									<li>
										<div class="sub_grp">
											<label><?php echo wp_strip_all_tags($meta_info['title']); ?>
											<input type="checkbox" class="metaboxes_input" name="wp_metabox[<?php echo $post_type ?>][<?php echo $priority_key; ?>][<?php echo $meta_info['id']; ?>]" <?php echo $checked; ?>>
											<span class="res_lbl_span"></span>
										</label>
										</div>
									</li>

								<?php
}
							}
						}
						if (isset($val_box[$priority_arr[1]])) {
							foreach ($val_box[$priority_arr[1]] as $metaboxe_dir => $meta_info) {
								if (isset($meta_info['id']) && !empty($meta_info['id'])) {
									$checked = '';
									if (isset($get_metabox[$post_type][$priority_key][$meta_info['id']])) {
										$checked = ($get_metabox[$post_type][$priority_key][$meta_info['id']] == 'on' ? 'checked' : '');
									}
									?>
									<li>
										<div class="sub_grp">
											<label><?php echo esc_html($meta_info['title']); ?>
											<input type="checkbox" class="metaboxes_input" name="wp_metabox[<?php echo $post_type ?>][<?php echo $priority_key; ?>][<?php echo $meta_info['id']; ?>]" <?php echo $checked; ?> >
											<span class="res_lbl_span"></span>
										</label>
									</div>
								</li>
								<?php
}

							}

						}
						if (isset($val_box[$priority_arr[2]])) {
							foreach ($val_box[$priority_arr[2]] as $metaboxe_dir => $meta_info) {
								if (isset($meta_info['id']) && !empty($meta_info['id'])) {
									$checked = '';
									if (isset($get_metabox[$post_type][$priority_key][$meta_info['id']])) {
										$checked = ($get_metabox[$post_type][$priority_key][$meta_info['id']] == 'on' ? 'checked' : '');
									}
									?>
									<li>
										<div class="sub_grp">
											<label><?php echo esc_html($meta_info['title']); ?>
												<input type="checkbox" class="metaboxes_input" name="wp_metabox[<?php echo $post_type ?>][<?php echo $priority_key; ?>][<?php echo $meta_info['id']; ?>]" <?php echo $checked; ?>>
												<span class="res_lbl_span"></span>
											</label>
										</div>
									</li>
								<?php
}
							}
						}

					}

				}
			}
		}
		public function uram_permission_handle() {
			global $menu, $submenu, $wp_roles;
			$struct_array = array(
				'admin_bar_notification' => array(),
				'dashboard_metaboxes' => array(),
				'main_menu' => array(),
				'sub_menu' => array(),
				'post_type' => array(),
				'custom_urls' => array(),
			);
			$metaboxes_val = get_option('wp_meta_boxes_data');

			$user_restrict = $get_permission_main_menu = $get_permission_sub_menu = $get_admin_bar = $uram_dashboard = $get_metaboxes = $getCustomURLs = $get_home_menus = '';
			if (isset($_GET['user_role']) && sanitize_text_field($_GET['user_role']) != 0) {
				$user_id = sanitize_text_field($_GET['user_role']);
				$user_restrict = unserialize(get_user_meta($user_id, 'user_restriction_option', true));
				if (isset($user_restrict) && is_array($user_restrict)) {
					$get_permission_main_menu = isset($user_restrict['main_menu']) ? $user_restrict['main_menu'] : '';
					$get_permission_sub_menu = isset($user_restrict['sub_menu']) ? $user_restrict['sub_menu'] : '';
					$get_admin_bar = isset($user_restrict['admin_bar_notification']) ? $user_restrict['admin_bar_notification'] : '';
					$uram_dashboard = isset($user_restrict['dashboard_metaboxes']) ? $user_restrict['dashboard_metaboxes'] : '';
					$get_metaboxes = isset($user_restrict['meta_boxs']) ? $user_restrict['meta_boxs'] : '';
					$get_home_menus = isset($user_restrict['home_page_menus']) ? $user_restrict['home_page_menus'] : '';
					if (array_key_exists('custom_urls', $user_restrict)) {
						$getCustomURLs = $user_restrict['custom_urls'];
					}
				}
			} else if (isset($_GET['sys_role']) && $_GET['sys_role'] != '') {
				$user_restrict = unserialize(get_option('user_restriction_option', true));

				if (isset($user_restrict)) {
					$sys_role = sanitize_text_field($_GET['sys_role']);

					$get_permission_main_menu = $user_restrict['system_role'][$sys_role]['main_menu'];

					$get_permission_sub_menu = $user_restrict['system_role'][$sys_role]['sub_menu'];
					$get_admin_bar = $user_restrict['system_role'][$sys_role]['admin_bar_notification'];
					$uram_dashboard = $user_restrict['system_role'][$sys_role]['dashboard_metaboxes'];
					$get_metaboxes = $user_restrict['system_role'][$sys_role]['meta_boxs'];
					$get_home_menus = $user_restrict['system_role'][$sys_role]['home_page_menus'];
					if (array_key_exists('custom_urls', $user_restrict['system_role'][$sys_role])) {
						$getCustomURLs = $user_restrict['system_role'][$sys_role]['custom_urls'];
					}
				}
			}
			$user_data = get_users();
			$all_role = $wp_roles->get_names();
			include_once URAM_TEMP . 'res_wp_permission_form.php';
		}
		public function hide_current_plugin() {
			global $wp_list_table;
			$hidearr = array('user-rights-access-manager/user-rights-access-manager.php');
			$user_id = get_current_user_id();
			$restrictions = get_user_meta($user_id, 'user_restriction_option', true);
			if (!empty($restrictions)) {
				$myplugins = $wp_list_table->items;
				foreach ($myplugins as $key => $val) {
					if (in_array($key, $hidearr)) {
						unset($wp_list_table->items[$key]);
					}
				}
			}
		}
	}

//class call
	new wpres_admin_menu_handle();
}
?>
