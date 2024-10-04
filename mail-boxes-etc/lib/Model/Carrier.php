<?php

class Mbe_Shipping_Model_Carrier {

	const SHIPMENT_CONFIGURATION_MODE_ONE_SHIPMENT_PER_ITEM = 1;
	const SHIPMENT_CONFIGURATION_MODE_ONE_SHIPMENT_PER_SHOPPING_CART_WEIGHT_MULTI_PARCEL = 2;
	const SHIPMENT_CONFIGURATION_MODE_ONE_SHIPMENT_PER_SHOPPING_CART_ITEMS_MULTI_PARCEL = 3;

	const HANDLING_TYPE_PER_SHIPMENT = "S";
	const HANDLING_TYPE_PER_PARCEL = "P";

	const HANDLING_TYPE_FIXED = "F";

	const MBE_CACHE_ID = 'mbe_cache_getrates';


	/**
	 * Carrier's code, as defined in parent class
	 *
	 * @var string
	 */
	protected $_code = 'mbe_shipping';

	/** @var $logger Mbe_Shipping_Helper_Logger */
	protected $logger = null;
	/** @var $shippingHelper Mbe_Shipping_Helper_Data */
	protected $shippingHelper = null;

	protected $allowedShipmentServicesArray;


	public function __construct() {
		$this->shippingHelper               = new Mbe_Shipping_Helper_Data();
		$this->logger                       = new Mbe_Shipping_Helper_Logger();
		$this->allowedShipmentServicesArray = $this->shippingHelper->getAllowedShipmentServicesArray(); // set a local parameter to be updated (for uap) outside getRates
	}


	public function getRequestWeight( $request ) {
		$result = 0;
		foreach ( $request['contents'] as $item ) {
			$result += ( (float) $item['data']->get_weight() * $item['quantity'] );
		}

		return $result;
	}

	public function collectRates( $request ) {
		if ( $this->shippingHelper->isEnabledCustomMapping() ) {
			// If custom mapping is used no MBE methods will be available
			return [];
		} else {
			$this->logger->log( 'collectRates' );

			if ( ! $this->shippingHelper->isEnabled() ) {
				$this->logger->log( 'module disabled' );

				return false;
			}
			$this->logger->log( 'module enabled' );


			$destCountry = $request['destination']['country'];
			$destRegion  = $request['destination']['state'];
			//TODO:verify
			$city = $request['destination']['city'];

			$destPostCode = $request['destination']['postcode'];

			$this->logger->log( "Destination: COUNTRY: " . $destCountry . " - REGION CODE: " . $destRegion . " - POSTCODE: " . $destPostCode );


			$shipmentConfigurationMode = $this->shippingHelper->getShipmentConfigurationMode();


			$shipments = array();
			//$baseSubtotalInclTax = $request['contents_cost'];//wrong because it is without taxes
			$baseSubtotalInclTax = 0;
			foreach ( $request['contents'] as $item ) {
				$baseSubtotalInclTax += $item['line_total'] + $item['line_tax'];
			}

			$boxesDimensionWeight             = [];
			$boxesSingleParcelDimensionWeight = [];

			$this->logger->log( "SHIPMENTCONFIGURATIONMODE: " . $shipmentConfigurationMode );

			if ( $shipmentConfigurationMode == self::SHIPMENT_CONFIGURATION_MODE_ONE_SHIPMENT_PER_ITEM ) {

				$iteration = 1;
				foreach ( $request['contents'] as $item ) {
					$boxesDimensionWeight             = [];
					$boxesSingleParcelDimensionWeight = [];

					$products = $this->getProductsFromCart([$item], true);

					// Retrieve the product info using the new box structure
					$this->shippingHelper->getBoxesArray(
						$boxesDimensionWeight,
						$boxesSingleParcelDimensionWeight,
						$item['data']->get_weight(),
						$this->shippingHelper->
						getPackageInfo( $item['data']->get_sku() )
					);

					for ( $i = 1; $i <= $item['quantity']; $i ++ ) {
						$this->logger->log( "Product Iteration: " . $iteration );
//						$weight = $item['data']->get_weight();
						if ( $this->shippingHelper->getShipmentsInsuranceMode() == Mbe_Shipping_Helper_Data::MBE_INSURANCE_WITH_TAXES ) {
							if ( version_compare( WC()->version, '3', '>=' ) ) {
								$insuranceValue = wc_get_price_including_tax( $item["data"] );
							} else {
								$insuranceValue = $item["data"]->get_price_including_tax();
							}
						} else {
							if ( version_compare( WC()->version, '3', '>=' ) ) {
								$insuranceValue = wc_get_price_excluding_tax( $item["data"] );
							} else {
								$insuranceValue = $item["data"]->get_price_excluding_tax();
							}
						}
						// $boxesDimensionWeight is used directly, since we use 1 box for each shipment and this method is run for every item (1 item - 1 box - 1 shipment)
						$shipments = $this->getRates( $destCountry, $destRegion, $city, $destPostCode, $baseSubtotalInclTax, $boxesDimensionWeight, 1, $products, $shipments, $iteration, $insuranceValue );
						$iteration ++;
					}
				}
			} elseif ( $shipmentConfigurationMode == self::SHIPMENT_CONFIGURATION_MODE_ONE_SHIPMENT_PER_SHOPPING_CART_WEIGHT_MULTI_PARCEL ) {
				$insuranceValue = 0;
				$products = $this->getProductsFromCart($request['contents']);

				foreach ( $request['contents'] as $item ) {
					$packageInfo = $this->shippingHelper->getPackageInfo( $item['data']->get_sku() );

					$itemQty = $item['quantity'];

					for ( $i = 1; $i <= $itemQty; $i ++ ) {
						$boxesDimensionWeight = $this->shippingHelper->getBoxesArray(
							$boxesDimensionWeight,
							$boxesSingleParcelDimensionWeight,
							$item['data']->get_weight(),
							$packageInfo
						);
					}

					$insuranceValue += $this->getSubtotalForInsurance( $item );
				}

				$boxesMerged = $this->shippingHelper->mergeBoxesArray(
					$boxesDimensionWeight,
					$boxesSingleParcelDimensionWeight
				);

				$numBoxes = $this->shippingHelper->countBoxesArray( $boxesMerged );

				$this->logger->log( "Num Boxes: " . $numBoxes );

				$shipments = $this->getRates(
					$destCountry, $destRegion, $city, $destPostCode, $baseSubtotalInclTax, $boxesMerged, $numBoxes, $products, [], 1, $insuranceValue
				);
			} elseif ( $shipmentConfigurationMode == self::SHIPMENT_CONFIGURATION_MODE_ONE_SHIPMENT_PER_SHOPPING_CART_ITEMS_MULTI_PARCEL ) {
				$numBoxes       = 0;
				$insuranceValue = 0;
				$products = $this->getProductsFromCart($request['contents']);

				foreach ( $request['contents'] as $item ) {
					$insuranceValue += $this->getSubtotalForInsurance( $item );
					$numBoxes       += $item['quantity'];
					for ( $i = 1; $i <= $item['quantity']; $i ++ ) {
						$this->shippingHelper->getBoxesArray(
							$boxesDimensionWeight,
							$boxesSingleParcelDimensionWeight,
							$item['data']->get_weight(),
							$this->shippingHelper->getPackageInfo( $item['data']->get_sku(), true )
						);
					}
				}
				$this->logger->log( "Num Boxes: " . $numBoxes );
				// $boxesSingleParcelDimensionWeight is used directly, since we always use 1 box for each item (we're not using packages CSV)
				$shipments = $this->getRates( $destCountry, $destRegion, $city, $destPostCode, $baseSubtotalInclTax, $boxesSingleParcelDimensionWeight, $numBoxes, $products, array(), 1, $insuranceValue );

			} else {
				$this->logger->log( 'SHIPMENT CONFIGURATION MODE - Value not set or incorrect, try to save the settings page again '.($shipmentConfigurationMode?' - '.$shipmentConfigurationMode:''), true);
			}


			//TODO- remove subzone if it is the same for all shipping methods

			$subZones = array();
			foreach ( $shipments as $shipment ) {

				if ( ! in_array( $shipment->subzone_id, $subZones ) ) {
					array_push( $subZones, $shipment->subzone_id );
				}

			}
			$useSubZone = false;
			if ( count( $subZones ) > 1 ) {
				$useSubZone = true;
			}

			$result = array();
			if ( empty( $shipments ) ) {
				$errorTitle = 'Unable to retrieve shipping methods';
				$this->logger->log( $errorTitle );
			} else {
				foreach ( $shipments as $shipment ) {

					// Check the conditions to show delivery point service
					$showDeliveryPointService = false;
					if ( $shipment->method === MBE_DELIVERY_POINT_SERVICE ) {
						$showDeliveryPointService = $this->isDeliveryPointEnabled( $this->shippingHelper->mergeBoxesArray(
							$boxesDimensionWeight,
							$boxesSingleParcelDimensionWeight
						), $request['contents'] );
					}

					if($shipment->method !== MBE_DELIVERY_POINT_SERVICE || $showDeliveryPointService) {
						// Add Rate to the list. In case of the Delivery Point Rate, add additional info
						if ( $useSubZone ) {
							$currentRate = $this->_getRate( $shipment->title_full, $shipment->shipment_code, $shipment->price, $shipment->delivery_point_services ?? null, $shipment->tax_and_duties_data ?? null );
						} else {
							$currentRate = $this->_getRate( $shipment->title, $shipment->shipment_code, $shipment->price, $shipment->delivery_point_services ?? null, $shipment->tax_and_duties_data ?? null );
						}
						$result[] = $currentRate;
					}
				}
			}
		}

		return $result;
	}


	private function getRates( $destCountry, $destRegion, $city, $destPostCode, $baseSubtotalInclTax, $weight, $boxes, $products, $oldResults = array(), $iteration = 1, $insuranceValue = 0 ) {
		$shipmentsCache = json_decode( wp_cache_get( self::MBE_CACHE_ID ) );
		$cachedId       = md5( serialize( [
			$destCountry,
			$destRegion,
			$city,
			$destPostCode,
			$baseSubtotalInclTax,
			$weight,
			$boxes
		] ) );

		$ratesHelper = new Mbe_Shipping_Helper_Rates();
		$ws = new Mbe_Shipping_Model_Ws();

		if ( ! empty( $shipmentsCache ) && $shipmentsCache->id === $cachedId ) {
			// return cached rates
			$this->logger->log( "getRates from cache" );
			$shipments = $shipmentsCache->data;
			$molRatesforCsv = $shipmentsCache->molRatesforCsv; // retrieve cached mol rates if any for CSV Tax&Duties
		} else {
			// delete cache if exists but not for the same request
			if ( ! empty( $shipmentsCache ) ) {
				wp_cache_delete( self::MBE_CACHE_ID );
			}
			$this->logger->log( "getRates" );

			if ( $ratesHelper->useCustomRates( $destCountry ) ) {
				$totalWeight = $this->shippingHelper->totalWeightBoxesArray( $weight );
				$shipments = $ratesHelper->getCustomRates( $destCountry, $destRegion, $city, $destPostCode, $totalWeight, $insuranceValue );
				if($this->shippingHelper->isEnabledTaxAndDuties()) $molRatesforCsv = $ws->estimateShipping( $destCountry, $destRegion, $city, $destPostCode, $weight, $boxes, $insuranceValue, $products );
			} else {
				$shipments = $ws->estimateShipping( $destCountry, $destRegion, $city, $destPostCode, $weight, $boxes, $insuranceValue, $products );
			}
			$this->logger->logVar( $shipments, 'ws estimateShipping result' );
			// set cache
			wp_cache_add( self::MBE_CACHE_ID, wp_json_encode( [ 'id' => $cachedId, 'data' => $shipments, 'molRatesforCsv' => $molRatesforCsv??null ] ) );
		}

		$result = null;
		if ( $shipments ) {
			$newResults = [];

			// Adding Delivery point method to the list of allowed services if mbe services are selected
			if(!empty(array_intersect(MBE_ESTIMATE_DELIVERY_POINT_SERVICES, $this->allowedShipmentServicesArray))) {
				$this->allowedShipmentServicesArray[] = MBE_DELIVERY_POINT_SERVICE;
			}

			// Iterates over the shipments and generate the methods list, keeping the last one in case of duplicates
			foreach ( $shipments as $shipment ) {
				$shipmentMethod = $shipment->Service;

				$shipmentMethodKey = $shipment->Service . "_" . $shipment->IdSubzone;


				if ( in_array( $shipmentMethod, $this->allowedShipmentServicesArray ) ) {
					$shipmentTitle = __( $shipment->ServiceDesc, 'mail-boxes-etc' );
					if ( $shipment->SubzoneDesc ) {
						$shipmentTitle .= " - " . __( $shipment->SubzoneDesc, 'mail-boxes-etc' );
					}

					$shipmentPrice = $shipment->NetShipmentTotalPrice;

					$shipmentPrice = $this->applyFee( (float)$shipmentPrice, $boxes );

					$shippingThreshold = $this->shippingHelper->getThresholdByShippingService( $shipmentMethod . ( $this->shippingHelper->getCountry() === $destCountry ? '_dom' : '_ww' ) );

					$isFreeShipping = $shippingThreshold != '' && $baseSubtotalInclTax >= $shippingThreshold;

					if ( $isFreeShipping ) {
						$shipmentPrice = 0;
					}


					$customLabel            = esc_html( $this->shippingHelper->getShippingMethodCustomLabel( $shipment->Service ) );
					$current                = new stdClass();
					$current->title         = $customLabel ?: ( __( $shipment->ServiceDesc, 'mail-boxes-etc' ) );
					$current->title_full    = $shipmentTitle;
					$current->method        = $shipmentMethod;
					$current->price         = $shipmentPrice;
					$current->subzone       = $shipment->SubzoneDesc;
					$current->subzone_id    = $shipment->IdSubzone;
					$current->shipment_code = $shipmentMethodKey;

					// Add all GEL couriers codes and prices to be used in the frontend map widget
					if(MBE_DELIVERY_POINT_SERVICE === $shipmentMethod) {
						if( $isFreeShipping ) {
							$shipment->PriceForMap = array_fill( 0, count( $shipment->PriceForMap??[] ), 0 );
						} else {
							$shipment->PriceForMap = array_map(function($value) use ($boxes) {
								return $this->applyFee($value, $boxes);
							}, $shipment->PriceForMap??[]);
						}

						$current->delivery_point_services = [
							'courier' => $shipment->CourierService ?? [],
							'mol'     => $shipment->MolService ?? [],
							'prices'  => $shipment->PriceForMap,
						];
					}


					// If we are using CSV CustomRates retrieve the Tax&Duties data from the WS rates
					if($ratesHelper->useCustomRates( $destCountry ) && !empty($molRatesforCsv)) {
						$filteredMolRates = array_filter(
							$molRatesforCsv,
							function ($item) use ($shipmentMethod) {
								return isset($item->Service) && $item->Service === $shipmentMethod;
							}
						);
						// Get the first match if there are multiple matches, or NULL if there is no match.
						$molService = reset($filteredMolRates);

						if(!empty($molService)) {
							$shipment->CustomDutiesGuaranteed  = $molService->CustomDutiesGuaranteed;
							$shipment->NetTaxAndDutyTotalPrice = $molService->NetTaxAndDutyTotalPrice;
						}
					}

					// Add Tax and Duties data
					if($this->shippingHelper->isEnabledTaxAndDuties() && isset($shipment->CustomDutiesGuaranteed)) {
						$current->tax_and_duties_data = [
							'custom_duties_guaranteed' => $shipment->CustomDutiesGuaranteed,
							'net_tax_and_duty_total_price' => round($shipment->NetTaxAndDutyTotalPrice, 2),
						];

					}

					$newResults[ $shipmentMethodKey ] = $current;

				}
			}
			if ( $iteration == 1 ) {
				$result = $newResults;
			} else {

				foreach ( $newResults as $newResultKey => $newResult ) {
					if ( array_key_exists( $newResultKey, $oldResults ) ) {
						$newResult->price        += $oldResults[ $newResultKey ]->price;
						$result[ $newResultKey ] = $newResult;
					}

				}
			}

			return $result;
		}
		return [];
	}

	public function applyFee( $value, $packages = 1 ) {
		$handlingType   = $this->shippingHelper->getHandlingType();
		$handlingAction = $this->shippingHelper->getHandlingAction();
		$handlingFee    = (float)$this->shippingHelper->getHandlingFee();

		if ( $handlingAction == self::HANDLING_TYPE_PER_SHIPMENT ) {
			$packages = 1;
		}

		if ( self::HANDLING_TYPE_FIXED == $handlingType ) {
			//fixed
			$result = $value + ($handlingFee * $packages);
		} else {
			//percent
			$result = $value * ( 100 + $handlingFee ) / 100;
		}

		return $result;

	}

	protected function _getRate( $title, $method, $price, $deliveryPointServices = [], $taxAndDutiesData = null) {
		$price = $this->shippingHelper->round( $price );

		return array( 'label' => $title, 'method' => $method, 'price' => $price, 'delivery_point_services' => $deliveryPointServices, 'tax_and_duties_data' => $taxAndDutiesData);
	}

	public function isTrackingAvailable() {
		return true;
	}

	// TODO : Check if it's better to get items prices from the cart and not from inventory (to avoid free gift to be added to the estimate)
	protected function getSubtotalForInsurance( $item ) {
		if ( $this->shippingHelper->getShipmentsInsuranceMode() == Mbe_Shipping_Helper_Data::MBE_INSURANCE_WITH_TAXES ) {
			if ( version_compare( WC()->version, '3', '>=' ) ) {
				$insuranceValue = wc_get_price_including_tax( $item["data"], array( 'qty' => $item['quantity'] ) );
			} else {
				$insuranceValue = $item["data"]->get_price_including_tax() * $item['quantity'];
			}
		} else {
			if ( version_compare( WC()->version, '3', '>=' ) ) {
				$insuranceValue = wc_get_price_excluding_tax( $item["data"], array( 'qty' => $item['quantity'] ) );
			} else {
				$insuranceValue = $item["data"]->get_price_excluding_tax() * $item['quantity'];
			}
		}

		return $insuranceValue;
	}


	/**
	 * @param $boxes
	 * If shipping mode is SHIPMENT_CONFIGURATION_MODE_ONE_SHIPMENT_PER_ITEM $boxes is the last one used for getRates, it's always a "settings" box as CSV it's not used for this mode,
	 * so it must be used for dimensions check only since it contains only 1 item
	 * In the other cases it's the proper list of boxes for the shipment
	 * @param $items
	 *
	 * @return bool
	 */
	private function isDeliveryPointEnabled( $boxes, $items ) {
		$oneParcel = (
			( $this->shippingHelper->getShipmentConfigurationMode() == self::SHIPMENT_CONFIGURATION_MODE_ONE_SHIPMENT_PER_ITEM ) ||
			( $this->shippingHelper->countBoxesArray( $boxes ) === 1 &&
			  in_array( $this->shippingHelper->getShipmentConfigurationMode(), [
				  self::SHIPMENT_CONFIGURATION_MODE_ONE_SHIPMENT_PER_SHOPPING_CART_WEIGHT_MULTI_PARCEL,
				  self::SHIPMENT_CONFIGURATION_MODE_ONE_SHIPMENT_PER_SHOPPING_CART_ITEMS_MULTI_PARCEL
			  ] )
			)
		);

		// Check longest size of the last box used for getRates. This must be modified if CSV will be enabled for SHIPMENT_CONFIGURATION_MODE_ONE_SHIPMENT_PER_ITEM shipping mode
		// Nested if to avoid useless check and operations
		if ( $oneParcel ) {
//			$longestSize = $this->shippingHelper->longestSizeBoxesArray( $boxes );
//			$box         = $boxes[ $this->shippingHelper->arrayKeyFirst( $boxes ) ]; // since it's one parcel we can check only the first element of the array
			//	Longest Size Ok and Total Size Ok
//			if ( $longestSize <= MBE_UAP_LONGEST_LIMIT_97_CM
//			     && ( $longestSize + ( 2 * $box['dimensions']['width'] ) + ( 2 * $box['dimensions']['height'] ) ) <= MBE_UAP_TOTAL_SIZE_LIMIT_300_CM ) {

			$weightOk = true;
			if ( $this->shippingHelper->getShipmentConfigurationMode() == Mbe_Shipping_Model_Carrier::SHIPMENT_CONFIGURATION_MODE_ONE_SHIPMENT_PER_ITEM ) {
				foreach ( $items as $item ) {
					if ( $this->shippingHelper->convertWeight( $item['data']->get_weight() ) > MBE_DELIVERY_POINT_WEIGHT_LIMIT ) {
						$weightOk = false;
						break;
					}
				}
			} else {
				$weightOk = $this->shippingHelper->convertWeight( $this->shippingHelper->totalWeightBoxesArray( $boxes ) ) <= MBE_DELIVERY_POINT_WEIGHT_LIMIT;
			}
			if ( $weightOk ) {
				// All the checks are OK
				return true;
			}
//			}
		}

		return false;
	}

	protected function getProductsFromCart($cart, $single = false) {
		$products       = array();

		foreach ( $cart as $item ) {
			$product        = $this->shippingHelper->getProductFromItem( $item );

			$p              = new stdClass;
			$p->SKUCode     = $product->get_sku();
			$p->Description = $this->shippingHelper->getProductTitleFromItem( $item );
			$p->Quantity    = ($single?1:$item['quantity']);
			$p->Price       = $product->get_price();
			$p->Currency    = get_woocommerce_currency();

			$products[] = $p;
		}
		return $products;
	}

}