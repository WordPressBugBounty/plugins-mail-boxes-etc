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

	    $insurance = $shippingHelper->isShippingWithInsurance( $shippingMethod );
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


			    $products       = array();
			    $p              = new stdClass;
			    $p->SKUCode     = $product->get_sku();
			    $p->Description = $shippingHelper->getProductTitleFromItem( $item );
			    $p->Quantity    = 1;
				$p->Currency    = $order->get_currency();

			    if ( isset( $item["subtotal"] ) ) {
				    $subTotal    = $item["subtotal"];
				    $subTotalTax = $item["subtotal_tax"];
			    } else {
				    $subTotal    = $item["line_subtotal"];
				    $subTotalTax = $item["line_subtotal_tax"];
			    }

			    $p->Price = ( $subTotal ) / $itemQty;
			    $products[] = $p;

			    $goodsValue = $p->Price;

			    if ( $shippingHelper->getShipmentsInsuranceMode() == Mbe_Shipping_Helper_Data::MBE_INSURANCE_WITH_TAXES ) {
				    $insuranceValue = ( $subTotal + $subTotalTax ) / $itemQty;
			    } else {
				    $insuranceValue = ( $subTotal ) / $itemQty;
			    }

			    for ( $i = 1; $i <= $itemQty; $i ++ ) {
				    $qty                = array();
				    $qty[ $id_product ] = 1;

				    // $boxesDimensionWeight is used directly, since we use 1 box for each shipment
				    $result = $result && self::createSingleShipment( $order, $service, $subzone, $boxesDimensionWeight, $products, 1, $insurance, $insuranceValue, [], $goodsValue, $isCod, $codValue, $pickupInfo);
			    }
		    }
	    } elseif ( $shipmentConfigurationMode == Mbe_Shipping_Model_Carrier::SHIPMENT_CONFIGURATION_MODE_ONE_SHIPMENT_PER_SHOPPING_CART_WEIGHT_MULTI_PARCEL ) {
//            $maxPackageWeight = $shippingHelper->getMaxPackageWeight();
//            $boxesWeights = array();
		    $products       = array();
		    $goodsValue     = 0.0;
		    $insuranceValue = 0.0;
		    $codValue       = $orderTotal;

		    foreach ( $order->get_items() as $item ) {
			    $itemQty = $item['qty'];
//                $id_product = $item['product_id'];
			    $product     = $shippingHelper->getProductFromItem( $item );
			    $packageInfo = $shippingHelper->getPackageInfo( $product->get_sku() );


			    $p              = new stdClass;
			    $p->SKUCode     = $product->get_sku();
			    $p->Description = $shippingHelper->getProductTitleFromItem( $item );
			    $p->Quantity    = $itemQty;
			    $p->Currency    = $order->get_currency();

			    $weightPrice = self::getProductWeightPrice($product);

			    $p->Price   = $weightPrice['price'];
			    $products[] = $p;

			    for ( $i = 1; $i <= $itemQty; $i ++ ) {

				    $boxesDimensionWeight = $shippingHelper->getBoxesArray(
					    $boxesDimensionWeight,
					    $boxesSingleParcelDimensionWeight,
					    $weightPrice['weight'],
					    $packageInfo
				    );
				    $goodsValue = $goodsValue + $weightPrice['price'];
			    }

			    $insuranceValue += self::getInsuranceValue($item);
		    }


		    $boxesMerged = $shippingHelper->mergeBoxesArray(
			    $boxesDimensionWeight,
			    $boxesSingleParcelDimensionWeight
		    );
		    $numBoxes = $shippingHelper->countBoxesArray( $boxesMerged );

		    $logger->logVar( $numBoxes, "boxes amount" );
		    $logger->logVar( $boxesMerged, "boxes weights" );
		    $logger->logVar( $goodsValue, "goods value" );

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
			    $pickupInfo
		    );
	    }
        elseif ($shipmentConfigurationMode == Mbe_Shipping_Model_Carrier::SHIPMENT_CONFIGURATION_MODE_ONE_SHIPMENT_PER_SHOPPING_CART_ITEMS_MULTI_PARCEL) {
            $boxesWeights = array();
            $products = array();
            $numBoxes = 0;
            $goodsValue = 0.0;
	        $insuranceValue = 0;
            $codValue = $orderTotal;

            foreach ($order->get_items() as $item) {
                $itemQty = $item['qty'];
                $numBoxes += $itemQty;
                $id_product = $item['product_id'];
                $product = $shippingHelper->getProductFromItem($item);


                $p = new stdClass;
                $p->SKUCode = $product->get_sku();
                $p->Description = $shippingHelper->getProductTitleFromItem($item);
                $p->Quantity = $itemQty;
	            $p->Currency = $order->get_currency();

	            $weightPrice = self::getProductWeightPrice($product);

	            $p->Price   = $weightPrice['price'];
	            $products[] = $p;

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

               $insuranceValue += self::getInsuranceValue($item);
            }

            $logger->logVar($numBoxes, "boxes amount");
            $logger->logVar($boxesWeights, "boxes weights");
            $logger->logVar($goodsValue, "goods value");

			// $boxesSingleParcelDimensionWeight is used directly, since we always use 1 box for each item (we're not using packages CSV)
            $result = self::createSingleShipment($order, $service, $subzone, $boxesSingleParcelDimensionWeight, $products, $numBoxes, $insurance, $insuranceValue, [], $goodsValue, $isCod, $codValue, $pickupInfo);

        }
        return $result;
    }

	/**
	 *
	 * @throws \MbeExceptions\ApiRequestException
	 */
    public static function createSingleShipment($order, $service, $subzone, $weight, $products, $boxes, $insurance, $insuranceValue, $qty = [], $goodsValue = 0.0, $isCod = false, $codValue = 0.0, $pickupInfo = [])
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
						throw new Exception(__($message));
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

                $mbeShipment = $ws->createShipping($countryId, $region, $postCode, $weight, $boxes, $products, $service, $subzone, $notes, $firstName, $lastName, $companyName, $address, $phone, $city, $email, $goodsValue, $reference, $isCod, $codValue, $insurance, $insuranceValue, $isPickup, $senderInfo, $pickupData);

                $logger->logVar($mbeShipment, "MBE SHIPMENT");

	            if ($mbeShipment) {
		            $trackingNumber = $mbeShipment->MasterTrackingMBE;
		            $label = !empty($mbeShipment->Labels)?$mbeShipment->Labels->Label:null;

		            if (is_array($label)) {
			            $i = 1;
			            foreach ($label as $l) {
				            $fileName = 'MBE_' . $orderId . '_' . $trackingNumber . '_' . $i;
				            if(!empty($l) && self::saveShipmentDocument($l->Type, $l->Stream, $fileName)) {
					            self::saveMultipleShipmentInfo($orderId, woocommerce_mbe_tracking_admin::SHIPMENT_SOURCE_TRACKING_FILENAME, $fileName . '.' . strtolower($l->Type), true);
				            }
				            $i++;
			            }
		            }
		            else {
			            $fileName = 'MBE_' . $orderId . '_' . $trackingNumber;
			            if(!empty($label) && self::saveShipmentDocument($label->Type, $label->Stream, $fileName)) {
				            self::saveMultipleShipmentInfo( $orderId, woocommerce_mbe_tracking_admin::SHIPMENT_SOURCE_TRACKING_FILENAME, $fileName . '.' . strtolower( $label->Type ), true );
			            } else {
							$logger->log('Missing label in response or error saving the label file');
			            }
		            }

		            self::saveMultipleShipmentInfo($orderId, woocommerce_mbe_tracking_admin::SHIPMENT_SOURCE_TRACKING_NUMBER, $trackingNumber);

		            if ( \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled() ) {
			            $order->update_meta_data( woocommerce_mbe_tracking_admin::SHIPMENT_SOURCE_TRACKING_NUMBER, $trackingNumber);
			            $order->update_meta_data( woocommerce_mbe_tracking_admin::SHIPMENT_SOURCE_TRACKING_NAME, $serviceName);
			            $order->update_meta_data( woocommerce_mbe_tracking_admin::SHIPMENT_SOURCE_TRACKING_SERVICE, $service);
			            $order->update_meta_data( woocommerce_mbe_tracking_admin::SHIPMENT_SOURCE_TRACKING_ZONE, $subzone);
			            $order->update_meta_data( woocommerce_mbe_tracking_admin::SHIPMENT_SOURCE_TRACKING_URL, self::getTrackingUrlBySystem() );
						$order->save();
		            } else {
			            update_post_meta( $orderId, woocommerce_mbe_tracking_admin::SHIPMENT_SOURCE_TRACKING_NUMBER, $trackingNumber, true );
			            update_post_meta( $orderId, woocommerce_mbe_tracking_admin::SHIPMENT_SOURCE_TRACKING_NAME, $serviceName, true );
			            update_post_meta( $orderId, woocommerce_mbe_tracking_admin::SHIPMENT_SOURCE_TRACKING_SERVICE, $service, true );
			            update_post_meta( $orderId, woocommerce_mbe_tracking_admin::SHIPMENT_SOURCE_TRACKING_ZONE, $subzone, true );
			            update_post_meta( $orderId, woocommerce_mbe_tracking_admin::SHIPMENT_SOURCE_TRACKING_URL, self::getTrackingUrlBySystem() );
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
	    if ( \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled() ) {
		    $order = wc_get_order($post_id);
		    $old_meta = $order->get_meta($key);
		    if (!empty($old_meta)) {
			    $old_meta = $old_meta . Mbe_Shipping_Helper_Data::MBE_SHIPPING_TRACKING_SEPARATOR . $value;
				$order->update_meta_data($key,$old_meta);
		    } else {
				$order->add_meta_data($key, $value, true);
		    }
		    $order->save();
	    } else {
		    $old_meta = get_post_meta($post_id, $key, true);
		    // Update post meta
		    if (!empty($old_meta)) {
			    $old_meta = $old_meta . Mbe_Shipping_Helper_Data::MBE_SHIPPING_TRACKING_SEPARATOR . $value;
			    update_post_meta($post_id, $key, $old_meta);
		    }
		    else {
			    add_post_meta($post_id, $key, $value, true);
		    }
	    }


    }


    public static function saveShipmentDocument($type, $content, $filename)
    {
        $helper = new Mbe_Shipping_Helper_Data();
        $logger = new Mbe_Shipping_Helper_Logger();

        $ext = "txt";
        if ($type == "HTML") {
            $ext = "html";
        }
        elseif ($type == "PDF") {
            $ext = "pdf";
        }
        elseif ($type == "GIF") {
            $ext = "gif";
        }
        $filePath = $helper->getShipmentFilePath($filename, $ext);
        $saveResult = file_put_contents($filePath, $content);

        $message = "Saving shipping document :" . $filePath;

        if ($saveResult) {
            $message .= " OK";
        }
        else {
            $message .= " FAILURE";
        }

        $logger->log($message);
		return $saveResult;
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

	protected static function getInsuranceValue($item)
	{
		$shippingHelper = new Mbe_Shipping_Helper_Data();

		$subTotalKey     = isset($item["subtotal"]) ? "subtotal" : "line_subtotal";
		$subTotalTaxKey  = isset($item["subtotal"]) ? "subtotal_tax" : "line_subtotal_tax";

		$subTotal    = $item[$subTotalKey];
		$subTotalTax = $item[$subTotalTaxKey];

		if ($shippingHelper->getShipmentsInsuranceMode() === Mbe_Shipping_Helper_Data::MBE_INSURANCE_WITH_TAXES) {
			return $subTotal + $subTotalTax;
		}

		return $subTotal;
	}

	protected static function getProductWeightPrice( $product )
	{
		if ( version_compare( WC()->version, '3', '>=' ) ) {
			$productResult['weight'] = (float)$product->get_weight();
			$productResult['price']  = (float)$product->get_price();
		} else {
			$productResult['weight'] = (float)$product->weight;
			$productResult['price']  = (float)$product->price;
		}

		return $productResult;
	}

}

?>