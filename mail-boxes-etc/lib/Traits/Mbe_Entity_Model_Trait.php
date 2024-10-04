<?php

namespace Traits;

trait Mbe_Entity_Model_Trait {

	public function tableExists( $tableName ) {
		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.SchemaChange
		if ( ! $wpdb->get_var( $wpdb->prepare("SHOW TABLES LIKE %s", $tableName )) == $tableName ) {
			return false;
		}

		return true;
	}

	public function truncate( $tableName ) {
		global $wpdb;
		// Do not use real TRUNCATE to avoid reusing ids
		if ( $this->tableExists( $tableName ) && false === $wpdb->query( 'TRUNCATE ' . $this->getTableName() ) ) {
			$helper = new \Mbe_Shipping_Helper_Data();
			$helper->setWpAdminMessages( [
				'message' => urlencode( $wpdb->last_error ),
				'status'  => urlencode( 'error' )
			] );
			\WC_Admin_Settings::add_error( __( 'Error while truncating table ' ) . $this->getTableName() . ' :' . $wpdb->last_error );

			return false;
		}

		return true;
	}

	public function insertRow( $tableName, $row ) {
		global $wpdb;
		try {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.SchemaChange
			if ( false === $wpdb->insert( $tableName, $row ) ) {
				$message = __( 'Error while adding data to the table ' ) . $tableName . ' :' . $wpdb->last_error;
				$helper = new \Mbe_Shipping_Helper_Data();
				$helper->setWpAdminMessages( [
					'message' => urlencode( $message ),
					'status'  => urlencode( 'error' )
				] );
				\WC_Admin_Settings::add_error( $message );

				return false;
			}
		} catch ( \Exception $e ) {
			$message = __( 'Unexpected error' ) . ': ' . $e->getMessage();
			$helper = new \Mbe_Shipping_Helper_Data();
			$helper->setWpAdminMessages( [
				'message' => urlencode( $message ),
				'status'  => urlencode( 'error' )
			] );
			\WC_Admin_Settings::add_error( $message );
		}

		return true;
	}

	public function updateRow( $tableName, $id, $row ) {
		global $wpdb;
		try {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.SchemaChange
			if ( false === $wpdb->update( $tableName, $row, [ 'id' => $id ] ) ) {
				$message = __( 'Error while updating data in the table ' ) . $tableName . ' :' . $wpdb->last_error;
				$helper = new \Mbe_Shipping_Helper_Data();
				$helper->setWpAdminMessages( [
					'message' => urlencode( $message ),
					'status'  => urlencode( 'error' )
				] );
				\WC_Admin_Settings::add_error( $message );

				return false;
			}
		} catch ( \Exception $e ) {
			$message = __( 'Unexpected error' ) . ': ' . $e->getMessage();
			$helper = new \Mbe_Shipping_Helper_Data();
			$helper->setWpAdminMessages( [
				'message' => urlencode( $message ),
				'status'  => urlencode( 'error' )
			] );
			\WC_Admin_Settings::add_error( $message );
		}

		return true;
	}

	public function deleteRow( $tableName, $id ) {
		global $wpdb;
		try {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.SchemaChange
			if ( false === $wpdb->delete( $tableName,[ 'id' => $id ] ) ) {
				$message = __( 'Error while updating data in the table ' ) . $tableName . ' :' . $wpdb->last_error;
				$helper = new \Mbe_Shipping_Helper_Data();
				$helper->setWpAdminMessages( [
					'message' => urlencode( $message ),
					'status'  => urlencode( 'error' )
				] );
				\WC_Admin_Settings::add_error( $message );

				return false;
			}
		} catch ( \Exception $e ) {
			$message = __( 'Unexpected error' ) . ': ' . $e->getMessage();
			$helper = new \Mbe_Shipping_Helper_Data();
			$helper->setWpAdminMessages( [
				'message' => urlencode( $message ),
				'status'  => urlencode( 'error' )
			] );
			\WC_Admin_Settings::add_error( $message );
		}

		return true;
	}

}