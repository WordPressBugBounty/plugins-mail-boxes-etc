<?php

class Mbe_Shipping_Model_Ws
{
    private $helper;
    protected $logger = null;
    private $ws;
    protected $wsUrl;
    protected $wsUsername;
    protected $wsPassword;
    protected $system;
	protected $customer;

    public function __construct()
    {
        $this->helper = new Mbe_Shipping_Helper_Data();
        $this->logger = new Mbe_Shipping_Helper_Logger();

	    $debug = $this->helper->debug();
	    $this->ws = new MbeWs($debug);
	    $this->wsUrl = $this->helper->getWsUrl();
	    $this->wsUsername = $this->helper->getWsUsername();
	    $this->wsPassword = $this->helper->getWsPassword();
	    $this->system = $this->helper->getCountry();
	    $this->customer = $this->getCustomer();

//	    if ($debug) {
//		    $this->helper->checkDir($this->helper->getMbeLogDir());
//	    }
    }

    public function getCustomer()
    {
	    $cacheKey = md5($this->wsUrl . $this->wsUsername . $this->wsPassword . $this->system);
	    if (get_transient($cacheKey) !== false) {
		    return get_transient($cacheKey);
	    }
	    return $this->cacheCustomerData();
    }

    public function getCustomerPermission($permissionName)
    {
        $result = null;
        if ($this->customer) {
            $result = $this->customer->Permissions->$permissionName??null;
        }

        return $result;
    }

    public function getAvailableOptions()
    {
        $result = false;
        if ($this->wsUrl && $this->wsUsername && $this->wsPassword) {
            $result = $this->ws->getAvailableOptions($this->wsUrl, $this->wsUsername, $this->wsPassword);
        }

        return $result;
    }

    public function getAllowedShipmentServices($customer)
    {


        $result = array();
        if ($this->wsUrl && $this->wsUsername && $this->wsPassword) {
            //$customer = $this->ws->getCustomer($this->wsUrl, $this->wsUsername, $this->wsPassword, $this->system);
            if ($customer && $customer->Enabled) {
                if (isset($customer->Permissions->enabledServices)) {
                    $enabledServices = $customer->Permissions->enabledServices;
                    $enabledServicesDesc = $customer->Permissions->enabledServicesDesc;

                    $enabledServicesArray = explode(",", $enabledServices);
                    $enabledServicesDescArray = explode(",", $enabledServicesDesc);

                    for ($i = 0; $i < count($enabledServicesArray); $i++) {
                        $service = $enabledServicesArray[$i];
                        $serviceDesc = $enabledServicesDescArray[$i];


                        $serviceDesc .= ' (' . $service . ')';

                        $currentShippingType = array(
                            'value' => $service,
                            'label' => $serviceDesc,
                        );

                        if (!in_array($currentShippingType, $result)) {
                            array_push($result, $currentShippingType);
                        }

                        //SHIPPING WITH INSURANCE
                        if (isset($customer->Permissions->canSpecifyInsurance) && $customer->Permissions->canSpecifyInsurance) {
                            $currentShippingWithInsuranceType = array(
                                'value' => $this->helper->convertShippingCodeWithInsurance($service),
                                'label' => $this->helper->convertShippingLabelWithInsurance($serviceDesc),
                            );
                            if (!in_array($currentShippingWithInsuranceType, $result)) {
                                array_push($result, $currentShippingWithInsuranceType);
                            }
                        }

                    }
                }
            }
        }
        return $result;
    }

    public function getLabelFromShipmentType($shipmentCode)
    {
        $result = $shipmentCode;
        $allowedShipmentServices = $this->getAllowedShipmentServices($this->customer);
        foreach ($allowedShipmentServices as $allowedShipmentService) {
            if ($allowedShipmentService["value"] == $shipmentCode) {
                $result = $allowedShipmentService["label"];
                break;
            }
        }
        return $result;
    }

    private function convertInsuranceShipping($shippingList)
    {
        $result = false;
        if ($shippingList) {
            $newShippingList = array();
            foreach ($shippingList as $shipping) {

                if ($shipping->InsuranceAvailable) {
                    $newShipping = $shipping;
                    $newShipping->Service = $this->helper->convertShippingCodeWithInsurance($newShipping->Service);
                    $newShipping->ServiceDesc = $this->helper->convertShippingLabelWithInsurance($newShipping->ServiceDesc);
                    array_push($newShippingList, $newShipping);
                }
            }
            if (!empty($newShippingList)) {
                $result = $newShippingList;
            }
        }
        return $result;
    }

	public function estimateShipping($country, $region, $city, $postCode, $weight, $boxes, $insuranceValue, $products)
	{
		$this->logger->log('ESTIMATESHIPPING');
//        $weight = $this->helper->convertWeight($weight);

		$wsUrl = $this->helper->getWsUrl();
		$wsUsername = $this->helper->getWsUsername();
		$wsPassword = $this->helper->getWsPassword();
		$system = $this->helper->getCountry();

		$result = [];

		if ($wsUrl && $wsUsername && $wsPassword) {
			$items = $this->setItems($weight);

			$shipmentType = $this->helper->getDefaultShipmentType();

			$this->logger->logVar($items, 'ESTIMATESHIPPING ITEMS');

			//Shipping without insurance
			$resultWithoutInsurance = $this->ws->estimateShipping(
				$wsUrl, $wsUsername, $wsPassword, $shipmentType, $system, $country, $region, $city, $postCode, $items, $products, false, $insuranceValue
			);

			//Shipping with insurance
			$resultWithInsurance = $this->ws->estimateShipping(
				$wsUrl, $wsUsername, $wsPassword, $shipmentType, $system, $country, $region, $city, $postCode, $items, $products, true, $insuranceValue
			);
			$resultWithInsurance = $this->convertInsuranceShipping($resultWithInsurance);

			if ($resultWithInsurance && $resultWithoutInsurance) {
				$result = array_merge($resultWithInsurance, $resultWithoutInsurance);
			}
			else {
				if ($resultWithInsurance) {
					$result = $resultWithInsurance;
				}
				if ($resultWithoutInsurance) {
					$result = $resultWithoutInsurance;
				}
			}

            // Remove multiple MOL delivery point services
			foreach ( $result as $key=>$value ) {
				if(in_array($value->Service, MBE_ESTIMATE_DELIVERY_POINT_SERVICES) ){
					unset($result[$key]);
				}
			}
			// Add common delivery point service
			if(is_array($resultWithoutInsurance)) {
				$result[] = $this->helper->getDeliveryPointServices($resultWithoutInsurance);
				$result = array_values(array_filter($result)); //Clean empty array items and reindex (remove null deliverypointservices)
			}
		}

		if (empty($result)) {$result = false;} // to keep result retro-compatible
		return $result;
	}

	/**
	 * @throws \MbeExceptions\ApiRequestException
	 */
	public function createShipping($country, $region, $postCode, $weight, $boxes, $products, $service, $subzone, $notes, $firstName, $lastName, $companyName, $address, $phone, $city, $email, $goodsValue = 0.0, $reference = "", $isCod = false, $codValue = 0.0, $insurance = false, $insuranceValue = 0.0, $isPickup = false, $senderInfo = [], $pickupData = [])
	{
//		$weight = $this->helper->convertWeight($weight, 'kg');
		$this->logger->log('CREATE SHIPPING');
		$this->logger->logVar($goodsValue, 'GOODS VALUE');
		$this->logger->logVar(func_get_args(), 'CREATE SHIPPING ARGS');

		$wsUrl = $this->helper->getWsUrl();
		$wsUsername = $this->helper->getWsUsername();
		$wsPassword = $this->helper->getWsPassword();
		$system = $this->helper->getCountry();
		$result = false;
		if ($wsUrl && $wsUsername && $wsPassword) {
			$items = $this->setItems($weight);

			$shipmentType = $this->helper->getDefaultShipmentType();

			$this->logger->logVar($items, 'CREATE SHIPPING ITEMS');

			$shipperType = $this->getShipperType();
			if ($isPickup) {
				$result = $this->ws->createPickupShipping(
					$wsUrl, $wsUsername, $wsPassword, $shipmentType, $service, $subzone, $system, $notes, $firstName, $lastName, $companyName,
					$address, $phone, $city, $region, $country, $postCode, $email, $items, $products, $shipperType, $goodsValue, $reference,
					$isCod, $codValue, $insurance, $insuranceValue,	$senderInfo, $pickupData
				);
			} else {
				$result = $this->ws->createShipping(
					$wsUrl, $wsUsername, $wsPassword, $shipmentType, $service, $subzone, $system, $notes, $firstName, $lastName, $companyName,
					$address, $phone, $city, $region, $country, $postCode, $email, $items, $products, $shipperType, $goodsValue, $reference,
					$isCod, $codValue, $insurance, $insuranceValue
				);
			}
		}
		return $result;
	}
    public function getShipperType()
    {
        $shipperType = "MBE";
        $canCreateCourierWaybill= $this->helper->getCanCreateCourierWaybill()??0;

        if ($canCreateCourierWaybill) {
            $shipperType = "COURIERLDV";
        }

        return $shipperType;
    }

    public function mustCloseShipments()
    {
	    $canCreateCourierWaybill= $this->helper->getCanCreateCourierWaybill()??false;

	    return !$canCreateCourierWaybill;
    }

    public function getCustomerMaxParcelWeight()
    {
        return $this->customer->Permissions->maxParcelWeight;
    }

    public function getCustomerMaxShipmentWeight()
    {
        return $this->customer->Permissions->maxShipmentWeight;
    }

    public function isCustomerActive()
    {
        $result = false;
        if ($this->customer) {
            $result = $this->customer->Enabled;
        }
        return $result;
    }

    public function closeShipping(array $shipmentIds)
    {
        $trackingNumbers = array();
        foreach ($shipmentIds as $shipmentId) {
            $tracks = $this->helper->getTrackings($shipmentId);
            foreach ($tracks as $track) {
                array_push($trackingNumbers, $track);
            }
        }
        $this->closeTrackingNumbers($trackingNumbers);

    }

    public function closeTrackingNumbers(array $trackingNumbers)
    {
        $this->logger->log('CLOSE SHIPPING');



        $result = false;

        if ($this->wsUrl && $this->wsUsername && $this->wsPassword) {
            $result = $this->ws->closeShipping($this->wsUrl, $this->wsUsername, $this->wsPassword, $this->system, $trackingNumbers);

            if ($result) {
                foreach ($trackingNumbers as $trackingNumber) {
                    $filePath = $this->helper->getTrackingFilePath($trackingNumber);
                    file_put_contents($filePath, $result->Pdf);
                }
            }
        }

        return $result;
    }

	public function  cacheCustomerData()
	{
		$result = false;
		if ( $this->wsUrl && $this->wsUsername && $this->wsPassword ) {
			$result = $this->ws->getCustomer( $this->wsUrl, $this->wsUsername, $this->wsPassword, $this->system );
			if ( $result ) {
				set_transient(
					md5( $this->wsUrl . $this->wsUsername . $this->wsPassword . $this->system ),
					$result,
					12 * HOUR_IN_SECONDS
				);
			}
			$this->logger->logVar( $result, 'WS getCustomer' );
		}

		return $result;
	}

	public function setItems($boxesWeight)
	{
		$items = [];
		foreach ($boxesWeight as $box) {
			foreach ($box['weight'] as $weight) {
				$item = new \stdClass;
				$item->Weight = $this->helper->convertWeight($weight);
				$item->Dimensions = new \stdClass;
				$item->Dimensions->Lenght = $box['dimensions']['length'];
				$item->Dimensions->Height = $box['dimensions']['height'];
				$item->Dimensions->Width = $box['dimensions']['width'];
				$items[] = $item;
			}
		}
		return $items;
	}

	public function returnShipping($toReturnIds)
	{
		$result = [];
		foreach ($toReturnIds as $toReturnId) {
			$tracks = $this->helper->getTrackings($toReturnId);
			if (!empty($tracks)) {
//  			foreach ($tracks as $track) {
//	    			array_push($trackingNumbers, $track);
//		    	}
//      		return $this->returnTrackingNumbers($trackingNumbers);
	            $result[$toReturnId] = $this->returnTrackingNumbers([$tracks[0]]); // TODO : only manage the 1st order's shipment for now
			}
		}
		return $result;
	}

	public function returnTrackingNumbers(array $trackingNumbers)
	{
		$this->logger->logVar( $trackingNumbers, 'RETURN SHIPPING ');

		$returnTracking = [];

		if ($this->wsUrl && $this->wsUsername && $this->wsPassword) {
			foreach ( $trackingNumbers as $trackingNumber ) {
				$result = $this->ws->returnShipping($this->wsUrl, $this->wsUsername, $this->wsPassword, $this->system, $trackingNumber);
				if (!empty($result)) {
					$returnTracking[$trackingNumber] = $result->MasterTrackingMBE;
				} else {
					// error on creation of the return shipment
					$returnTracking[$trackingNumber] = false;
				}
			}
		}
		return $returnTracking;
	}

	public function getPickupDefaultData() {
		return $this->ws->getPickupDefaultData($this->wsUrl, $this->wsUsername, $this->wsPassword, $this->system);
	}

	public function setPickupDefaultData(array $data) {
		return $this->ws->setPickupDefaultData($this->wsUrl, $this->wsUsername, $this->wsPassword, $this->system, $data);
	}

	public function getPickupAddressesData() {
		return $this->ws->getCustomerPickupAddresses($this->wsUrl, $this->wsUsername, $this->wsPassword, $this->system);
	}

	/**
	 * @throws \MbeExceptions\ApiRequestException
	 */
	public function addPickupAddressData(array $address) {
		return $this->ws->createPickupAddress($this->wsUrl, $this->wsUsername, $this->wsPassword, $this->system, $address);
	}

	/**
	 * @throws \MbeExceptions\ApiRequestException
	 */
	public function updatePickupAddressData(array $address) {
		return $this->ws->updatePickupAddress($this->wsUrl, $this->wsUsername, $this->wsPassword, $this->system, $address);
	}

	public function deletePickupAddressData($addressId) {
		return $this->ws->deletePickupAddress($this->wsUrl, $this->wsUsername, $this->wsPassword, $this->system, $addressId);
	}

	public function getPickupManifest( $masterTrackingId ) {
		return $this->ws->getPickupManifest($this->wsUrl, $this->wsUsername, $this->wsPassword, $this->system, $masterTrackingId);
	}

	public function getDefaultPickupAddress() {
		return $this->ws->getDefaultPickupAddress($this->wsUrl, $this->wsUsername, $this->wsPassword, $this->system);
	}

	public function getPickupAddressById($id) {
		return $this->ws->getPickupAddressById($this->wsUrl, $this->wsUsername, $this->wsPassword, $this->system, $id);
	}

	/**
	 * @throws \MbeExceptions\ApiRequestException
	 */
	public function closePickupShippping($pickupBatchid, $pickupBatchData) {
		return $this->ws->closePickupShippping($this->wsUrl, $this->wsUsername, $this->wsPassword, $this->system, $pickupBatchid, $pickupBatchData);
	}

	/**
	 * @throws \MbeExceptions\ShippingDocumentException
	 */
	public function getDeliveryPointShippingDocument( $masterTrackingId ) {
		return $this->ws->getShippingDocument($this->wsUrl, $this->wsUsername, $this->wsPassword, $this->system, $masterTrackingId);
	}

}