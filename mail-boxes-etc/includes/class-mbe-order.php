<?php

use Dompdf\Dompdf;
use \iio\libmergepdf\Merger;
use \iio\libmergepdf\Driver\TcpdiDriver;

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class Mbe_E_Link_Order_List_Table extends WP_List_Table
{

	use \Traits\ShipmentActions;

	protected $helper;
	protected $logger;
	protected $ws;
	protected $mustCloseShipments;

    function __construct()
    {
        global $status, $page;

        parent::__construct(array(
            'singular' => 'order',     //singular name of the listed records
            'plural'   => 'orders',    //plural name of the listed records
            'ajax'     => true        //does this table support ajax?
        ));
	    $this->ws = new Mbe_Shipping_Model_Ws();
	    $this->helper = new Mbe_Shipping_Helper_Data();
	    $this->logger = new Mbe_Shipping_Helper_Logger();
    }

	function column_ID($item)
    {

        $actions = array(
            'edit' => sprintf('<a href="' . get_home_url() . '/wp-admin/post.php?post=%s&action=edit">%s</a>', $this->itemId($item), __('Edit', 'mail-boxes-etc')),
        );
	    https://mbe-wordpress/wp-admin/post.php?post=20452&action=edit
        return sprintf('%s %s',
            $this->itemId($item),
            $this->row_actions($actions)
        );

        return sprintf('%1$s <span style="color:silver"></span>%2$s', $this->itemId($item), $this->row_actions($actions));
    }

    function column_post_author($item)
    {
        $order = wc_get_order($this->itemId($item));

        if (version_compare(WC()->version, '3', '>=')) {
            $billingFirstName = $order->get_billing_first_name();
            $billingLastName = $order->get_billing_last_name();
        }
        else {
            $billingFirstName = $order->billing_first_name;
            $billingLastName = $order->billing_last_name;
        }
        return sprintf('%1$s %2$s', $billingFirstName, $billingLastName);
    }

    function column_carrier($item)
    {
        //TODO: verify
        $order = wc_get_order($this->itemId($item));
        $serviceName = $this->helper->getServiceName($order);
        return $serviceName;
        //return sprintf('%s', get_post_meta($this->itemId($item), 'woocommerce_mbe_tracking_name', true));
    }


    function column_tracking($item)
    {
        $trackings = $this->helper->getTrackings($this->itemId($item));
        if (empty($trackings)) {
            return '';
        }
        else {
            $html = '';
	        if ( \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled() ) {
				$order = wc_get_order($this->itemId($item));
				$url = $order->get_meta('woocommerce_mbe_tracking_url');
	        } else {
		        $url = get_post_meta($this->itemId($item), 'woocommerce_mbe_tracking_url', true);
	        }

            $trackingString = $this->helper->getTrackingsString($this->itemId($item));
            if (count($trackings) > 1) {
                $html .= "<a target='_blank' href=" . $url . $trackingString . ">" . __('Track all', 'mail-boxes-etc') . "</a><br/>";
            }

            $i = 0;
            foreach ($trackings as $t) {
                if ($i > 0) {
                    $html .= "<br/>";
                }
                $html .= "<a target='_blank' href=" . $url . $t . ">" . $t . "</a>";
                $i++;
            }
            return $html;
        }
    }

    function column_post_date($item)
    {
	    $order = wc_get_order($this->itemId($item));
	    $date = new DateTimeImmutable($order->get_date_created());
	    return wp_date( get_option( 'date_format' ), $date->getTimestamp() );
    }

    function column_total($item)
    {
        $order = wc_get_order($this->itemId($item));
        return $order->get_total() . ' &euro;';
    }

    function column_payment($item)
    {
        $order = wc_get_order($this->itemId($item));
        if (version_compare(WC()->version, '3', '>=')) {
            $paymentMethodTitle = $order->get_payment_method_title();
        }
        else {
            $paymentMethodTitle = $order->payment_method_title;
        }
        return $paymentMethodTitle;
    }

    function column_status($item)
    {
        return $this->helper->isShippingOpen($this->itemId($item)) ? __('Opened', 'mail-boxes-etc') : __('Closed', 'mail-boxes-etc');
    }

    function column_files($item)
    {
		if($this->helper->isDeliveryPointOrder($this->itemId($item)) && $this->helper->hasTracking($this->itemId($item))) {
			$actionUrl = sprintf( "%sadmin-post.php?action=mbe_download_delivery_point_waybill&mbe_delivery_point_postid=%s&nonce=%s", get_admin_url(), $this->itemId($item), wp_create_nonce( 'mbe_download_delivery_point_waybill' ) );
//			return '<a href="'. get_admin_url() . 'admin-post.php?action=mbe_download_delivery_point_waybill&mbe_delivery_point_postid=' . $this->itemId($item) . '&nonce=' . wp_create_nonce( 'mbe_download_delivery_point_waybill' ) . '">
//						<button style="margin: 0px 5px 0px 5px; color:#778899FF; cursor:pointer" type="button" title="' . __( 'Download waybill', 'mail-boxes-etc' ) . '">
//								<svg style="height: 20px;width: 20px; padding-top: 4px"  aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
//    								<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 9h6m-6 3h6m-6 3h6M7 9h0m0 3h0m0 3h0M4 5h16c.6 0 1 .4 1 1v12c0 .6-.4 1-1 1H4a1 1 0 0 1-1-1V6c0-.6.4-1 1-1Z"/>
//  								</svg>
//	                    </button>
//                    </a>';

			return ' <button style="margin: 0px 5px 0px 5px; color:#778899FF; cursor:pointer" type="button" title="' . __( 'Download waybill', 'mail-boxes-etc' ) . '"
 					  onclick="mbeButtonAction(this, \'' . $actionUrl . '\',\'_self\', \'false\')">
								<svg style="height: 20px;width: 20px; padding-top: 4px"  aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
    								<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 9h6m-6 3h6m-6 3h6M7 9h0m0 3h0m0 3h0M4 5h16c.6 0 1 .4 1 1v12c0 .6-.4 1-1 1H4a1 1 0 0 1-1-1V6c0-.6.4-1 1-1Z"/>
  								</svg>
					 </input>';
		} else {
			$files = $this->helper->getFileNames( $this->itemId($item) );

			$trackings = $this->helper->getTrackings( $this->itemId($item) );
			if ( empty( $files ) ) {
				return '';
			} else {
				$html = '';
				for ( $i = 0; $i < count( $files ); $i ++ ) {
					$filename = __( 'Label', 'mail-boxes-etc' ) . " " . ( $i + 1 );
					$path     = $this->helper->mbeUploadUrl() . DIRECTORY_SEPARATOR . $files[ $i ];
					$html     .= "<a target='_blank' href=" . $path . " style='margin-bottom:5px;display: inline-block;'>" . $filename . "</a></br>";
				}
				if ( isset( $trackings[0] ) && ! $this->helper->isTrackingOpen( $trackings[0] ) ) {
					$path = $this->helper->mbeUploadUrl() . DIRECTORY_SEPARATOR . 'MBE_' . $trackings[0] . "_closed.pdf";
					$html .= "<a target='_blank' href=" . $path . " style='margin-bottom:5px;display: inline-block;'>" . __( 'Closure file', 'mail-boxes-etc' ) . "</a></br>";
				}

				return $html;
			}
		}
    }

	function column_pickup_manifest($item) {
		if ( $this->helper->isPickupShipped( $this->itemId($item) ) && $this->helper->hasTracking( $this->itemId($item) ) ) {
			return '<a href="'. get_admin_url() . 'admin-post.php?action=mbe_download_pickup_manifest&mbe_pickup_postid='. $this->itemId($item) .'&nonce='.wp_create_nonce('mbe_download_pickup_manifest') .'">
						<button  style="margin: 0px 5px 0px 5px; color:#778899FF; cursor:pointer" type="button" title="'.__('Download manifest', 'mail-boxes-etc').'">
								<svg style="height: 20px;width: 20px; padding-top: 4px" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
						            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.5 10.25a2.25 2.25 0 1 0 0 4.5 2.25 2.25 0 0 0 0-4.5Zm0 0a2.225 2.225 0 0 0-1.666.75H12m3.5-.75a2.225 2.225 0 0 1 1.666.75H19V7m-7 4V3h5l2 4m-7 4H6.166a2.225 2.225 0 0 0-1.666-.75M12 11V2a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v9h1.834a2.225 2.225 0 0 1 1.666-.75M19 7h-6m-8.5 3.25a2.25 2.25 0 1 0 0 4.5 2.25 2.25 0 0 0 0-4.5Z"/>
								</svg>
	                    </button>
                    </a>';
//			return '<form action="' . esc_url( admin_url( 'admin-post.php' ) ) .'" method="post" id="mbe_download_pickup_manifest" style="display: inline-flex">
//						<input type="hidden" name="action" value="mbe_download_pickup_manifest"/>
//						<input type="hidden" name="nonce" value="'.wp_create_nonce('mbe_download_pickup_manifest') .'"/>
//						<input type="hidden" name="mbe_pickup_postid" value="'. urlencode( $this->itemId($item) ).'"/>
//
//						<button type="button" style="margin: 0px 5px 0px 5px; color:#778899FF" title="'.__('Download manifest', 'mail-boxes-etc').'">
//							<svg style="height: 16px;width: 16px; padding-top: 4px" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
//					            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.5 10.25a2.25 2.25 0 1 0 0 4.5 2.25 2.25 0 0 0 0-4.5Zm0 0a2.225 2.225 0 0 0-1.666.75H12m3.5-.75a2.225 2.225 0 0 1 1.666.75H19V7m-7 4V3h5l2 4m-7 4H6.166a2.225 2.225 0 0 0-1.666-.75M12 11V2a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v9h1.834a2.225 2.225 0 0 1 1.666-.75M19 7h-6m-8.5 3.25a2.25 2.25 0 1 0 0 4.5 2.25 2.25 0 0 0 0-4.5Z"/>
//							</svg>
//						</button>
//				    </form>';
		}
	}

    function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="id[]" value="%s" />',
//            $this->itemId($item)
            $this->itemId($item)
        );
    }

    function get_columns()
    {
        $columns = array('cb' => '<input type="checkbox" />');
        $columns['id'] = __('Id', 'mail-boxes-etc');

        if ($this->mustCloseShipments) {
            $columns['status'] = __('Status', 'mail-boxes-etc');
        }
        $columns['post_author'] = __('Customer', 'mail-boxes-etc');
        $columns['payment'] = __('Payment', 'mail-boxes-etc');
        $columns['post_date'] = __('Date', 'mail-boxes-etc');
        $columns['total'] = __('Total', 'mail-boxes-etc');
        $columns['carrier'] = __('Carrier', 'mail-boxes-etc');
        $columns['tracking'] = __('Tracking', 'mail-boxes-etc');
        $columns['files'] = __('Downloads', 'mail-boxes-etc');
	    $columns['pickup_manifest'] = __('Pickup manifest', 'mail-boxes-etc');

        return $columns;
    }

    function get_sortable_columns()
    {
        $sortable_columns = array(
            'id'        => array('id', true),
            'post_date' => array('date', true),
        );

        return $sortable_columns;
    }

    function get_bulk_actions()
    {
        $actions = array();
        if (!($this->helper->isCreationAutomatically() ||
              ($this->helper->getPickupRequestEnabled() && Mbe_Shipping_Helper_Data::MBE_PICKUP_REQUEST_AUTOMATIC === $this->helper->getPickupRequestMode())
        )) {
            $actions['creation'] = __('Shipment creation (no pickup)', 'mail-boxes-etc' );
        }
        if (!$this->helper->isClosureAutomatically()) {
            if ($this->mustCloseShipments) {
                $actions['closure'] = __('Close shipments in MBE Online', 'mail-boxes-etc');
            }
        }
		if (!$this->helper->isOnlineMBE()) {
			$actions['return'] = __('Create return shipment', 'mail-boxes-etc');
		}
        $actions['downloadLabels'] = __('Download shipping Labels', 'mail-boxes-etc');
	    $actions['downloadPickupManifest'] = __('Download pickup manifest', 'mail-boxes-etc');

		if($this->helper->getPickupRequestEnabled() && !$this->helper->isCreationAutomatically() ) {
			$actions['creation_pickup'] = __( 'Shipments creation (with pickup data)', 'mail-boxes-etc' );
		}

        return $actions;
    }

    function process_bulk_action()
    {

        if (isset($_REQUEST['id'])) {
            $post_ids = array_map('absint', (array)$_REQUEST['id']);

            switch ($this->current_action()) {
                case 'creation':
                    $this->processShipmentCreation( $post_ids );
	                wp_redirect( admin_url('admin.php?page=' . WOOCOMMERCE_MBE_TABS_PAGE ) );
					exit;
	                break;
                case 'closure':
                    if (!$this->helper->isClosureAutomatically()) {
                        if ($this->mustCloseShipments) {

                            $toClosedIds = array();
                            $alreadyClosedIds = array();
                            $withoutTracking = array();
							$pickupIds = array();

	                        foreach ($post_ids as $post_id) {
                                if (!$this->helper->hasTracking($post_id)) {
	                                $withoutTracking[] = $post_id;
                                } elseif (!empty($this->helper->getOrderPickupCustomDataId($post_id))) {
	                                $pickupIds[] = $post_id;
                                } elseif ($this->helper->isShippingOpen($post_id) && empty($this->helper->getOrderPickupCustomDataId($post_id))) { // additional check to be sure that pickup shipment won't be closed
                                    $toClosedIds[] = $post_id;
                                } else {
                                    $alreadyClosedIds[] = $post_id;
                                }
                            }
                            $this->ws->closeShipping($toClosedIds);
                            if (count($withoutTracking) > 0) {
                                $this->helper->setWpAdminMessages([
									'message' =>  sprintf(__('%s - Total of %d order(s) without tracking number yet.', 'mail-boxes-etc'), date('Y-m-d H:i:s'), count($withoutTracking)),
	                                'status' => 'warning'
	                            ]);
                            }
							if(count($pickupIds) > 0) {
								$this->helper->setWpAdminMessages([
									'message' => sprintf(__('%s - Total of %d order(s) are pickup shipment.', 'mail-boxes-etc'), date('Y-m-d H:i:s'), count($pickupIds)),
									'status' => 'warning'
								]);
							}
                            if (count($toClosedIds) > 0) {
	                            $this->helper->setWpAdminMessages([
	                                'message' =>  sprintf(__('%s - Total of %d order(s) have been closed.', 'mail-boxes-etc'), date('Y-m-d H:i:s'), count($toClosedIds)),
	                                'status' => 'warning'
	                            ]);
                            }

                            if (count($alreadyClosedIds) > 0) {
                                $this->helper->setWpAdminMessages([
									'message' =>  sprintf(__('%s - Total of %d order(s) was already closed', 'mail-boxes-etc'), date('Y-m-d H:i:s'), count($alreadyClosedIds)),
	                                'status' => 'warning'
	                            ]);
                            }
                        }
                    }
                    break;
	            case 'downloadLabels':
	            	try {
			            $labelsPdf = [];
			            foreach ( $post_ids as $post_id ) {
				            if ( $this->helper->isDeliveryPointOrder( $post_id ) ) {

					            if ( \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled() ) {
						            $order = wc_get_order($post_id);
						            $masterTrackingNumber = $order->get_meta(woocommerce_mbe_tracking_admin::SHIPMENT_SOURCE_TRACKING_NUMBER);
					            } else {
						            $masterTrackingNumber = get_post_meta( $post_id, woocommerce_mbe_tracking_admin::SHIPMENT_SOURCE_TRACKING_NUMBER, true );
					            }

					            $response = $this->ws->getDeliveryPointShippingDocument( $masterTrackingNumber );

					            if ( $response !== false && empty( $response->Errors ) ) {
						            $outputPdf   = file_get_contents( $response->CourierWaybill ); // load the remote pdf
						            $labelsPdf[] = $this->convertShippingLabelToPdf( 'pdf', $outputPdf );
					            } else {
//						            $errMsg = sprintf( __( "Download waybill", 'mail-boxes-etc' ) . " - " . __("waybill for order %s is still generating. Please try again in 1 minute", 'mail-boxes-etc' ), $post_id );
						            $errMsg = __( "Download waybill", 'mail-boxes-etc' ) . " $post_id - " . __($response->Errors->Error->Description??'', 'mail-boxes-etc' );
						            $this->helper->logErrorAndSetWpAdminMessage( $errMsg, $this->logger );
					            }
				            } else {
					            $labels = $this->helper->getFileNames( $post_id );
					            if ( is_array( $labels ) ) {
						            foreach ( $labels as $l ) {
							            $labelType   = preg_replace( '/.*\./', '', $l );
							            $labelsPdf[] = $this->convertShippingLabelToPdf( $labelType, file_get_contents( $this->helper->mbeUploadDir() . DIRECTORY_SEPARATOR . $l ) );
						            }
					            }
				            }
			            }
			            if ( ! empty( $labelsPdf ) ) {
				            $outputPdf     = $this->combineLabelsPdf( $labelsPdf );
				            $outputPdfPath = $this->helper->mbeUploadDir() . DIRECTORY_SEPARATOR . current_datetime()->getTimestamp() . rand( 0, 999 ) . '.pdf';
				            $this->helper->setWpAdminMessages( [
					            'message' => urlencode( sprintf( __( "Download waybill", 'mail-boxes-etc' ) . " - " . __("%s Downloaded waybills", 'mail-boxes-etc' ), count( $labelsPdf ) ) ),
					            'status'  => urlencode( 'info' )
				            ] );

				            if ( file_put_contents( $outputPdfPath, $outputPdf ) !== false ) {
					            wp_redirect( admin_url( 'admin.php?page=' . WOOCOMMERCE_MBE_TABS_PAGE . '&reload-download=' . urlencode( $outputPdfPath ) ) );
					            exit;
				            } else {
					            $errMess = __( 'MBE Download shipping labels - error writing to file ' . $outputPdfPath, 'mail-boxes-etc' );
					            $this->logger->log( $errMess );
				            }
			            } else {
				            $this->logger->log( __( 'MBE Download shipping labels - no label to download ', 'mail-boxes-etc' ) );
			            }
//		            } catch (\MbeExceptions\ShippingDocumentException $e) {
//			            $errMsg = sprintf( __( "Download waybill", 'mail-boxes-etc' ) . " - " . __("%s", 'mail-boxes-etc' ), $e->getMessage() );
//			            $this->helper->logErrorAndSetWpAdminMessage( $errMsg, $this->logger );
		            } catch (\Exception $e) {
	            		$errMess = __( "Download waybill", 'mail-boxes-etc' ) . " - " . __('Unexpected error', 'mail-boxes-etc') . ' - ' . $e->getMessage() ;
			            $this->logger->log($errMess);
		            }
		            break;
	            case 'return' :
		            $this->createReturnShipment($post_ids);
//		            remove_query_arg( array( '_wp_http_referer', '_wpnonce' ), wp_unslash( $_SERVER['REQUEST_URI'] ) );
		            wp_redirect( admin_url('admin.php?page=' . WOOCOMMERCE_MBE_TABS_PAGE ) );
					exit;
					break;
	            case 'downloadPickupManifest' :
		            try {
						$masterTrackingIds = [];
			            foreach ( $post_ids as $post_id ) {
				            if (!$this->helper->isPickupShipped($post_id)) {
					            throw new \MbeExceptions\ValidationException(__('Please select only pickup batches', 'mail-boxes-etc'));
				            }

							if(!$this->helper->hasTracking($post_id)) {
								throw new \MbeExceptions\ValidationException(sprintf(__('Order %s does not have a tracking number', 'mail-boxes-etc'), $post_id));
							}

				            if ( \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled() ) {
					            $order = wc_get_order($post_id);
					            $masterTrackingNumber = $order->get_meta(woocommerce_mbe_tracking_admin::SHIPMENT_SOURCE_TRACKING_NUMBER, false);
				            } else {
					            $masterTrackingNumber = get_post_meta($post_id, woocommerce_mbe_tracking_admin::SHIPMENT_SOURCE_TRACKING_NUMBER);
				            }

							// This shouldn't be necessary, but we check it just to avoid unexpected issues
				            if ($masterTrackingNumber) {
								$masterTrackingIds = array_merge($masterTrackingIds, $masterTrackingNumber);
							}
//                          else {
//								$masterTrackingIds[] = $masterTrackingNumber;
//							}
			            }
			            if (!empty($masterTrackingIds)) {
				            $manifestPdf = [];
				            foreach ( $masterTrackingIds as $masterTrackingId ) {
					            $response = $this->ws->getPickupManifest($masterTrackingId);
					            $manifestPdf[] = $response->Label->Stream;
				            }

							if(!empty($manifestPdf)) {
								$outputPdf = $this->combineLabelsPdf( $manifestPdf );


								$outputPdfPath = $this->helper->mbeUploadDir() . DIRECTORY_SEPARATOR . current_datetime()->getTimestamp() . rand( 0, 999 ) . '.pdf';
								if ( file_put_contents( $outputPdfPath, $outputPdf ) !== false ) {
									// dowload the files
									header( 'Content-Description: File Transfer' );
									header( 'Content-Type: application/pdf' );
									header( "Content-Disposition: attachment; filename=mbe-pickup-manifest.pdf" );
									header( 'Content-Transfer-Encoding: binary' );
									header( 'Expires: 0' );
									header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
									header( 'Pragma: public' );
									header( 'Content-Length: ' . filesize( $outputPdfPath ) );
									ob_clean();
									flush();
									readfile( $outputPdfPath );
									wp_delete_file( $outputPdfPath );
									exit;
								} else {
									$errMess = __( 'MBE Download pickup manifest - error writing to file ' . $outputPdfPath, 'mail-boxes-etc' );
									$this->logger->log( $errMess );
									$this->helper->setWpAdminMessages( [
										'message' => urlencode( $errMess ),
										'status'  => urlencode( 'error' )
									] );
								}
							}
			            } else {
				            $errMess = __('MBE Download pickup manifest - no document to download', 'mail-boxes-etc');
				            $this->logger->log( $errMess );
				            $this->helper->setWpAdminMessages( [
					            'message' => urlencode( $errMess ),
					            'status'  => urlencode( 'error' )
				            ] );
			            }
		            } catch (\MbeExceptions\ValidationException $e) {
			            $errMess = __( 'MBE Download pickup manifest - ' . $e->getMessage(), 'mail-boxes-etc') ;
			            $this->logger->log($errMess);
			            $this->helper->setWpAdminMessages( [
				            'message' => urlencode( $errMess ),
				            'status'  => urlencode( 'error' )
			            ] );
					} catch (\Exception $e) {
			            $errMess = __( 'MBE Download pickup manifest - Unexpected error' , 'mail-boxes-etc') . ' - ' . $e->getMessage() ;
			            $this->logger->log($errMess);
			            $this->helper->setWpAdminMessages( [
				            'message' => urlencode( $errMess ),
				            'status'  => urlencode( 'error' )
			            ] );
		            }
					break;
	            case 'creation_pickup' :
					try {
						// Check if any of the selected orders has already been shipped and throw a ValidationException
						foreach ( $post_ids as $post_id ) {
							if ($this->helper->hasTracking( $post_id ) ) {
								throw new \MbeExceptions\ValidationException(__('Please select only not shipped orders', 'mail-boxes-etc'));
			                }
						}
						// Open default data editor and create a row for the batch ( pre-fill with default values from remote )
						do_action( MBE_ESHIP_ID . '_before_create_pickup', $post_ids );
						exit;
					} catch (\MbeExceptions\ValidationException $e) {
						$this->logger->log('MBE Creation pickup - ' . $e->getMessage());
						$this->helper->setWpAdminMessages( [
							'message' => urlencode( $e->getMessage() ),
							'status'  => urlencode( 'error' )
						] );
					}
		            break;
            }
        }
        else {
            if ($this->current_action()) {
                echo '<div class="error"><p>' . esc_html__('Please select items.', 'mail-boxes-etc') . '</p></div>';
            }
        }
    }

    function prepare_items() {
	    global $wpdb;

	    $this->mustCloseShipments = $this->ws->mustCloseShipments(); // Use a local parameter to avoid calling the function multiple times for each row

	    $per_page = 10;
		$max_num_pages = 0;
	    $columns  = $this->get_columns();
	    $hidden   = array();
	    $sortable = $this->get_sortable_columns();

	    $this->_column_headers = array( $columns, $hidden, $sortable );

	    $orders_pickup_batch_ids   = $this->helper->select_pickup_orders_ids();

	    $this->process_bulk_action();

//	    if ( \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled() ) {
//		    $table_name        = $wpdb->prefix . 'wc_orders';
//		    $postmetaTableName = $wpdb->prefix . 'wc_orders_meta';
//		    $postmetaTableJoin = ' AS pm ON pm.order_id = p.ID ';
//		    $postTypeWhere     = "type = 'shop_order'";

		    $order_ids = array(
			    'limit'      => - 1,
//		    'type'     => 'shop_order',
			    'return'     => 'ids',
//			'meta_key'     => '_shipping_method',
//			'meta_value'   => 'mbe_eship|wf_mbe_shipping',
//			'comparison' => 'REGEXP'
			    'meta_query' =>
//				array(
				    array(
					    'key'     => '_shipping_method',
					    'value'   => 'mbe_eship|wf_mbe_shipping',
					    'compare' => 'REGEXP'
				    )
//		    )
		    );

		    $orders_custom_mapping_ids = array(
			    'limit'      => - 1,
//			'type'     => 'shop_order',
			    'return'     => 'ids',
			    'meta_key'   => 'woocommerce_mbe_tracking_custom_mapping',
			    'meta_value' => 'yes',
//			'meta_query' => array(
//				'key'     => 'woocommerce_mbe_tracking_custom_mapping',
//				'value'   => 'yes',
//				'compare' => '='
//			)
		    );


//		$order_query_args = array(
//			'limit'    => $per_page,
//		    'page'     => $this->get_pagenum(),
//		    'type'     => 'shop_order',
//			'paginate' => true,
//			'field_query' => array(
//				'relation' => 'AND',
//				array(
//					'relation' => 'OR',
//					array(
//						'field' => 'id',
//						'value' => wc_get_orders($order_ids),
//						'compare' => 'IN'
//					),
//					array(
//						'field' => 'id',
//						'value' => wc_get_orders($orders_custom_mapping_ids),
//						'compare' => 'IN'
//					)
//				),
//				array(
//					'field' => 'id',
//					'value' => array_column($wpdb->get_results($orders_pickup_batch_ids), 'order_id'),
//					'compare' => 'NOT IN'
//				)
//			)
//		);

		    $order_query_args = array(
			    'limit'       => $per_page,
			    'page'        => $this->get_pagenum(),
			    'post_type'   => 'shop_order',
			    'paginate'    => true,
//			'relations' => 'AND',
			    'meta_query'  => array(
				    array(
					    'relation' => 'OR',
					    array(
						    'key'     => '_shipping_method',
						    'value'   => 'mbe_eship|wf_mbe_shipping',
						    'compare' => 'REGEXP'
					    ),
					    array(
						    'key'   => 'woocommerce_mbe_tracking_custom_mapping',
						    'value' => 'yes',
					    )
					    // OR method_id in order_itemmeta regexp 'mbe_eship|wf_mbe_shipping'
				    )
			    ),
			    'field_query' => array(
				    array(
					    'field'   => 'id',
					    'value'   => array_column( $wpdb->get_results( $orders_pickup_batch_ids ), 'order_id' ), // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.SchemaChange
					    'compare' => 'NOT IN'
				    )
			    )
		    );
			// TODO : wc_get_orders must be used only for HPOS, as for legacy it discards meta_query and field_query
		    $mbeOrders = wc_get_orders( $order_query_args );

//		    $newOrders = wc_get_orders( $order_query_args );
			// TODO : Check - Filter all the orders not only the already filtered ones
//			$mbeOrders = [];
//		    foreach ($newOrders->orders as $order) {
//			    foreach ($order->get_items('shipping') as $item_id => $item) {
//				    if (preg_match("/MBE_ESHIP_ID.'|wf_mbe_shipping'/", $item->get_method_id()) ) {
//					    $mbeOrders[] = $order;
//					    break;
//				    }
//			    }
//		    }

		    $max_num_pages = $mbeOrders->max_num_pages;
//	        $total_items = $mbeOrders->total??0;
		    // $total_items = count($mbeOrders);
		    $this->items = []; // this will be overwritten below

		    // Check in case the user has attempted to page beyond the available range of orders.
		    if ( 0 === $max_num_pages && $order_query_args['page'] > 1 ) {
			    $count_query_args          = $order_query_args;
			    $count_query_args['page']  = 1;
			    $count_query_args['limit'] = 1;
			    $order_count               = wc_get_orders( $count_query_args );
			    $max_num_pages             = (int) ceil( $order_count->total / $order_query_args['limit'] );
		    }

//		    $this->set_pagination_args(
//			    array(
//				    'total_items' => $newOrders->total ?? 0,
//				    'per_page'    => $per_page,
//				    'total_pages' => $max_num_pages,
//			    )
//		    );


		    if ( \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled() ) {
			    $table_name        = $wpdb->prefix . 'wc_orders';
			    $postmetaTableName = $wpdb->prefix . 'wc_orders_meta';
			    $postmetaTableJoin = ' AS pm ON pm.order_id = p.ID ';
			    $postTypeWhere     = "type = 'shop_order'";
		    } else {
			    $table_name        = $wpdb->prefix . 'posts';
			    $postmetaTableName = $wpdb->prefix . 'postmeta';
			    $postmetaTableJoin = ' AS pm ON pm.post_id = p.ID ';
			    $postTypeWhere     = "post_type = 'shop_order'";
		    }

		    $paged   = isset( $_REQUEST['paged'] ) ? max( 0, intval( $_REQUEST['paged'] ) - 1 ) : 0;
		    $paged   = $paged * $per_page;
		    $orderby = ( isset( $_REQUEST['orderby'] ) && in_array( $_REQUEST['orderby'], array_keys( $this->get_sortable_columns() ) ) ) ? sanitize_sql_orderby( $_REQUEST['orderby'] ) : 'id';
		    $order   = ( isset( $_REQUEST['order'] ) && in_array( $_REQUEST['order'], array(
				    'asc',
				    'desc'
			    ) ) ) ? sanitize_text_field( $_REQUEST['order'] ) : 'desc';

		    $order_ids                 = $this->helper->select_mbe_ids();
		    $orders_custom_mapping_ids = $this->helper->select_custom_mapping_ids();

		    $order_filter              = 'AND ((ID IN (' . $order_ids . ') OR ID IN (' . $orders_custom_mapping_ids . ')) AND ID NOT IN (' . $orders_pickup_batch_ids . '))';

		    if ( isset( $_REQUEST["s"] ) && $_REQUEST["s"] != "" ) {
			    $search = esc_sql( $_REQUEST["s"] );

			    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.SchemaChange
			    $total_items = $wpdb->get_var( "SELECT COUNT(DISTINCT(p.ID)) FROM $table_name AS p 
                                           LEFT JOIN $postmetaTableName . $postmetaTableJoin
                                           WHERE $postTypeWhere
                                           {$order_filter}
                                           AND pm.meta_value LIKE '%$search%'
                                           " );

			    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.SchemaChange
			    $query       = $wpdb->prepare( "SELECT p.*, p.ID as ID FROM $table_name AS p
                                       LEFT JOIN $postmetaTableName . $postmetaTableJoin
                                       WHERE $postTypeWhere
                                       {$order_filter}
                                       AND pm.meta_key IN ('_billing_last_name', '_billing_first_name', 'woocommerce_mbe_tracking_name','woocommerce_mbe_tracking_number', '_order_total', '_payment_method_title')
                                       AND (pm.meta_value LIKE '%%$search%%')
                                       GROUP BY p.ID
                                       ORDER BY $orderby $order LIMIT %d OFFSET %d", array( $per_page, $paged ) );

			    $this->items = $wpdb->get_results( $query, ARRAY_A );  // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.SchemaChange
		    } else {

			    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.SchemaChange
			    $total_items = $wpdb->get_var( "SELECT COUNT(id) FROM $table_name
                                            WHERE $postTypeWhere
                                            {$order_filter}" );
			    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.SchemaChange
			    $this->items = $wpdb->get_results( $wpdb->prepare( "SELECT id as ID, $table_name.* FROM $table_name
                                                        WHERE $postTypeWhere
                                                        {$order_filter}
                                                        ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged ), ARRAY_A );
		    }

			$max_num_pages = ceil( $total_items / $per_page ) ;

//	    }

	    $this->set_pagination_args( array(
		    'total_items' => $total_items, // total items defined above
		    'per_page'    => $per_page, // per page constant defined at top of method
		    'total_pages' => $max_num_pages// calculate pages count
	    ) );
    }

	public function convertShippingLabelToPdf(string $labelType, $label)
	{
		$dompdf = new Dompdf();

		switch ($labelType) {
			case 'html':
				$domOptions = $dompdf->getOptions();
//				$domOptions->setIsHtml5ParserEnabled(true);
				$domOptions->setIsRemoteEnabled(true);
				$domOptions->setDefaultMediaType('print');
				$dompdf->setPaper('A4', 'landscape');
				$dompdf->setOptions($domOptions);
				$dompdf->loadHtml($label);
				$dompdf->render();
				return $dompdf->output();
			case 'gif':
				$gifHtml = '<img style="min-width:90%; max-height:90%" src="data:image/gif;base64,' . base64_encode($label) . '">';
				$dompdf->loadHtml($gifHtml);
				$dompdf->render();
				return $dompdf->output();
			default: //pdf
				return $label;
		}
	}

	/**
	 * Merge an array of PDF stream labels
	 *
	 * @param array $labelsContent
	 * @return string
	 */
	public function combineLabelsPdf(array $labelsContent)
	{
		$outputPdf = new Merger(new TcpdiDriver());
		foreach ( $labelsContent as $item ) {
			$outputPdf->addRaw($item);
		}
		return $outputPdf->merge();
	}

	function itemId($item) {
		if ( \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled() ) {
			//return $item->get_id();
			return $item['id'];
		} else {
			return $item['ID'];
		}
	}

}