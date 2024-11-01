<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of metaboxes_restriction
 *
 * @author prism
 */
if (!class_exists('wpres_metaboxes_restrictions')) {

	class wpres_metaboxes_restrictions {

		function __construct() {
			$this->restriction_handle_metaboxes();
		}

		public function restriction_handle_metaboxes() {

			$wp_meta_boxes = get_option('wp_meta_boxes_data');
			$user_restrict = array();

			if (is_user_logged_in()) {
				$user_id = get_current_user_id();
				$user = wp_get_current_user();
				$role = (array) $user->roles;
				$role_restrict = unserialize(get_option('user_restriction_option'));
				$user_restrict = unserialize(get_user_meta($user_id, 'user_restriction_option', true));
				if (!$user_restrict) {
					if (isset($role_restrict['system_role'][$role[0]])) {
						$user_restrict = $role_restrict['system_role'][$role[0]];
					}
				}

				if ($user_restrict) {
					$priority_arr = array('default', 'high', 'low');
					$context_arr = array('normal', 'side', 'advanced');
					if ($wp_meta_boxes) {
						foreach ($wp_meta_boxes as $post_type => $context) {
							if (isset($wp_meta_boxes[$post_type])) {
								foreach ($wp_meta_boxes[$post_type] as $contex => $priority_arr_val) {

									if (isset($wp_meta_boxes[$post_type][$contex][$priority_arr[0]])) {

										foreach ($priority_arr_val[$priority_arr[0]] as $meta_dir => $meta_info) {
											if (isset($user_restrict['meta_boxs'][$post_type]) &&
												isset($user_restrict['meta_boxs'][$post_type][$contex]) &&
												isset($user_restrict['meta_boxs'][$post_type][$contex][$meta_info['id']]) &&
												$user_restrict['meta_boxs'][$post_type][$contex][$meta_info['id']] != 'on') {
												remove_meta_box($meta_info['id'], $post_type, $contex);
											}

										}
									}
									if (isset($wp_meta_boxes[$post_type][$contex][$priority_arr[1]])) {
										foreach ($priority_arr_val[$priority_arr[1]] as $meta_dir => $meta_info) {
											if (isset($user_restrict['meta_boxs'][$post_type]) &&
												isset($user_restrict['meta_boxs'][$post_type][$contex]) &&
												isset($user_restrict['meta_boxs'][$post_type][$contex][$meta_info['id']]) && $user_restrict['meta_boxs'][$post_type][$contex][$meta_info['id']] != 'on') {
												remove_meta_box($meta_info['id'], $post_type, $contex);
											}

										}
									}
									if (isset($wp_meta_boxes[$post_type][$contex][$priority_arr[2]])) {
										foreach ($priority_arr_val[$priority_arr[2]] as $meta_dir => $meta_info) {
											if (isset($user_restrict['meta_boxs'][$post_type]) &&
												isset($user_restrict['meta_boxs'][$post_type][$contex]) &&
												isset($user_restrict['meta_boxs'][$post_type][$contex][$meta_info['id']]) && $user_restrict['meta_boxs'][$post_type][$contex][$meta_info['id']] != 'on') {
												remove_meta_box($meta_info['id'], $post_type, $contex);
											}

										}
									}
								}
							}
						}
					}
				}
			}
		}

	}

	new wpres_metaboxes_restrictions();
}