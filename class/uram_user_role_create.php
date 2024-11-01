<?php

/**
 * Description of uram_user_role_create
 *
 * @see create user role
 * @author prism
 */

if (!class_exists('uram_user_role_create')) {

	// class user role create
	class uram_user_role_create {
		// class constructor
		function __construct() {
			$this->uram_create_new_role(); // new role create
			$this->uram_user_role_check_access(); // user role check access
		}
		//new role creare function
		function uram_create_new_role() {

			if (!isset($wp_roles)) {
				$wp_roles = new WP_Roles();
			}

			$adm = $wp_roles->get_role('administrator');
			$wp_roles->add_role('uram_guest', 'URAM Guest', $adm->capabilities); //Adding a 'new_role' with all admin caps
		}
		//user access
		function uram_user_role_check_access() {
			if (is_user_logged_in()) {
				$wp_roles = new WP_Roles();
				$set_cap_role = $wp_roles->get_role('uram_guest'); // gets the author role

				$set_cap_role->remove_cap('delete_published_posts');
				$set_cap_role->remove_cap('edit_published_posts');
				$set_cap_role->remove_cap('publish_posts');

				$user = wp_get_current_user();
				$role = (array) $user->roles;
				$urole = isset($role[0]) ? $role[0] : '';
				
				if ($urole == 'uram_guest') {
					$uram_user_role = 'uram_guest';
					$cap_delete = false;
					include_once URAM_INC . 'handle_post.php';
				}
			}
		}

	}
	// user role create class called
	new uram_user_role_create();

}
