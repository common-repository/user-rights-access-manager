<div class='uram_Permission_main'>
	<form method="post" id="admin_permission_form" action="<?php echo esc_url(admin_url('admin.php?page=uram_permission_list')); ?>">
		<div class="role_handle">
			<div class="select_user_role_wise">

				<h1 class="wp-heading-inline"><?php esc_html_e('Set Permissions', 'user-rights-access-manager'); ?></h1>
				<div class="select_role_div">
					<?php
					$currnt_user_login = wp_get_current_user();
					if (isset($_GET['user_role'])) {
						?>
						<input type="hidden" name="page" value="uram_permission_list">
						<label><?php esc_html_e('Select user to restrict ', 'user-rights-access-manager'); ?></label>
						<select name="user_role_sel" id="select_role_box" <?php echo isset( $_GET['action'] ) && sanitize_text_field($_GET['action']) ? 'disabled' : ''; ?> required>
							<option value=" "><?php esc_html_e('Select User', 'user-rights-access-manager'); ?> </option>
							<?php
							$select_id = isset($_GET['user_role']) ? sanitize_text_field($_GET['user_role']) : '';
							foreach ($user_data as $key => $value) 
							{
								$user_restrict = unserialize(get_user_meta($value->ID, 'user_restriction_option', true));
								if (!$user_restrict && $currnt_user_login->user_login != $value->user_login || ( isset( $_GET['action'] ) && sanitize_text_field($_GET['action']) == 'edit') ) 
								{
									?>
									<option value="<?php echo esc_attr($value->ID); ?>" <?php echo ($value->ID == $select_id ? 'selected' : ''); ?> ><?php echo esc_html($value->user_login); ?></option>
									<?php
								}
							}
							?>
						</select>
						<?php
						if (isset($_GET['action']) && sanitize_text_field($_GET['action']) == 'edit') {
							?> 
							<input type="hidden" name="user_role_sel" value="<?php echo sanitize_text_field($_GET['user_role']) ? sanitize_text_field($_GET['user_role']) : ''; ?>">

							<?php
						}
					}
					$role_restrict = unserialize(get_option('user_restriction_option'));
					if (isset($_GET['sys_role'])) {
						?>
						<input type="hidden" name="page" value="uram_permission_list">
						<label><?php esc_html_e('Select role to restrict', 'user-rights-access-manager'); ?></label>
						<select name="sys_role_sel" id="select_role_system" <?php echo isset( $_GET['action'] ) && sanitize_text_field($_GET['action']) ? 'disabled' : ''; ?> required>
							<option value=" "> <?php esc_html_e('Select Role', 'user-rights-access-manager'); ?> </option>
							<?php
							$select_id = isset($_GET['sys_role']) ? sanitize_text_field($_GET['sys_role']) : '';
							foreach ($all_role as $key_role => $role_show) 
							{
								if(!isset($_GET['action']))
								{
									$currentuserloginroles = isset($currnt_user_login->roles[0]) ? $currnt_user_login->roles[0] : '';
									if( ( !is_array( $role_restrict ) && $key_role != $currentuserloginroles ) || ( isset( $role_restrict['system_role'] ) && is_array( $role_restrict['system_role'] ) && !array_key_exists($key_role,$role_restrict['system_role']) && $key_role != $currentuserloginroles) )
									{
										?>	 
										<option value="<?php echo esc_attr($key_role); ?>" <?php echo ($key_role == $select_id ? 'selected' : ''); ?> ><?php echo esc_html($role_show); ?></option>
										<?php
									}
								}
								else
								{
									if(is_array( $role_restrict['system_role'] ) && $key_role != $currnt_user_login->roles[0])
									{
										?>	 
										<option value="<?php echo esc_attr($key_role); ?>" <?php echo ($key_role == $select_id ? 'selected' : ''); ?> ><?php echo esc_html($role_show); ?></option>
										<?php
									}
								}	
							}
							?>
						</select>
						<?php
						if (isset($_GET['action']) && sanitize_text_field($_GET['action']) == 'edit') 
						{
							?>
							<input type="hidden" name="sys_role_sel" value="<?php echo sanitize_text_field($_GET['sys_role']) ? sanitize_text_field($_GET['sys_role']) : ''; ?>">
							<?php
						}
					}
					?>

				</div>
			</div>
		</div>

		<?php
		if (isset($_GET['user_role']) || isset($_GET['sys_role'])) 
		{
			?>
			<div class="notification_bar_div">
				<table>
					<tr>
						<td class="admin_bar_hide">
							<label>
								<input type="checkbox" name="hide_admin_bar" <?php echo ($get_admin_bar == 'on' ? 'checked' : ''); ?>><?php esc_html_e('Remove Admin Bar Menu Items', 'user-rights-access-manager'); ?>
								<span class="res_lbl_span"></span>
							</label>
						</td>
					</tr>
					<tr>
						<td class="dashboard">
							<label> 
								<input type="checkbox" name="remove_dashboard_metabox" <?php echo ($uram_dashboard == 'on' ? 'checked' : ''); ?>><?php esc_html_e('Remove Dashboard Metaboxes', 'user-rights-access-manager'); ?>
								<span class="res_lbl_span"></span>
							</label>
						</td>
					</tr>
				</table>
			</div>
			<ul id="accordion" class="main_menu_ul">
				<?php
				$class_add ='';
				foreach ($menu as $key => $value) {
					if (!empty($value[0])) {
						$actual_size = 0;
						if (isset($submenu[$value[2]])) {
							$submenu_list = isset($submenu[$value[2]]) ? $submenu[$value[2]] :
							$actual_size = sizeof($submenu_list);
						}

						if (isset($get_permission_sub_menu[$value[2]])) 
						{
							$set_size = is_array($get_permission_sub_menu[$value[2]]) ? sizeof($get_permission_sub_menu[$value[2]]) : 0;
						}

						if ($actual_size)
						{
							$class_add = ($actual_size != $set_size && $set_size != 0 ) ? 'check_not_all' : '';
						}
						$chil_onn_off = '';
						$menu_active = '';
						if (isset($get_permission_main_menu[$value[2]]) && $get_permission_main_menu[$value[2]] == 'on') {
							$chil_onn_off = 'style=display:block;';
							$menu_active = 'menu_active';
						}
						if( $value[2] == 'uram_permission_list' ){
							continue;
						}
						?>
						<li class="res_accordion_li <?php echo esc_attr($menu_active); ?>">
							<div class='sub_grp'>
								<label class="<?php echo esc_attr($class_add); ?>" ><?php echo ($value[0]); ?>
								<input type='checkbox' class="main_menu_id" name="menus[<?php echo $value[2]; ?>]" <?php echo isset($get_permission_main_menu[$value[2]]) ? 'checked' : ''; ?>>
								<span class="res_lbl_span"></span>
							</label>

							</div>
							<ul id="inside_accordion" <?php echo esc_attr($chil_onn_off); ?> class="sub_menu_ul">
								<?php
								if (isset($submenu_list) && isset($submenu[$value[2]])) 
								{
									foreach ($submenu_list as $subkey => $subvalue) 
									{   
										$submenus_res = $subvalue[2];
										$submenus_res = str_replace('&amp;', '&', $submenus_res);
										?>
										<li>
											<div class='sub_grp'>
												<label><?php echo ($subvalue[0]); ?> 
													<input type='checkbox' class="sub_menu_id" name="submenus[<?php echo $value[2]; ?>][<?php echo $subvalue[2]; ?>]" <?php echo isset($get_permission_sub_menu[$value[2]][$submenus_res]) ? 'checked' : ''; ?>>
													<span class="res_lbl_span"></span>
												</label>
											</div>
										</li>
										<?php
									}
								}
								?>
							</ul>
							<ul id="inside_accordion" <?php echo esc_attr($chil_onn_off); ?> class="metaboxes_ul" >
								<?php 
								foreach ($submenu_list as $subkey => $subvalue)
								{
									$this->wpres_metaboxes_add_remove_handle($metaboxes_val,$subvalue[2],$get_metaboxes);

								}
							?>
							</ul>
						</li>
					<?php
					} 
				}
				$checked = "";
				$style = "";
				$add_cls = '';
				if($getCustomURLs){
					if($getCustomURLs['is_on'] == "on"){
						$checked = "checked";
						$style = 'style=display:block;';
						$add_cls = 'menu_active';
					}else{
						$checked = "";
						$style = 'style=display:none;';
					}
				}
				?>
				<li class="res_accordion_li <?php echo esc_attr($add_cls); ?>">
					<div class='sub_grp'>
						<label class="custom_url_class" ><?php esc_html_e("Custom URLs",'user-rights-access-manager'); ?>
							<input type='checkbox' class="main_menu_id" name="custom_url_checkbox" <?php echo $checked; ?>>
							<span class="res_lbl_span"></span>
						</label>
					</div>
					<ul id="inside_accordion custom_lik_ui" class="sub_menu_ul" <?php echo esc_attr($style); ?>>
						<textarea rows="7" style="width: 100%;" name="wprsCustomUrls" placeholder="Example : /mysite.com/wp-admin/edit-comments.php?comment_status=moderated"><?php
						if($getCustomURLs && $getCustomURLs['urls']){
							foreach ($getCustomURLs['urls'] as $value) {
								echo esc_textarea($value)."\n";
							}
						}
						?></textarea>
						<p style="padding-left: 10px;"><b> <?php esc_html_e("Note :",'user-rights-access-manager'); ?></b></p>
						<p style="padding-left: 10px;"><?php esc_html_e("- One URL per line",'user-rights-access-manager') ?><br/>
							<?php esc_html_e("- Do not include http or https or www",'user-rights-access-manager'); ?> </p>
					</ul>
				</li>
				<?php
				$checked = "";
				$style = $add_cls = "";
				if($get_home_menus){
					if(isset($get_home_menus['is_on']) && $get_home_menus['is_on'] == "on"){
						$checked = "checked";
						$style = 'style=display:block;';
						$add_cls = 'menu_active';
					}else{
						$checked = "";
						$style = 'style=display:none;';
					}
				}
				?>
				<li class="res_accordion_li <?php echo esc_attr($add_cls); ?>" >
					<div class='sub_grp'>
						<label class="custom_url_class" ><?php esc_html_e("Home Page Menu",'user-rights-access-manager'); ?>
							<input type='checkbox' class="main_menu_id" name="home_page_menu" <?php echo $checked; ?>>
							<span class="res_lbl_span"></span>
						</label>
					</div>
					<ul id="inside_accordion" class="sub_menu_ul homemenu_list" <?php echo esc_attr($style); ?>>
						<?php

						$pages_get = isset($get_home_menus['pages']) ? $get_home_menus['pages'] : array();
						include_once(URAM_CLASS."uram_walker_pagedropdown_multiple.php");
						$args = array(	                                		
							'name'=>'wram_pages[]',
							'class'=>'uram_res_select_pages',
							'selected'=>$pages_get,
							'echo' => false,
							'walker' => new Uram_Walker_PageDropdown_Multiple(), 
						);

						$pagesDropdown = wp_dropdown_pages( $args );

						//Remove the wrapping select tag from the options so we can use
						//our own select tag with the multiple attribute
						$options = preg_replace( '#^\s*<select[^>]*>#', '', $pagesDropdown );
						$options = preg_replace( '#</select>\s*$#', '', $options );
						?>
						<select class="uram_res_select_pages" name="wram_pages[]" multiple="multiple" style = "width:100%;">
							<?php 
							echo esc_html($options);
							?>	
						</select>
					</ul>
				</li>  
			</ul>
			<?php
			$tab = '';
			if (isset($_GET['user_role'])) {
				$tab = 'user';
			}
			if (isset($_GET['sys_role'])) {
				$tab = 'role';
			}
			?>
			<div class="button_handle">
				<input type="hidden" name="set_permission_menus" value="menus">
				<input type="hidden" name="tab" value="<?php echo esc_attr($tab); ?>">
				<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('res_nonce') ?>">
				<input type="submit" class="button button-primary" id="res_submit_btn" name="posttype_submit" value="<?php esc_html_e('Set Restriction', 'user-rights-access-manager'); ?>" >
				<a class="btn_cancel button-primary" href="<?php echo esc_url(admin_url('admin.php?page=uram_permission_list&tab=') ) . $tab; ?>" ><?php esc_html_e('Cancel', 'user-rights-access-manager'); ?></a>
			</div>
			<?php 
		}
		?> 
	</form>	
</div>
