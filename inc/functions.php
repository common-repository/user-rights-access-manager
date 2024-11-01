<?php
/*function uram_custom_sanitize_array(&$array) {
	foreach ($array as &$value) {
		if (!is_array($value)) {
			$value = sanitize_text_field($value);
		} else {
			uram_custom_sanitize_array($value);
		}
	}
	return $array;
}*/
function uram_custom_sanitize_array($array_or_string) {
    if( is_string($array_or_string) ){
        $array_or_string = sanitize_text_field($array_or_string);
    }elseif( is_array($array_or_string) ){
        foreach ( $array_or_string as $key => &$value ) {
            if ( is_array( $value ) ) {
                $value = uram_custom_sanitize_array($value);
            } else {
                $value = sanitize_text_field( $value );
            }
        }
    }
    return $array_or_string;
}
function uram_get_current_admin_url() {
	$uri = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
	$uri = preg_replace( '|^.*/wp-admin/|i', '', $uri );

	if ( ! $uri ) {
		return '';
	}

	return remove_query_arg( array( '_wpnonce', '_wc_notice_nonce', 'wc_db_update', 'wc_db_update_nonce', 'wc-hide-notice' ), admin_url( $uri ) );
}

?>