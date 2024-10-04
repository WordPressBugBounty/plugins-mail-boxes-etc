<?php

namespace Traits;

use Mbe_Shipping_Model_Pickup_Custom_Data;
use mbe_tracking_factory;
use MbeExceptions\ValidationException;

trait ShipmentActions {

	protected function process_order($orderId, $pickupInfo = [])
	{
		$helper           = new \Mbe_Shipping_Helper_Data();

		include_once MBE_ESHIP_PLUGIN_DIR . 'includes/class-mbe-tracking-factory.php';
//		$orderId = $helper->getOrderId($order);
		return mbe_tracking_factory::create($orderId, $pickupInfo);
	}

	/**
	 * Check if the same pickup batch row exsits for all the given orders IDs and return its ID or false
	 *
	 * @param $ordersIds
	 *
	 * @return int|boolean
	 */
	protected function checkPickupBatchId( $ordersIds ) {
		$helper        = new \Mbe_Shipping_Helper_Data();
		$customDataIds = [];

		// Get the custom pickup data table id just to be sure that the orders are linked to the same data (to avoid issues due to some data not up to date)
		foreach ( $ordersIds as $id ) {
			$customDataIds[] = $helper->getOrderPickupCustomDataId( $id );
		}

		if ( empty( $customDataIds ) ) {
			return false;
		}

		if ( count( array_unique( $customDataIds ) ) > 1 ) {
			$helper->setWpAdminMessages( [
				'message' => urlencode( __( 'Error the shipments don\'t belong to the same Pickup batch', 'mail-boxes-etc' ) ),
				'status'  => urlencode( 'error' )
			] );

			return false;
		}

		// Return the batch ID
		$pickupBatchModel = new Mbe_Shipping_Model_Pickup_Custom_Data();

		return $pickupBatchModel->getRow( $customDataIds[0] )['pickup_batch_id'];

	}

	public function processShipmentCreation( array $post_ids, $pickupInfo = [] ) {
		$helper            = new \Mbe_Shipping_Helper_Data();
		$toCreationIds     = array();
		$alreadyCreatedIds = array();
		$errorsIds         = array();
		$return            = ['status'=>true, 'count' => 0];

		foreach ( $post_ids as $post_id ) {
			$post_id = (int) $post_id;
// TODO Check bulk status order update
			if ( $helper->hasTracking( $post_id ) ) {
				array_push( $alreadyCreatedIds, $post_id );
			} else {
//				$order = new \WC_Order( $post_id );
				if ( $this->process_order( $post_id, $pickupInfo ) ) {
					array_push( $toCreationIds, $post_id );
				} else {
					array_push( $errorsIds, $post_id );
				}

			}
		}
		if ( count( $toCreationIds ) > 0 ) {
			$helper->setWpAdminMessages( [
				'message' => urlencode( sprintf( __( 'Total of %d order shipment(s) have been created.', 'mail-boxes-etc' ), count( $toCreationIds ) ) ),
				'status'  => urlencode( 'updated' )
			] );
			$return['count'] = count( $toCreationIds );
//                        echo '<div class="updated"><p>' . sprintf(__('Total of %d order shipment(s) have been created.', 'mail-boxes-etc'), $toCreationIds)  . '</p></div>';
		}
		if ( count( $alreadyCreatedIds ) > 0 ) {
			$helper->setWpAdminMessages( [
				'message' => urlencode( sprintf( __( 'Total of %d order shipment(s) was already created.', 'mail-boxes-etc' ), count( $alreadyCreatedIds ) ) ),
				'status'  => urlencode( 'warning' )
			] );
		}
		if ( count( $errorsIds ) > 0 ) {
			$helper->setWpAdminMessages( [
				'message' => urlencode( sprintf( __( 'WARNING %d shipments couldn\'t be created due to an error.', 'mail-boxes-etc' ), count( $errorsIds ) ) ),
				'status'  => urlencode( 'error' )
			] );
			// Return false to notify issues in creation (mainly for pickup batches) and return the count of failed creation
			$return = ['status'=>false, 'count' => count( $errorsIds )];
		}

		return $return;
	}


	public function createPickup( array $post_ids, $hasPickupBatch = false ): void {
		$pickupCustomData = new Mbe_Shipping_Model_Pickup_Custom_Data();
		$helper           = new \Mbe_Shipping_Helper_Data();
		$logger           = new \Mbe_Shipping_Helper_Logger();
		$ws               = new \Mbe_Shipping_Model_Ws();
		$customDataId     = null;
		try {
			if(!$helper->getPickupRequestEnabled()) {
				throw new ValidationException( __( 'Pickup request is not enabled', 'mail-boxes-etc' ) );
			}
			$logger->log('CreatePickup method - Start');
			if (( 1 === count( $post_ids ) || \Mbe_Shipping_Helper_Data::MBE_PICKUP_REQUEST_AUTOMATIC === $helper->getPickupRequestMode()) && !$hasPickupBatch) {
				// One shot pickup or automatic pickup batch
				$logger->log('CreatePickup method - One shot');

				// If pickup mode is set to auto, the metaitem is empty
				$customDataId = $helper->getOrderPickupCustomDataId( $post_ids[0] );

				// Validate pickup custom data if any
				if(!empty($customDataId)) {
					$this->validateCustomPickupData($customDataId);
				}

				$pickupInfo = [
					'is-pickup'      => true,
					'is-batch'       => false,
					'custom-data-id' => $customDataId ?: null,
				];

				$creationResult = $this->processShipmentCreation(
					$post_ids,
					$pickupInfo,
				);

				if ( $creationResult['status'] ) {
					// Set the batch status to CONFIRMED
					$pickupCustomData->setStatus($customDataId, 'confirmed');

					$helper->setWpAdminMessages( [
						'message' => urlencode( __( 'The pickup has been sent', 'mail-boxes-etc' ) ),
						'status'  => urlencode( 'success' )
					] );
				} else {
					$helper->setWpAdminMessages( [
						'message' => urlencode( __( 'The pickup cannot be sent', 'mail-boxes-etc' ) ),
						'status'  => urlencode( 'error' )
					] );
				}

			} else { // manual pickup batch
				$logger->log('CreatePickup method - Manual batch');

				// Check orders batch id
				$batchId = $this->checkPickupBatchId( $post_ids );
				if ( false !== $batchId &&
				     ('ready' === strtolower($pickupCustomData->getStatus( $batchId )) || 'initialized' === strtolower($pickupCustomData->getStatus( $batchId )))
				) {
					$batchIdRow = $pickupCustomData->getBatchIdRow( $batchId );
					$customDataId = $batchIdRow->id;

					// Validate pickup custom data
					$this->validateCustomPickupData($customDataId);

					// Get the custom pickup data Id from the table just to be sure it's up to date
					$pickupInfo = [
						'is-pickup'      => true,
						'is-batch'       => true,
						'custom-data-id' => $customDataId,
					];

					$creationResult = $this->processShipmentCreation(
						$post_ids,
						$pickupInfo
					);

					if ( $creationResult['status'] ) {
						$pickupCustomData->setStatus($customDataId, 'initialized');
						// Close the pickup batch (convert the customData stdObj to an associative array) in case of errot it throws an ApiRequestException
						$ws->closePickupShippping($batchId, json_decode(json_encode($pickupCustomData->getBatchIdRow( $batchId )),true));

						// Set the batch status to CONFIRMED
						$pickupCustomData->setStatus($customDataId, 'confirmed');

						$helper->setWpAdminMessages( [
							'message' => urlencode( sprintf( __( 'The pickup batch %s has been sent', 'mail-boxes-etc' ), $batchId ) ),
							'status'  => urlencode( 'success' )
						] );

					} else if($creationResult['count'] < count( $post_ids ) ) {
						// Set the batch status to INITIALIZED
						$pickupCustomData->setStatus($customDataId, 'initialized');
					}
				}
			}
		} catch ( \MbeExceptions\ApiRequestException $e ) {
			$helper->setWpAdminMessages( [
				'message' => urlencode( __( $e->getMessage(), 'mail-boxes-etc' ) ),
				'status'  => urlencode( 'error' )
			] );

			// Delete the pickup custom data row on error to avoid creating a duplicate and since it makes no sense to enable row edit for one shot shipments
//			if ( 1 === count( $post_ids ) && !$hasPickupBatch ) {
//				if($helper->detachOrderFromPickupBatch( $post_ids[0] )) {
//					$pickupCustomData->deleteRow( $customDataId );
//				} else {
//					$logger->log(sprintf(__('Cannot delete the pickup custom data row since order %d cannot be detached as it is a shipped pickup', 'mail-boxes-etc'), $post_ids[0]));
//				}
//			}
		} catch (ValidationException $e) {
			$logger->log($e->getMessage());
			$helper->setWpAdminMessages( [
				'message' => urlencode( __( $e->getMessage(), 'mail-boxes-etc' ) ),
				'status'  => urlencode( 'error' )
			]);
		}
	}

	/**
	 * @throws ValidationException
	 */
	protected function validateCustomPickupData( $customDataId ) {
		$pickupCustomData = new Mbe_Shipping_Model_Pickup_Custom_Data();

		$validated = $pickupCustomData->validate_row( $pickupCustomData->getRow($customDataId) );

		if($validated === true) {
			return true;
		} else {
			throw new ValidationException(wp_kses_post($validated));
		}

	}

	public function createReturnShipment( $post_ids ) {
		$helper           = new \Mbe_Shipping_Helper_Data();
		$ws               = new \Mbe_Shipping_Model_Ws();

		if (!$helper->isOnlineMBE()) {
			$toReturnIds = array();
			$alreadyReturnedIds = array();
			$withoutTracking = array();

			foreach ($post_ids as $post_id) {
				if (!$helper->hasTracking($post_id)) {
					array_push($withoutTracking, $post_id);
				}
				elseif ($helper->isReturned($post_id)) {
					array_push($alreadyReturnedIds, $post_id);
				}
				else {
					array_push($toReturnIds, $post_id);
				}
			}
			$returnedShipping = $ws->returnShipping($toReturnIds);
			$returnedIds = 0;
			foreach ( $toReturnIds as $return_id ) {
				$order = wc_get_order($return_id);
				$returnTrackingMeta = '';
				$returnedTrackings = 0;
				$trackingList = $helper->getTrackings($helper->getOrderId($order));
				$returnedIds += count(array_filter($returnedShipping[$return_id], function($x) { return !empty($x); }));
				foreach ( $trackingList as $tracking ) {
					$returnedTrackings += (!empty($returnedShipping[$return_id][$tracking])?1:0);
					$returnTrackingMeta .= (!empty($returnedShipping[$return_id][$tracking])?$returnedShipping[$return_id][$tracking]:'').\Mbe_Shipping_Helper_Data::MBE_SHIPPING_TRACKING_SEPARATOR;
				}
				if ($returnedTrackings>0) {
					// add or update metadata
					if ( \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled() ) {
						$order->update_meta_data(\Mbe_Shipping_Helper_Data::SHIPMENT_SOURCE_RETURN_TRACKING_NUMBER,substr($returnTrackingMeta, 0,-1));
						$order->save();
					} else {
						update_post_meta($helper->getOrderId($order), \Mbe_Shipping_Helper_Data::SHIPMENT_SOURCE_RETURN_TRACKING_NUMBER, substr($returnTrackingMeta, 0,-1), true);
					}
				}
			}

			if (count($withoutTracking) > 0) {
				$helper->setWpAdminMessages([
					'message' => sprintf(__('%s - Total of %d order(s) without tracking number yet.', 'mail-boxes-etc'), date('Y-m-d H:i:s'), $withoutTracking) ,
					'status'   => 'error'
				]);
			}
			$helper->setWpAdminMessages([
				'message' => sprintf(__('%s - Total of %d return shipments created.', 'mail-boxes-etc'), date('Y-m-d H:i:s'), $returnedIds) ,
				'status'   => $returnedIds>0?'updated':'error'
			]);

			if (count($alreadyReturnedIds) > 0) {
				$helper->setWpAdminMessages([
					'message' => sprintf(__('%s - Total of %d order were already returned', 'mail-boxes-etc'), date('Y-m-d H:i:s'), $alreadyReturnedIds),
					'status'   => 'error'
				]);
			}
		}
		return true;
	}

}