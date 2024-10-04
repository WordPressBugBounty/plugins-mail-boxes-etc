<?php

class Mbe_Shipping_Model_Csv_Package_Product  implements Mbe_Shipping_Entity_Model_Interface
{
	use \Traits\Mbe_Entity_Model_Trait {
		insertRow as protected traitInserRow;
		truncate as protected traitTruncate;
		tableExists as protected traitTableExists;
		insertRow as protected traitInsertRow;
		updateRow as protected traitUpdateRow;
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
		return $wpdb->prefix . Mbe_Shipping_Helper_Data::MBE_CSV_PACKAGES_PRODUCT_TABLE_NAME;
	}

	public function getSqlCreate() {
		global $wpdb;
		$charset = $wpdb->get_charset_collate();

		return "CREATE TABLE " . $this->getTableName() . " (
        custom_package tinyint(1) null ,
        single_parcel tinyint(1) null ,
        product_sku varchar(64) not null ,
        package_code varchar(50) not null ,
        id int(10) unsigned auto_increment ,
        primary key  (id),
        unique key MBE_PKG_PROD_PACKAGE_PRODUCT_UNIQUE  (package_code, product_sku),
        unique key MBE_PKG_PROD_PRODUCT_SKU  (product_sku),
        key MBE_PKG_PROD_PACKAGE_CODE  (package_code)
        ) $charset;";
	}

}