<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of res_wp_role_list_wpl_list
 * @see 'res_wp_role_list_wpl_list'
 * @author prism
 */
if (!class_exists('WP_List_Table')) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}
// class add role listing
class res_wp_role_list_wpl_list extends WP_List_Table {
	// prepare items function
	public function prepare_items() {

		$columns = $this->get_columns();
		$sortable = $this->get_sortable_columns();
		$this->process_bulk_action();
		$data = $this->table_data();
		$perPage = 15;
		$currentPage = $this->get_pagenum();
		$totalItems = count($data);
		$this->set_pagination_args(array(
			'total_items' => $totalItems,
			'per_page' => $perPage,
		));
		$data = array_slice($data, (($currentPage - 1) * $perPage), $perPage);
		$this->_column_headers = array($columns, array(), $sortable);
		$this->items = $data;
	}

	//sortable column
	public function get_columns() {
		$columns = array(
			'cb' => '<input type="checkbox" />', // Render a checkbox instead of text.
			'user_role' => __('Role', 'user-rights-access-manager'),
		);
		return $columns;
	}
	// column return value
	protected function column_cb($item) {
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />', 'delete_ids', // Let's simply repurpose the table's singular label ("movie").
			$item['item_id']// The value of the checkbox should be the record's ID.
		);
	}
	//default column return value
	public function column_default($item, $column_name) {

		return $item[$column_name];
	}
	// column user role in add edit/delete link
	public function column_user_role($item) {

		$actions['edit'] = sprintf(
			'<a href="%1$s">%2$s</a>', esc_url(wp_nonce_url(admin_url('admin.php?page=uram_permission_list&action=edit&sys_role=') . $item['item_id'])), _x('Edit', 'List table row action', 'user-rights-access-manager')
		);
		$actions['delete'] = sprintf(
			'<a class="delete-role-restiction" href="%1$s">%2$s</a>', esc_url(wp_nonce_url(admin_url('admin.php?page=uram_permission_list&action=delete&tab=role&role_data=') . $item['item_id'], 'deleteres_' . $item['item_id'])), _x('Delete', 'List table row action', 'user-rights-access-manager')
		);
		return '<a href="' . esc_url(wp_nonce_url(admin_url('admin.php?page=uram_permission_list&action=edit&sys_role=') . $item['item_id'])) . '" class="row-title">' . $item['user_role'] . '</a>' . $this->row_actions($actions);
	}
	// sortable function
	public function get_sortable_columns() {

		return array(
			'user_role' => array('user_role', false),
		);
	}
	// get data and return table role data
	private function table_data() {
		global $wp_roles;
		$get_role_key = $wp_roles->get_names();
		$data = array();
		$role_restrict = unserialize(get_option('user_restriction_option'));
		if (isset($role_restrict['system_role'])) {
			foreach ($role_restrict['system_role'] as $role_key => $role_val) {

				$data[] = array(
					'user_role' => $get_role_key[$role_key],
					'item_id' => $role_key,
				);
			}
		}
		return $data;
	}

	//sorting
	private function sort_data($a, $b) {
		// Set defaults
		$orderby = 'title';
		$order = 'asc';
		// If orderby is set, use this as the sort column
		if (!empty($_GET['orderby'])) {
			$orderby = sanitize_text_field($_GET['orderby']);
		}
		// If order is set use this as the order
		if (!empty($_GET['order'])) {
			$order = sanitize_text_field($_GET['order']);
		}
		$result = strcmp($a[$orderby], $b[$orderby]);
		if ($order === 'asc') {
			return $result;
		}
		return -$result;
	}

	//bulk action
	protected function get_bulk_actions() {
		$actions = array(
			'delete' => _x('Delete', 'List table bulk action', 'wp-list-table-example'),
		);
		return $actions;
	}
	// bulk action show in table
	protected function process_bulk_action() {
		// Detect when a bulk action is being triggered.
		$nonce = isset($_GET['_wpnonce']) ? sanitize_text_field($_GET['_wpnonce']) : '';
		$id = isset($_GET['role_data']) ? sanitize_text_field($_GET['role_data']) : '';

		if ('delete' === $this->current_action() && wp_verify_nonce($nonce, 'deleteres_' . $id) || 'delete' == $this->current_action() && isset($_GET['_wpnonce']) && wp_verify_nonce(sanitize_text_field($_GET['_wpnonce']), 'bulk-' . $this->_args['plural'])) {
			$user_restrict = unserialize(get_option('user_restriction_option', true));

			if (isset($_GET['delete_ids'])) {
				$delete_ids = sanitize_text_field($_GET['delete_ids']);
				foreach ($delete_ids as $val_dele) {
					unset($user_restrict['system_role'][$val_dele]);
					update_option('user_restriction_option', serialize($user_restrict));
				}

				//wp_redirect(admin_url('admin.php?page=uram_permission_list&tab=role&action=delete&success'));
				//exit();
			}
			if (isset($_GET['role_data']) && !empty($_GET['role_data'])) {
				$user_role = sanitize_text_field($_GET['role_data']);
				if (isset($user_restrict['system_role'][$user_role])) {
					unset($user_restrict['system_role'][$user_role]);
					update_option('user_restriction_option', serialize($user_restrict));
					//wp_redirect(admin_url('admin.php?page=uram_permission_list&tab=role&action=delete&success'));
					//exit();
				}
			}
		}
	}
	// table navigation function
	protected function display_tablenav($which) {
		if ('top' === $which) {
			wp_nonce_field('bulk-' . $this->_args['plural']);
		}
		?>
	<div class="tablenav <?php echo esc_attr($which); ?>">

	    <?php if ($this->has_items()): ?>
	        <div class="alignleft actions bulkactions">
		    <?php $this->bulk_actions($which);?>
	        </div>
		<?php
endif;
		$this->extra_tablenav($which);

		$this->pagination($which);
		?>

	    <br class="clear" />
	</div>
	<?php
}
	// add extra table navigation in add "Add Restriction" button.
	public function extra_tablenav($which) {
		if ('top' === $which) {
			?>
	    <a class="page-title-action btn_res_add" href="<?php echo esc_url(admin_url('admin.php?page=uram_permission_list&sys_role')); ?>"> <?php esc_html_e('Add Restriction', 'user-rights-access-manager');?></a>
	    <?php
}
	}

}
