<?php

class Mbe_Shipping_Model_Csv_Shipping  implements Mbe_Shipping_Entity_Model_Interface
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
		return $wpdb->prefix . Mbe_Shipping_Helper_Data::MBE_CSV_RATES_TABLE_NAME;
	}

	public function getSqlCreate() {
		global $wpdb;
		$charset = $wpdb->get_charset_collate();

		return "CREATE TABLE " . $this->getTableName() . " (
	    id_mbeshippingrate int(10) unsigned NOT NULL AUTO_INCREMENT,
        country varchar(4) NOT NULL DEFAULT '',
        region varchar(10) NOT NULL DEFAULT '',
        city varchar(30) NOT NULL DEFAULT '',
        zip varchar(10) NOT NULL DEFAULT '',
        zip_to varchar(10) NOT NULL DEFAULT '',
        weight_from decimal(12,4) NOT NULL DEFAULT 0,
        weight_to decimal(12,4) NOT NULL DEFAULT 0,
        price decimal(12,4) DEFAULT 0,
        delivery_type varchar(255) DEFAULT '',
        PRIMARY KEY  (id_mbeshippingrate)
     ) $charset;";
	}
}