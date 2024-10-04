<?php

class Mbe_Shipping_Model_Csv_Pickup_Address  implements Mbe_Shipping_Entity_Model_Interface
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
		return $wpdb->prefix . Mbe_Shipping_Helper_Data::MBE_CSV_PICKUP_ADDRESSES_TABLE_NAME;
	}

	public function getSqlCreate() { // Not used at the moment
		global $wpdb;
		$charset = $wpdb->get_charset_collate();

		return "CREATE TABLE " . $this->getTableName() . " (
        id int(10) unsigned auto_increment ,
        pickup_address_id varchar(256) DEFAULT '',
        trade_name varchar(256) NOT NULL DEFAULT '',
        address_1 varchar(256) NOT NULL DEFAULT '',
        address_2 varchar(256) NOT NULL DEFAULT '',
        address_3 varchar(256) NOT NULL DEFAULT '',
        zip_code varchar(10) NOT NULL DEFAULT '',
        city varchar(30) NOT NULL DEFAULT '',
        province varchar(10) NOT NULL DEFAULT '',
        country varchar(4) NOT NULL DEFAULT '',
        reference varchar(50) NOT NULL DEFAULT '',
        phone_1 varchar(50) NOT NULL DEFAULT '',
        phone_2 varchar(50) NOT NULL DEFAULT '',
        email_1 varchar(256) NOT NULL DEFAULT '',
        email_2 varchar(256) NOT NULL DEFAULT '',
        fax varchar(50) NOT NULL DEFAULT '',
        res boolean DEFAULT false,
        mmr boolean DEFAULT false,
        ltz boolean DEFAULT false,
        is_default boolean DEFAULT false,
        primary key  (id)
    ) $charset;";
	}

}