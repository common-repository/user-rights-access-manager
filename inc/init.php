<?php
/**
 * 
 * Summary: Wordpress restriction plugin restricted to all menu pages action and hook.
 *
 * Description: Restricted plugin init class here. all hooks handle here.
 *
 * @since 1.0.1
 *
 * @see init_prism_restriction
 * 
 * @author prism
 */
if (!class_exists('wpres_init_prism_restriction')) {

    class wpres_init_prism_restriction {

	function __construct() {
	    
	    // actions here
	    add_action('admin_enqueue_scripts', array($this, 'uram_restrict_scripts')); // script enqueues
	    add_action('admin_menu', array($this, 'wpres_admin_menus_add_removed_by_user'), 9999, 1); // admin menu select option and selected option removed here
	    // @see 'admin_init hook'
	    add_action('admin_init', array($this, 'uram_res_cloneRole'), 999);
	    // metabox handle 
	   
	    // ajax handle
	    add_action('wp_ajax_remove_user_res_records',array($this,'wpres_remove_user_res_records_call'));
	    //includes files
	    include_once(URAM_METABOX . 'dashboard_metabox.php');
	    include_once(URAM_METABOX . 'admin_toolbar_handle.php');
	    include_once(URAM_CLASS . 'uram_menu_restriction.php');
	}
	
	public function wpres_remove_user_res_records_call(){
	    $status = 'false';
	    $message = '';
	    if(isset($_POST['user_id']))
	    {
		delete_user_meta(sanitize_text_field($_POST['user_id']), 'user_restriction_option');
		$message = __("User Restriction Removed",'user-rights-access-manager') .".";
		$status = 'true';
	    }
	    if(isset($_POST['user_role']))
	    {
		$user_role = sanitize_text_field($_POST['user_role']);
		$user_restrict = unserialize(get_option('user_restriction_option', true));
		if($user_restrict['system_role'][$user_role])
		{
		    unset($user_restrict['system_role'][$user_role]);
		    update_option('user_restriction_option',serialize($user_restrict));
		}
		$message = __("Role Restriction Removed",'user-rights-access-manager') .".";
		$status = 'true';
	    }
	    echo json_encode(array('status'=>$status,'message'=>$message));
	    die;
	}
	// enqueue scripts
	public function uram_restrict_scripts() {
	    wp_enqueue_script('uram_js', URAM_JS . 'uram.js');
	    wp_enqueue_style('uram_css', URAM_CSS . 'uram_css.css');
	    wp_localize_script( 'uram_js', 'res_ajax', array( 'ajax_url' => admin_url('admin-ajax.php'),'user_confirm' => __('Note that deleting a restricted role will cause any assigned users to gain full access! Are you sure you want to continue?', 'user-rights-access-manager'),'user_role_confirm' => __('Note that unassigning a user will grant him full access based on his role! Are you sure you want to continue?','user-rights-access-manager')));
	    wp_enqueue_script('uram_select2_js', URAM_JS . 'select2.min.js');
	    wp_enqueue_style('uram_select2_css', URAM_CSS . 'select2.min.css');
	   
	}

	// admin-menu add removed opration by admin
	public function wpres_admin_menus_add_removed_by_user() {
	    global $submenu, $menu;

	    if (is_user_logged_in()) {

		add_menu_page(__('User Rights Access Manager','user-rights-access-manager'), __('User Rights Access Manager','user-rights-access-manager'), 'manage_options', 'uram_permission_list', array($this, 'wp_res_restrict_user_list_callable'),"dashicons-shield",99);
		$user = wp_get_current_user();
		$role = (array) $user->roles;
		include(URAM_ADMIN . 'admin_menu_handle.php'); // admin user options set permission
		include(URAM_CLASS . "user_wise_restriction.php"); // user seted permission handle
		include(URAM_CLASS . "uram_user_role_restriction_class.php");
		$role = isset($role[0]) ? $role[0] :'' ;
		new uram_user_role_restriction_class($role); // class object 
	
	    }
	}

	public function wp_res_restrict_user_list_callable() {
	    include_once(URAM_ADMIN . 'user_list_admin.php');
	}
	// admin init handle.
	// create new role and restrict core and remove register type and message handle.
	public function uram_res_cloneRole() {
	    global $wp_roles, $submenu, $uram_user_role;
	    
	    //include files
	    include_once(URAM_INC . 'registration_hook.php');
	    include_once(URAM_CLASS . 'uram_user_role_create.php'); // user role create and basic permissions set like (rwx)
	    
	     $struct_array = array(
		'admin_bar_notification' => array(),
		'dashboard_metaboxes' => array(),
		'main_menu' => array(),
		'sub_menu' => array(),
		'meta_boxs'=>array(),
        'custom_urls' => array(),
        'home_page_menus' =>array(),
	    );
	     
	    $nonce = isset( $_POST['_wpnonce'] ) ? sanitize_text_field($_POST['_wpnonce']) : '';
	    if (isset($_POST['posttype_submit']) && isset($_POST['set_permission_menus']) && wp_verify_nonce( $nonce, 'res_nonce' )) {
		
		$bool = false;

		if (isset($_POST['user_role_sel']) && sanitize_text_field($_POST['user_role_sel']) != 0) {

		    $struct_array['main_menu'] = is_array($_POST['menus'])? uram_custom_sanitize_array($_POST['menus']) :'';
		    $struct_array['sub_menu'] = is_array($_POST['submenus'])? uram_custom_sanitize_array($_POST['submenus']) : '';

		    if (isset($_POST['hide_admin_bar'])) {
			$struct_array['admin_bar_notification'] = 'on';
		    } else {
			$struct_array['admin_bar_notification'] = 'off';
		    }
		    if (isset($_POST['remove_dashboard_metabox'])) {
			$struct_array['dashboard_metaboxes'] = 'on';
		    } else {
			$struct_array['dashboard_metaboxes'] = 'off';
		    }
	        if(isset($_POST['custom_url_checkbox'])){
	            if(isset($_POST['wprsCustomUrls']) && !empty($_POST['wprsCustomUrls'])){
	                $urls = explode(" ",uram_custom_sanitize_array($_POST['wprsCustomUrls']));
	                $struct_array['custom_urls'] = array('is_on'=>'on',"urls" => $urls);
	            }else{
	                $struct_array['custom_urls'] = array('is_on'=>'on',"urls" => "");
	            }
	        }else{
	            if(isset($_POST['wprsCustomUrls']) && !empty($_POST['wprsCustomUrls'])){
	                $urls = explode(" ",uram_custom_sanitize_array($_POST['wprsCustomUrls']));
	                $struct_array['custom_urls'] = array('is_on'=>'off',"urls" => $urls);
	            }else{
	                $struct_array['custom_urls'] = array('is_on'=>'off',"urls" => "");
	            }
	        }
	        if(isset($_POST['home_page_menu'])){
	        	$struct_array['home_page_menus']['is_on'] = sanitize_text_field($_POST['home_page_menu']);
	        }else{
	        	unset($struct_array['home_page_menus']['is_on']);
	        }
	        if(isset($_POST['wram_pages']) && !empty($_POST['wram_pages']))
    		{
    			$struct_array['home_page_menus']['pages'] = uram_custom_sanitize_array($_POST['wram_pages']);
    		}else{
    			unset($struct_array['home_page_menus']['pages']);	
    		}
    		
		    $struct_array['meta_boxs'] = isset($_POST['wp_metabox']) ? sanitize_text_field($_POST['wp_metabox']) : '';
		    update_user_meta(sanitize_text_field($_POST['user_role_sel']), 'user_restriction_option', serialize($struct_array));
		    $bool = true;
		} else if (isset($_POST['sys_role_sel']) && sanitize_text_field($_POST['sys_role_sel']) != '') {

		    $struct_array_new = unserialize(get_option('user_restriction_option'));

		    $struct_array['main_menu'] = is_array($_POST['menus'])? uram_custom_sanitize_array($_POST['menus']) :'';
		    $struct_array['sub_menu'] = is_array($_POST['submenus'])? uram_custom_sanitize_array($_POST['submenus']) :'';

		    if (isset($_POST['hide_admin_bar'])) {
			$struct_array['admin_bar_notification'] = 'on';
		    } else {
			$struct_array['admin_bar_notification'] = 'off';
		    }
		    if (isset($_POST['remove_dashboard_metabox'])) {
			$struct_array['dashboard_metaboxes'] = 'on';
		    } else {
			$struct_array['dashboard_metaboxes'] = 'off';
		    }
            if(isset($_POST['custom_url_checkbox'])){
                if(isset($_POST['wprsCustomUrls']) && !empty($_POST['wprsCustomUrls'])){
                    $urls = explode(" ",uram_custom_sanitize_array($_POST['wprsCustomUrls']));
                    $struct_array['custom_urls'] = array('is_on'=>'on',"urls" => $urls);
                }else{
                    $struct_array['custom_urls'] = array('is_on'=>'on',"urls" => "");
                }
            }else{
                if(isset($_POST['wprsCustomUrls']) && !empty($_POST['wprsCustomUrls'])){
                    $urls = explode(" ",uram_custom_sanitize_array($_POST['wprsCustomUrls']));
                    $struct_array['custom_urls'] = array('is_on'=>'off',"urls" => $urls);
                }else{
                    $struct_array['custom_urls'] = array('is_on'=>'off',"urls" => "");
                }
            }
            if(isset($_POST['home_page_menu'])){
	        	$struct_array['home_page_menus']['is_on'] = sanitize_text_field($_POST['home_page_menu']);
	        }else{
	        	unset($struct_array['home_page_menus']['is_on']);
	        }
	        if(isset($_POST['wram_pages']) && !empty($_POST['wram_pages']))
    		{
    			$struct_array['home_page_menus']['pages'] = uram_custom_sanitize_array($_POST['wram_pages']);
    		}else{
    			unset($struct_array['home_page_menus']['pages']);	
    		}
		    $struct_array['meta_boxs'] = isset($_POST['wp_metabox']) ? sanitize_text_field($_POST['wp_metabox']) : '';
		    $struct_array_new['system_role'][sanitize_text_field($_POST['sys_role_sel'])] = $struct_array;
		    update_option('user_restriction_option', serialize($struct_array_new));
		    $bool = true;
		}

		if ($bool == true) {
		    $tab = sanitize_text_field($_POST['tab']);
		    wp_redirect(admin_url('admin.php?page=uram_permission_list&tab=' . $tab . '&success'));
		    exit;
		} else {
		    wp_redirect(admin_url('admin.php?page=uram_permission_list&tab=' . $tab . '&error'));
		    exit;
		}
	    }
	    
	   
	}
	
    }

   new wpres_init_prism_restriction();
}
