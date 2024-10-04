<?php

class Mbe_Shipping_Helper_Rates
{
    protected $_rate_table_name;
	protected $helper;


    public function __construct()
    {
	    $this->helper = new Mbe_Shipping_Helper_Data();
	    $this->_rate_table_name = $this->helper->getShipmentCsvTable();
    }

    public function uninstallRatesTable()
    {
        global $wpdb;
//        $sql = "DROP TABLE IF EXISTS `" . $this->_rate_table_name . "`";
	    return $wpdb->query($wpdb->prepare("DROP TABLE IF EXISTS %i", $this->_rate_table_name));  // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.SchemaChange
    }

    public function truncate()
    {
        global $wpdb;
//	    $truncateSql = " TRUNCATE `" . $this->_rate_table_name . "` ";
	    return $wpdb->query($wpdb->prepare("TRUNCATE %i", $this->_rate_table_name)); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.SchemaChange
    }

    public function insertRate($country, $region, $city, $zip, $zipTo, $weightFrom, $weightTo, $price, $deliveryType)
    {
        global $wpdb;

	    $city = esc_sql($city);
	    $region = esc_sql($region);
	    $country = esc_sql($country);
	    $zip = esc_sql($zip);
	    $zipTo = esc_sql($zipTo);
	    $weightFrom = esc_sql($weightFrom);
	    $weightTo = esc_sql($weightTo);
	    $price = esc_sql($price);
	    $deliveryType = esc_sql($deliveryType);

//        $sql = "
//                INSERT INTO `" . $this->_rate_table_name . "` (
//                    `country`,`region`,`city`,`zip`,`zip_to`,`weight_from`,`weight_to`,`price`,`delivery_type`
//                )
//                VALUES (
//                    '" . $country . "',
//                    '" . $region . "',
//                    '" . $city . "',
//                    '" . $zip . "',
//                    '" . $zipTo . "',
//                    " . $weightFrom . ",
//                    " . $weightTo . ",
//                    " . $price . ",
//                    '" . $deliveryType . "'
//                );
//            ";

	    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.SchemaChange
	    return $wpdb->query($wpdb->prepare(
		    "INSERT INTO %i ( `country`,`region`,`city`,`zip`,`zip_to`,`weight_from`,`weight_to`,`price`,`delivery_type` ) 
				  VALUES (%s, %s, %s, %s, %s, %d, %d, %d, %s) ",
		    $this->_rate_table_name,
		    $country,
		    $region,
		    $city,
		    $zip,
		    $zipTo,
		    $weightFrom,
		    $weightTo,
		    $price,
		    $deliveryType
	    ));
    }


    public function useCustomRates($country)
    {
        global $wpdb;
	    $country = esc_sql($country);

        $helper = new Mbe_Shipping_Helper_Data();

        if ($helper->getShipmentsCsvMode() == Mbe_Shipping_Helper_Data::MBE_CSV_MODE_DISABLED) {
			return false;
        }

		if ($helper->getShipmentsCsvMode() == Mbe_Shipping_Helper_Data::MBE_CSV_MODE_TOTAL) {
            return true;
        }

		if ($helper->getShipmentsCsvMode() == Mbe_Shipping_Helper_Data::MBE_CSV_MODE_PARTIAL) {
//            $sql = "SELECT * FROM `" . $this->_rate_table_name . "` WHERE `country` = '" . $country . "'";
            $rates = $wpdb->get_results($wpdb->prepare("SELECT * FROM %i WHERE country = %s", $this->_rate_table_name, $country),"ARRAY_A");  // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.SchemaChange
            if (is_array($rates) && count($rates) > 0) {
                return true;
            }
        }

		return false;
    }

    public function applyInsuranceToRate($rate, $insuranceValue)
    {
        $result = $rate;

        $helper = new Mbe_Shipping_Helper_Data();

        $percentageValue = $helper->getShipmentsCsvInsurancePercentage() / 100 * (float)$insuranceValue;
        $fixedValue = $helper->getShipmentsCsvInsuranceMin();

        if ($percentageValue < $fixedValue) {
            $result += $fixedValue;
        }
        else {
            $result += $percentageValue;
        }
        return $result;
    }


    public function getCustomRates($country, $region, $city, $postCode, $weight, $insuranceValue)
    {
	    global $wpdb;
	    $result = array();
	    $newdata = array();

	    $postCode = esc_sql($postCode);
	    $city = esc_sql($city);
	    $region = esc_sql($region);
	    $country = esc_sql($country);
	    $zipSql = " '" . $postCode . "' BETWEEN zip AND zip_to";

        $helper = new Mbe_Shipping_Helper_Data();
	    $weight = $helper->convertWeight($weight, 'kg');
        $services = $helper->getAllowedShipmentServicesArray();

        foreach ($services as $service) {
            for ($j = 0; $j <= 7; $j++) {

                $sql = "SELECT * FROM `" . $this->_rate_table_name . "` ";

                switch ($j) {
                    case 0:
                        $sql .= "WHERE ";
                        $sql .= "`country` = '" . $country . "'";
                        $sql .= " AND ";
                        $sql .= "`region` = '" . $region . "'";
                        $sql .= " AND ";
                        $sql .= " STRCMP(LOWER(city),LOWER('" . $city . "')) = 0";
                        $sql .= " AND ";
                        $sql .= $zipSql;

                        break;

                    case 1:
                        $sql .= "WHERE ";
                        $sql .= "`country` = '" . $country . "'";
                        $sql .= " AND ";
                        $sql .= "`region` = '" . $region . "'";
                        $sql .= " AND ";
                        $sql .= "`city` = ''";
                        $sql .= " AND ";
                        $sql .= $zipSql;

                        break;
                    case 2:
                        $sql .= "WHERE ";
                        $sql .= "`country` = '" . $country . "'";
                        $sql .= " AND ";
                        $sql .= "`region` = '" . $region . "'";
                        $sql .= " AND ";
                        $sql .= "STRCMP(LOWER(city),LOWER('" . $city . "')) = 0";
                        $sql .= " AND ";
                        $sql .= " zip = '' ";

                        break;
                    case 3:
                        $sql .= "WHERE ";
                        $sql .= "`country` = '" . $country . "'";
                        $sql .= " AND ";
                        //$sql .= "`region` = '" . $region . "'";
                        $sql .= "`region` = ''";
                        $sql .= " AND ";
                        $sql .= "STRCMP(LOWER(city),LOWER('" . $city . "')) = 0";
                        $sql .= " AND ";
                        $sql .= $zipSql;

                        break;
                    case 4:
                        $sql .= "WHERE ";
                        $sql .= "`country` = '" . $country . "'";
                        $sql .= " AND ";
                        //$sql .= "`region` = '" . $region . "'";
                        $sql .= "`region` = ''";
                        $sql .= " AND ";
                        $sql .= "STRCMP(LOWER(city),LOWER('" . $city . "')) = 0";
                        $sql .= " AND ";
                        $sql .= "zip = ''";

                        break;
                    case 5:
                        $sql .= "WHERE ";
                        $sql .= "`country` = '" . $country . "'";
                        $sql .= " AND ";
                        //$sql .= "`region` = '" . $region . "'";
                        $sql .= "`region` = ''";
                        $sql .= " AND ";
                        $sql .= "city = ''";
                        $sql .= " AND ";
                        $sql .= $zipSql;

                        break;
                    case 6:
                        $sql .= "WHERE ";
                        $sql .= "`country` = '" . $country . "'";
                        $sql .= " AND ";
                        $sql .= "`region` = '" . $region . "'";
                        $sql .= " AND ";
                        $sql .= "city = ''";
                        $sql .= " AND ";
                        $sql .= "zip = ''";
                        break;
                    case 7:
                        $sql .= "WHERE ";
                        $sql .= "`country` = '" . $country . "'";
                        $sql .= " AND ";
                        $sql .= "`region` = ''";
                        $sql .= " AND ";
                        $sql .= "city = ''";
                        $sql .= " AND ";
                        $sql .= "zip = ''";
                        break;

                }
                $sql .= " AND weight_from <= " . $weight . " AND weight_to >=" . $weight;
                $sql .= " AND delivery_type = '" . $service . "'";

                $sql .= " ORDER BY country DESC, region DESC, zip DESC";


                $rows = $wpdb->get_results($sql, 'ARRAY_A');  // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.SchemaChange


                if (!empty($rows)) {
                    // have found a result or found nothing and at end of list!
                    foreach ($rows as $data) {
                        $newdata[$data["delivery_type"]] = $data;
                    }
                    break;
                }

            }
        }
	    $resultWithoutInsurance = [];
	    $resultWithInsurance = [];
		$resultDeliveryPoint = [];

        $ws = new Mbe_Shipping_Model_Ws();
        foreach ($newdata as $data) {
            $rate = new \stdClass;
            $rate->Service = $data["delivery_type"];
            $rate->ServiceDesc = $ws->getLabelFromShipmentType($data["delivery_type"]);
            $rate->SubzoneDesc = '';
            $rate->IdSubzone = '';

            $rate->NetShipmentTotalPrice = $data["price"];

			// Extract all the delivery point CourierService codes
	        if(in_array($data["delivery_type"], MBE_ESTIMATE_DELIVERY_POINT_SERVICES)) {
                $customerData = $ws->getCustomer();
		        $enabledServices = explode(',', $customerData->Permissions->enabledServices);
		        $enabledCourierServices = explode(',',$customerData->Permissions->enabledCourierServices);
		        foreach ($enabledServices  as $key=>$value ) {
					if($data["delivery_type"] === $value) {
						$netRate = clone $rate;
						$netRate->CourierService = $enabledCourierServices[$key];
						$resultDeliveryPoint[] = $netRate;
					}
				}
	        } else {
		        $resultWithoutInsurance[] = $rate;

				//rate with insurance
		        $rateWithInsurance = new \stdClass;
		        $rateWithInsurance->Service = $this->helper->convertShippingCodeWithInsurance($data["delivery_type"]);
		        $rateWithInsurance->ServiceDesc = $this->helper->convertShippingLabelWithInsurance($ws->getLabelFromShipmentType($data["delivery_type"]));;
		        $rateWithInsurance->SubzoneDesc = '';
		        $rateWithInsurance->IdSubzone = '';

		        $rateWithInsurance->NetShipmentTotalPrice = $this->applyInsuranceToRate($data["price"], $insuranceValue);
		        $resultWithInsurance[] = $rateWithInsurance;
	        }


        }
	    $result = array_merge($resultWithInsurance, $resultWithoutInsurance);

	    // Add common delivery point service
	    if(is_array($resultDeliveryPoint)) {
		    $result[] = $this->helper->getDeliveryPointServices($resultDeliveryPoint);
		    $result = array_values(array_filter($result)); //Clean empty array items and reindex (remove null deliverypointservices if any)
	    }

        return $result;
    }

}