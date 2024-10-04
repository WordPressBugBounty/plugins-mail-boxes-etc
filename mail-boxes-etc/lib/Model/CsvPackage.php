<?php

class Mbe_Shipping_Model_Csv_Package implements Mbe_Shipping_Entity_Model_Interface
{
	use \Traits\Mbe_Entity_Model_Trait {
		insertRow as protected traitInserRow;
		truncate as protected traitTruncate;
		tableExists as protected traitTableExists;
		insertRow as protected traitInsertRow;
		updateRow as protected traitUpdateRow;
	}

	protected $packagesProductModel;

	public function __construct() {
		$this->packagesProductModel = new Mbe_Shipping_Model_Csv_Package_Product();
	}

	public function tableExists() {
		return $this->traitTableExists($this->getTableName());
	}

	public function truncate() {
		return $this->traitTruncate($this->getTableName());
	}

	public function insertRow($row ) {
		return $this->traitInserRow($this->getTableName(), $row);
	}

	public function updateRow($id, $row ) {
		return $this->traitUpdateRow($this->getTableName(), $id, $row);
	}

	public function getTableName() {
		global $wpdb;
		return $wpdb->prefix . Mbe_Shipping_Helper_Data::MBE_CSV_PACKAGES_TABLE_NAME;
	}

	public function getSqlCreate() {
		global $wpdb;
		$charset = $wpdb->get_charset_collate();

		return "CREATE TABLE " . $this->getTableName() . " (
			max_weight decimal(12,4) default 0 not null,
        length decimal(12,4) default 0 not null,
        width decimal(12,4) default 0 not null,
        height decimal(12,4) default 0 not null,
        package_label varchar(255) not null,
        package_code varchar(55) not null,
        id int(10) unsigned auto_increment,
        primary key  (id),
        unique key wp_mbe_shipping_standard_packages_package_code_uindex  (package_code)
        ) $charset;";
	}

	public function getPackageInfobyProduct( $productSku ) {
		global $wpdb;
		$main_table      = $this->getTableName();
		$packagesProduct = $this->packagesProductModel->getTableName();

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.SchemaChange
		return $wpdb->get_results( $wpdb->prepare(
			"SELECT $main_table.*," .
			" $packagesProduct.id as id_product, $packagesProduct.single_parcel, $packagesProduct.custom_package " .
			" FROM $main_table " .
			" LEFT JOIN $packagesProduct ON " .
			" $main_table.package_code = $packagesProduct.package_code " .
			" WHERE $packagesProduct.product_sku = %s"
			, $productSku
		), ARRAY_A );
	}

	public function getStandardPackages() {
		global $wpdb;
		$main_table = $this->getTableName();
		$join_table = $this->packagesProductModel->getTableName();

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.SchemaChange
		return $wpdb->get_results(
			"SELECT $main_table.package_label, $main_table.id  FROM $main_table" .
			" LEFT JOIN $join_table ON " .
			" $main_table.package_code = $join_table.package_code " .
			" WHERE $join_table.custom_package <> true OR $join_table.custom_package is null"
			, ARRAY_A );
	}

	public function getCsvPackages() {
		global $wpdb;
		$main_table = $this->getTableName();

		return $wpdb->get_results($wpdb->prepare("SELECT *  FROM %i ", $main_table), ARRAY_A ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.SchemaChange

	}

	public function getPackageInfobyId( $packageId ) {
		global $wpdb;
		$main_table = $this->getTableName();

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.SchemaChange
		$a = $wpdb->get_results( $wpdb->prepare(
			"SELECT *, 0 as single_parcel, 0 as custom_package FROM $main_table " .
			" WHERE id = %d"
			, $packageId )
			, ARRAY_A );
		return $a;
	}

}