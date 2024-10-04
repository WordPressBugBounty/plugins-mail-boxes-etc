<?php

class Mbe_Shipping_Helper_Data
{
	private $_mbeMediaDir = 'mbe';
    private $_csvDir = 'csv';
	const MBE_LOG_WS = 'mbe_ws.log';
	const MBE_LOG_PLUGIN = 'mbe_shipping.log';
	const XML_PATH_MBE_LOG_FOLDER_NAME =  MBE_ESHIP_ID . '_' . 'log_folder';
//    const MBE_SHIPPING_PREFIX = "mbe_shipping_";
	const MBE_CSV_PACKAGES_RESERVED_CODE = 'settings';
	const MBE_WC_ELINK_SETTINGS_PREFIX = 'woocommerce_wf_mbe_shipping_';
    const MBE_ELINK_SETTINGS = self::MBE_WC_ELINK_SETTINGS_PREFIX . 'settings';
	const MBE_SETTINGS_CSV_SHIPMENTS = MBE_ESHIP_ID . '_' . self::XML_PATH_SHIPMENTS_CSV;
	const MBE_SETTINGS_CSV_PACKAGE = MBE_ESHIP_ID . '_' . self::XML_PATH_PACKAGES_CSV;
	const MBE_SETTINGS_CSV_PACKAGE_PRODUCT = MBE_ESHIP_ID . '_' . self::XML_PATH_PACKAGES_PRODUCT_CSV;
    const SHIPMENT_SOURCE_TRACKING_NUMBER = "woocommerce_mbe_tracking_number";
    const SHIPMENT_SOURCE_TRACKING_FILENAME = 'woocommerce_mbe_tracking_filename';
	const SHIPMENT_SOURCE_RETURN_TRACKING_NUMBER = "woocommerce_mbe_return_tracking_number";

	const MBE_CSV_PACKAGES_TABLE_NAME = MBE_ESHIP_ID . '_' . 'standard_packages';
	const MBE_CSV_PACKAGES_PRODUCT_TABLE_NAME = MBE_ESHIP_ID . '_' . 'standard_packages_products';
	const MBE_CSV_PICKUP_ADDRESSES_TABLE_NAME = MBE_ESHIP_ID . '_' . 'pickup_addresses';
	const MBE_CSV_RATES_TABLE_NAME = MBE_ESHIP_ID . '_' . 'shipping_rate';
	const MBE_CSV_PACKAGE_TEMPLATE = 'mbe_package_csv_template.csv';
	const MBE_CSV_PACKAGE_PRODUCT_TEMPLATE = 'mbe_package_product_csv_template.csv';
	const MBE_CSV_RATES_TEMPLATE_CSV = 'mbe_csv_template.csv';

	const MBE_PICKUP_CUSTOM_DATA_TABLE_NAME = MBE_ESHIP_ID . '_' . 'pickup_custom_data';

    //MAIN
    const CONF_DEBUG = 'mbe_debug';
    const XML_PATH_ENABLED = 'mbe_active';
    const XML_PATH_COUNTRY = 'country';
    //WS
    const XML_PATH_WS_URL = 'url';
    const XML_PATH_WS_USERNAME = 'username';
    const XML_PATH_WS_PASSWORD = 'password';
	const XML_PATH_MBE_USERNAME = 'mbe_username';
	const XML_PATH_MBE_PASSWORD = 'mbe_password';
    //OPTIONS
    const XML_PATH_DESCRIPTION = 'description';
    const XML_PATH_DEFAULT_SHIPMENT_TYPE = 'default_shipment_type';
    const XML_PATH_ALLOWED_SHIPMENT_SERVICES = 'allowed_shipment_services';
    const XML_PATH_SHIPMENT_CONFIGURATION_MODE = 'default_shipment_mode';
    const XML_PATH_DEFAULT_LENGTH = 'default_length';
    const XML_PATH_DEFAULT_WIDTH = 'default_width';
    const XML_PATH_DEFAULT_HEIGHT = 'default_height';
    const XML_PATH_MAX_PACKAGE_WEIGHT = 'max_package_weight';
    const XML_PATH_MAX_SHIPMENT_WEIGHT = 'max_shipment_weight';
    const XML_PATH_HANDLING_TYPE = 'handling_type';
    const XML_PATH_HANDLING_ACTION = 'handling_action';
    const XML_PATH_HANDLING_FEE = 'handling_fee';
    const XML_PATH_HANDLING_FEE_ROUNDING = 'handling_fee_rounding';
    const XML_PATH_HANDLING_FEE_ROUNDING_AMOUNT = 'handling_fee_rounding_amount';
    const XML_PATH_SALLOWSPECIFIC = 'sallowspecific';
    const XML_PATH_SPECIFICCOUNTRY = 'specificcountry';
    const XML_PATH_SORT_ORDER = 'sort_order';
    const XML_PATH_MAXIMUM_TIME_FOR_SHIPPING_BEFORE_THE_END_OF_THE_DAY = 'maximum_time_for_shipping_before_the_end_of_the_day';
    const XML_PATH_SPECIFICERRMSG = 'specificerrmsg';
    const XML_PATH_WEIGHT_TYPE = 'weight_type';
    const XML_PATH_SHIPMENTS_CLOSURE_MODE = 'shipments_closure_mode';
    const XML_PATH_SHIPMENTS_CLOSURE_TIME = 'shipments_closure_time';
    const XML_PATH_SHIPMENTS_CREATION_MODE = 'shipments_creation_mode';
    const XML_PATH_ADD_TRACK_ID = 'mbe_add_track_id';
    const XML_PATH_SHIPMENT_CUSTOM_LABEL = 'mbe_custom_label';
// STANDARD PACKAGES CSV
	const XML_PATH_CSV_STANDARD_PACKAGE_USE_CSV = 'use_packages_csv';
	const XML_PATH_CSV_STANDARD_PACKAGE_DEFAULT = 'csv_packages_default';

    const XML_PATH_SHIPMENTS_CSV = 'shipments_csv';
    const XML_PATH_SHIPMENTS_CSV_FILE = 'shipments_csv_file';

	const XML_PATH_PACKAGES_CSV = 'packages_csv';
	const XML_PATH_PACKAGES_CSV_FILE = 'packages_csv_file';
	const XML_PATH_PACKAGES_PRODUCT_CSV = 'packages_product_csv';
	const XML_PATH_PACKAGES_PRODUCT_CSV_FILE = 'packages_product_csv_file';

    const XML_PATH_SHIPMENTS_CSV_MODE = 'shipments_csv_mode';
    const XML_PATH_SHIPMENTS_CSV_INSURANCE_MIN = 'mbe_shipments_csv_insurance_min';
    const XML_PATH_SHIPMENTS_CSV_INSURANCE_PERCENTAGE = 'mbe_shipments_csv_insurance_per';
    const XML_PATH_SHIPMENTS_INSURANCE_MODE = 'mbe_shipments_ins_mode';


    const XML_PATH_THRESHOLD = 'mbelimit';

    const XML_PATH_CAN_CREATE_COURIER_WAYBILL = 'mbe_can_create_courier_waybill';

    //const

    const MBE_SHIPMENT_STATUS_CLOSED = "Closed";
    const MBE_SHIPMENT_STATUS_OPEN = "Opened";

    const MBE_CLOSURE_MODE_AUTOMATICALLY = 'automatically';
    const MBE_CLOSURE_MODE_MANUALLY = 'manually';

    const MBE_CREATION_MODE_AUTOMATICALLY = 'automatically';
    const MBE_CREATION_MODE_MANUALLY = 'manually';

	const MBE_PICKUP_REQUEST_AUTOMATIC = 'automatic';
	const MBE_PICKUP_REQUEST_MANUAL = 'manual';


    const MBE_CSV_MODE_DISABLED = 'disabled';
    const MBE_CSV_MODE_TOTAL = 'total';
    const MBE_CSV_MODE_PARTIAL = 'partial';
    const MBE_INSURANCE_WITH_TAXES = 'insurance_with_taxes';
    const MBE_INSURANCE_WITHOUT_TAXES = 'insurance_without_taxes';
    const MBE_SHIPPING_WITH_INSURANCE_CODE_SUFFIX = '_INSURANCE';
    const MBE_SHIPPING_WITH_INSURANCE_LABEL_SUFFIX = ' + Insurance';
    const MBE_SHIPPING_TRACKING_SEPARATOR = ',';
    const MBE_SHIPPING_TRACKING_SEPARATOR__OLD = '--';

	// New settings Courier Mode
	const MBE_COURIER_MODE_CSV = 1;
	const MBE_COURIER_MODE_MAPPING = 2;
	const MBE_COURIER_MODE_SERVICES = 3;
	const XML_PATH_COURIER_CONFIG_MODE = 'mbe_courier_config_mode';

	//New settings Login Mode
	const XML_PATH_LOGIN_MODE = 'mbe_login_mode';
	const XML_PATH_LOGIN_LINK_ADV = 'mbe_login_link_advanced';
	const MBE_LOGIN_MODE_SIMPLE = 1;
	const MBE_LOGIN_MODE_ADVANCED = 2;

	// Pickup request settings
	const XML_PATH_PICKUP_REQUEST_MODE = 'pickup_options_mode';
	const XML_PATH_PICKUP_REQUEST_ENABLED = 'pickup_options_enabled';
	const XML_PATH_PICKUP_OPTIONS_CUTOFF = 'pickup_options_default_cutoff';
	const XML_PATH_PICKUP_OPTIONS_NOTES = 'pickup_options_default_notes';
	const XML_PATH_PICKUP_OPTIONS_DEFAULT_PICKUP_TIME = 'pickup_options_default_pickup_time';
	const XML_PATH_PICKUP_OPTIONS_ALTERNATIVE_PICKUP_TIME = 'pickup_options_alternative_pickup_time';
	// Pickup request meta
	const META_FIELD_IS_PICKUP_SHIPPING = "woocommerce_mbe_is_pickup_shipping";
	const META_FIELD_PICKUP_BATCH_ID = "woocommerce_mbe_pickup_batch_id";
	const META_FIELD_PICKUP_CUSTOM_DATA_ID = "woocommerce_mbe_pickup_custom_data_id";

	// Delivery Point
	const GEL_PROXIMITY_URL = "https://platform.gelproximity.com";
	const GEL_PROXIMITY_URL_END_USER = self::GEL_PROXIMITY_URL . "/gel-enduser-client/";
	const META_FIELD_DELIVERY_POINT_CUSTOM_DATA = "woocommerce_mbe_delivery_point_custom_data";
	const META_FIELD_DELIVERY_POINT_SHIPMENT = 'woocommerce_mbe_delivery_point_shipment';
	const META_FIELD_DELIVERY_POINT_SERVICES = 'delivery_point_services';
//	const META_FIELD_DELIVERY_POINT_MOL_SERVICES = 'delivery_point_mol_services';

	// Tax and Duties
	const XML_PATH_TAX_DUTIES_ENABLED = 'mbe_taxduties_enabled';
	const XML_PATH_TAX_DUTIES_MODE = 'mbe_taxduties_mode';
	const MBE_TAX_AND_DUTIES_DDP = 1;
	const MBE_TAX_AND_DUTIES_DAP = 0;

	protected $csv_package_model;
	protected $csv_package_product_model;
	protected $_options;


	public function __construct()
    {
//        $this->_options = get_option(self::MBE_SETTINGS);
	    $this->csv_package_model = new Mbe_Shipping_Model_Csv_Package();
	    $this->csv_package_product_model = new Mbe_Shipping_Model_Csv_Package_Product();
    }

    public function checkMbeDir($path)
    {
        $this->checkDir($path);
        if (!file_exists($path)) {
            mkdir($path);
        }
    }

    public function checkDir($dir)
    {
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }
    }

    //TODO: check if is necessary to specify storeid or get current storeid
    public function isEnabled()
    {
        return (bool) $this->getOption(self::XML_PATH_ENABLED);
    }

    public function debug()
    {
        $result = ($this->getOption(self::CONF_DEBUG) == 1);
        return $result;
    }


    public function enabledCountry($country = null)
    {
        if ($this->getSallowspecific()) {
            return in_array($country, $this->getSpecificcountry());
        }
        return true;
    }

	public function hasOption($field)
	{
//		Load Options as WC
		if ( !class_exists(WC_Admin_Settings::class)) {
			require_once(WP_PLUGIN_DIR . '/woocommerce/includes/admin/class-wc-admin-settings.php');
		}
		return !(null === WC_Admin_Settings::get_option( MBE_ESHIP_ID . '_' . $field, null));
	}

    public function getOption($field, $default=false)
    {
//		Load Options as WC
	    if ( !class_exists(WC_Admin_Settings::class)) {
		    require_once(WP_PLUGIN_DIR . '/woocommerce/includes/admin/class-wc-admin-settings.php');
	    }
	    return WC_Admin_Settings::get_option( MBE_ESHIP_ID . '_' . $field, $default);
    }

    public function setOption($field, $value)
    {
        return update_option( MBE_ESHIP_ID . '_' . $field, $value);
    }

	public function setPostOption($field, $value)
	{
		if (isset($_POST[$field])) {
			$_POST[$field] = $value;
			return true;
		}
		return false;
	}

    public function getCountry()
    {
        return $this->getOption(self::XML_PATH_COUNTRY);
    }

    public function getWsUrl()
    {
        return $this->getOption(self::XML_PATH_WS_URL);
    }

    public function getWsUsername()
    {
        return $this->getOption(self::XML_PATH_WS_USERNAME);
    }

    public function getWsPassword()
    {
        return $this->getOption(self::XML_PATH_WS_PASSWORD);
    }

	public function setWsUsername($value)
	{
		return $this->setOption(self::XML_PATH_WS_USERNAME, $value);
	}

	public function setWsPassword($value)
	{
		return $this->setOption(self::XML_PATH_WS_PASSWORD, $value);
	}

	public function setWsUrl($countryCode='it')
	{
		return $this->setOption(self::XML_PATH_WS_URL, 'https://api.mbeonline.'. strtolower($countryCode) .'/ws/e-link.wsdl');
	}

	public function getMbeUsername()
	{
		return $this->getOption(self::XML_PATH_MBE_USERNAME);
	}

	public function getMbePassword()
	{
		return $this->getOption(self::XML_PATH_MBE_PASSWORD);
	}

	public function getLoginMode()
	{
		return $this->getOption(self::XML_PATH_LOGIN_MODE,self::MBE_LOGIN_MODE_SIMPLE);
	}

	public function setLoginMode($simple=true)
	{
		if ($simple) {
			return $this->setOption(self::XML_PATH_LOGIN_MODE, self::MBE_LOGIN_MODE_SIMPLE);
		} else {
			return $this->setOption(self::XML_PATH_LOGIN_MODE, self::MBE_LOGIN_MODE_ADVANCED);
		}
	}

	public function getLoginLinkAdvanced()
	{
		return $this->getOption(Mbe_Shipping_Helper_Data::XML_PATH_LOGIN_LINK_ADV);
	}

	public function getDescription()
    {
        return $this->getOption(self::XML_PATH_DESCRIPTION);
    }

    public function getDefaultShipmentType()
    {
        return $this->getOption(self::XML_PATH_DEFAULT_SHIPMENT_TYPE);
    }

    public function getAllowedShipmentServices()
    {
        return $this->getOption(self::XML_PATH_ALLOWED_SHIPMENT_SERVICES, []);
    }

    public function convertShippingCodeWithInsurance($code)
    {
        return $code . self::MBE_SHIPPING_WITH_INSURANCE_CODE_SUFFIX;
    }

    public function convertShippingLabelWithInsurance($label)
    {
        return $label . self::MBE_SHIPPING_WITH_INSURANCE_LABEL_SUFFIX;
    }

    public function convertShippingCodeWithoutInsurance($code)
    {
        return str_replace(self::MBE_SHIPPING_WITH_INSURANCE_CODE_SUFFIX, "", $code);
    }

    public function isShippingWithInsurance($code)
    {
        $result = false;
        /*
        $shippingSuffix = substr($code, -strlen(self::MBE_SHIPPING_WITH_INSURANCE_CODE_SUFFIX));
        if ($shippingSuffix == self::MBE_SHIPPING_WITH_INSURANCE_CODE_SUFFIX) {
            $result = true;
        }
        */
        if (!empty($code) && strpos($code, self::MBE_SHIPPING_WITH_INSURANCE_CODE_SUFFIX) !== false) {
            $result = true;
        }
        return $result;
    }


    public function getAllowedShipmentServicesArray()
    {
        $allowedShipmentServicesArray = $this->getAllowedShipmentServices()?:[];
        /*$ws = new Mbe_Shipping_Model_Ws();
        $canSpecifyInsurance = $ws->getCustomerPermission('canSpecifyInsurance');

        $result = array();
        foreach ($allowedShipmentServicesArray as $item) {
            $canAdd = true;
            if (!$canSpecifyInsurance) {
                if (strpos($item, self::MBE_SHIPPING_WITH_INSURANCE_CODE_SUFFIX) !== false) {
                    $canAdd = false;
                }
            }
            if ($canAdd) {
                array_push($result, $item);
            }
        }

        return $result;*/
        return $allowedShipmentServicesArray;
    }

    public function getShipmentConfigurationMode()
    {
        return $this->getOption(self::XML_PATH_SHIPMENT_CONFIGURATION_MODE, Mbe_Shipping_Model_Carrier::SHIPMENT_CONFIGURATION_MODE_ONE_SHIPMENT_PER_SHOPPING_CART_WEIGHT_MULTI_PARCEL);
    }

    public function getDefaultLength()
    {
        return $this->getOption(self::XML_PATH_DEFAULT_LENGTH, 0);
    }

    public function getDefaultWidth()
    {
        return $this->getOption(self::XML_PATH_DEFAULT_WIDTH, 0);
    }

    public function getDefaultHeight()
    {
        return $this->getOption(self::XML_PATH_DEFAULT_HEIGHT, 0);
    }

    public function getCanCreateCourierWaybill()
    {
	    $ws = new Mbe_Shipping_Model_Ws();
		return $ws->getCustomerPermission('canCreateCourierWaybill');
    }

	public function isEnabledThirdPartyPickups() {
		$ws = new Mbe_Shipping_Model_Ws();
		return $ws->getCustomerPermission('enabledThirdPartyPickups');
	}

	public function getGelProximityMerchantCode() {
		$ws = new Mbe_Shipping_Model_Ws();
		$customer = $ws->getCustomer();
		return $customer->MerchantCode??null;
	}

	public function getGelProxymityApiKey() {
		$ws = new Mbe_Shipping_Model_Ws();
		$customer = $ws->getCustomer();
		return $customer->DeliveryPointApiKey??null;
	}

	public function getGelProxymityUrlEndUser() {
		return self::GEL_PROXIMITY_URL_END_USER ;
	}

	public function getPermissionEnabledTaxAndDuties() {
		$ws = new Mbe_Shipping_Model_Ws();
		return (bool)$ws->getCustomerPermission('enabledTaxAndDuties');
	}

	public function isEnabledTaxAndDuties() {
		return (bool)$this->getOption(self::XML_PATH_TAX_DUTIES_ENABLED);
	}

	public function setEnabledTaxAndDuties($value) {
		return $this->setOption(self::XML_PATH_TAX_DUTIES_ENABLED, $value);
	}

	public function getTaxAndDutiesMode() {
		return $this->getOption(self::XML_PATH_TAX_DUTIES_MODE, 1);
	}

	public function getTaxAndDutiesModeName() {
		switch ($this->getOption(self::XML_PATH_TAX_DUTIES_MODE, 1)) {
			case self::MBE_TAX_AND_DUTIES_DAP:
				return 'DAP';
			case self::MBE_TAX_AND_DUTIES_DDP:
				return 'DDP';
		}
	}

	public function addTaxAndDutiesToTotal() {
		return $this->isEnabledTaxAndDuties() && self::MBE_TAX_AND_DUTIES_DDP == $this->getTaxAndDutiesMode();
	}

	public function mustDisableTaxAndDuties() {
		// If the Customer cannot use tax and duties, it must be disabled
		// If the merchant can use the tax and duties functionality but the manual pickup is enabled, tax & duties must be disabled
		// If it's "service mapping" is used, tax & duties must be disabled
		return ( !$this->getPermissionEnabledTaxAndDuties() ||
				(
					$this->getPermissionEnabledTaxAndDuties()
		         && $this->getPickupRequestEnabled()
		         && $this->getPickupRequestMode() === self::MBE_PICKUP_REQUEST_MANUAL
				) ||
		          $this->isEnabledCustomMapping()
		);
	}

	/**
	 * Check the package weight and replace it if necessary,
	 * return the value using the woocommerce unit of measure
	 *
	 */
	protected function checkMaxWeight($baseWeight, $type)
	{
		$ws = new Mbe_Shipping_Model_Ws();
		if ( $this->getDefaultShipmentType() == "ENVELOPE" ) {
			$maxWeight = wc_get_weight(
				0.5,
				get_option( 'woocommerce_weight_unit' ),
				'kg'
			);
		} else {
			$maxWeight = wc_get_weight(
				$ws->getCustomerPermission( $type ),
				get_option( 'woocommerce_weight_unit' ),
				'kg'
			);
		}

		if ($maxWeight > 0 && $maxWeight < $baseWeight) {
			$baseWeight = $maxWeight;
		}
		return $baseWeight;
	}

	public function getMaxPackageWeight($storeId = null)
	{
		$result = $this->getOption(self::XML_PATH_MAX_PACKAGE_WEIGHT);
		return $this->checkMaxWeight($result, 'maxParcelWeight');
	}


    public function getMaxShipmentWeight()
    {
        $result = $this->getOption(self::XML_PATH_MAX_SHIPMENT_WEIGHT);
	    return $this->checkMaxWeight($result, 'maxShipmentWeight');
    }

    public function getHandlingType()
    {
        return $this->getOption(self::XML_PATH_HANDLING_TYPE);
    }

    public function getHandlingAction()
    {
        return $this->getOption(self::XML_PATH_HANDLING_ACTION);
    }

    public function getHandlingFee()
    {
	    $handlingFee = $this->getOption(self::XML_PATH_HANDLING_FEE);
	    return !empty($handlingFee)?$handlingFee:0;
    }

    public function getHandlingFeeRounding()
    {
        return $this->getOption(self::XML_PATH_HANDLING_FEE_ROUNDING);
    }

    public function getHandlingFeeRoundingAmount()
    {
        $result = 1;
        if ($this->getOption(self::XML_PATH_HANDLING_FEE_ROUNDING_AMOUNT) == 2) {
            $result = 0.5;
        }
        return $result;
    }

    public function getSallowspecific()
    {
        return $this->getOption(self::XML_PATH_SALLOWSPECIFIC);
    }

    public function getSpecificcountry()
    {
        return $this->getOption(self::XML_PATH_SPECIFICCOUNTRY);
    }

    public function getSortOrder()
    {
        return $this->getOption(self::XML_PATH_SORT_ORDER);
    }

    public function getMaximumTimeForShippingBeforeTheEndOfTheDay()
    {
        return $this->getOption(self::XML_PATH_MAXIMUM_TIME_FOR_SHIPPING_BEFORE_THE_END_OF_THE_DAY);
    }

    public function getSpecificerrmsg()
    {
        return $this->getOption(self::XML_PATH_SPECIFICERRMSG);
    }

    public function getWeightType()
    {
        return $this->getOption(self::XML_PATH_WEIGHT_TYPE);
    }

    public function getShipmentsClosureMode()
    {
        return $this->getOption(self::XML_PATH_SHIPMENTS_CLOSURE_MODE);
    }

    public function getShipmentsCreationMode()
    {
        return $this->getOption(self::XML_PATH_SHIPMENTS_CREATION_MODE);
    }

    public function getShipmentsClosureTime()
    {
        return $this->getOption(self::XML_PATH_SHIPMENTS_CLOSURE_TIME);
    }

	protected function updateOrderItemShippingMeta($orderId, $metaValue, $metaKey) {
		if (empty($orderId) || empty($metaValue) || empty($metaKey)) {
			return false;
		}

		$order = wc_get_order($orderId);
		$shippingItems = $order->get_items('shipping');

		if (empty($shippingItems)) {
			return false;
		}

		$isUpdated = false;
		// Using foreach to split item id and data, but there should be only one row
		foreach ($shippingItems as $orderItemId => $orderItemData) {
			$isUpdated = wc_update_order_item_meta(
				$orderItemId,
				$metaKey,
				$metaValue
			);
		}

		return $isUpdated;
	}

	protected function getOrderItemShippingMeta($orderId, $metaKey) {
		if (empty($orderId) || empty($metaKey)) {
			return false;
		}

		$order = wc_get_order($orderId);
		$shippingItems = $order->get_items('shipping');

		if (empty($shippingItems)) {
			return false;
		}

		$metaValue = false;
		// Using foreach to split item id and data, but there should be only one row
		foreach ($shippingItems as $orderItemId => $orderItemData) {
			$metaValue = wc_get_order_item_meta(
				$orderItemId,
				$metaKey,
			);
		}

		return $metaValue;
	}

	protected function deleteOrderItemShippingMeta($orderId, $metaKey) {
		if (empty($orderId) || empty($metaKey)) {
			return false;
		}

		$order = wc_get_order($orderId);
		$shippingItems = $order->get_items('shipping');

		if (empty($shippingItems)) {
			return false;
		}

		$isDeleted = false;
		// Using foreach to split item id and data, but there should be only one row
		foreach ($shippingItems as $orderItemId => $orderItemData) {
			$isDeleted = wc_delete_order_item_meta(
				$orderItemId,
				$metaKey,
			);
		}

		return $isDeleted;
	}

	public function setOrderDeliveryPointCustomData( $orderId, $value ) {
		return $this->updateOrderItemShippingMeta($orderId, $value, self::META_FIELD_DELIVERY_POINT_CUSTOM_DATA);
	}

	public function setOrderDeliveryPointShipment( $orderId, $value ) {
		return $this->updateOrderItemShippingMeta($orderId, $value, self::META_FIELD_DELIVERY_POINT_SHIPMENT);
	}

	public function getOrderDeliveryPointCustomData( $orderId ) {
		return $this->getOrderItemShippingMeta($orderId, self::META_FIELD_DELIVERY_POINT_CUSTOM_DATA);
	}

	public function getOrderDeliveryPointShipment( $orderId ) {
		return $this->getOrderItemShippingMeta($orderId, self::META_FIELD_DELIVERY_POINT_SHIPMENT);
	}

	public function getOrderDeliveryPointServices( $orderId ) {
		return $this->getOrderItemShippingMeta($orderId, self::META_FIELD_DELIVERY_POINT_SERVICES);
	}

//	public function getOrderDeliveryPointMolServices( $orderId ) {
//		return $this->getOrderItemShippingMeta($orderId, self::META_FIELD_DELIVERY_POINT_MOL_SERVICES);
//	}

	public function getPickupRequestMode() {
		return $this->getOption(self::XML_PATH_PICKUP_REQUEST_MODE);
	}

	public function getPickupRequestEnabled() {
		return $this->getOption(self::XML_PATH_PICKUP_REQUEST_ENABLED);
	}

	public function getOrderPickupCustomDataId( $orderId ) {
		return $this->getOrderItemShippingMeta($orderId, self::META_FIELD_PICKUP_CUSTOM_DATA_ID);
//		$order = New WC_Order($orderId);
//		// Using foreach to split item id and data, but there should be only one row
//		foreach ( $order->get_items( 'shipping' ) as $orderItemId => $orderItemData ) {
//			$customDataId = wc_get_order_item_meta( $orderItemId, Mbe_Shipping_Helper_Data::META_FIELD_PICKUP_CUSTOM_DATA_ID );
//		}
//		 return $customDataId;
	}

	public function getOrderPickupBatchId( $orderId ) {
		return $this->getOrderItemShippingMeta($orderId, self::META_FIELD_PICKUP_BATCH_ID);
//		$order = New WC_Order($orderId);
//		// Using foreach to split item id and data, but there should be only one row
//		foreach ( $order->get_items( 'shipping' ) as $orderItemId => $orderItemData ) {
//			$customDataId = wc_get_order_item_meta( $orderItemId, Mbe_Shipping_Helper_Data::META_FIELD_PICKUP_BATCH_ID);
//		}
//		return $customDataId;
	}

	public function setOrderPickupCustomDataId( $orderId, $value ) {
		return $this->updateOrderItemShippingMeta($orderId, $value, self::META_FIELD_PICKUP_CUSTOM_DATA_ID);
	}

	public function setOrderPickupBatchId( $orderId, $value ) {
		return $this->updateOrderItemShippingMeta($orderId, $value, self::META_FIELD_PICKUP_BATCH_ID);
	}

	public function deleteOrderPickupCustomDataId( $orderId ) {
		return $this->deleteOrderItemShippingMeta($orderId, self::META_FIELD_PICKUP_CUSTOM_DATA_ID);
//		$order = New WC_Order($orderId);
//		// Using foreach to split item id and data, but there should be only one row
//		foreach ( $order->get_items( 'shipping' ) as $orderItemId => $orderItemData ) {
//			$deleted = wc_delete_order_item_meta( $orderItemId, Mbe_Shipping_Helper_Data::META_FIELD_PICKUP_CUSTOM_DATA_ID );
//		}
//		return $deleted;
	}

	public function deleteOrderPickupBatchId( $orderId ) {
		return $this->deleteOrderItemShippingMeta($orderId, self::META_FIELD_PICKUP_BATCH_ID);
//		$order = New WC_Order($orderId);
//		// Using foreach to split item id and data, but there should be only one row
//		foreach ( $order->get_items( 'shipping' ) as $orderItemId => $orderItemData ) {
//			$deleted = wc_delete_order_item_meta( $orderItemId, Mbe_Shipping_Helper_Data::META_FIELD_PICKUP_BATCH_ID );
//		}
//		return $deleted;
	}

	public function detachOrderFromPickupBatch( $orderId ) {
		if($this->isPickupShipped($orderId)) {
			return false;
		}
		$this->deleteOrderPickupCustomDataId( $orderId );
		$this->deleteOrderPickupBatchId( $orderId );
		return true;
	}

	public function isPickupShipped( $post_id ) {
		if(empty($post_id)) {return false;}
		// $post_id = is_null($post_id) ? (int)$_GET['post'] : (int)$post_id;

		$order = wc_get_order($post_id);
		foreach ( $order->get_items( 'shipping' ) as $orderItemId => $orderItemData ) {
			$value = wc_get_order_item_meta( $orderItemId, Mbe_Shipping_Helper_Data::META_FIELD_IS_PICKUP_SHIPPING, true );
		}
		return !empty($value);
	}

	public function setIsPickupShipped( $orderId, $value = false ) {
		$order = wc_get_order($orderId);
		// Using foreach to split item id and data, but there should be only one row
		foreach ( $order->get_items( 'shipping' ) as $orderItemId => $orderItemData ) {
			if (!empty($orderItemId)) {
				wc_update_order_item_meta( $orderItemId, Mbe_Shipping_Helper_Data::META_FIELD_IS_PICKUP_SHIPPING, $value );
			} else {
				throw new Exception(esc_html__('Pickup info cannot be set in order items', 'mail-boxes-etc'));
			}
		}
		return !empty($value);
	}

	public function getLocalPickupDefaultData() {
		$pickupDefaultData = [];

		$pickupDefaultData['cutoff'] = $this->getOption(self::XML_PATH_PICKUP_OPTIONS_CUTOFF);
		$pickupDefaultData['notes'] = $this->getOption( self::XML_PATH_PICKUP_OPTIONS_NOTES );
		$pickupDefaultData['preferred_from'] = $this->getOption(self::XML_PATH_PICKUP_OPTIONS_DEFAULT_PICKUP_TIME . '_start');
		$pickupDefaultData['preferred_to'] = $this->getOption(self::XML_PATH_PICKUP_OPTIONS_DEFAULT_PICKUP_TIME . '_end');
		$pickupDefaultData['alternative_from'] = $this->getOption(self::XML_PATH_PICKUP_OPTIONS_ALTERNATIVE_PICKUP_TIME . '_start');
		$pickupDefaultData['alternative_to'] = $this->getOption(self::XML_PATH_PICKUP_OPTIONS_ALTERNATIVE_PICKUP_TIME . '_end');

		return $pickupDefaultData;
	}

	public function setLocalPickupDefaultData($pickupDefaultData) {

		$this->setOption(self::XML_PATH_PICKUP_OPTIONS_CUTOFF, strtolower($pickupDefaultData->Cutoff) );
		$this->setOption(self::XML_PATH_PICKUP_OPTIONS_NOTES, $pickupDefaultData->Notes );
        $this->setOption(self::XML_PATH_PICKUP_OPTIONS_DEFAULT_PICKUP_TIME . '_start', $pickupDefaultData->PreferredFrom );
		$this->setOption(self::XML_PATH_PICKUP_OPTIONS_DEFAULT_PICKUP_TIME . '_end', $pickupDefaultData->PreferredTo );
		$this->setOption(self::XML_PATH_PICKUP_OPTIONS_ALTERNATIVE_PICKUP_TIME . '_start', $pickupDefaultData->AlternativeFrom );
		$this->setOption(self::XML_PATH_PICKUP_OPTIONS_ALTERNATIVE_PICKUP_TIME . '_end', $pickupDefaultData->AlternativeTo );

		return true;

	}

	public function hasDefaultPickupAddress() {
		$ws = new Mbe_Shipping_Model_Ws();
		return !empty($ws->getDefaultPickupAddress());
	}

    public function isCreationAutomatically()
    {
        return $this->getShipmentsCreationMode() == self::MBE_CREATION_MODE_AUTOMATICALLY;
    }

    public function isClosureAutomatically()
    {
        return $this->getShipmentsClosureMode() == self::MBE_CLOSURE_MODE_AUTOMATICALLY;
    }

    public function hasTracking($post_id)
    {
        //$post_id = is_null($post_id) ? (int)$_GET['post'] : (int)$post_id;
	    if(empty($post_id)) {return false;}

	    if ( \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled() ) {
		    $order = wc_get_order($post_id);
		    $value = $order->get_meta(self::SHIPMENT_SOURCE_TRACKING_NUMBER);
	    } else {
		    $value = get_post_meta($post_id, self::SHIPMENT_SOURCE_TRACKING_NUMBER, true);
	    }
        return !empty($value);
    }


    public function round($value)
    {
        $result = $value;
        $handlingFeeRounding = $this->getHandlingFeeRounding();
        $handlingFeeRoundingAmount = $this->getHandlingFeeRoundingAmount();

        if ($handlingFeeRounding == 2) {

            if ($handlingFeeRoundingAmount == 1) {
                $result = round($value, 0);
            }
            else {
                $result = round($value, 2);
            }

        }
        elseif ($handlingFeeRounding == 3) {
            if ($handlingFeeRoundingAmount == 1) {
                $result = floor($value);
            }
            else {
                $result = floor($value * 2) / 2;
            }
        }
        elseif ($handlingFeeRounding == 4) {
            if ($handlingFeeRoundingAmount == 1) {
                $result = ceil($value);
            }
            else {
                $result = ceil($value * 2) / 2;
            }
        }
        return $result;
    }

    public function getNameFromLabel($label)
    {
        $name = $label;
        $name = strtolower($name);
        $name = str_replace(" ", "_", $name);
        $name = str_replace(" ", "_", $name);
        return $name;
    }

    public function getThresholdByShippingService($shippingService)
    {
        $shippingService = strtolower($shippingService);
        return $this->getOption(self::XML_PATH_THRESHOLD . "_" . $shippingService);
    }


    public function getTrackingStatus($trackingNumber)
    {
        $result = self::MBE_SHIPMENT_STATUS_OPEN;

        $mbeDir = $this->mbeUploadDir();
        $filePath = $mbeDir . DIRECTORY_SEPARATOR . 'MBE_' . $trackingNumber . "_closed.pdf";

        if (file_exists($filePath)) {
            $result = self::MBE_SHIPMENT_STATUS_CLOSED;
        }
        return $result;
    }

    public function isShippingOpen($shipmentId)
    {
        $result = true;
        $tracks = $this->getTrackings($shipmentId);
        foreach ($tracks as $track) {
            $result = $result && $this->isTrackingOpen($track);
        }
        return $result;
    }

    public function getFileNames($orderId)
    {
        $result = array();
	    if ( \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled() ) {
		    $order = wc_get_order($orderId);
		    $files = $order->get_meta(self::SHIPMENT_SOURCE_TRACKING_FILENAME);
	    } else {
		    $files = get_post_meta($orderId, self::SHIPMENT_SOURCE_TRACKING_FILENAME, true);
	    }
        if ($files != '') {
            if (strpos($files, self::MBE_SHIPPING_TRACKING_SEPARATOR) !== false) {
                $result = explode(self::MBE_SHIPPING_TRACKING_SEPARATOR, $files);
            }
            else {
                $result = explode(self::MBE_SHIPPING_TRACKING_SEPARATOR__OLD, $files);
            }
        }
        return $result;
    }

    public function getTrackings($shipmentId)
    {
	    if ( \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled() ) {
		    $order = wc_get_order($shipmentId);
		    $tracking = $order->get_meta(self::SHIPMENT_SOURCE_TRACKING_NUMBER);
	    } else {
		    $tracking = get_post_meta($shipmentId, self::SHIPMENT_SOURCE_TRACKING_NUMBER, true);
	    }

        if ( str_contains( $tracking, self::MBE_SHIPPING_TRACKING_SEPARATOR ) ) {
            $value = explode(self::MBE_SHIPPING_TRACKING_SEPARATOR, $tracking);

        }
        else {
            $value = explode(self::MBE_SHIPPING_TRACKING_SEPARATOR__OLD, $tracking);
        }

        return is_array($value) ? (array_filter($value, function ($value) {
            return $value !== '';
        })) : $value;
    }

    public function getTrackingsString($shipmentId)
    {
	    if ( \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled() ) {
		    $order = wc_get_order($shipmentId);
		    $result = $order->get_meta(self::SHIPMENT_SOURCE_TRACKING_NUMBER);
	    } else {
		    $result = get_post_meta($shipmentId, self::SHIPMENT_SOURCE_TRACKING_NUMBER, true);
	    }
        //compatibility replace
        if ( str_contains( $result, self::MBE_SHIPPING_TRACKING_SEPARATOR__OLD ) ) {
            $result = str_replace(self::MBE_SHIPPING_TRACKING_SEPARATOR__OLD, self::MBE_SHIPPING_TRACKING_SEPARATOR, $result);
        }
        return $result;
    }

    public function getTrackingSetting() {
	    return $this->getOption(self::XML_PATH_ADD_TRACK_ID);
    }

    public function isTrackingOpen($trackingNumber)
    {
        return $this->getTrackingStatus($trackingNumber) == self::MBE_SHIPMENT_STATUS_OPEN;
    }

    public function mbeUploadDir()
    {
        $value = wp_upload_dir();
        $path = $value['basedir'] . DIRECTORY_SEPARATOR . $this->_mbeMediaDir;
        $this->checkMbeDir($path);
        return $path;
    }

	public function mbeUploadUrl()
	{
		$value = wp_upload_dir();
		return $value['baseurl'] . DIRECTORY_SEPARATOR . $this->_mbeMediaDir;
	}

    public function mbeCsvUploadDir()
    {
        $result = $this->mbeUploadDir() . DIRECTORY_SEPARATOR . $this->_csvDir;
        $this->checkMbeDir($result);
        return $result;
    }

	public function generateSha256RandomString() {
		return hash('sha256', microtime() . mt_rand());
	}

	public function getMbeLogDir() {
		//Check the old log files and delete it if any
		$this->deleteOldLogs();

		$mbeLogDir = $this->getOption(self::XML_PATH_MBE_LOG_FOLDER_NAME);

		if(empty($mbeLogDir)) {
			$mbeLogDir = substr($this->generateSha256RandomString(), 0, 15);
			$this->setOption(self::XML_PATH_MBE_LOG_FOLDER_NAME, $mbeLogDir);
		}

		$mbeLogDirPath = MBE_ESHIP_PLUGIN_DIR . $mbeLogDir;
		$this->checkMbeDir( $mbeLogDirPath );
		return $mbeLogDirPath;
	}

    public function getMbeCsvUploadUrl()
    {
	    $wpUploadDir = wp_upload_dir();
        return $wpUploadDir['baseurl']. DIRECTORY_SEPARATOR . $this->_mbeMediaDir . '/' . $this->_csvDir;
    }

    public function getShipmentFilePath($shipmentIncrementId, $ext)
    {
        return $this->mbeUploadDir() . DIRECTORY_SEPARATOR . $shipmentIncrementId . "." . $ext;
    }

    public function getTrackingFilePath($trackingNumber)
    {
        $mbeDir = $this->mbeUploadDir();
        $filePath = $mbeDir . DIRECTORY_SEPARATOR . 'MBE_' . $trackingNumber . "_closed.pdf";
        return $filePath;
    }

    public function isMbeShippingCustomMapping($shippingMethod)
    {
    	if ($this->isEnabledCustomMapping()) {
		    $defaultMethods = WC()->shipping()->get_shipping_methods();
		    $customMapping  = [];
		    foreach ( $defaultMethods as $default_method ) {
			    $mapping = $this->getOption( 'mbe_custom_mapping_' . strtolower( $default_method->id ) );
			    if ( ! empty( $mapping ) ) {
				    $customMapping[] = strtolower( $default_method->id ) . ':' . $mapping;
			    }
		    }
		    if ( array_search( $shippingMethod, $customMapping ) !== false ) {
			    return true;
		    }
	    }
	    return false;
    }

    public function isMbeShipping($order)
    {
        $shippingMethod = $this->getShippingMethod($order)??'';
	    // Handle already shipped orders added with custom mapping, if custom mapping is currently disabled.
	    if ( \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled() ) {
		    $customMappingOrder = !empty($this->getTrackings($order->get_id())) && ($order->get_meta(woocommerce_mbe_tracking_admin::SHIPMENT_SOURCE_TRACKING_CUSTOM_MAPPING) === 'yes');
	    } else {
		    $customMappingOrder = !empty($this->getTrackings($order->get_id())) && (get_post_meta( $order->get_id(), woocommerce_mbe_tracking_admin::SHIPMENT_SOURCE_TRACKING_CUSTOM_MAPPING,true) === 'yes');
	    }
	    // Check if the method is an MBE one (old or new) or if is a custom mapped default one
	    if ( !empty($shippingMethod) && (
			    $this->isMBEShippingMethod( $shippingMethod )
			    || $this->isMbeShippingCustomMapping($shippingMethod)
			    || $customMappingOrder
		    )
        ) {
            return true;
        }
        return false;
    }

	/**
	 * @param $order
	 *
	 * @return false|string
	 */
    public function getShippingMethod($order)
    {
        if (version_compare(WC()->version, '2.1', '>=')) {
            $order_item_id = null;
            foreach ($order->get_items('shipping') as $key => $item) {
                $order_item_id = $key;
            }
	        try {
		        if ( $order_item_id ) {
			        $shippingMethod = wc_get_order_item_meta( $order_item_id, 'method_id', true );
			        if ( $this->isEnabledCustomMapping() ) {
				        $customMapping = $this->getOption( 'mbe_custom_mapping_' . $shippingMethod );
				        if ( ! empty( $customMapping ) ) {
					        $shippingMethod = $shippingMethod . ':' . $customMapping;
				        }
			        }
			        return $shippingMethod;
		        } else {
			        return false;
		        }
	        } catch (Exception $e) {
				return false;
	        }
        }
    }

    public function getServiceName($order)
    {
        if (version_compare(WC()->version, '2.1', '>=')) {
            foreach ($order->get_items('shipping') as $key => $item) {
                return $item['name'];
            }
        }
        else {
	        $orderId = $this->getOrderId($order);
	        if ( \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled() ) {
		        $order = wc_get_order($orderId);
		        return $order->get_meta('_shipping_method_title');
	        } else {
		        return get_post_meta($orderId, '_shipping_method_title', true);
	        }
        }
    }

	public function getOrderId($order)
	{
		if (version_compare(WC()->version, '3', '>=')) {
			$orderId = $order->get_id();
		}
		else {
			$orderId = $order->id;
		}
		return $orderId;
	}

    public function getProduct($id_product)
    {
        if (version_compare(WC()->version, '2.1', '>=')) {
            return new WC_Product($id_product);
        }
        else {
            return get_product($id_product);
        }
    }

    public function getProductVariation($id_product)
    {
        return new WC_Product_Variation($id_product);
    }

    public function getProductFromItem($item)
    {
        $product_id = $item['product_id'];
        $variation_id = $item['variation_id'];
        if ($variation_id) {
            $product = $this->getProductVariation($variation_id);
        }
        else {
            $product = $this->getProduct($product_id);
        }
        return $product;
    }

    public function getProductSkuFromItem($item)
    {
        $product = $this->getProductFromItem($item);
        $sku = $product->get_sku();
        return $sku;
    }

    public function getProductTitleFromItem($item)
    {
        $product = $this->getProductFromItem($item);
        if (version_compare(WC()->version, '3.0', '>=')) {
            $title = $product->get_name();
        }
        else {
            $title = $product->get_title();
        }

        if (version_compare(WC()->version, '3.0', '>=')) {
            $productType = $product->get_type();
        }
        else {
            $productType = $product->product_type;
        }

        $variationsString = '';
        if ($productType == 'variation') {
            if (version_compare(WC()->version, '3.0', '>=')) {
                $available_variations = $product->get_variation_attributes();
            }
            else {
                $available_variations = $product->variation_data;
            }
            foreach ($available_variations as $key => $value) {
                $attributeLabel = str_replace('attribute_', '', $key);
                if ($variationsString != '') {
                    $variationsString .= ', ';
                }
                $variationsString .= $attributeLabel . ': ' . $value;
            }
        }

        if ($variationsString) {
            $title .= ' (' . $variationsString . ')';
        }

        return $title;
    }

    public function getShipmentsCsv()
    {
        return $this->getOption(self::XML_PATH_SHIPMENTS_CSV);
    }

	public function getShipmentCsvTable()
	{
		global $wpdb;
		return $wpdb->prefix . self::MBE_CSV_RATES_TABLE_NAME;
	}

    public function getShipmentsCsvFile()
    {
        return $this->getOption(self::XML_PATH_SHIPMENTS_CSV_FILE);
    }

    public function getShipmentsCsvFileUrl()
    {
        $result = $this->getShipmentsCsvFile();
        $wrongName =  $this->mbeUploadDir(). DIRECTORY_SEPARATOR . 'csv'.$result;
        if (file_exists($wrongName)) { //check for file with "bugged" name and move it to the right folder
	        if ( !rename( $wrongName, $this->mbeCsvUploadDir() . DIRECTORY_SEPARATOR . $result ) ) {
		        return false;
	        }
        }
        if ($result) {
            $result = $this->MbeCsvUploadDir() . DIRECTORY_SEPARATOR . $result;
        }
        return $result;
    }

	public function getShipmentsCsvFileDir()
	{
		$result = $this->getShipmentsCsvFile();
		$wrongName =  $this->mbeUploadDir(). DIRECTORY_SEPARATOR . 'csv'.$result;
		if (file_exists($wrongName)) { //check for file with "bugged" name and move it to the right folder
			if ( !rename( $wrongName, $this->mbeCsvUploadDir() . DIRECTORY_SEPARATOR . $result ) ) {
				return false;
			}
		}
		return $this->MbeCsvUploadDir() . DIRECTORY_SEPARATOR . $result;
	}

    public function getShipmentsCsvTemplateFileUrl()
    {
	    $source = MBE_ESHIP_PLUGIN_DIR . self::MBE_CSV_RATES_TEMPLATE_CSV;
	    $dest = $this->mbeCsvUploadDir() . DIRECTORY_SEPARATOR . self::MBE_CSV_RATES_TEMPLATE_CSV;
		if ($this->templateFileCreate( $dest, $source ) === '') {
			return  '';
		}
	    return $this->getMbeCsvUploadUrl() . DIRECTORY_SEPARATOR . self::MBE_CSV_RATES_TEMPLATE_CSV;
    }

	public function getShipmentsCsvTemplateFileDir()
	{
		$source = MBE_ESHIP_PLUGIN_DIR . self::MBE_CSV_RATES_TEMPLATE_CSV;
		$dest = $this->mbeCsvUploadDir() . DIRECTORY_SEPARATOR . self::MBE_CSV_RATES_TEMPLATE_CSV;
		return $this->templateFileCreate( $dest, $source );
	}

	public function getLogWsPath()
	{
		return trailingslashit($this->getMbeLogDir()) . self::MBE_LOG_WS;
	}

	public function getLogPluginPath()
	{
		return trailingslashit($this->getMbeLogDir()) . self::MBE_LOG_PLUGIN;
	}

    public function isEnabledCustomMapping()
    {
	    return $this->getOption(self::XML_PATH_COURIER_CONFIG_MODE)==self::MBE_COURIER_MODE_MAPPING;
//	    return $this->getOption('mbe_enable_custom_mapping') === 'yes';
    }

    public function getShipmentsCsvMode()
    {
	    if ($this->getOption(self::XML_PATH_COURIER_CONFIG_MODE)<>self::MBE_COURIER_MODE_CSV ) {
		    return self::MBE_CSV_MODE_DISABLED;
	    } else {
		    return $this->getOption(self::XML_PATH_SHIPMENTS_CSV_MODE);
	    }
    }

    public function getShipmentsCsvInsuranceMin()
    {
        return floatval($this->getOption(self::XML_PATH_SHIPMENTS_CSV_INSURANCE_MIN));
    }

    public function getShipmentsCsvInsurancePercentage()
    {
        return floatval($this->getOption(self::XML_PATH_SHIPMENTS_CSV_INSURANCE_PERCENTAGE));
    }

    public function getShipmentsInsuranceMode()
    {
        return $this->getOption(self::XML_PATH_SHIPMENTS_INSURANCE_MODE);
    }

    public function getShippingMethodCustomLabel($methodCode)
    {
    	$customLabel = trim($this->getOption(self::XML_PATH_SHIPMENT_CUSTOM_LABEL . '_' . strtolower($methodCode)));
    	if (!empty($customLabel)) {
    		return $customLabel;
	    }
		return false;
    }

	public function getPackagesCsvFile()
	{
		return $this->getOption(self::XML_PATH_PACKAGES_CSV_FILE);
	}

	public function getPackagesCsv()
	{
		return $this->getOption(self::XML_PATH_PACKAGES_CSV);
	}

	public function getCurrentCsvPackagesDir()
	{
		$packagesCsv = $this->getPackagesCsvFile();
		if (empty($packagesCsv)) {
			return '';
		}
		return $this->MbeCsvUploadDir() . '/' . $packagesCsv;
	}

	public function getCsvTemplatePackagesDir()
	{
		$source = MBE_ESHIP_PLUGIN_DIR . self::MBE_CSV_PACKAGE_TEMPLATE;
		$dest = $this->mbeCsvUploadDir() . DIRECTORY_SEPARATOR . self::MBE_CSV_PACKAGE_TEMPLATE;
		return $this->templateFileCreate( $dest, $source );
	}

	public function getPackagesProductCsv()
	{
		return $this->getOption(self::XML_PATH_PACKAGES_PRODUCT_CSV);
	}
	public function getPackagesProductCsvFile()
	{
		return $this->getOption(self::XML_PATH_PACKAGES_PRODUCT_CSV_FILE);
	}

	public function getCurrentCsvPackagesProductDir()
	{
		$packagesProductCsv = $this->getPackagesProductCsvFile();
		if (empty($packagesProductCsv)) {
			return '';
		}
		return $this->MbeCsvUploadDir() . '/' . $packagesProductCsv;
	}

	public function getCsvTemplatePackagesProductDir()
	{
		$source = MBE_ESHIP_PLUGIN_DIR . self::MBE_CSV_PACKAGE_PRODUCT_TEMPLATE;
		$dest = $this->mbeCsvUploadDir() . DIRECTORY_SEPARATOR . self::MBE_CSV_PACKAGE_PRODUCT_TEMPLATE;
		return $this->templateFileCreate( $dest, $source );
	}

	public function isCsvStandardPackageEnabled($storeId = null)
	{
		return $this->getOption(self::XML_PATH_CSV_STANDARD_PACKAGE_USE_CSV);
	}

	public function disableCsvStandardPackage()
	{
		$this->setOption(self::XML_PATH_CSV_STANDARD_PACKAGE_USE_CSV, false);
	}

	public function useCsvStandardPackages()
	{
		$standardPackagesCount = count( $this->csv_package_model->getCsvPackages() );
		return $this->isCsvStandardPackageEnabled()
		       && $standardPackagesCount > 0
		       && Mbe_Shipping_Model_Carrier::SHIPMENT_CONFIGURATION_MODE_ONE_SHIPMENT_PER_SHOPPING_CART_WEIGHT_MULTI_PARCEL == $this->getShipmentConfigurationMode();
	}

	public function getCsvStandardPackageDefault($storeId = null)
	{
		return $this->getOption(self::XML_PATH_CSV_STANDARD_PACKAGE_DEFAULT);
	}

	public function setCsvStandardPackageDefault($value)
	{
		$this->setOption(self::XML_PATH_CSV_STANDARD_PACKAGE_DEFAULT, $value);
	}

	/**
	 * Return the CSV package information related to a specific product, if any
	 * Otherwise it returns the CSV standard package selected as the default one
	 *
	 * @param null $productSku
	 * @return array|null
	 *
	 */
	protected function getCsvPackageInfo($productSku)
	{
		$packagesInfo = $this->csv_package_model->getPackageInfobyProduct( $productSku );
		if (count($packagesInfo) <= 0) {
			$packagesInfo = $this->csv_package_model->getPackageInfobyId( $this->getCsvStandardPackageDefault() );
		}
		$packageInfoResult = $packagesInfo[0];
		$packageInfoResult['max_weight'] = $this->checkMaxWeight($packageInfoResult['max_weight'], 'maxParcelWeight');

		return $packageInfoResult;
	}

	protected function getSettingsPackageInfo($singleParcel)
	{
		return [
			'id' => null,
			'package_code' => self::MBE_CSV_PACKAGES_RESERVED_CODE,
			'package_label' => 'Package from settings',
			'height' => $this->getDefaultHeight(),
			'width' => $this->getDefaultWidth(),
			'length' => $this->getDefaultLength(),
			'max_weight' => $this->getMaxPackageWeight(),
			'single_parcel' => $singleParcel,
			'custom_package' => false
		];
	}

	/**
	 * @param $productSku
	 * @param false $singleParcel // All the packages based on settings (not CSV) must be set as "single parcels"
	 * @return array|null
	 */
	public function getPackageInfo($productSku, $singleParcel = false)
	{
		if ($this->useCsvStandardPackages()) {
			return $this->getCsvPackageInfo($productSku);
		} else {
			return $this->getSettingsPackageInfo($singleParcel);
		}
	}

	public function getStandardPackagesForSelect()
	{
		return $this->toSelectArray( $this->csv_package_model->getStandardPackages(), 'id', 'package_label' );
	}

	public function getBoxesArray(&$boxesArray, &$boxesSingleParcelArray, $itemWeight, $packageInfo)
	{
		$itemWeight = (float)$itemWeight;
		if ($packageInfo['single_parcel'] || $packageInfo['custom_package']) {
			if (!isset($boxesSingleParcelArray[$packageInfo['package_code']])) {
				$boxesSingleParcelArray[$packageInfo['package_code']] = $this->addEmptyBoxType($packageInfo);
			}
			$boxesSingleParcelArray[$packageInfo['package_code']]['weight'][] = $itemWeight;
		} else {
			$canAddToExistingBox = false;
			if (!isset($boxesArray[$packageInfo['package_code']])) {
				$boxesArray[$packageInfo['package_code']] = $this->addEmptyBoxType($packageInfo);
			}
			$boxesPackage = &$boxesArray[$packageInfo['package_code']]; // by ref to simplify the code
			$boxesCount = count($boxesPackage['weight']);
			for ($j = 0; $j < $boxesCount; $j++) {
				$newWeight = (float)$boxesPackage['weight'][$j] + $itemWeight;
				if ($newWeight <= (float)$packageInfo['max_weight']) {
					$canAddToExistingBox = true;
					$boxesPackage['weight'][$j] = $newWeight;
					break;
				}
			}
			if (!$canAddToExistingBox) {
				$boxesPackage['weight'][] = $itemWeight;
			}
		}
		return $boxesArray;
	}

	public function addEmptyBoxType($packageInfo)
	{
		return [
			'maxweight' => $packageInfo['max_weight'],
			'dimensions' => [
				'length' => $packageInfo['length'],
				'width' => $packageInfo['width'],
				'height' => $packageInfo['height']
			],
			'weight' => []
		];
	}

	/**
	 * This method returns an array for all the boxes grouped by box type
	 */
	public function mergeBoxesArray($boxes, $boxesSingleParcel)
	{
		foreach ($boxes as $key => $value) {
			if (isset($boxesSingleParcel[$key])) {
				foreach ($boxesSingleParcel[$key]['weight'] as $item) {
					$boxes[$key]['weight'][] = $item;
				}
				// remove the merged package
				unset($boxesSingleParcel[$key]);
			}
		}
		//  append all the remaining packages
		return $boxes+$boxesSingleParcel; // array union to keep the key = packageId
	}

	public function countBoxesArray($boxesArray)
	{
		$count = 0;
		$countArray = array_column($boxesArray, 'weight');
		foreach ($countArray as $box) {
			$count += count($box);
		}
		return $count;
	}

	public function totalWeightBoxesArray($boxesArray) {
		$totalWeight = 0;
		if ( is_array( $boxesArray ) ) {
			foreach ( $boxesArray as $box ) {
				$totalWeight += array_sum( $box['weight'] );
			}
		}
		return $totalWeight;
	}

	/**
	 * Compare all the dimensions for all the boxes and returns the biggest one
	 */
	public function longestSizeBoxesArray($boxesArray)
	{
		$sortArray = [];
		$dimensionsArray = array_column($boxesArray, 'dimensions');
		foreach ( $dimensionsArray as $item ) {
			rsort($item);
			$sortArray[] = $item[0];
		}
		rsort($sortArray);
		return $sortArray[0];
	}

	/**
	 * Convert a weight based on WordPress unit value to a custom unit
	 *
	 * @param $weight
	 * @param string $toUnit
	 *
	 * @return array|float|int
	 */
	public function convertWeight($weight, $toUnit = 'kg')
    {
	    if (is_array($weight)) {
		    foreach ($weight as $key=>$value) {
			    $weight[$key] = wc_get_weight($value, $toUnit);
		    }
		    return $weight;
	    } else {
		    return wc_get_weight($weight, $toUnit);
	    }

    }

	public function toSelectArray($result, $value = 'id', $label = 'label') {
		$listArray = [];
		foreach ( $result as $item ) {
			$listArray[ $item[$value] ] = $item[$label];
		}
		return $listArray;
	}

	protected function getMaxWeightByType($type) {
		$ws = new Mbe_Shipping_Model_Ws();
		if ( $this->getDefaultShipmentType() == "ENVELOPE" ) {
			$maxParcelWeight = wc_get_weight(
				0.5,
				get_option( 'woocommerce_weight_unit' ),
				'kg'
			);
		} else {
			$maxParcelWeight = wc_get_weight(
				$ws->getCustomerPermission( $type ),
				get_option( 'woocommerce_weight_unit' ),
				'kg'
			);
		}

		return $maxParcelWeight;
	}

	protected function templateFileCreate( string $dest, string $source ): string {
		if ( ! file_exists( $dest ) || ( file( $dest ) !== file( $source ) ) ) {
			if ( ! copy( $source, $dest ) ) {
				return '';
			}
		}
		return $dest;
	}


	public function isOnlineMBE()
	{
		return (strpos(strtolower($this->getWsUrl()),'onlinembe') !== false);
	}

	public function isReturned($post_id) {
		if(empty($post_id)) {return false;}
		if ( \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled() ) {
			$order = wc_get_order($post_id);
			return $order->get_meta(self::SHIPMENT_SOURCE_RETURN_TRACKING_NUMBER);
		} else {
			return !empty(get_post_meta($post_id, self::SHIPMENT_SOURCE_RETURN_TRACKING_NUMBER, true));
		}
	}

	public function arrayKeyFirst($array) {
		if (!function_exists('array_key_first')) {
			function array_key_first(array $array) {
				foreach($array as $key => $unused) {
					return $key;
				}
				return NULL;
			}
		} else {
			return array_key_first($array);
		}
	}

	public function select_mbe_ids()
	{
		// TODO filter out status wc-checkout-draft
		global $wpdb;
		$postmetaTableName = $wpdb->prefix . 'postmeta';
		$shippingMethods = MBE_ESHIP_ID.'|wf_mbe_shipping'; // search also for orders created with the old plugin

		if (version_compare(WC()->version, '2.1', '>=')) {
			return "SELECT order_id FROM {$wpdb->prefix}woocommerce_order_items AS oi INNER JOIN $wpdb->order_itemmeta AS oim ON oi.order_item_id = oim.order_item_id WHERE oi.order_item_type = 'shipping' AND oim.meta_key = 'method_id' AND oim.meta_value REGEXP '{$shippingMethods}'";
		}
		else {
			return "SELECT post_id FROM {$postmetaTableName} AS pm WHERE pm.meta_key = '_shipping_method' AND pm.meta_value REGEXP '{$shippingMethods}'";
		}
	}

	public function select_custom_mapping_ids()
	{
		// TODO filter out status wc-checkout-draft from main table
		if ( \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled() ) {
			global $wpdb;
			return "SELECT DISTINCT order_id FROM {$wpdb->prefix}wc_orders_meta WHERE meta_key = '".woocommerce_mbe_tracking_admin::SHIPMENT_SOURCE_TRACKING_CUSTOM_MAPPING."' AND meta_value = 'yes'";
		} else {
			// get orders with custom mapped shipping method
			$customMappingFilter = array(
				'post_type' => 'shop_order',
				'post_status' => 'wc-%',
				'nopaging' => 'true',
				'fields' => 'ids',
				'meta_key' => woocommerce_mbe_tracking_admin::SHIPMENT_SOURCE_TRACKING_CUSTOM_MAPPING,
				'meta_value' => 'yes',
			);

			$sqlFilter = new WP_Query($customMappingFilter);
			return $sqlFilter->request;
		}

	}

	/**
	 * Generate the SQL SELECT string to retrieve the orders related to a Pickup Batch
	 * if the parameter is null the function returns all the orders
	 *
	 * @param $batchId
	 *
	 * @return string
	 */
	public function select_pickup_batch_orders_ids($batchId = null) {
		global $wpdb;
		$filterId = " AND oim.meta_value " . ($batchId?" = '" . $batchId ."'":"IS NOT NULL");

		return "SELECT order_id FROM {$wpdb->prefix}woocommerce_order_items AS oi INNER JOIN $wpdb->order_itemmeta AS oim ON oi.order_item_id = oim.order_item_id
                WHERE oim.meta_key = '".self::META_FIELD_PICKUP_BATCH_ID."' {$filterId} ";
	}

	/**
	 * Generate the SQL SELECT string to retrieve the orders related to a Pickup
	 * if the parameter is null the function returns all the orders
	 *
	 * @param $pickpCustomDataId
	 *
	 * @return string
	 */
	public function select_pickup_orders_ids($pickpCustomDataId = null) {
		global $wpdb;
		$filterId = " AND oim.meta_value " . ($pickpCustomDataId? " = '" . $pickpCustomDataId . "'":"IS NOT NULL");

		return "SELECT order_id FROM {$wpdb->prefix}woocommerce_order_items AS oi INNER JOIN $wpdb->order_itemmeta AS oim ON oi.order_item_id = oim.order_item_id 
                WHERE oim.meta_key = '".self::META_FIELD_PICKUP_CUSTOM_DATA_ID."' {$filterId} ";
	}

	public function checkStartEndTime($start, $end){
		$timeStart = new DateTime($start);
		$timeEnd = new DateTime($end);

		if ($timeEnd <= $timeStart) {
			return false;
		}
		return true;
	}

	/**
	 *  Queue a new admin message to the existing list
	 *
	 * @param array $message
	 *
	 * @return void
	 */
	public function setWpAdminMessages(array $message = [ 'message' => '', 'status'   => 'info' ]) {
		$wpAdminMessage = maybe_unserialize( get_option( 'mbe_shipping_wp_admin_messages' , []) )??[];
		$wpAdminMessage[] = $message;
		update_option( 'mbe_shipping_wp_admin_messages', serialize($wpAdminMessage) );
	}

	public function getDeliveryPointServices( $estimateShipping ) {
		$lowestPriceDeliveryService = null;
		$courierServices            = [];
		$molServices                = [];
		$priceForMap                = [];

		foreach ( $estimateShipping as $shippingOption ) {
			if ( $this->isEnabledDeliveryPointService( $shippingOption ) ) {
				$molServices[] = $shippingOption->Service;
				$courierServices[] = $shippingOption->CourierService;
				$priceForMap[] = $shippingOption->NetShipmentTotalPrice;
				$lowestPriceDeliveryService = $this->getLowestPriceService( $shippingOption, $lowestPriceDeliveryService );
			}
		}

		if ( $lowestPriceDeliveryService ) {
			$lowestPriceDeliveryService->MolService = $molServices;
			$lowestPriceDeliveryService->CourierService = $courierServices;
			$lowestPriceDeliveryService->PriceForMap = $priceForMap;
			$lowestPriceDeliveryService->Service = MBE_DELIVERY_POINT_SERVICE;
			$lowestPriceDeliveryService->ServiceDesc = MBE_DELIVERY_POINT_DESCRIPTION;
		}

		return $lowestPriceDeliveryService;
	}

	public function isEnabledDeliveryPointService( $shippingOption ) {
		return in_array( $shippingOption->Service, MBE_ESTIMATE_DELIVERY_POINT_SERVICES ) && in_array( $shippingOption->Service, $this->getAllowedShipmentServicesArray() );
	}

	public function getLowestPriceService( $currentOption, $existingOption ) {
		if ( empty( $existingOption ) || $currentOption->NetShipmentTotalPrice < $existingOption->NetShipmentTotalPrice ) {
			return $currentOption;
		}

		return $existingOption;
	}

	public function isDeliveryPointOrder( $post_id ) {
		if(empty($post_id)) {return false;}
		return $this->getOrderDeliveryPointShipment($post_id) == 'Yes';
	}

	public function logErrorAndSetWpAdminMessage($errMsg, $logger = null)
	{
		if (!empty($logger)) $logger->log( $errMsg );
		$this->setWpAdminMessages([
			'message' => urlencode( $errMsg ),
			'status'  => urlencode( 'error' )
		]);
	}

	public function logErrorAndSetWCAdminMessage($errMsg, $logger = null)
	{
		if (!empty($logger)) $logger->log( $errMsg );
		WC_Admin_Settings::add_error(MBE_ESHIP_PLUGIN_NAME . ' - ' . $errMsg);
	}

	public function mbe_download_file( $filePath, $fileName = '', $fileType = 'text/csv', $deleteAfter = false ) {
		$fileNamefromPath = [];
		if ( $fileName === '' && preg_match( '/(?P<filename>[\w\-. ]+)$/', $filePath, $fileNamefromPath ) ) {
			$fileName = $fileNamefromPath['filename'] ?? 'mbe_download_file.csv';
		}
		try {
			if ( is_file( $filePath ) ) {
				header( 'Content-Description: File Transfer' );
				header( 'Content-Type: ' . $fileType );
				header( "Content-Disposition: attachment; filename=" . $fileName );
				header( 'Content-Transfer-Encoding: binary' );
				header( 'Expires: 0' );
				header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
				header( 'Pragma: public' );
				header( 'Content-Length: ' . filesize( $filePath ) );
				ob_clean();
				flush();
				readfile( $filePath );
				if ( $deleteAfter ) {
					wp_delete_file( $filePath );
				}
				exit;
			}
			error_log( __( 'MBE Download file - files not found' ) );
		} catch ( \Exception $e ) {
			error_log( __( 'MBE Download file - Unexpected error' ) . ' - ' . $e->getMessage() );
		}
		exit;
	}

	/**
	 * @param string $shippingMethod
	 *
	 * @return false|int
	 */
	public function isMBEShippingMethod( $shippingMethod ) {
		if (is_array($shippingMethod)) {
			$shippingMethod = $shippingMethod[0];
		}
		return preg_match( '/' . MBE_ESHIP_ID . '|wf_mbe_shipping/', $shippingMethod??'' );
	}

	/**
	 * @param $grossPrice
	 *
	 * @return float|int
	 */
	public function calculateNetPriceFromGross( $grossPrice, $taxPercentage ) {
		return $grossPrice / ( 1 + ( $taxPercentage / 100 ) );
	}

	public function createHtaccessDenyAll( $filePath ) {
		$content = 'deny from all';
		if(file_put_contents(trailingslashit($filePath) . '.htaccess', $content) === false) {
			return false;
		}

		return true;

	}

	/**
	 * @return void
	 */
	protected function deleteOldLogs(): void
	{
		if (file_exists(MBE_ESHIP_PLUGIN_OLD_LOG_DIR . DIRECTORY_SEPARATOR . 'mbe_ws.log')) {
			wp_delete_file(MBE_ESHIP_PLUGIN_OLD_LOG_DIR . DIRECTORY_SEPARATOR . 'mbe_ws.log');
		}
		if (file_exists(MBE_ESHIP_PLUGIN_OLD_LOG_DIR . DIRECTORY_SEPARATOR . 'mbe_shipping.log')) {
			wp_delete_file(MBE_ESHIP_PLUGIN_OLD_LOG_DIR . DIRECTORY_SEPARATOR . 'mbe_shipping.log');
		}
		if (file_exists(MBE_ESHIP_PLUGIN_OLD_LOG_DIR . DIRECTORY_SEPARATOR . '.htaccess')) {
			wp_delete_file(MBE_ESHIP_PLUGIN_OLD_LOG_DIR . DIRECTORY_SEPARATOR . '.htaccess');
		}
		if (file_exists(MBE_ESHIP_PLUGIN_OLD_LOG_DIR)) {
			rmdir(MBE_ESHIP_PLUGIN_OLD_LOG_DIR);
		}
	}

}