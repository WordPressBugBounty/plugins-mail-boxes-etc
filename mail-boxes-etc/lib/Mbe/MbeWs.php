<?php

use MbeExceptions\HttpRequestException as HttpRequestException;
use MbeExceptions\ShippingDocumentException;

class MbeWs {
	private $_log;
	protected $helper;
	protected $pluginVersionMessage;
	protected $wsUrl;
	protected $apiToken;
	protected $apiCustomer;

	private const EXPECTED_STATUS = "OK";

	public function __construct( $log = false ) {
		$this->_log                 = $log;
		$this->helper               = new Mbe_Shipping_Helper_Data();
		$this->pluginVersionMessage = MBE_ESHIP_PLUGIN_NAME . ' version ' . MBE_ESHIP_PLUGIN_VERSION . ' :';
		$this->wsUrl       = $this->helper->getWsUrl();
		$this->apiToken    = null;
		$this->apiCustomer = null;
	}

    private function log($message)
    {
        if ($this->_log) {
            $row = date_format(new DateTime(), 'Y-m-d\TH:i:s\Z');
            $row .= " - ";
            $row .= $this->pluginVersionMessage . $message . "\n\r";
            file_put_contents($this->helper->getLogWsPath(), $row, FILE_APPEND);
        }
    }

    public function logVar($var, $message = null)
    {
        if ($this->_log) {
            if ($message) {
                $this->log($message);
            }
            $this->log(print_r($var, true));
        }
    }

    function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function getCustomer($ws, $username, $password, $system)
    {
        $this->log('GET CUSTOMER');
        $result = false;

        try {
            $soapClient = new MbeSoapClient($ws, array('encoding' => 'utf-8', 'trace' => 1), $username, $password, false);
            $internalReferenceID = $this->generateRandomString();

            //WS ARGS
            $args = new stdClass;
            $args->RequestContainer = new stdClass;

            $args->RequestContainer->Action = "GET";

            $args->RequestContainer->SystemType = $system;

            $args->RequestContainer->Customer = new stdClass;
            $args->RequestContainer->Customer->Login = "";

            $args->RequestContainer->Credentials = new stdClass;
            $args->RequestContainer->Credentials->Username = $username;
            $args->RequestContainer->Credentials->Passphrase = $password;

            $args->RequestContainer->InternalReferenceID = $internalReferenceID;

            $args->RequestContainer->Action = "GET";

			$logArgs = json_decode(json_encode($args), true);
			$logArgs['RequestContainer']['Credentials'] = null;

            $this->logVar($logArgs, 'GET CUSTOMER ARGS');
            $soapResult = $soapClient->__soapCall("ManageCustomerRequest", array($args));

            $lastResponse = $soapClient->__getLastResponse();
            $this->logVar($lastResponse, 'GET CUSTOMER RESPONSE');

            if (isset($soapResult->RequestContainer->Errors)) {
                $this->logVar($soapResult->RequestContainer->Errors, 'GET CUSTOMER ERRORS');
            }

            if (isset($soapResult->RequestContainer->Status) && $soapResult->RequestContainer->Status == "OK") {
                //if (isset($soapResult->RequestContainer->InternalReferenceID) && $soapResult->RequestContainer->InternalReferenceID == $internalReferenceID) {
                $result = $soapResult->RequestContainer->Customer;
                //}
            }
        }
        catch (Exception $e) {
            $this->log('GET CUSTOMER EXCEPTION');
            $this->log($e->getMessage());
        }
        $this->logVar($result, 'GET CUSTOMER RESULT');
        return $result;
    }

    public function estimateShipping(
		$ws, $username, $password, $shipmentType, $system, $country, $region, $city, $postCode, $items, $products, $insurance = false, $insuranceValue = 0.00
    )
    {
		$messageTitle = 'ESTIMATE SHIPPING';
        $this->log($messageTitle);
        $result = false;

        try {
            $soapClient = new MbeSoapClient($ws, array('encoding' => 'utf-8', 'trace' => 1), $username, $password);
            $internalReferenceID = $this->generateRandomString();

            //WS ARGS
	        $args = $this->setBaseClass( $system, $username, $password );

	        $args->RequestContainer->InternalReferenceID = $internalReferenceID;


            $args->RequestContainer->ShippingParameters = new stdClass;

            $args->RequestContainer->ShippingParameters->DestinationInfo = new stdClass;

            $args->RequestContainer->ShippingParameters->DestinationInfo->ZipCode = $postCode;
            $args->RequestContainer->ShippingParameters->DestinationInfo->City = $city;
            $args->RequestContainer->ShippingParameters->DestinationInfo->State = $region;
            $args->RequestContainer->ShippingParameters->DestinationInfo->Country = $country;
            //$args->RequestContainer->ShippingParameters->DestinationInfo->idSubzone = "";

            $args->RequestContainer->ShippingParameters->ShipType = "EXPORT";

            $args->RequestContainer->ShippingParameters->PackageType = $shipmentType;

            $args->RequestContainer->ShippingParameters->Items = $items;
//	        $itemsa = new stdClass();
//	        $itemsa->Item = $items[0];
//	        $args->RequestContainer->ShippingParameters->Items = $itemsa;

            $args->RequestContainer->ShippingParameters->Insurance = $insurance;

			if ($insurance) {
                $args->RequestContainer->ShippingParameters->InsuranceValue = $insuranceValue;
            }

			if($this->helper->getPickupRequestEnabled()
			   && $this->helper->getShipmentsCreationMode() === Mbe_Shipping_Helper_Data::MBE_CREATION_MODE_AUTOMATICALLY
			   && $this->helper->getPickupRequestMode() === Mbe_Shipping_Helper_Data::MBE_PICKUP_REQUEST_AUTOMATIC
			) {
		        $defaultPickupAddress = $this->getDefaultPickupAddress( $ws, $username, $password, $system );

				if (empty($defaultPickupAddress)) {
					throw new Exception('Default pickup address is missing');
				}

				$args->RequestContainer->ShippingParameters->SenderInfo = new stdClass();
				$args->RequestContainer->ShippingParameters->SenderInfo->ZipCode = $defaultPickupAddress[0]['ZipCode'];
				$args->RequestContainer->ShippingParameters->SenderInfo->City = $defaultPickupAddress[0]['City'];
				$args->RequestContainer->ShippingParameters->SenderInfo->State = $defaultPickupAddress[0]['Province'];
				$args->RequestContainer->ShippingParameters->SenderInfo->Country = $defaultPickupAddress[0]['Country'];
			}

			if($this->helper->isEnabledTaxAndDuties()) {
				$args->RequestContainer->ShippingParameters->LanguageCode = $this->helper->getCountry();
				$args->RequestContainer->ShippingParameters->TaxAndDutyPluginAct = true;
				$args->RequestContainer->ShippingParameters->ProformaIncoterms = $this->helper->getTaxAndDutiesModeName();
				$args->RequestContainer->ShippingParameters->ProformaInvoice = $this->generateProforma($products);
			}

	        $soapResult = $soapClient->__soapCall("ShippingOptionsRequest", array($args));

	        $logArgs = json_decode(json_encode($args));
	        $logArgs->RequestContainer->Credentials = null;

	        $this->logVar($logArgs, $messageTitle . ' ARGS');
	        $this->logVar($soapClient->__getLastRequest(), $messageTitle . ' XML REQUEST');

            $lastResponse = $soapClient->__getLastResponse();
            $this->logVar($lastResponse, $messageTitle . ' XML RESPONSE');

            if (isset($soapResult->RequestContainer->Errors)) {
                $this->logVar($soapResult->RequestContainer->Errors, $messageTitle . ' ERRORS');
            }

            if (isset($soapResult->RequestContainer->Status) && $soapResult->RequestContainer->Status == "OK") {
                if (isset($soapResult->RequestContainer->InternalReferenceID) && $soapResult->RequestContainer->InternalReferenceID == $internalReferenceID) {
                    if (isset($soapResult->RequestContainer->ShippingOptions->ShippingOption)) {
                        if (is_array($soapResult->RequestContainer->ShippingOptions->ShippingOption)) {
                            $result = $soapResult->RequestContainer->ShippingOptions->ShippingOption;
                        }
                        else {
                            $result = array($soapResult->RequestContainer->ShippingOptions->ShippingOption);
                        }
                    }
                }
            }

        }
        catch (Exception $e) {
            $this->log($messageTitle . ' EXCEPTION');
            $this->log($e->getMessage());
        }
        $this->logVar($result, $messageTitle . ' RESULT');
        return $result;
    }

	/**
	 * @throws \MbeExceptions\ApiRequestException
	 */
	public function createShipping(
		$ws, $username, $password, $shipmentType, $service, $subZone, $system,
		$notes, $firstName, $lastName, $companyName, $address, $phone, $city,
		$state, $country, $postCode, $email, $items, $products, $shipperType = 'MBE',
		$goodsValue = 0.0, $reference = "", $isCod = false, $codValue = 0.0,
		$insurance = false, $insuranceValue = 0.0
    )
    {
	    $messageTitle  = 'CREATE SHIPPING';
//        $this->log($messageTitle);
//        $this->logVar(func_get_args(), $messageTitle);

        $result = false;


        try {
            $soapClient = new MbeSoapClient($ws, array('encoding' => 'utf-8', 'trace' => 1), $username, $password);
            $internalReferenceID = $this->generateRandomString();

            //WS ARGS
	        $args = $this->setShipmentContainer( $system, $username, $password, $internalReferenceID, $firstName, $lastName, $companyName, $address, $phone, $postCode, $city, $state, $country, $email, $subZone, $shipperType, $isCod, $codValue, $insurance, $insuranceValue, $service, $shipmentType, $reference, $items, $products, $goodsValue, $notes );

			$logArgs = json_decode(json_encode($args), true);
	        $logArgs['RequestContainer']['Credentials'] = null;

	        $this->logVar($logArgs, $messageTitle . ' ARGS');


            $soapResult = $soapClient->__soapCall("ShipmentRequest", array($args));


            $lastResponse = $soapClient->__getLastResponse();
            $this->logVar($lastResponse, $messageTitle . ' RESPONSE');

            if (isset($soapResult->RequestContainer->Errors)) {
                $this->logVar($soapResult->RequestContainer->Errors, $messageTitle . ' ERRORS');
            }
            if (isset($soapResult->RequestContainer->Status) && $soapResult->RequestContainer->Status == "OK") {
                if (isset($soapResult->RequestContainer->InternalReferenceID) && $soapResult->RequestContainer->InternalReferenceID == $internalReferenceID) {
                    $result = $soapResult->RequestContainer;
                }
            }

        }
        catch (Exception $e) {
            $this->log($messageTitle . ' EXCEPTION');
            $this->log($e->getMessage());
        }
        $this->logVar($result, $messageTitle . ' RESULT');
        return $result;
    }

    public function closeShipping($ws, $username, $password, $system, $trackings)
    {
        $this->log('CLOSE SHIPPING');


        $result = false;


        try {
            $soapClient = new MbeSoapClient($ws, array('encoding' => 'utf-8', 'trace' => 1), $username, $password);
            $internalReferenceID = $this->generateRandomString();

            //WS ARGS
            $args = new stdClass;
            $args->RequestContainer = new stdClass;
            $args->RequestContainer->SystemType = $system;

            $args->RequestContainer->Credentials = new stdClass;
            $args->RequestContainer->Credentials->Username = $username;
            $args->RequestContainer->Credentials->Passphrase = $password;

            $args->RequestContainer->InternalReferenceID = $internalReferenceID;

            $masterTrackingsMBE = array();
            foreach ($trackings as $track) {
                array_push($masterTrackingsMBE, $track);
            }


            $args->RequestContainer->MasterTrackingsMBE = $masterTrackingsMBE;

	        $logArgs = json_decode(json_encode($args), true);
	        $logArgs['RequestContainer']['Credentials'] = null;

            $this->logVar($logArgs, 'CLOSE SHIPPING ARGS');


            $soapResult = $soapClient->__soapCall("CloseShipmentsRequest", array($args));

            $lastResponse = $soapClient->__getLastResponse();

            $this->logVar($lastResponse, 'CLOSE SHIPPING RESPONSE');

            if (isset($soapResult->RequestContainer->Errors)) {
                $this->logVar($soapResult->RequestContainer->Errors, 'CLOSE SHIPPING ERRORS');
            }

            if (isset($soapResult->RequestContainer->Status) && $soapResult->RequestContainer->Status == "OK") {
                $result = $soapResult->RequestContainer;
            }

        }
        catch (Exception $e) {
            $this->log('CLOSE SHIPPING EXCEPTION');
            $this->log($e->getMessage());
        }
        $this->logVar($result, 'CLOSE SHIPPING RESULT');
        return $result;
    }

	public function returnShipping($ws, $username, $password, $system, $tracking)
	{
		$this->log('RETURN SHIPPING - ' . $tracking);

		$result = false;

		try {
			$soapClient = new MbeSoapClient($ws, array('encoding' => 'utf-8', 'trace' => 1), $username, $password, false);
			$internalReferenceID = 'RETURN-SHIPPING-'.$tracking;

			//WS ARGS
			$args = $this->setBaseClass( $system, $username, $password );

			$args->RequestContainer->InternalReferenceID = $internalReferenceID;

			$args->RequestContainer->MbeTracking = $tracking;
			$args->RequestContainer->CustomerAsReceiver = true;
			$args->RequestContainer->ShipmentOrigin = MBE_ESHIP_PLUGIN_NAME . " WooCommerce " . MBE_ESHIP_PLUGIN_VERSION;
			$args->RequestContainer->Referring = '';

			$logArgs = json_decode(json_encode($args), true);
			$logArgs['RequestContainer']['Credentials'] = null;

			$this->logVar($logArgs, 'RETURN SHIPPING ARGS');

			$soapResult = $soapClient->__soapCall("ShipmentReturnRequest", array($args));

			$lastResponse = $soapClient->__getLastResponse();

			$this->logVar($lastResponse, 'RETURN SHIPPING RESPONSE');

			if (isset($soapResult->RequestContainer->Errors)) {
				$this->logVar($soapResult->RequestContainer->Errors, 'RETURN SHIPPING ERRORS');
			}

			if (isset($soapResult->RequestContainer->Status) && $soapResult->RequestContainer->Status == "OK") {
				$result = $soapResult->RequestContainer;
			}
		}
		catch (Exception $e) {
			$this->log('RETURN SHIPPING EXCEPTION');
			$this->log($e->getMessage());
		}
		$this->logVar($result, 'RETURN SHIPPING RESULT');
		return $result;
	}


	/**
	 * @throws Exception | ShippingDocumentException
	 */
	public function getShippingDocument($ws, $username, $password, $system, $tracking) {
		$messageTitle = 'GET SHIPPING DOCUMENT';
		$result = false;

		try {
			$soapClient = new MbeSoapClient( $ws, array(
				'encoding' => 'utf-8',
				'trace'    => 1
			), $username, $password );
			$internalReferenceID = $this->generateRandomString();

			//WS ARGS
			$args = $this->setBaseClass( $system, $username, $password );
			$args->RequestContainer->InternalReferenceID = $internalReferenceID;

			$args->RequestContainer->TrackingMBE = $tracking;
			$args->RequestContainer->CourierWaybill = true;

			$logArgs = json_decode(json_encode($args), true);
			$logArgs['RequestContainer']['Credentials'] = null;

			$this->logVar($logArgs, $messageTitle. ' ARGS');

			$soapResult = $soapClient->__soapCall("shipmentDocumentsRequest", array($args));

			$lastResponse = $soapClient->__getLastResponse();
			$this->logVar($lastResponse, $messageTitle.' RESPONSE');

			$this->checkResponseErrors( $soapResult, $messageTitle );

			if ($this->isResponseValid($soapResult, $internalReferenceID)
			    && !empty($soapResult->RequestContainer->ShipmentDocuments->ShipmentDocument->CourierWaybill??null)
			) {
				$this->logVar($result, $messageTitle. ' RESULTS');
			} else {
				$this->logVar( $soapResult->RequestContainer, $messageTitle. ' ERROR');
//				throw new ShippingDocumentException($soapResult->RequestContainer->ShipmentDocuments->ShipmentDocument->Errors->Error->Description);
			}
			$result = $soapResult->RequestContainer->ShipmentDocuments->ShipmentDocument;

		} catch (Exception $e) {
			$this->log($messageTitle . 'EXCEPTION');
			$this->log($e->getMessage());
			if ( ShippingDocumentException::class === get_class($e)) {
				throw new ShippingDocumentException(esc_html($e->getMessage()));
			} else {
				throw $e;
			}
		}

		return $result;
	}

//	PICKUP REQUEST

	/**
	 * @throws \MbeExceptions\ApiRequestException
	 */
	public function createPickupShipping(
		$ws, $username, $password, $shipmentType, $service, $subZone, $system,
		$notes, $firstName, $lastName, $companyName, $address, $phone, $city,
		$state, $country, $postCode, $email, $items, $products, $shipperType = 'MBE',
		$goodsValue = 0.0, $reference = "", $isCod = false, $codValue = 0.0,
		$insurance = false, $insuranceValue = 0.0,
		$senderInfo = [],
		$pickupData = []
	) {
		$messageTitle = 'CREATE PICKUP SHIPPING';
//		$this->log($messageTitle);
//
//		$this->logVar(func_get_args(), $messageTitle);

		$result = false;


		try {
			$soapClient          = new MbeSoapClient($ws, array('encoding' => 'utf-8', 'trace' => 1), $username, $password);
			$internalReferenceID = $this->generateRandomString();
			$args                = $this->setShipmentContainer( $system, $username, $password, $internalReferenceID, $firstName, $lastName, $companyName, $address, $phone, $postCode, $city, $state, $country, $email, $subZone, $shipperType, $isCod, $codValue, $insurance, $insuranceValue, $service, $shipmentType, $reference, $items, $products, $goodsValue, $notes );

			if($this->helper->getPickupRequestEnabled() ) {
				switch ( $this->helper->getPickupRequestMode() ) {
					case Mbe_Shipping_Helper_Data::MBE_PICKUP_REQUEST_AUTOMATIC:
						$args->RequestContainer->Pickup                       = new stdClass();
						$args->RequestContainer->Pickup->MolDefaultPickupTime = true;
						break;
					case Mbe_Shipping_Helper_Data::MBE_PICKUP_REQUEST_MANUAL:

						if ( ! empty( $pickupData['pickup_batch_id'] ) ) { // If it's a pickup batch
							$args->RequestContainer->Pickup                       = new stdClass();
							$args->RequestContainer->Pickup->MolDefaultPickupTime = false;
							$args->RequestContainer->Pickup->PickupBatchId        = $pickupData['pickup_batch_id'];
							$args->RequestContainer->Pickup->PickupAddressId      = $pickupData['pickup_address_id'];

						} else { // If it's a single pickup
							if ( empty( $senderInfo ) ) {
								$message = 'Pickup request cannot be sent, sender information are missing';
								throw new Exception( __( $message ) );
							}
							if ( empty( $pickupData ) ) {
								$message = 'Pickup request cannot be sent, pickup data are missing';
								throw new Exception( __( $message ) );
							}
							$args->RequestContainer->Sender              = new stdClass();
							$args->RequestContainer->Sender->Name        = $senderInfo['company-name'];
							$args->RequestContainer->Sender->CompanyName = $senderInfo['company-name'];
							$args->RequestContainer->Sender->Address     = $senderInfo['address'];
							$args->RequestContainer->Sender->Phone       = $senderInfo['phone'];
							$args->RequestContainer->Sender->ZipCode     = $senderInfo['zipcode'];
							$args->RequestContainer->Sender->City        = $senderInfo['city'];
							$args->RequestContainer->Sender->State       = $senderInfo['state'];
							$args->RequestContainer->Sender->Country     = $senderInfo['country'];
							$args->RequestContainer->Sender->Email       = $senderInfo['email'];

							$args->RequestContainer->Pickup                              = new stdClass();
							$args->RequestContainer->Pickup->PickupData                  = new stdClass();
							$args->RequestContainer->Pickup->PickupData->Notes           = $pickupData['notes'] ?? '';
							$args->RequestContainer->Pickup->PickupData->Date            = $pickupData['date'];
							$args->RequestContainer->Pickup->PickupData->PreferredFrom   = $pickupData['preferred_from'];
							$args->RequestContainer->Pickup->PickupData->PreferredTo     = $pickupData['preferred_to'];
							$args->RequestContainer->Pickup->PickupData->AlternativeFrom = $pickupData['alternative_from'] ?? '';
							$args->RequestContainer->Pickup->PickupData->AlternativeTo   = $pickupData['alternative_to'] ?? '';
						}
						break;
					default:
						# code...
						break;
				}

			} else {
				$message = 'User is not enabled for third party pickup';
				$this->log($message);
				throw new Exception(__($message));
			}

			$logArgs = json_decode(json_encode($args), true);
			$logArgs['RequestContainer']['Credentials'] = null;

			$this->logVar($logArgs, $messageTitle . ' ARGS');

			$soapResult = $soapClient->__soapCall("ShipmentRequest", array($args));

			$lastResponse = $soapClient->__getLastResponse();
			$this->logVar($lastResponse, $messageTitle.' RESPONSE');

			$this->checkResponseErrors( $soapResult, $messageTitle );

			if ($this->isResponseValid($soapResult, $internalReferenceID)) {
			        $result = $soapResult->RequestContainer;
			}

		} catch (\MbeExceptions\ApiRequestException $e) {
			$this->log( $messageTitle . ' EXCEPTION' );
			$this->log( $e->getMessage() );
			throw $e;
		} catch (Exception $e) {
			$this->log($messageTitle . ' EXCEPTION');
			$this->log($e->getMessage());
		}
		$this->logVar($result, $messageTitle.' RESULT');
		return $result;
	}

	/**
	 * @throws \MbeExceptions\ApiRequestException
	 */
	public function closePickupShippping(
		$ws, $username, $password, $system, $pickupBatchID, $pickupData
	) {
		$messageTitle = 'CLOSE PICKUP SHIPPING ';
		$this->log($messageTitle);
		$internalReferenceID = $this->generateRandomString();

		$result = false;

		try {
			$soapClient = new MbeSoapClient( $ws, array(
				'encoding' => 'utf-8',
				'trace'    => 1
			), $username, $password, false );

			$args = new stdClass;
			$args->RequestContainer = new stdClass;
			$args->RequestContainer->System = $system;
			$args->RequestContainer->Credentials = new stdClass();
			$args->RequestContainer->Credentials->Username = $username;
			$args->RequestContainer->Credentials->Passphrase = $password;
			$args->RequestContainer->InternalReferenceID = $internalReferenceID;
			$args->RequestContainer->PickupBatchId = $pickupBatchID;
			$args->RequestContainer->PickupData = new stdClass();
			$args->RequestContainer->PickupData->Notes = $pickupData['notes']??'';
			$args->RequestContainer->PickupData->Date = $pickupData['date'];
			$args->RequestContainer->PickupData->PreferredFrom = $pickupData['preferred_from'];
			$args->RequestContainer->PickupData->PreferredTo = $pickupData['preferred_to'];
			$args->RequestContainer->PickupData->AlternativeFrom = $pickupData['alternative_from']??'';
			$args->RequestContainer->PickupData->AlternativeTo = $pickupData['alternative_to']??'';

			$response = $this->sendRequest( $args, "CourierPickupClosureRequest", $messageTitle, $soapClient );

			if ( isset( $soapResult->RequestContainer->Status ) && $soapResult->RequestContainer->Status == "OK" ) {
				$result = $soapResult->RequestContainer;
			}

			$this->checkResponseErrors( $soapResult, $messageTitle );

		} catch (\MbeExceptions\ApiRequestException $e) {
			$this->log( $messageTitle . ' EXCEPTION' );
			$this->log( $e->getMessage() );
			throw $e;
		} catch (Exception $e) {
			$this->log($messageTitle . ' EXCEPTION');
			$this->log($e->getMessage());
		}

		$this->logVar($result, $messageTitle . ' RESULT');
		return $result;
	}

	public function getCustomerPickupAddresses($ws, $username, $password, $system) {
		$messageTitle = 'GET CUSTOMER PICKUP ADDRESSES ';
		$this->log($messageTitle);
		$internalReferenceID = $this->generateRandomString();

		$result = false;

		try {
			$soapClient = new MbeSoapClient( $ws, array(
				'encoding' => 'utf-8',
				'trace'    => 1
			), $username, $password, false );

			$args = new stdClass;
			$args->RequestContainer = new stdClass;
			$args->RequestContainer->System = $system;
			$args->RequestContainer->Credentials = new stdClass();
			$args->RequestContainer->Credentials->Username = $username;
			$args->RequestContainer->Credentials->Passphrase = $password;

			$args->RequestContainer->InternalReferenceID = $internalReferenceID;
			$response = $this->sendRequest( $args, "GetPickupAddressesRequest", $messageTitle, $soapClient );

			$result = [];
			if (is_array($response->PickupAddress)) {
				$result = array_map(function ($address) {
					return get_object_vars($address->PickupContainer);
				}, $response->PickupAddress);
			} else {
				$result[] = get_object_vars($response->PickupAddress->PickupContainer);
			}

		} catch (Exception $e) {
			$this->log($messageTitle . ' EXCEPTION');
			$this->log($e->getMessage());
		}

		$this->logVar($result, $messageTitle . ' RESULT');
		return $result;
	}

	/**
	 * @throws \MbeExceptions\ApiRequestException
	 */
	public function createPickupAddress($ws, $username, $password, $system, $addressData, $update = false) {
		$messageTitle = ($update?'UPDATE':'CREATE') . ' PICKUP ADDRESS';
		$this->log($messageTitle);
		$internalReferenceID = $this->generateRandomString();

		$result = false;

		try {
			$soapClient = new MbeSoapClient( $ws, array(
				'encoding' => 'utf-8',
				'trace'    => 1
			), $username, $password, false );

			$args                                            = new stdClass;
			$args->RequestContainer                          = new stdClass;
			$args->RequestContainer->System                  = $system;
			$args->RequestContainer->Credentials             = new stdClass;
			$args->RequestContainer->Credentials->Username   = $username;
			$args->RequestContainer->Credentials->Passphrase = $password;

			$args->RequestContainer->InternalReferenceID = $internalReferenceID;

			$args->RequestContainer->PickupContainer = new stdClass();
			if ( $update && !array_key_exists( 'pickup_address_id', $addressData ) ) {
				throw new \MbeExceptions\ApiRequestException( __( 'ID field is missing. Cannot update the address in MBE Online' ) );
			}

			if ($update) {
				$args->RequestContainer->PickupContainer->PickupAddressId = $addressData['pickup_address_id'];
			}

			$args->RequestContainer->PickupContainer->TradeName = $addressData['trade_name'];
			$args->RequestContainer->PickupContainer->Address1  = $addressData['address_1'];
			$args->RequestContainer->PickupContainer->Address2  = $addressData['address_2'];
			$args->RequestContainer->PickupContainer->Address3  = $addressData['address_3'];
			$args->RequestContainer->PickupContainer->ZipCode   = $addressData['zip_code'];
			$args->RequestContainer->PickupContainer->City      = $addressData['city'];;
			$args->RequestContainer->PickupContainer->Province  = $addressData['province'];
			$args->RequestContainer->PickupContainer->Country   = strtoupper($addressData['country']);
			$args->RequestContainer->PickupContainer->Reference = $addressData['reference'];
			$args->RequestContainer->PickupContainer->Phone1    = $addressData['phone_1'];
			$args->RequestContainer->PickupContainer->Phone2    = $addressData['phone_2'];
			$args->RequestContainer->PickupContainer->Email1    = $addressData['email_1'];
			$args->RequestContainer->PickupContainer->Email2    = $addressData['email_2'];
			$args->RequestContainer->PickupContainer->Fax       = $addressData['fax'];
			$args->RequestContainer->PickupContainer->RES       = $addressData['res'];
			$args->RequestContainer->PickupContainer->MMR       = $addressData['mmr'];
			$args->RequestContainer->PickupContainer->LTZ       = $addressData['ltz'];
			$args->RequestContainer->PickupContainer->IsDefault = $addressData['is_default'];

			$result = $this->sendRequest( $args, "CreatePickupAddressRequest", $messageTitle, $soapClient );

		} catch (\MbeExceptions\ApiRequestException $e) {
			$this->log($messageTitle . ' EXCEPTION');
			$this->log($e->getMessage());
			throw $e;
		} catch (Exception $e) {
			$this->log($messageTitle . ' EXCEPTION');
			$this->log($e->getMessage());
		}

		$this->logVar($result, $messageTitle . ' RESULT');
		return $result;
	}

	/**
	 * @throws \MbeExceptions\ApiRequestException
	 */
	public function updatePickupAddress($ws, $username, $password, $system, $addressData) {
		return $this->createPickupAddress($ws, $username, $password, $system, $addressData, true);
	}

	public function deletePickupAddress($ws, $username, $password, $system, $addressId) {
		$messageTitle = 'DELETE PICKUP ADDRESS';
		$this->log($messageTitle);
		$internalReferenceID = $this->generateRandomString();

		$result = false;

		try {
			$soapClient = new MbeSoapClient( $ws, array(
				'encoding' => 'utf-8',
				'trace'    => 1
			), $username, $password, false );

			$args = $this->setBaseClass( $system, $username, $password );

			$args->RequestContainer->InternalReferenceID = $internalReferenceID;

			$args->RequestContainer->PickupAddressId = $addressId;

			$result = $this->sendRequest( $args, 'DeletePickupAddressRequest', $messageTitle, $soapClient );

		} catch (Exception $e) {
			$this->log($messageTitle . ' EXCEPTION');
			$this->log($e->getMessage());
		}

		$this->logVar($result, $messageTitle . ' RESULT');
		return $result;
	}

	public function setPickupDefaultData($ws, $username, $password, $system, $data) {

		$messageTitle = 'SET PICKUP DEFAULT DATA';
		$this->log($messageTitle);
		$internalReferenceID = $this->generateRandomString();

		$result = false;

		try {
			$soapClient = new MbeSoapClient( $ws, array(
				'encoding' => 'utf-8',
				'trace'    => 1
			), $username, $password, false );

			$args = $this->setBaseClass( $system, $username, $password );

			$args->RequestContainer->InternalReferenceID = $internalReferenceID;

			$args->RequestContainer->Cutoff          = $data['cutoff'];
			$args->RequestContainer->Notes           = $data['notes'];
			$args->RequestContainer->PreferredFrom   = $data['preferred_from'];
			$args->RequestContainer->PreferredTo     = $data['preferred_to'];
			$args->RequestContainer->AlternativeFrom = $data['alternative_from'];
			$args->RequestContainer->AlternativeTo   = $data['alternative_to'];

			$result = $this->sendRequest( $args, 'SetPickupDefaultDataRequest', $messageTitle, $soapClient );

		} catch (Exception $e) {
			$this->log($messageTitle . ' EXCEPTION');
			$this->log($e->getMessage());
		}

		$this->logVar($result, $messageTitle . ' RESULT');
		return $result;


	}

	public function getPickupDefaultData($ws, $username, $password, $system) {

		$messageTitle = 'GET PICKUP DEFAULT DATA';
		$this->log($messageTitle);
		$internalReferenceID = $this->generateRandomString();

		$result = false;

		try {
			$soapClient = new MbeSoapClient( $ws, array(
				'encoding' => 'utf-8',
				'trace'    => 1
			), $username, $password, false );

			$args = $this->setBaseClass( $system, $username, $password );

			$args->RequestContainer->InternalReferenceID = $internalReferenceID;

			$result = $this->sendRequest( $args, 'GetPickupDefaultDataRequest', $messageTitle, $soapClient );

		} catch (Exception $e) {
			$this->log($messageTitle . ' EXCEPTION');
			$this->log($e->getMessage());
		}

		$this->logVar($result, $messageTitle . ' RESULT');
		return $result;


	}

	public function getPickupManifest($ws, $username, $password, $system, $masterTrackingId) {
		$messageTitle = 'GET PICKUP MANIFEST DATA';
		$this->log($messageTitle);
		$internalReferenceID = $this->generateRandomString();

		$result = false;

		try {
			$soapClient = new MbeSoapClient( $ws, array(
				'encoding' => 'utf-8',
				'trace'    => 1
			), $username, $password, false );

//			$masterTrackingMBE = [];
//			foreach ( $masterTrackingIds as $masterTrackingId ) {
//				$masterTrackingMBE[] = $masterTrackingId;
//			}

			$args = $this->setBaseClass( $system, $username, $password );
			$args->RequestContainer->InternalReferenceID = $internalReferenceID;
			$args->RequestContainer->MasterTrackingMBE = $masterTrackingId;
			$result = $this->sendRequest( $args, 'PickupManifestListRequest', $messageTitle, $soapClient );

		} catch (Exception $e) {
			$this->log($messageTitle . ' EXCEPTION');
			$this->log($e->getMessage());
			throw $e;
		}

		$this->logVar($result, $messageTitle . ' RESULT');
		return $result;
	}

	/**
	 * Generates a proForma object and remove Price from the product object, as it's not needed for the request
	 *
	 * @param $products
	 *
	 * @return array
	 */
	protected function generateProforma($products) {
		$proForma = [];
		foreach ( $products as $product ) {
			$item = new stdClass();
			$item->Amount = $product->Quantity;
			$item->Currency = mb_substr($product->Currency,0,10, 'UTF-8');
			$item->Value = $product->Price;
			$item->Unit = 'PCS';
			$item->Description = mb_substr($product->Description,0,35,'UTF-8');
			$proForma[] = $item;
		}
		return $proForma;
	}

	protected function generateProducts($products) {
		foreach ( $products as $product ) {
			unset( $product->Price );
			unset( $product->Currency );
		}
		return $products; // this is not really needed, as $products in the calling environment is updated when elements are unset, but we want to use the function return value directly
	}

	public function getApiBearer( $user, $password ) {
		$response = $this->httpPostRequest(
			preg_replace( '/(ws\/e-link\.wsdl)$/i', '', $this->wsUrl ) . 'oauth/token',
			[
				'headers' => [
					'Accept'        => ' application/json, text/plain, */*',
					'Content-Type'  => ' application/x-www-form-urlencoded;charset=UTF-8',
					'Authorization' => ' Basic dGVsZXBvcnQtZmU6',
				],
				'body'    => 'grant_type=password&username=' . $user . '&password=' . $password
			]
		);
//		$this->logVar( $response, 'API BEARER' );
		$this->log('API BEARER' );

		return $response;
	}

	public function getApiCustomer( $apiBearer ) {
		$response = $this->httpGetRequest(
			preg_replace( '/(ws\/e-link\.wsdl)$/i', '', $this->wsUrl ) . 'oauth/mine',
			[
				'headers' => [
					'Accept'          => ' application/json, text/plain, */*',
					'Accept-Language' => ' it,it-IT;q=0.9,en;q=0.8,en-GB;q=0.7,en-US;q=0.6',
					'Authorization'   => ' Bearer ' . $apiBearer->access_token,
				],
			]
		);
//		$this->logVar( $response, 'API CUSTOMER' );
		$this->log( 'API CUSTOMER' );

		return $response;
	}

	public function getApiToken( $user, $password ) {
		$apiBearer = $this->getApiBearer( $user, $password );
		if ( ! empty( $apiBearer ) ) {
			$apiCustomer = $this->getApiCustomer( $apiBearer );
			if ( ! empty( $apiCustomer ) ) {
				$response = $this->httpPostRequest(
					preg_replace( '/(ws\/e-link\.wsdl)$/i', '', $this->wsUrl ) . 'oauth/select-store',
					[
						'headers' => [
							'Accept'        => 'application/json, text/plain, */*',
							'Content-Type'  => 'application/json',
							'Authorization' => ' Bearer ' . $apiBearer->access_token,
						],
						'body'    => json_encode( $apiCustomer[0] )
					]
				);

//				$this->logVar( $response, 'API TOKEN' );
				$this->log('API TOKEN');

				return $response;
			}
		}

		return null;
	}

	public function getApiKey( $user, $password ) {
		$apiToken = $this->getApiToken( $user, $password );
		if ( ! empty( $apiToken ) ) {
			$response = $this->httpGetRequest(
				preg_replace( '/(ws\/e-link\.wsdl)$/i', '', $this->wsUrl ) . 'auth-registry/apikey?idEntity=' . $apiToken->{'legal-entity-id'} . '&legalEntityType=CUSTOMER',
				[
					'headers' => [
						'Accept'        => ' application/json, text/plain, */*',
						'Authorization' => ' Bearer ' . $apiToken->access_token
					]
				]
			);
//			$this->logVar( $response, 'GET API KEY' );
			$this->log('GET API KEY');
			return $response;
		}

		return null;
	}

	public function deleteApiKey( $user, $password ) {
		$apiKey = $this->getApiKey( $user, $password );
		if ( ! empty( $apiKey->content ) ) {
			$apiToken = $this->getApiToken( $user, $password );
			if ( ! empty( $apiToken ) ) {
				$response = $this->httpDeleteRequest(
					preg_replace( '/(ws\/e-link\.wsdl)$/i', '', $this->wsUrl ) . 'auth-registry/apikey?apikey=' . $apiKey->content[0]->apiKey,
					[
						'headers' => [
							'Accept'        => ' application/json, text/plain, */*',
							'Authorization' => ' Bearer ' . $apiToken->access_token
						]
					]
				);
//				$this->logVar( $response, 'DELETE API KEY' );
				$this->log('DELETE API KEY');
			}

			return true;
		}

		return false;
	}

	/**
	 * @throws Exception
	 */
	public function generateApiKey( $user, $password ) {
		$apiKeyDeleted = $this->deleteApiKey( $user, $password );
		if ( $apiKeyDeleted ) {
			$apiToken = $this->getApiToken( $user, $password );
			if ( ! empty( $apiToken ) ) {
				$response = $this->httpPostRequest(
					preg_replace( '/(ws\/e-link\.wsdl)$/i', '', $this->wsUrl ) . 'auth-registry/apikey',
					[
						'headers' => [
							'Accept'        => ' application/json, text/plain, */*',
							'Authorization' => ' Bearer ' . $apiToken->access_token
						],
						'body'    => 'legalEntityType=CUSTOMER&roleName=ONLINEMBE_USER&idEntity=' . $apiToken->{'legal-entity-id'} . '&username=' . $apiToken->username,
					]
				);
//				$this->logVar( $response, 'GENERATE API KEY' );
				$this->log('GENERATE API KEY');

				return $response;
			}
		}

		return null;
	}

	/**
	 * @throws Exception
	 */
	public function httpGetRequest( $url, $options ) {
		$response = wp_remote_get( $url, $options );
		return $this->checkHttpResponse($response, $url);
	}

	/**
	 * @throws Exception
	 */
	public function httpPostRequest( $url, $options ) {
		$response = wp_remote_post( $url, $options );
		return $this->checkHttpResponse($response, $url, 'POST');
	}

	/**
	 * @throws Exception
	 */
	public function httpDeleteRequest( $url, $options ) {
		$options['method'] = 'DELETE';
		$response          = wp_remote_request( $url, $options );
		return $this->checkHttpResponse($response, $url, 'DELETE');
	}

	/**
	 * @throws HttpRequestException
	 */
	protected function checkHttpResponse( $response, $url, $method = 'GET' ) {
		if ( ( ! is_wp_error( $response ) ) && ( preg_match( '(2[0-9]{2})', wp_remote_retrieve_response_code( $response ) ) ) ) {
			$responseBody = json_decode( $response['body'] );
			if ( empty( $response['body'] ) || json_last_error() === JSON_ERROR_NONE ) {
				return $responseBody;
			}
			throw new HttpRequestException( esc_html('JSON Error: ' . json_last_error_msg()) );
		}
		$message = wp_remote_retrieve_response_code( $response ) . ' - ' . wp_remote_retrieve_response_message( $response ) . ' - ' . wp_remote_retrieve_body( $response );
		$this->log( $method . ' Http Request: ' . $url . ' - ' . $message );
		throw new HttpRequestException( esc_html($message) );
	}

	/**
	 * @throws \MbeExceptions\ApiRequestException
	 */
	private function sendRequest($args, $functionName, $messageTitle, $soapClient) {
		$result = false;

		$logArgs = json_decode(json_encode($args), true);
		$logArgs['RequestContainer']['Credentials'] = null;

		$this->logVar( $logArgs, $messageTitle . ' ARGS' );

		$soapResult = $soapClient->__soapCall( $functionName, array( $args ) );

		$lastResponse = $soapClient->__getLastResponse();

		$this->logVar( $lastResponse, $messageTitle . ' RESPONSE' );

		if ( isset( $soapResult->RequestContainer->Errors ) ) {
			$this->logVar( $soapResult->RequestContainer->Errors, $messageTitle . ' ERRORS' );
			throw new \MbeExceptions\ApiRequestException(esc_html($soapResult->RequestContainer->Errors->Error->Description));
		}

		if ( isset( $soapResult->RequestContainer->Status ) && $soapResult->RequestContainer->Status == "OK" ) {
			$result = $soapResult->RequestContainer;
		}

		return $result;
	}

	private function setBaseClass( $system, $username, $password ): stdClass {

		$args                                            = new stdClass;
		$args->RequestContainer                          = new stdClass;
		$args->RequestContainer->System                  = $system;
		$args->RequestContainer->Credentials             = new stdClass;
		$args->RequestContainer->Credentials->Username   = $username;
		$args->RequestContainer->Credentials->Passphrase = $password;

		return $args;
	}

	private function setShipmentContainer( $system, $username, $password, string $internalReferenceID, $firstName, $lastName, $companyName, $address, $phone, $postCode, $city, $state, $country, $email, $subZone, $shipperType, $isCod, $codValue, $insurance, $insuranceValue, $service, $shipmentType, $reference, $items, $products, $goodsValue, $notes ): stdClass {
//WS ARGS
		$args = $this->setBaseClass( $system, $username, $password );

		$args->RequestContainer->InternalReferenceID = $internalReferenceID;

		$args->RequestContainer->Recipient = new stdClass;

		$recipientName        = $firstName . " " . $lastName;
		$RecipientCompanyName = $companyName;
		$recipientName        = mb_substr( $recipientName, 0, 35, 'UTF-8' );
		$RecipientCompanyName = mb_substr( $RecipientCompanyName, 0, 35, 'UTF-8' );
		if ( empty( $RecipientCompanyName ) ) {
			$RecipientCompanyName = $recipientName;
		}

		$args->RequestContainer->Recipient->Name = $recipientName;
		//$args->RequestContainer->Recipient->LastName = $lastName;
		$args->RequestContainer->Recipient->CompanyName = $RecipientCompanyName;
		$args->RequestContainer->Recipient->Address     = $address;
		//$args->RequestContainer->Recipient->Phone = $phone;
		$args->RequestContainer->Recipient->Phone   = mb_substr( $phone, 0, 50, 'UTF-8' );
		$args->RequestContainer->Recipient->ZipCode = $postCode;
		$args->RequestContainer->Recipient->City    = $city;
		$args->RequestContainer->Recipient->State   = $state;
		$args->RequestContainer->Recipient->Country = $country;
		$args->RequestContainer->Recipient->Email   = $email;
		if ( $subZone ) {
			$args->RequestContainer->Recipient->SubzoneId = $subZone;
		}

		$shipmentNode = new stdClass;

		$shipmentNode->ShipperType = $shipperType;//"MBE";//COURIERLDV - MBE
		$shipmentNode->Description = "ECOMMERCE SHOP PURCHASE";
		$shipmentNode->COD         = $isCod;
		if ( $isCod ) {
			$shipmentNode->CODValue      = $codValue;
			$shipmentNode->MethodPayment = "CASH";//CASH - CHECK
		}

		$shipmentNode->Insurance = $insurance;
		if ( $insurance ) {
			$shipmentNode->InsuranceValue = $insuranceValue;
		}

		$shipmentNode->Service = $service;//SEE /SSE

		//$shipmentNode->Courier = "";
		//$shipmentNode->CourierService = "SEE";
		//$shipmentNode->CourierAccount = "";
		$shipmentNode->PackageType = $shipmentType;
		//$shipmentNode->Value = 0;
		$shipmentNode->Referring = $reference;

		$shipmentNode->Items = $items;

		$shipmentNode->ProformaInvoice = $this->generateProforma( $products );

		$shipmentNode->Products = $this->generateProducts( $products );

		$shipmentNode->Value = $goodsValue;

		$shipmentNode->ShipmentOrigin = MBE_ESHIP_PLUGIN_NAME . " WooCommerce " . MBE_ESHIP_PLUGIN_VERSION;

		$shipmentNode->Notes = mb_substr( $notes, 0, 50, 'UTF-8' );

		// Add information for the delivery point, if used
		$order_delivery_point_shipment = $this->helper->getOrderDeliveryPointShipment($reference);

		if ( $order_delivery_point_shipment === 'Yes' ) {
			// Remove new line, if any, in case of delivery point address
			$args->RequestContainer->Recipient->Address = str_replace( "\n", ' ', $address );
			$deliveryPointData = json_decode($this->helper->getOrderDeliveryPointCustomData($reference));
			$shipmentNode->Service = $this->getDeliveryPointMolServiceByNetworkCode($deliveryPointData->networkCode, $reference);
			$shipmentNode->Courier = strtolower($deliveryPointData->serviceType) === 'labeling' ? $deliveryPointData->networkName : 'GEL';
			$shipmentNode->CourierService = $deliveryPointData->networkCode;
//			$args->RequestContainer->Shipment->CourierServiceId = $deliveryPointData->networkCode;

			// Set new delivery point object for API
			$args->RequestContainer->RecipientDeliveryPoint                  = clone $args->RequestContainer->Recipient;
			$args->RequestContainer->RecipientDeliveryPoint->DeliveryPointId = sanitize_text_field($deliveryPointData->code);
		}

		if($this->helper->isEnabledTaxAndDuties()) {
			$shipmentNode->LanguageCode = $this->helper->getCountry();
			$shipmentNode->TaxAndDutyPluginAct = true;
		}

		$args->RequestContainer->Shipment = $shipmentNode;

		return $args;
	}

	public function getDefaultPickupAddress( $ws, $username, $password, $system ): array {
		$pickupAddresses = $this->getCustomerPickupAddresses( $ws, $username, $password, $system );
		$address = [];
		if(!empty($pickupAddresses)) {
			$address = array_values( array_filter($pickupAddresses , function ( $var ) {
				return ( $var['IsDefault'] == true );
			} ) );
		}
		return empty($address)?[]:$address;
	}

	public function getPickupAddressById( $ws, $username, $password, $system, $id ): array {
		$pickupAddresses = $this->getCustomerPickupAddresses( $ws, $username, $password, $system );
		$address = [];
		if(!empty($pickupAddresses)) {
			$address = array_values( array_filter( $this->getCustomerPickupAddresses( $ws, $username, $password, $system ), function ( $var ) use ( $id ) {
				return ( $var['PickupAddressId'] == $id );
			} ) );
		}
		return empty($address)?[]:$address[0];
	}

	/**
	 * @throws \MbeExceptions\ApiRequestException
	 */
	protected function checkResponseErrors( $soapResult, string $messageTitle ): void {
		if ( isset( $soapResult->RequestContainer->Errors ) ) {
			$this->logVar( $soapResult->RequestContainer->Errors, $messageTitle . ' ERRORS' );
			$message = '';
			if ( is_array( $soapResult->RequestContainer->Errors->Error ) ) {
				foreach ( $soapResult->RequestContainer->Errors->Error as $error ) {
					$message .= ', ' . $error->Description;
				}
			} else {
				$message = $soapResult->RequestContainer->Errors->Error->Description;
			}
			throw new \MbeExceptions\ApiRequestException( esc_html($message) );
		}
	}

	private function isResponseValid(object $soapResult, string $internalReferenceID): bool {
		return isset($soapResult->RequestContainer->Status)
		       && $soapResult->RequestContainer->Status == self::EXPECTED_STATUS
		       && isset($soapResult->RequestContainer->InternalReferenceID)
		       && $soapResult->RequestContainer->InternalReferenceID == $internalReferenceID;
	}

	protected function getDeliveryPointMolServiceByNetworkCode( $networkCode, $orderId ) {
		$combined = array_combine( $this->helper->getOrderDeliveryPointServices($orderId)['courier'],  $this->helper->getOrderDeliveryPointServices($orderId)['mol']);
		return $combined[$networkCode]??null;
	}

}