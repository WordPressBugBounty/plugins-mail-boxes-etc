<?php

include_once 'class-mbe-tracking-admin.php';

class mbe_tracking_factory
{
	const ITALIAN_URL = "https://www.mbe.it/it/tracking?c=";
	const SPAIN_URL = "https://www.mbe.es/es/tracking?c=";
	const GERMANY_URL = "https://www.mbe.de/de/tracking?c=";
	const FRANCE_URL = "https://www.mbefrance.fr/fr/suivi?c=";
	const POLSKA_URL = "https://www.mbe.pl/pl/tracking?c=";
	const CROATIA_URL = "https://www.mbe.hr/hr/tracking?c=";
	const AUSTRIA_URL = "https://www.mbe.at/tracking?c=";
	const PORTUGAL_URL = "https://www.mbe.pt/pt/tracking?c=";
	const UK_URL = "https://www.mbe.co.uk/track?tracking=";

	const DEFAULT_URL = "";

	/**
	 * @throws \MbeExceptions\ApiRequestException
	 */
	public static function create($order_id, $pickupInfo = []) {

	    $result = true;

	    $logger         = new Mbe_Shipping_Helper_Logger();
	    $shippingHelper = new Mbe_Shipping_Helper_Data();
	    $logger->log( "CREATESHIPMENT" );
	    $order = wc_get_order( $order_id );


	    $shippingMethod = $shippingHelper->getShippingMethod( $order );
//	    $serviceName    = $shippingHelper->getServiceName( $order );

	    if ( ! $shippingMethod ) {
		    return false;
	    }

	    $val            = explode( ':', $shippingMethod );
	    $shippingMethod = $val[1]??'';

		$insuranceCode = $shippingHelper->isShippingWithInsurance( $shippingMethod );
		$insurance     = !empty( $insuranceCode );
	    if ( $insurance ) {
		    $shippingMethod = $shippingHelper->convertShippingCodeWithoutInsurance( $shippingMethod );
	    }

	    $shippingMethodArray = explode( '_', $shippingMethod );
		$shippingMethodArray = empty($shippingMethodArray)?[]:$shippingMethodArray;

		$service = $shippingMethodArray[0] ?? null;
		$subzone = $shippingMethodArray[1] ?? null;

	    $orderTotal = $order->get_total();


	    $isCod = false;

	    if ( version_compare( WC()->version, '3', '>=' ) ) {
		    $paymentMethod = $order->get_payment_method();
	    } else {
		    $paymentMethod = $order->payment_method;
	    }
	    if ( $paymentMethod == "cod" ) {
		    $isCod = true;
	    }

	    $shipmentConfigurationMode        = $shippingHelper->getShipmentConfigurationMode();
	    $boxesDimensionWeight             = [];
	    $boxesSingleParcelDimensionWeight = [];

		if ( $shipmentConfigurationMode == Mbe_Shipping_Model_Carrier::SHIPMENT_CONFIGURATION_MODE_ONE_SHIPMENT_PER_ITEM ) {

		    $productsAmount = 0;
		    foreach ( $order->get_items() as $item ) {
			    $productsAmount += $item['qty'];
		    }

		    $codValue = $orderTotal / $productsAmount;

		    foreach ( $order->get_items() as $item ) {
			    $itemQty    = $item['qty'];
			    $id_product = $item['product_id'];
			    $product    = $shippingHelper->getProductFromItem( $item );

			    if ( version_compare( WC()->version, '3', '>=' ) ) {
				    $itemWeight = $product->get_weight();
			    } else {
				    $itemWeight = $product->weight;
			    }

			    $boxesDimensionWeight             = [];
			    $boxesSingleParcelDimensionWeight = [];

			    // Retrieve the product info using the new box structure
			    $shippingHelper->getBoxesArray(
				    $boxesDimensionWeight,
				    $boxesSingleParcelDimensionWeight,
				    $itemWeight,
				    $shippingHelper->getPackageInfo( $product->get_sku() )
			    );


			    $products = $shippingHelper->createProductsArrayForShipping($order, true, $item);

			    if ( isset( $item["subtotal"] ) ) {
				    $subTotal    = $item["subtotal"];
				    $subTotalTax = $item["subtotal_tax"];
			    } else {
				    $subTotal    = $item["line_subtotal"];
				    $subTotalTax = $item["line_subtotal_tax"];
			    }

			    $goodsValue = $products[0]->Price;

			    if ( $shippingHelper->getShipmentsInsuranceMode() == Mbe_Shipping_Helper_Data::MBE_INSURANCE_WITH_TAXES ) {
				    $insuranceValue = ( $subTotal + $subTotalTax ) / $itemQty;
			    } else {
				    $insuranceValue = ( $subTotal ) / $itemQty;
			    }

			    for ( $i = 1; $i <= $itemQty; $i ++ ) {
				    $qty                = array();
				    $qty[ $id_product ] = 1;

				    // $boxesDimensionWeight is used directly, since we use 1 box for each shipment
				    $result = $result && self::createSingleShipment( $order, $service, $subzone, $boxesDimensionWeight, $products, 1, $insurance, $insuranceValue, [], $goodsValue, $isCod, $codValue, $pickupInfo, $insuranceCode);
			    }
		    }
	    } elseif ( $shipmentConfigurationMode == Mbe_Shipping_Model_Carrier::SHIPMENT_CONFIGURATION_MODE_ONE_SHIPMENT_PER_SHOPPING_CART_WEIGHT_MULTI_PARCEL ) {
			if ( $shippingHelper->canEditDynamicPackageData()
			     && $shippingHelper->hasDynamicPackageData( $order_id ) ) {
				// Custom Dynamic Packages for manual order
				$logger->log( "Manual Shipping - Using custom dynamic packages for order " . $order_id );

				$goodsValue     = 0.0;
				$insuranceValue = 0.0;
				$codValue       = $orderTotal;
				$products       = $shippingHelper->createProductsArrayForShipping( $order );

				foreach ( $order->get_items() as $item ) {
					$itemQty     = $item['qty'];
					$product     = $shippingHelper->getProductFromItem( $item );
					$weightPrice = $shippingHelper->getProductWeightPrice( $product );

					foreach ( json_decode( $shippingHelper->getOrderDynamicPackageData( $order_id ), ARRAY_A ) as $packageKey => $packageData ) {
						$packageName                 = 'manual-dynamic-package-' . $packageKey;
						$boxesMerged[ $packageName ] = [
							'dimensions' => [
								'length' => $packageData['length'],
								'width'  => $packageData['width'],
								'height' => $packageData['height'],
							],
							'weight'     => array_fill( 0, $packageData['parcels'], $packageData['weight'] ),
							"maxweight"  => ""
						];
					}

					$goodsValue     = $goodsValue + $weightPrice['price'] * $itemQty;
					$insuranceValue += $shippingHelper->getInsuranceValue( $item );
				}

				$numBoxes = $shippingHelper->countBoxesArray( $boxesMerged );

			}
			else {
//            $maxPackageWeight = $shippingHelper->getMaxPackageWeight();
//            $boxesWeights = array();
			    $goodsValue     = 0.0;
			    $insuranceValue = 0.0;
			    $codValue       = $orderTotal;

			    $logger->logVar( $order->get_items(), "order items" );

			    $products = $shippingHelper->createProductsArrayForShipping( $order );

			    foreach ( $order->get_items() as $item ) {
				    $itemQty = $item['qty'];
//                $id_product = $item['product_id'];
				    $product     = $shippingHelper->getProductFromItem( $item );
				    $packageInfo = $shippingHelper->getPackageInfo( $product->get_sku() );

				    $weightPrice = $shippingHelper->getProductWeightPrice( $product );

				    for ( $i = 1; $i <= $itemQty; $i ++ ) {

					    $boxesDimensionWeight = $shippingHelper->getBoxesArray(
						    $boxesDimensionWeight,
						    $boxesSingleParcelDimensionWeight,
						    $weightPrice['weight'],
						    $packageInfo
					    );
					    $goodsValue           = $goodsValue + $weightPrice['price'];
				    }

				    $insuranceValue += $shippingHelper->getInsuranceValue( $item );
			    }


			    $boxesMerged = $shippingHelper->mergeBoxesArray(
				    $boxesDimensionWeight,
				    $boxesSingleParcelDimensionWeight
			    );
			    $numBoxes    = $shippingHelper->countBoxesArray( $boxesMerged );

			    $logger->logVar( $numBoxes, "boxes amount" );
			    $logger->logVar( $boxesMerged, "boxes weights" );
			    $logger->logVar( $goodsValue, "goods value" );

			}

			$result = self::createSingleShipment(
				$order,
				$service,
				$subzone,
				$boxesMerged,
				$products,
				$numBoxes,
				$insurance,
				$insuranceValue,
				[],
				$goodsValue,
				$isCod,
				$codValue,
				$pickupInfo,
				$insuranceCode
			);
		}
        elseif ($shipmentConfigurationMode == Mbe_Shipping_Model_Carrier::SHIPMENT_CONFIGURATION_MODE_ONE_SHIPMENT_PER_SHOPPING_CART_ITEMS_MULTI_PARCEL) {
            $boxesWeights = array();
            $numBoxes = 0;
            $goodsValue = 0.0;
	        $insuranceValue = 0;
            $codValue = $orderTotal;

            $products = $shippingHelper->createProductsArrayForShipping($order);

            foreach ($order->get_items() as $item) {
                $itemQty = $item['qty'];
                $numBoxes += $itemQty;
                $id_product = $item['product_id'];
                $product = $shippingHelper->getProductFromItem($item);

                $weightPrice = $shippingHelper->getProductWeightPrice($product);

                $logger->logVar($weightPrice['price'], "product price");

                for ($i = 1; $i <= $itemQty; $i ++ ) {
	                $shippingHelper->getBoxesArray(
		                $boxesDimensionWeight,
		                $boxesSingleParcelDimensionWeight,
		                $weightPrice['weight'],
		                $shippingHelper->getPackageInfo( $product->get_sku(), true )
	                );
	                $goodsValue = $goodsValue + $weightPrice['price'];
                }

               $insuranceValue += $shippingHelper->getInsuranceValue($item);
            }

            $logger->logVar($numBoxes, "boxes amount");
            $logger->logVar($boxesWeights, "boxes weights");
            $logger->logVar($goodsValue, "goods value");

			// $boxesSingleParcelDimensionWeight is used directly, since we always use 1 box for each item (we're not using packages CSV)
            $result = self::createSingleShipment($order, $service, $subzone, $boxesSingleParcelDimensionWeight, $products, $numBoxes, $insurance, $insuranceValue, [], $goodsValue, $isCod, $codValue, $pickupInfo, $insuranceCode);

        }
        return $result;
    }

	/**
	 *
	 * @throws \MbeExceptions\ApiRequestException
	 */
    public static function createSingleShipment($order, $service, $subzone, $weight, $products, $boxes, $insurance, $insuranceValue, $qty = [], $goodsValue = 0.0, $isCod = false, $codValue = 0.0, $pickupInfo = [], $insuranceCode = null)
    {

        $logger = new Mbe_Shipping_Helper_Logger();
        $helper = new Mbe_Shipping_Helper_Data();
//        $logger->log('CREATE SINGLE SHIPMENT');
//        $logger->logVar(func_get_args(), 'CREATE SINGLE SHIPMENT ARGS');
        try {

            $shippingMethod = $helper->getShippingMethod($order);
            $serviceName = $helper->getServiceName($order);

            if ($shippingMethod) {
//                $val = explode(':', $shippingMethod);
//                $shippingMethod = $val[1];
//                $shippingMethodArray = explode('_', $shippingMethod);
//
//                $service = $shippingMethodArray[0];
//                $subzone = $shippingMethodArray[1];

                if (version_compare(WC()->version, '3', '>=')) {
                    $firstName = $order->get_shipping_first_name();
                    $lastName = $order->get_shipping_last_name();
                    $address = trim($order->get_shipping_address_1() . ' ' . $order->get_shipping_address_2());
                    $phone = $order->get_billing_phone();
                    $city = $order->get_shipping_city();
                    $countryId = $order->get_shipping_country();
                    $region = $order->get_shipping_state();
                    $postCode = $order->get_shipping_postcode();
                    $email = $order->get_billing_email();

                    $companyName = $order->get_shipping_company();
                    $notes = $order->get_customer_note();
                }
                else {
                    $firstName = $order->shipping_first_name;
                    $lastName = $order->shipping_last_name;
                    $address = trim($order->shipping_address_1 . ' ' . $order->shipping_address_2);
                    $phone = $order->billing_phone;
                    $city = $order->shipping_city;
                    $countryId = $order->shipping_country;
                    $region = $order->shipping_state;
                    $postCode = $order->shipping_postcode;
                    $email = $order->billing_email;

                    $notes = $order->customer_note;
                    $companyName = $order->shipping_company;
                }

                $ws = new Mbe_Shipping_Model_Ws();
	            $orderId = $helper->getOrderId($order);
                $reference = $orderId;

	            // Retrieve pickup data
	            $pickupData = [];
	            $senderInfo = [];
				$isPickup = $pickupInfo['is-pickup'] ?? false;
	            if($isPickup && Mbe_Shipping_Helper_Data::MBE_PICKUP_REQUEST_MANUAL === $helper->getPickupRequestMode() ) {
		            $pickupModel = new Mbe_Shipping_Model_Pickup_Custom_Data();
		            $pickupData = $pickupModel->getRow($pickupInfo['custom-data-id']);

					$ws = new Mbe_Shipping_Model_Ws();
		            $pickupAddress = $ws->getPickupAddressById($pickupData['pickup_address_id']);
					if (empty($pickupAddress)) {
						$message = 'Pickup address cannot be retrieved';
						throw new Exception(__($message, 'mail-boxes-etc'));
					}
		            $senderInfo = [
			            'company-name'      => $pickupAddress['TradeName'],
			            'address'         => $pickupAddress['Address1'],
			            'zipcode'          => $pickupAddress['ZipCode'],
			            'city'              => $pickupAddress['City'],
			            'state'          => $pickupAddress['Province'],
			            'country'           => $pickupAddress['Country'],
			            'name'         => $pickupAddress['Reference'],
			            'phone'           => $pickupAddress['Phone1'],
			            'email'           => $pickupAddress['Email1'],
		            ];

	            }

                $mbeShipment = $ws->createShipping($countryId, $region, $postCode, $weight, $boxes, $products, $service, $subzone, $notes, $firstName, $lastName, $companyName, $address, $phone, $city, $email, $goodsValue, $reference, $isCod, $codValue, $insurance, $insuranceValue, $isPickup, $senderInfo, $pickupData, $insuranceCode);

                $logger->logVar($mbeShipment, "MBE SHIPMENT");

	            if ($mbeShipment) {
		            $trackingNumber = $mbeShipment->MasterTrackingMBE??'';
		            $courierTrackingNumber = $mbeShipment->CourierMasterTrk??'';
					$courierName = $mbeShipment->Courier??'';
		            $label = isset($mbeShipment->Labels) ? ($mbeShipment->Labels->Label ?? null) : null;

		            if (is_array($label)) {
			            $i = 1;
			            foreach ($label as $l) {
				            $fileName = 'MBE_' . $orderId . '_' . $trackingNumber . '_' . $i;
				            if(!empty($l) && self::saveShipmentDocument($l->Type, $l->Stream, $fileName)) {
					            self::saveMultipleShipmentInfo($orderId, Mbe_Shipping_Helper_Data::SHIPMENT_SOURCE_TRACKING_FILENAME, $fileName . '.' . strtolower($l->Type), true);
				            }
				            $i++;
			            }
		            }
		            else {
			            $fileName = 'MBE_' . $orderId . '_' . $trackingNumber;
			            if(!empty($label) && self::saveShipmentDocument($label->Type, $label->Stream, $fileName)) {
				            self::saveMultipleShipmentInfo( $orderId, Mbe_Shipping_Helper_Data::SHIPMENT_SOURCE_TRACKING_FILENAME, $fileName . '.' . strtolower( $label->Type ), true );
			            } else {
							$logger->log('Missing label in response or error saving the label file');
			            }
		            }

		            self::saveMultipleShipmentInfo($orderId, Mbe_Shipping_Helper_Data::SHIPMENT_SOURCE_TRACKING_NUMBER, $trackingNumber);

		            if ( \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled() ) {
			            $order->update_meta_data( Mbe_Shipping_Helper_Data::SHIPMENT_SOURCE_TRACKING_NUMBER, $trackingNumber);
			            $order->update_meta_data( Mbe_Shipping_Helper_Data::SHIPMENT_SOURCE_COURIER_TRACKING_NUMBER, $courierTrackingNumber);
			            $order->update_meta_data( Mbe_Shipping_Helper_Data::SHIPMENT_SOURCE_COURIER_NAME, $courierName);
			            $order->update_meta_data( Mbe_Shipping_Helper_Data::SHIPMENT_SOURCE_TRACKING_NAME, $serviceName);
			            $order->update_meta_data( woocommerce_mbe_tracking_admin::SHIPMENT_SOURCE_TRACKING_SERVICE, $service);
			            $order->update_meta_data( woocommerce_mbe_tracking_admin::SHIPMENT_SOURCE_TRACKING_ZONE, $subzone);
			            $order->update_meta_data( Mbe_Shipping_Helper_Data::SHIPMENT_SOURCE_TRACKING_URL, self::getTrackingUrlBySystem() );
						$order->save();
		            } else {
			            update_post_meta( $orderId, Mbe_Shipping_Helper_Data::SHIPMENT_SOURCE_TRACKING_NUMBER, $trackingNumber, true );
						update_post_meta( $orderId, Mbe_Shipping_Helper_Data::SHIPMENT_SOURCE_COURIER_TRACKING_NUMBER, $courierTrackingNumber, true );
						update_post_meta( $orderId, Mbe_Shipping_Helper_Data::SHIPMENT_SOURCE_COURIER_NAME, $courierName, true );
			            update_post_meta( $orderId, Mbe_Shipping_Helper_Data::SHIPMENT_SOURCE_TRACKING_NAME, $serviceName, true );
			            update_post_meta( $orderId, woocommerce_mbe_tracking_admin::SHIPMENT_SOURCE_TRACKING_SERVICE, $service, true );
			            update_post_meta( $orderId, woocommerce_mbe_tracking_admin::SHIPMENT_SOURCE_TRACKING_ZONE, $subzone, true );
			            update_post_meta( $orderId, Mbe_Shipping_Helper_Data::SHIPMENT_SOURCE_TRACKING_URL, self::getTrackingUrlBySystem() );
		            }

		            if(array_key_exists('is-pickup', $pickupInfo) && $pickupInfo['is-pickup']) {
						$helper->setIsPickupShipped($orderId, true);
		            }
		            return true;
	            }
            }

        } catch (\MbeExceptions\ApiRequestException $e) {
	        throw $e;
        } catch (Exception $e) {
            $logger->log($e->getMessage());
        }
        return false;
    }

    public static function saveMultipleShipmentInfo($post_id, $key, $value)
    {
	    $helper = new Mbe_Shipping_Helper_Data();
	    $helper->saveMultipleShipmentInfo($post_id, $key, $value);
    }

    public static function saveShipmentDocument($type, $content, $filename)
    {
        $helper = new Mbe_Shipping_Helper_Data();
		return $helper->saveShipmentDocument($type, $content, $filename);
    }

	public static function getTrackingUrlBySystem()
	{
		$helper = new Mbe_Shipping_Helper_Data();
		$system = $helper->getCountry();

		switch ($system) {
			case "IT":
				return self::ITALIAN_URL;
			case "ES":
				return self::SPAIN_URL;
			case "DE":
				return self::GERMANY_URL;
			case "AT":
				return self::AUSTRIA_URL;
			case "FR":
				return self::FRANCE_URL;
			case "PL":
				return self::POLSKA_URL;
			case "HR":
				return self::CROATIA_URL;
			case "UK":
				return self::UK_URL;
			case "PT":
				return self::PORTUGAL_URL;
			default:
				return self::DEFAULT_URL;
		}
	}


}

?>