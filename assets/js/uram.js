// tab panel handle
jQuery(document).on('click', '#role_restrict_li', function () {
    jQuery('#user_restrict_li').removeClass('active');
    jQuery('.user_restrict').removeClass('active');
    jQuery(this).addClass('active');
    jQuery(".role_restrict").addClass('active');
    window.location.href = jQuery('#res_admin_url').val()+"&tab=role";
});

jQuery(document).on('click', '#user_restrict_li', function () {
    jQuery('#role_restrict_li').removeClass('active');
    jQuery('.role_restrict').removeClass('active');
    jQuery(this).addClass('active');
    jQuery(".user_restrict").addClass('active');
    var url = window.location.href;
    if(url.search("tab=role") != -1)
    {
	window.location.href = jQuery('#res_admin_url').val();
    }
    
});


jQuery(document).ready(function(){
   //jQuery('.metabox_header').hide(); 
});
jQuery(document).on('click', '.main_menu_id', function () {

    var parent_ele = jQuery(this).parent().parent();
    var main_li = jQuery(this).parents('li');
    var ul_ele = parent_ele.next('ul');
    var metabox_ul = jQuery(this).parents('li').find('.metaboxes_ul');
    //var meta_header = jQuery(this).parents('li').find('.metabox_header');
    var li_ele = ul_ele.find('li .sub_menu_id');
    var metabox_checkbox = metabox_ul.find('li .metaboxes_input');
    //var main_li = ul_ele.find('li .sub_menu_id');
    if (jQuery(this).prop("checked") == true)
    {
	ul_ele.show('slow');
	//meta_header.show('slow');
	metabox_ul.show('slow');
	main_li.addClass('menu_active');
	jQuery.each(li_ele, function (i, value) {
	    jQuery(value).attr('checked', true);
	});
	jQuery.each(metabox_checkbox,function(j,value_check){
	    jQuery(value_check).attr('checked', true);
	});
	
    }
    if (jQuery(this).prop("checked") == false)
    {
	
	jQuery.each(li_ele, function (i, value) {
	    jQuery(value).attr('checked', false);
	});
	jQuery.each(metabox_checkbox,function(j,value_check){
	    jQuery(value_check).attr('checked', false);
	});
	main_li.removeClass('menu_active');
	ul_ele.hide('slow');
	//meta_header.hide('slow');
	metabox_ul.hide('slow');
    }

});

jQuery(document).on('click', '.sub_menu_id', function () {
    var self = jQuery(this);
    var submenu = jQuery(this).parents('.sub_menu_ul').find('.sub_menu_id');
    var metabox_menu = jQuery(this).parents('.sub_menu_ul').next('.metaboxes_ul').find('.metaboxes_input');
    
   click_handle_menus(submenu,metabox_menu,self,'submenu');
   
});

jQuery(document).on('click', '.metaboxes_input', function () {
    var self = jQuery(this);
    var metabox_menu = jQuery(this).parents('.metaboxes_ul').find('.metaboxes_input');
    var submenus = jQuery(this).parents('.metaboxes_ul').prev('.sub_menu_ul').find('.sub_menu_id');
    click_handle_menus(metabox_menu,submenus,self,'metabox');
});

function click_handle_menus(submenu,metabox_menu,self,name)
{
    var submenu_length = submenu.length;
    var metabox_length = metabox_menu.length;
    var total_length = metabox_length + submenu_length;
    var count_menu = 0;
    var count_meta = 0;
    jQuery(submenu).each(function () {
	if (jQuery(this).prop('checked') == true)
	{
	    count_menu++;
	}
    });
    jQuery(metabox_menu).each(function () {
	if (jQuery(this).prop('checked') == true)
	{
	    count_meta++;
	}
    });
    var count = count_meta+count_menu;
    
    
    if (count == 0)
    {
	self.parents('li').find('.main_menu_id').attr('checked', false);
	   self.parent().parent().parent().parent().hide('slow');
	if(name ='submenu')
	{ 
	    self.parent().parent().parent().parent().next('.metaboxes_ul').hide('slow');
	}
	if(name ='metabox')
	{
	    self.parent().parent().parent().parent().prev('.sub_menu_ul').hide('slow');
	}
	self.parents('.res_accordion_li').removeClass('menu_active');
	
	

    } 
}
// remove admin restriction 

jQuery(document).on('click', '.res_remove_cap', function () {


    if (window.confirm('Are you sure remove restrictions ?'))
    {
	var user_id = jQuery(this).next('#res_user_id_remove').val();
	var user_role = jQuery(this).next('#res_role_remove').val();

    } else {
	return false;
    }
    var use_this = jQuery(this);
    var postdata = {
	'action': 'remove_user_res_records',
	'user_id': user_id,
	'user_role': user_role,
    }
    jQuery.ajax({
	type: "POST",
	url: res_ajax.ajax_url,
	data: postdata,
	success: function (data) {
	    json_data = jQuery.parseJSON(data);
	    if (json_data.status == 'true')
	    {
		jQuery('.user_res_message').addClass('updated');
		jQuery('.user_res_message').text(json_data.message);
		use_this.parents('tr').remove();
	    }
	}
    });

});


jQuery(document).on('submit','#admin_permission_form',function(event){ 
  
    if(jQuery('#select_role_box').val() == " ")
    {
		alert('Please Select any user!');  
		return false;
    }
    if(jQuery('#select_role_system').val() == " ")
    {
		alert('Please select any user role!');
		return false;
    }
	return true;
    event.preventDefault(); 

});

jQuery(document).ready(function() {
    jQuery('.uram_res_select_pages').select2();
});

/**
 * User role wise delete button click confirm msg
 **/
jQuery(document).on('click', 'a.delete-role-restiction', function(){
    return confirm(''+res_ajax.user_role_confirm+'');
});

/**
 * User wise delete button click confirm msg
 **/
jQuery(document).on('click', 'a.delete_user_restriction', function(){
    return confirm(''+res_ajax.user_confirm+'');
});