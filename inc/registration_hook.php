<?php

class uram_registration_handle_class {

	function __construct() {

//registarion ajax hook remoce and recreate.
		remove_action('wp_ajax_registration_form_handle', 'registration_form_handle_callable');
		remove_action('wp_ajax_nopriv_registration_form_handle', 'registration_form_handle_callable');
		add_action('wp_ajax_registration_form_handle', array($this, 'wpres_new_registration_form_handle_callable'));
		add_action('wp_ajax_nopriv_registration_form_handle', array($this, 'wpres_new_registration_form_handle_callable'));
	}

	public function wpres_new_registration_form_handle_callable() {
		if (isset($_POST['form'])) {
			parse_str(sanitize_text_field($_POST['form']), $_POST);
			$fname = sanitize_text_field($_POST['uram_reg_fname']);
			$lname = sanitize_text_field($_POST['uram_reg_lname']);
			$email = sanitize_text_field($_POST['uram_reg_email']);
			$contact = sanitize_text_field($_POST['uram_reg_contact']);
			$username = sanitize_text_field($_POST['uram_reg_username']);
			$password = sanitize_text_field($_POST['uram_reg_password']);
			$reg = array();
			$reg['bool'] = true;
			$reg['input'] = array();
			$regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/';
			if (empty($fname)) {

				$reg['input'][] = 'uram_reg_fname';
				$reg['error'][] = __('First name is required.', 'user-rights-access-manager');
				$reg['bool'] = false;
			}
			if ($lname == '') {
				$reg['input'][] = 'uram_reg_lname';
				$reg['error'][] = __('Last name is required.', 'user-rights-access-manager');
				$reg['bool'] = false;
			}

			if ($email == '') {
				$reg['input'][] = 'uram_reg_email';
				$reg['error'][] = __('Email id is required.', 'user-rights-access-manager');
				$reg['bool'] = false;
			} elseif (!preg_match($regex, $email)) {
				$reg['input'][] = 'uram_reg_email';
				$reg['error'][] = __('Invalid Email id.', 'user-rights-access-manager');
				$reg_flag['bool'] = false;
			} else if (email_exists($email)) {
				$reg['input'][] = 'uram_reg_email';
				$reg['error'][] = __('Email id already exists.', 'user-rights-access-manager');
				$reg['bool'] = false;
			}
			/* if(strlen($contact) <= 2)
				      {
				      $reg['input'][] = 'uram_reg_contact';
				      $reg['error'][] = __('Contact is required.','user-rights-access-manager');
				      $reg['bool'] = false;
			*/
			if ($username == '') {
				$reg['input'][] = 'uram_reg_username';
				$reg['error'][] = __('Username is required.', 'user-rights-access-manager');
				$reg['bool'] = false;
			} else {
				if (username_exists($username)) {
					$reg['input'][] = 'uram_reg_username';
					$reg['error'][] = __('Username is already taken.', 'user-rights-access-manager');
					$reg['bool'] = false;
				}
			}

			if ($password == '') {
				$reg['input'][] = 'uram_reg_password';
				$reg['error'][] = __('password is required.', 'user-rights-access-manager');
				$reg['bool'] = false;
			} elseif (!preg_match('/^(?=.*\d)(?=.*[@#\-_$%^&+=§!\?])(?=.*[a-z])(?=.*[A-Z])[0-9A-Za-z@#\-_$%^&+=§!\?]{8,20}$/', $password)) {
				$reg['input'][] = 'uram_reg_password';
				$reg['error'][] = '<ul><li>' . __('Password required steps', 'user-rights-access-manager') . '</li>
					<li>' . __('at least one lowercase char', 'user-rights-access-manager') . '</li>
					<li>' . __('at least one uppercase char', 'user-rights-access-manager') . '</li>
					<li>' . __('at least one digit', 'user-rights-access-manager') . '</li>
					<li>' . __('at least one special sign of @#-_$%^&+=§!?', 'user-rights-access-manager') . '</li></ul>';
				$reg['bool'] = false;
			}
			$reg['url'] = '';
			if ($reg['bool'] == true) {
				if (!get_current_user_ID()) {
					$user_id = wp_create_user($username, $password, $email);
					$user_id_role = new WP_User($user_id);
					$user_id_role->set_role('uram_guest');
					update_user_meta($user_id, 'first_name', $fname);
					update_user_meta($user_id, 'last_name', $lname);
					update_user_meta($user_id, 'contact', $contact);
					$creds = array();
					$creds['user_login'] = $username;
					$creds['user_password'] = $password;
					$creds['remember'] = false;
					$user = wp_signon($creds, false);
					if (is_wp_error($user)) {
						$reg['bool'] = false;
						$reg = $user->get_error_message();
					} else {
						$reg['url'] = site_url();
						$course_list_id = get_option('uram_course_listing_page_id');
						if ($course_list_id) {
							$reg['url'] = get_permalink($course_list_id);
						}
						if (isset($_COOKIE['uram_login_redirect'])) {
							$reg['url'] = sanitize_text_field($_COOKIE['uram_login_redirect']);
							if (sanitize_text_field($_COOKIE['data_id'])) {
								global $woocommerce;
								$product_id = get_post_meta(sanitize_text_field($_COOKIE['data_id']), 'course_product_relation', true);
								if ($product_id) {
									$cart_url = wc_get_cart_url();
									$reg['url'] = $cart_url . '?add-to-cart=' . $product_id;
								}
							}
							if (isset($_SERVER['HTTP_COOKIE'])) {
								$cookies = explode(';', uram_custom_sanitize_array($_SERVER['HTTP_COOKIE']));
								foreach ($cookies as $cookie) {
									$parts = explode('=', $cookie);
									$name = trim($parts[0]);
									setcookie($name, '', time() - 1000);
									setcookie($name, '', time() - 1000, '/');
								}
							}
						}
					}
				}
			}
			echo json_encode(array('inputs' => $reg['input'], 'errors' => $reg['error'], 'status' => $reg['bool'], 'url' => $reg['url']));
			die();
		}
	}

}

new uram_registration_handle_class();
?>