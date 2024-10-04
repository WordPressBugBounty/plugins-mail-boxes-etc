<?php

global $wpdb;

$helper = new Mbe_Shipping_Helper_Data();
$logger = new Mbe_Shipping_Helper_Logger();

if ($helper->isEnabled() && $helper->isClosureAutomatically()) {

    $ws = new Mbe_Shipping_Model_Ws();
    if ($ws->mustCloseShipments()) {

        $logger->log('Cron Close shipments');

        $time = time();
        $to = date('Y-m-d H:i:s', $time);
        $lastTime = $time - 60 * 60 * 24 * 30; // 60*60*24*2
        $from = date('Y-m-d H:i:s', $lastTime);

        $post_status = implode("','", array('wc-processing', 'wc-completed'));

		$order_ids = $helper->select_mbe_ids();
	    $orders_custom_mapping_ids = $helper->select_custom_mapping_ids();
	    $orders_pickup_batch_ids = $helper->select_pickup_orders_ids();
	    $order_filter = 'AND ((ID IN ('.$order_ids.') OR ID IN ('.$orders_custom_mapping_ids.')) AND ID NOT IN ('.$orders_pickup_batch_ids.'))';

	    if ( \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled() ) {
		    $table_name        = $wpdb->prefix . 'wc_orders';
		    $postmetaTableName = $wpdb->prefix . 'wc_orders_meta';
		    $postmetaTableJoin = ' AS pm ON pm.order_id = p.ID ';
		    $postTypeWhere     = "type = 'shop_order'";
			$postStatus        = 'status';
		    $orderIdField      = 'id';
	    } else {
		    $table_name        = $wpdb->prefix . 'posts';
		    $postmetaTableName = $wpdb->prefix . 'postmeta';
		    $postmetaTableJoin = ' AS pm ON pm.post_id = p.ID ';
		    $postTypeWhere     = "post_type = 'shop_order'";
		    $postStatus        = 'post_status';
		    $orderIdField      = 'ID';
	    }
	    $logger->logVar('Selecting the orders with shipments to close');
	    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.SchemaChange
	    $results = $wpdb->get_results($wpdb->prepare(
			"SELECT * FROM %i 
         			WHERE $postTypeWhere $order_filter 
         			AND %i IN ('{$post_status}')"
		    , $table_name, $postStatus));

        $post_ids = array();
        foreach ($results as $order) {
            $post_ids[] = ($order->$orderIdField);
        }
        $logger->logVar($post_ids,'Orders not closed (all)');
        $toClosedIds = array();
        $alreadyClosedIds = array();
        $withoutTracking = array();
	    $pickupIds = array();

	    foreach ($post_ids as $post_id) {
		    if (!$helper->hasTracking($post_id)) {
			    $withoutTracking[] = $post_id;
		    } elseif (!empty($helper->getOrderPickupCustomDataId($post_id))) {
			    $pickupIds[] = $post_id;
		    } elseif ($helper->isShippingOpen($post_id) && empty($helper->getOrderPickupCustomDataId($post_id))) { // additional check to be sure that pickup shipment won't be closed
			    $toClosedIds[] = $post_id;
		    } else {
			    $alreadyClosedIds[] = $post_id;
		    }
	    }

        $logger->logVar($toClosedIds,'Orders with shipments to close');

        $ws->closeShipping($toClosedIds);

	    if (count($withoutTracking) > 0) {
		    $message = sprintf(__('%s - Total of %d order(s) without tracking number yet.', 'mail-boxes-etc'), date('Y-m-d H:i:s'), count($withoutTracking));
		    $logger->log($message);
	    }

	    if(count($pickupIds) > 0) {
		    $message = sprintf(__('%s - Total of %d order(s) are pickup shipment.', 'mail-boxes-etc'), date('Y-m-d H:i:s'), count($pickupIds));
		    $logger->log($message);
	    }

	    if (count($toClosedIds) > 0) {
		    $message = sprintf(__('%s - Total of %d order(s) have been closed.', 'mail-boxes-etc'), date('Y-m-d H:i:s'), count($toClosedIds));
		    $logger->log($message);
	    }

	    if (count($alreadyClosedIds) > 0) {
		    $message = sprintf(__('%s - Total of %d order(s) were already closed', 'mail-boxes-etc'), date('Y-m-d H:i:s'), count($alreadyClosedIds));
		    $logger->log($message);
	    }

    } else {
	    $logger->log('User must not close shipments');
    }
}
die();