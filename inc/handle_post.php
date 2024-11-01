<?php

if (isset($_GET['submit_error'])) {
	$class = 'notice notice-error';
	$message = __('Sorry, you are not allowed to Submit', 'user-rights-access-manager');

	printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
}

?>