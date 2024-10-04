<?php

if (!class_exists('WP_List_Table')) {
	require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class Mbe_Shipping_Csv_Editor extends WP_List_Table
{
	protected $tableName = '';
	protected $title;
	protected $csvType;
	protected $default;
	protected $columns = [];
	protected $sortable_columns = [];
	protected $backLink = 'mbe_welcome';
	protected $isRemote = false;
	protected $idColumn = 'id';
	protected $sql = '';


	public function __construct( $args = array() ) {
		global $wpdb;
		$this->tableName = $wpdb->prefix . $this->tableName;
		parent::__construct( $args );
	}

	/** Function to be overridden by the child class
	 *
	 */
	public function get_remoteRows($orderBy = null, $order = 'asc'): array {}

	/**
	 * Prepares the list of items for displaying.
	 */
	public function prepare_items() {
		global $wpdb;

		$this->_column_headers = [
			$this->get_columns(),
			array(),			// hidden
			$this->get_sortable_columns()
		];
		$this->process_bulk_action();
		$order = $this->get_items_query_order();

		if($this->isRemote) {
			$this->items = $this->get_remoteRows($_REQUEST['orderby']??null, $_REQUEST['order']??null);
		} else {
			$this->items = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %i {$order}", $this->tableName), ARRAY_A ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.SchemaChange
		}

		$this->set_pagination_args([]);
	}

	public function get_backlink() {
		return $this->backLink;
	}

	public function get_isRemote() {
		return $this->isRemote;
	}

	public function get_ID() {
		return $this->idColumn;
	}

	public function get_tablename(){
		return $this->tableName;
	}
	public function get_sql() {
		return $this->sql;
	}

	public function get_title($singular = true){
		if($singular) {
			return ucwords(__($this->_args['singular'], 'mail-boxes-etc'));
		}
		return ucwords(__($this->_args['plural'], 'mail-boxes-etc'));
	}

	public function get_defaults()
	{
		return $this->default;
	}

	function get_columns()
	{
		return $this->columns;
	}

	function get_sortable_columns()
	{
		return $this->sortable_columns;
	}

	function get_bulk_actions()
	{
		return array(
			'delete' => 'Delete'
		);
	}

	function process_bulk_action()
	{
		global $wpdb;
		$table_name = $this->tableName;

		if ('delete' === $this->current_action()) {
			$ids = isset($_REQUEST['id']) ? (array)$_REQUEST['id'] : [];

			if (!empty($ids)) {
				do_action(MBE_ESHIP_ID . '_csv_editor_deleting', $ids);
				if (!$this->isRemote) {
					$wpdb->query( $wpdb->prepare("DELETE FROM %i WHERE %i IN ( " . implode( ',', $ids ) . " )" , $table_name, $this->get_ID()) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.SchemaChange
				}
				do_action(MBE_ESHIP_ID . '_csv_editor_deleted', $ids, $this->csvType);
			}
		}
	}

	function column_default( $item, $column_name ) {
		esc_html_e($item[$column_name]);
	}

	function column_id($item)
	{
		$actions = array(
			'edit' => sprintf( '<a href="?page=' . MBE_ESHIP_ID . '_csv_edit_form&csv=%s&id=%s">%s</a>', $_REQUEST['csv'], $item[$this->get_ID()], __('Edit', 'mail-boxes-etc')),
			'delete' => sprintf('<a href="?page=%s&action=delete&csv=%s&id=%s">%s</a>', $_REQUEST['page'], $_REQUEST['csv'], $item[$this->get_ID()], __('Delete', 'mail-boxes-etc')),
		);

		return sprintf('%s %s',
			$item[$this->get_ID()],
			$this->row_actions($actions)
		);
	}

	function column_cb($item)
	{
		return sprintf(
			'<input type="checkbox" name="id[]" value="%s" />',
			$item[$this->get_ID()]
		);
	}

	protected function get_items_query_order() {
		if ( ! empty( $_REQUEST['orderby'] ) && in_array( $_REQUEST['orderby'], array_keys($this->sortable_columns) ) ) {
			$by =esc_sql( wc_clean( $_REQUEST['orderby'] ));

			if ( ! empty( $_REQUEST['order'] ) && 'asc' === strtolower( $_REQUEST['order'] ) ) {
				$order = 'ASC';
			} else {
				$order = 'DESC';
			}

			return "ORDER BY {$by} {$order}";
		}
		return 'ORDER BY ID ASC';
	}

	protected function extra_tablenav( $which ) {
		static $has_items;

		if ( ! isset( $has_items ) ) {
			$has_items = $this->has_items();
		}

		echo '<div class="alignright actions">';

		if ( 'top' === $which ) {
			echo sprintf( '<a class="add-new-h2" href="?page=' . esc_attr(MBE_ESHIP_ID) . '_csv_edit_form&action=new&csv=' . esc_attr($this->csvType) . '&id=%s">%s</a>', null, esc_html__('Add') . ' ' . esc_html($this->get_title()));
		}

		echo '</div>';
	}

	public function validate_row($item){}

	public function has_duplicates($field, $value, $type, $id = null)
	{
		if(!$this->get_isRemote()) {
			global $wpdb;
			$idParam = ! empty( $id ) ? " AND id <> $id" : '';
			$wpdb->get_results( $wpdb->prepare( "SELECT id FROM %i WHERE $field = $type $idParam", $this->tableName, $value ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.SchemaChange

			return $wpdb->num_rows > 0;
		}
		return false;
	}

	public function isReservedCode($value)
	{
		if($value === Mbe_Shipping_Helper_Data::MBE_CSV_PACKAGES_RESERVED_CODE) {
			return true;
		}
		return false;
	}

	protected function sortRemoteRows($remoteRows, $orderBy, $order) {
		usort( $remoteRows, function ( $a, $b ) use ( $orderBy, $order ) {
			if ( $order === 'desc' ) {
				return $a[ $orderBy ] < $b[ $orderBy ] ? 1 : - 1;
			} else {
				return $a[ $orderBy ] < $b[ $orderBy ] ? - 1 : 1;
			}
		} );

		return $remoteRows;
	}

}