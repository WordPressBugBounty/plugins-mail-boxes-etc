<?php
/*
	Plugin Name: MBE eShip
	Description: Mail Boxes Etc. Online MBE Plugin integration for main Ecommerce platforms.
	Version: 2.2.4
	Author: MBE Worldwide S.p.A.
	Author URI: https://www.mbeglobal.com/
	Text Domain: mail-boxes-etc
    WC requires at least: 6.2
    WC tested up to: 8.2
	Domain Path: /languages
*/

if ( ! defined( 'MBE_ESHIP_ID' ) ) {
	define( "MBE_ESHIP_ID", "mbe_eship" );
}

if ( ! defined( 'MBE_ESHIP_PLUGIN_DIR' ) ) {
	define( "MBE_ESHIP_PLUGIN_DIR", plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'MBE_ESHIP_PLUGIN_URL' ) ) {
	define( "MBE_ESHIP_PLUGIN_URL", plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'MBE_ESHIP_PLUGIN_OLD_LOG_DIR' ) ) {
	define( "MBE_ESHIP_PLUGIN_OLD_LOG_DIR", MBE_ESHIP_PLUGIN_DIR . 'log' );
}

if ( ! function_exists( 'get_plugin_data' ) ) {
	defined( 'ABSPATH' ) || exit;
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}
$plugin_data = get_plugin_data( __FILE__, false, false );

/** Plugin version */
if ( ! defined( 'MBE_ESHIP_PLUGIN_VERSION' ) ) {
	define( "MBE_ESHIP_PLUGIN_VERSION", $plugin_data['Version'] );
}

/** Plugin Name */
if ( ! defined( 'MBE_ESHIP_PLUGIN_NAME' ) ) {
	define( "MBE_ESHIP_PLUGIN_NAME", $plugin_data['Name'] );
}

/** MBE Elink Database version */
if ( ! defined( 'MBE_ESHIP_DATABASE_VERSION_OPTION' ) ) {
	define( 'MBE_ESHIP_DATABASE_VERSION_OPTION', 'mbe_eship_db_version' );
}
if ( ! defined( 'MBE_ESHIP_DATABASE_VERSION' ) ) {
	define( 'MBE_ESHIP_DATABASE_VERSION', '1.2.0' );
}

if ( ! defined( 'MBE_E_LINK_DEBUG_LOG' ) ) {
	define( 'MBE_E_LINK_DEBUG_LOG', true );
}

/** MBE GEL Proximity **/
if ( ! defined( 'MBE_ESTIMATE_DELIVERY_POINT_LABELING_SERVICES' ) ) {
	define( 'MBE_ESTIMATE_DELIVERY_POINT_LABELING_SERVICES', ['NMDP'] );
}

if ( ! defined( 'MBE_ESTIMATE_DELIVERY_POINT_PRENEGOTIATED_SERVICES' ) ) {
	define( 'MBE_ESTIMATE_DELIVERY_POINT_PRENEGOTIATED_SERVICES', ['GPP'] );
}

if ( ! defined( 'MBE_ESTIMATE_DELIVERY_POINT_SERVICES' ) ) {
	define( 'MBE_ESTIMATE_DELIVERY_POINT_SERVICES', array_merge(MBE_ESTIMATE_DELIVERY_POINT_LABELING_SERVICES,MBE_ESTIMATE_DELIVERY_POINT_PRENEGOTIATED_SERVICES));
}

if ( ! defined( 'MBE_ESTIMATE_DELIVERY_POINT_SERVICES_REGEXP' ) ) {
	define( 'MBE_ESTIMATE_DELIVERY_POINT_SERVICES_REGEXP', '/' . (implode('|', MBE_ESTIMATE_DELIVERY_POINT_SERVICES)) . '/');
}

if ( ! defined( 'MBE_DELIVERY_POINT_SERVICE' ) ) {
	define( 'MBE_DELIVERY_POINT_SERVICE', 'MDPS' );
}
if ( ! defined( 'MBE_DELIVERY_POINT_SERVICE_METHOD' ) ) {
	define( 'MBE_DELIVERY_POINT_SERVICE_METHOD', MBE_ESHIP_ID.':' . MBE_DELIVERY_POINT_SERVICE );
}
if ( ! defined( 'MBE_DELIVERY_POINT_DESCRIPTION' ) ) {
	define( 'MBE_DELIVERY_POINT_DESCRIPTION', 'Delivery Point' );
}
if ( ! defined( 'MBE_DELIVERY_POINT_WEIGHT_LIMIT' ) ) {
	define( 'MBE_DELIVERY_POINT_WEIGHT_LIMIT', 35 );
}

if ( ! defined( 'MBE_SCHEMA_FLAG_OPTION' ) ) {
    define('MBE_SCHEMA_FLAG_OPTION', MBE_ESHIP_ID . '_' . 'need_default_values');
}

if ( ! defined( 'WOOCOMMERCE_MBE_TABS_PAGE' ) ) {
	define('WOOCOMMERCE_MBE_TABS_PAGE', 'woocommerce_mbe_tabs');
}

if ( ! defined( 'WOOCOMMERCE_MBE_TABS_PICKUP_PAGE' ) ) {
	define('WOOCOMMERCE_MBE_TABS_PICKUP_PAGE', 'pickup_batches_tabs');
}

require_once( MBE_ESHIP_PLUGIN_DIR . '/lib/Helper/Data.php' );
require_once( MBE_ESHIP_PLUGIN_DIR . '/lib/Helper/Logger.php' );
//require_once(MBE_E_LINK_PLUGIN_DIR . '/lib/Helper/Tracking.php');
require_once( MBE_ESHIP_PLUGIN_DIR . '/lib/Helper/Csv.php' );
require_once( MBE_ESHIP_PLUGIN_DIR . '/lib/Helper/Rates.php' );
require_once( MBE_ESHIP_PLUGIN_DIR . '/lib/Traits/Mbe_Entity_Model_Trait.php' );
require_once( MBE_ESHIP_PLUGIN_DIR . '/lib/Traits/ShipmentActions.php' );
require_once( MBE_ESHIP_PLUGIN_DIR . '/lib/Interfaces/EntityModelInterface.php' );
require_once( MBE_ESHIP_PLUGIN_DIR . '/lib/Factories/CsvEntityFactory.php' );
require_once( MBE_ESHIP_PLUGIN_DIR . '/lib/Factories/CsvEditorEntityFactory.php' );
require_once( MBE_ESHIP_PLUGIN_DIR . '/lib/Model/CsvShipping.php' );
require_once( MBE_ESHIP_PLUGIN_DIR . '/lib/Model/CsvPackage.php' );
require_once( MBE_ESHIP_PLUGIN_DIR . '/lib/Model/CsvPackageProduct.php' );
require_once( MBE_ESHIP_PLUGIN_DIR . '/lib/Model/CsvPickupAddress.php' );
require_once( MBE_ESHIP_PLUGIN_DIR . '/lib/Model/PickupCustomData.php' );

require_once( MBE_ESHIP_PLUGIN_DIR . '/lib/Mbe/MbeWs.php' );
require_once( MBE_ESHIP_PLUGIN_DIR . '/lib/Mbe/MbeSoapClient.php' );
require_once( MBE_ESHIP_PLUGIN_DIR . '/lib/Model/Ws.php' );
require_once( MBE_ESHIP_PLUGIN_DIR . '/lib/Model/Carrier.php' );
require_once( MBE_ESHIP_PLUGIN_DIR . '/lib/Exceptions/MbeExceptions.php' );
require_once( MBE_ESHIP_PLUGIN_DIR . '/backward_compatibility/array_column.php' );

//if ( ! class_exists( '\Dompdf\Dompdf' ) ) {
//	require_once( MBE_ESHIP_PLUGIN_DIR . '/lib/dompdf/autoload.inc.php' );
//}

require_once( MBE_ESHIP_PLUGIN_DIR . '/lib/vendor/autoload.php' );

function mbe_eship_is_old_elink_plugin_active() {
	$activePlugins = array_keys( get_plugins() );
	$oldPlugin     = array_filter( $activePlugins, function ( $item ) {
		return preg_match( '/mail-boxes-etc.php/', $item );
	} );

	return is_plugin_active( array_values( $oldPlugin )[0] );
}

/**
 * Plugin activation check
 */
function mbe_eship_activation_check() {
	if ( ! class_exists( 'SoapClient' ) ) {
		deactivate_plugins( basename( __FILE__ ) );
		wp_die( 'Sorry, but you cannot run this plugin, it requires the <a href="http://php.net/manual/en/class.soapclient.php">SOAP</a> support on your server/hosting to function.' );
	}
}

/**
 * Transfer the settings from the old plugin to the new ones
 * @return void
 */
function mbe_eship_update_new_settings_check() {
	global $wpdb;
	$logger = new Mbe_Shipping_Helper_Logger();

	$schemaFlagOption          = MBE_ESHIP_ID . '_' . 'need_new_settings';
	$oldOption                 = get_option( Mbe_Shipping_Helper_Data::MBE_ELINK_SETTINGS );
	$mbeNeedUpdate             = ! ( get_option( $schemaFlagOption ) === 'no' ) && ! empty( $oldOption );
	// To check if plugin was already configured but options weren't migrated yet
	$notConfigured             = empty(get_option(MBE_ESHIP_ID . '_' .Mbe_Shipping_Helper_Data::XML_PATH_ALLOWED_SHIPMENT_SERVICES));
	$csv_rates_model           = new Mbe_Shipping_Model_Csv_Shipping();
	$csv_package_model         = new Mbe_Shipping_Model_Csv_Package();
	$csv_package_product_model = new Mbe_Shipping_Model_Csv_Package_Product();

	$tableExist = $csv_rates_model->tableExists() && $csv_package_model->tableExists() && $csv_package_product_model->tableExists();

	if ( $mbeNeedUpdate && $tableExist && $notConfigured ) {

		$logger->log( 'Migrating from MBE e-Link --- Starting', true );
//		try {

		$logger->log( 'Migrating from MBE e-Link --- Options', true );
		foreach ( $oldOption as $key => $value ) {
			update_option( MBE_ESHIP_ID . '_' . $key, $value );
		}

		$logger->log( 'Migrating from MBE e-Link --- Login', true );
		$wsUser = $oldOption[ Mbe_Shipping_Helper_Data::XML_PATH_WS_USERNAME ];
		$wsPwd  = $oldOption[ Mbe_Shipping_Helper_Data::XML_PATH_WS_PASSWORD ];

		// Set advanced login mode
		if ( ! empty( $wsUser ) && ! empty( $wsPwd ) ) {
			update_option( MBE_ESHIP_ID . '_' . Mbe_Shipping_Helper_Data::XML_PATH_LOGIN_MODE, Mbe_Shipping_Helper_Data::MBE_LOGIN_MODE_ADVANCED );
			update_option( MBE_ESHIP_ID . '_' . Mbe_Shipping_Helper_Data::XML_PATH_LOGIN_LINK_ADV, true );
		}

		$logger->log( 'Migrating from MBE e-Link --- Configuration mode', true );
		$csvMode = $oldOption[ Mbe_Shipping_Helper_Data::XML_PATH_SHIPMENTS_CSV_MODE ];
		if ( $csvMode <> Mbe_Shipping_Helper_Data::MBE_CSV_MODE_DISABLED ) {
			update_option( MBE_ESHIP_ID . '_' . Mbe_Shipping_Helper_Data::XML_PATH_COURIER_CONFIG_MODE, Mbe_Shipping_Helper_Data::MBE_COURIER_MODE_CSV );
		} else if ( $oldOption['mbe_enable_custom_mapping'] === 'yes' ) {
			update_option( MBE_ESHIP_ID . '_' . Mbe_Shipping_Helper_Data::XML_PATH_COURIER_CONFIG_MODE, Mbe_Shipping_Helper_Data::MBE_COURIER_MODE_MAPPING );
		} else {
			update_option( MBE_ESHIP_ID . '_' . Mbe_Shipping_Helper_Data::XML_PATH_COURIER_CONFIG_MODE, Mbe_Shipping_Helper_Data::MBE_COURIER_MODE_SERVICES );
		}

		// Migrate CSV tables
		$logger->log( 'Migrating from MBE e-Link --- Csv Tables', true );
		$csvTables = [
			$csv_rates_model->getTableName()           => 'mbeshippingrate',
			$csv_package_model->getTableName()         => 'mbe_shipping_standard_packages',
			$csv_package_product_model->getTableName() => 'mbe_shipping_standard_package_product',
		];

		foreach ( $csvTables as $key => $value ) {
            // Check if old table exists
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.SchemaChange
			if ( $wpdb->get_var( $wpdb->prepare("SHOW TABLES LIKE %s", $wpdb->prefix . $value  )) == $wpdb->prefix . $value  ) {
				$wpdb->query( $wpdb->prepare("INSERT INTO %i SELECT * FROM %i", $key, $wpdb->prefix . $value )); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.SchemaChange
			}
		}

		update_option( $schemaFlagOption, 'no' );

		$logger->log( 'Migrating from MBE e-Link --- Complete', true );
//		} catch ( Exception $e ) {
		//
//		}
	} else if (empty($oldOption)) {
		$logger->log('Migrating from MBE e-Link --- Old options are missing ');
	} else if ($mbeNeedUpdate && !$notConfigured) {
		$logger->log('Migrating from MBE e-Link --- No migration, Plugin already configured ');
	}
}

function mbe_eship_check_log_folder_htaccess() {
	$helper   = new Mbe_Shipping_Helper_Data();
	$dirPath  = trailingslashit( $helper->getMbeLogDir() );
	$filePath = $dirPath . '.htaccess';
	if(!file_exists($filePath)) {
		$helper->createHtaccessDenyAll($dirPath);
	}
}

function mbe_eship_uninstall_db_options() {
	global $wpdb;
	$csv_rates_model           = new Mbe_Shipping_Model_Csv_Shipping();
	$csv_package_model         = new Mbe_Shipping_Model_Csv_Package();
	$csv_package_product_model = new Mbe_Shipping_Model_Csv_Package_Product();
    $pickup_custom_data_model  = new Mbe_Shipping_Model_Pickup_Custom_Data();

	// Delete all the tables
	$tables = [
		$csv_rates_model->getTableName(),
		$csv_package_model->getTableName(),
		$csv_package_product_model->getTableName(),
        $pickup_custom_data_model->getTableName(),
	];

	// Remove "old plugin" tables too if any
	array_push( $tables,
		$wpdb->prefix . 'mbe_shipping_standard_package_product',
		$wpdb->prefix . 'mbe_shipping_standard_packages',
		$wpdb->prefix . 'mbeshippingrate' );

	foreach ( $tables as $table ) {
		$wpdb->query( $wpdb->prepare("DROP TABLE IF EXISTS %i", $table) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.SchemaChange
	}

	// Delete all the options
	$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '" . MBE_ESHIP_ID . "\_%'" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.SchemaChange

	// Delete the "old plugin" db version option, since we are removing the tables
	delete_option( 'mbe_elink_db_version' );

    // Remove all pickup meta items from the orders
    $queryMeta = $wpdb->prepare("DELETE FROM $wpdb->order_itemmeta where meta_key in (%s, %s, %s)",Mbe_Shipping_Helper_Data::META_FIELD_IS_PICKUP_SHIPPING ,Mbe_Shipping_Helper_Data::META_FIELD_PICKUP_BATCH_ID, Mbe_Shipping_Helper_Data::META_FIELD_PICKUP_CUSTOM_DATA_ID );
	$wpdb->query( $queryMeta ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.SchemaChange

}

function mbe_eship_install_db() {

	$helper         = new Mbe_Shipping_Helper_Data();
	$currentVersion = get_option( MBE_ESHIP_DATABASE_VERSION_OPTION ) ?: '0';

	if ( version_compare( $currentVersion, MBE_ESHIP_DATABASE_VERSION, '<' ) ) {
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		$dbDelta = dbDelta( get_mbe_elink_db_schema() );

		// ADD conditions to verify if the database has been correctly updated accordingly to the last schema changes
		// If only a new table is added , $updateCondition can be kept as "true" but mbe_elink_db_tables_exist() must be updated
		$updateCondition = true;

		if ( mbe_elink_db_tables_exist() && $updateCondition ) {
			update_option( MBE_ESHIP_DATABASE_VERSION_OPTION, MBE_ESHIP_DATABASE_VERSION );
		}
	}
}

function mbe_elink_db_tables_exist() {
	$csv_rates_model           = new Mbe_Shipping_Model_Csv_Shipping();
	$csv_package_model         = new Mbe_Shipping_Model_Csv_Package();
	$csv_package_product_model = new Mbe_Shipping_Model_Csv_Package_Product();
	$pickup_custom_data_model  = new Mbe_Shipping_Model_Pickup_Custom_Data();

	return $csv_rates_model->tableExists()
	       && $csv_package_model->tableExists()
	       && $csv_package_product_model->tableExists()
           && $pickup_custom_data_model->tableExists();
}

function get_mbe_elink_db_schema() {
	$csv_rates_model           = new Mbe_Shipping_Model_Csv_Shipping();
	$csv_package_model         = new Mbe_Shipping_Model_Csv_Package();
	$csv_package_product_model = new Mbe_Shipping_Model_Csv_Package_Product();
	$pickup_custom_data_model  = new Mbe_Shipping_Model_Pickup_Custom_Data();

	return $csv_rates_model->getSqlCreate()
           . $csv_package_model->getSqlCreate()
           . $csv_package_product_model->getSqlCreate()
           . $pickup_custom_data_model->getSqlCreate();
}

function mbe_eship_update_db_check() {
	$action = $_REQUEST['action'] ?? '';
	if ( ! empty( $action ) && $action !== 'deactivate' ) {
		mbe_eship_install_db();
	}
}

function declare_compatibility() {
//    HPOS
	if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	}
//    BLOCK CHECKOUT
	if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', __FILE__, false );
	}
}

register_uninstall_hook( __FILE__, 'mbe_eship_uninstall_db_options' );
register_activation_hook( __FILE__, 'mbe_eship_activation_check' );
register_activation_hook( __FILE__, 'mbe_eship_install_db' );
//register_activation_hook( __FILE__, 'mbe_eship_create_options_default' );
//add_action( 'plugins_loaded','mbe_eship_create_options_default' );
//add_action( 'plugins_loaded','mbe_eship_check_pickup_request_mode' );

add_action( 'plugins_loaded', 'mbe_eship_update_db_check', 9 );
add_action( 'plugins_loaded', 'mbe_eship_update_new_settings_check', 10 );
add_action( 'plugins_loaded', 'mbe_eship_check_log_folder_htaccess', 10 );
add_action( 'before_woocommerce_init', 'declare_compatibility');


/**
 * Check if WooCommerce is active
 */
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	if ( ! function_exists( 'mbe_e_link_get_settings_url' ) ) {
		function mbe_e_link_get_settings_url() {
			return version_compare( WC()->version, '2.1', '>=' ) ? "wc-settings" : "woocommerce_settings";
		}
	}

	if ( ! function_exists( 'mbe_e_link_plugin_override' ) ) {
		add_action( 'plugins_loaded', 'mbe_e_link_plugin_override' );
		function mbe_e_link_plugin_override() {
			if ( ! function_exists( 'WC' ) ) {
				function WC() {
					return $GLOBALS['woocommerce'];
				}
			}
		}
	}

	if ( ! class_exists( 'mbe_e_link_wooCommerce_shipping_setup' ) ) {
		class mbe_e_link_wooCommerce_shipping_setup {
            use \Traits\ShipmentActions;

			protected $helper;

			public function __construct() {
				add_filter( 'woocommerce_get_settings_pages', array( $this, 'load_custom_settings_tab' ) );

                // Check if Pickup request activation flag is set and in case force the default value setter to run
				add_action('mbe_eship_custom_settings_loaded', array( $this, 'mbe_eship_check_pickup_default_options'));

				// Check if Tax and Duties request activation flag is set and in case force the default value setter to run
				add_action('mbe_eship_custom_settings_loaded', array( $this, 'mbe_eship_check_tax_and_duties_default_options'));

                // Check default values for settings
                add_action('mbe_eship_custom_settings_loaded', array( $this, 'mbe_eship_create_options_default'),20);

                // Add settings link in the plugin list
				add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array(
					$this,
					'plugin_action_links'
				) );
				// Add MBE shipping method to WooCommerce but remove the tab from WooCommerce => Settings => Shipping
				add_filter( 'woocommerce_shipping_methods', array( $this, 'wf_mbe_wooCommerce_shipping_methods' ) );
				add_action( 'woocommerce_shipping_init', array( $this, 'wf_mbe_wooCommerce_shipping_init' ) );

				// Action for downloading log files
				add_action( 'admin_post_mbe_download_log_files', array( $this, 'mbe_download_log_files' ) );
				add_action( 'admin_post_mbe_download_standard_package_file', array(
					$this,
					'mbe_download_standard_package_file'
				), 10, 0 );
				add_action( 'admin_post_mbe_download_shipping_file', array(
					$this,
					'mbe_download_shipping_file'
				), 10, 0 );
				add_filter( 'query_vars', array( $this, 'mbe_add_query_vars' ) );

				// Action for deleting log files
				add_action( 'admin_post_mbe_delete_log_files', array( $this, 'mbe_delete_log_files' ) );

				// Action for generating API KEY
				add_action( 'admin_post_mbe_generate_api_key', array( $this, 'mbe_generate_api_key' ) );

				//Action for new settings
				add_action( 'admin_post_mbe_sign_in', array( $this, 'mbe_sign_in' ) );
				add_action( 'admin_post_mbe_goto_advanced_login', array( $this, 'mbe_goto_advanced_login' ) );
				add_action( 'admin_post_mbe_reset_login', array( $this, 'mbe_reset_login' ) );

                // Action to download single order pickup manifest
                add_action('admin_post_mbe_download_pickup_manifest', array($this, 'mbe_download_pickup_manifest'));

                // Action to download multiple waybill showing partial errors (called by add_order_list via JS when "reload-download" parameter is set)
                add_action('admin_post_mbe_download_multiple_waybill', array($this, 'mbe_download_multiple_waybill' ));

				// Action to download delivery point single order waybill
				add_action('admin_post_mbe_download_delivery_point_waybill', array($this, 'mbe_download_delivery_point_waybill'));

				// Add menu
				add_action( 'admin_menu', array( $this, 'add_mbe_tab' ) );

				// Load translations
				add_action( 'plugins_loaded', array( $this, 'wan_load_wf_shipping_mbe' ) );

				// Add Delivery Point Field to Admin Order details view
				add_action( 'woocommerce_admin_order_data_after_billing_address', array(
					$this,
					'wf_mbe_delivery_point_show_meta_field'
				), 10, 1 );

				// Add box in the order detail
				add_action( 'wp_loaded', array( $this, 'wf_mbe_wooCommerce_shipping_init_box' ) );

				// Get default data for pickup from MOL when loading pickup settings page or before creating a new pickup shipment
//				add_action('woocommerce_before_settings_'. MBE_ESHIP_ID , array($this, 'mbe_woocommerce_get_pickup_default_data'));
				add_action(MBE_ESHIP_ID . '_before_output_section_mbe_pickup', array($this, 'mbe_woocommerce_get_pickup_default_data'),5);
				add_action(MBE_ESHIP_ID.'_before_create_pickup', array($this, 'mbe_woocommerce_get_pickup_default_data'),5);

				// Send default data for pickup to MOL
				add_action('woocommerce_update_options_'. MBE_ESHIP_ID .'_mbe_pickup', array($this, 'mbe_woocommerce_set_pickup_default_data'));

                // Action to create a return shipment
                add_action('admin_post_mbe_create_return_shipment', array($this, 'mbe_create_return_shipment'));

				// Action to ship a pickup batch
				add_action('admin_post_mbe_send_pickup', array($this, 'mbe_send_pickup'));

                // Action to delete a pickup batch
                add_action('admin_post_mbe_delete_pickup_custom_data', array($this, 'mbe_delete_pickup_custom_data'));

                // Action to detach an order from a pickup batch
                add_action('admin_post_mbe_detach_order_from_batch', array($this, 'mbe_detach_order_from_batch'));

				// Action to check batch items after detach
                add_action(MBE_ESHIP_ID . '_after_detach', array($this, 'mbe_after_detach_check_pickup_items'));

                // Action to check pickup status after deleting the pickup addresses
                add_action(MBE_ESHIP_ID . '_csv_editor_deleted', array($this, 'mbe_after_delete_pickup_address_disable_pickup_if_no_default_address'),10,2);

                // Script to manage custom mbebuttons actions
				add_action('admin_enqueue_scripts', array($this, 'mbe_helper_scripts'));


				$this->helper = new Mbe_Shipping_Helper_Data();
				if ( $this->helper->isEnabled() ) {
					add_action( 'wp_enqueue_scripts', array($this, 'mbe_gel_proximity_scripts'));

					add_action( 'closure_event', array( $this, 'automatic_closure' ) );
					add_action( 'woocommerce_settings_saved', array( $this, 'add_cron_job' ) );
					add_action( 'woocommerce_admin_updated', array( $this, 'mbe_error_notice' ) );

					//Add admin notice
					add_action( 'admin_notices', array( $this, 'mbe_shipping_admin_notices' ) );

					// Add label "Free" to Frontend if the price is 0
					add_filter( 'woocommerce_cart_shipping_method_full_label', array(
						$this,
						'wf_mbe_wooCommerce_set_free_label'
					), 10, 2 );

					// Create shipping automatically on status processing
					add_action( 'woocommerce_order_status_processing', array( $this, 'mbe_update_tracking' ), 10, 2 );

					// Create shipping manually
					add_filter( 'woocommerce_order_actions', array( $this, 'add_order_meta_box_actions' ), 10 ,2 );
					add_action( 'woocommerce_order_action_mbe_shipping_creation', array(
						$this,
						'process_order_meta_box_actions'
					) );

					// Add frontend tracking
					add_action( 'woocommerce_order_details_after_order_table', array(
						$this,
						'woocommerce_order_details_tracking'
					) );

					// Add frontend GEL proximity delivery points map
					add_action( 'woocommerce_after_shipping_rate', array(
						$this,
						'wf_mbe_wooCommerce_shipping_gel_delivery_point_map'
					),10,2 );
                    
                    // Add filter to hide custom order item metakeys
                    add_filter('woocommerce_order_item_get_formatted_meta_data', array(
                        $this,
	                    'wf_mbe_wooCommerce_shipping_hide_custom_order_meta_keys'
                    ), 10, 2);

					// Add Delivery point dataset to the checkout fields list
//					add_filter( 'woocommerce_checkout_fields', array(
//						$this,
//						'wf_mbe_delivery_point_add_selected_location_field'
//					) );

					// Add Delivery point address form field validation
					add_action( 'woocommerce_after_checkout_validation', array(
						$this,
						'wf_mbe_delivery_point_validation'
					),10,2 );

					// Set Delivery Point address as order's shipping address
					add_filter( 'woocommerce_checkout_posted_data', array(
						$this,
						'wf_mbe_delivery_point_set_shipping_address'
					) );

					// Set Delivery Point shipment metafield value for the "old" shortcode checkout
					add_action( 'woocommerce_checkout_update_order_meta', array(
						$this,
						'wf_mbe_delivery_point_set_meta_field'
					), 10,2 );


					add_action( 'woocommerce_checkout_update_order_review', array(
						$this,
						'wf_mbe_delivery_point_set_meta_field_review'
					));

                    // Update package payload for dynamic shipping cost base on selected delivery point
					add_filter('woocommerce_cart_shipping_packages', array($this,
						'wf_mbe_delivery_point_update_package_for_cost_recalculation'
					));

		            // Remove the delivery point session variable
					add_action( 'woocommerce_checkout_order_created', array($this,'wf_mbe_remove_delivery_point_session_variable') );
					add_action( 'woocommerce_cart_emptied', array($this,'wf_mbe_remove_delivery_point_session_variable') );
					add_action( 'woocommerce_cart_is_empty', array($this,'wf_mbe_remove_delivery_point_session_variable') );

					// Set Custom Mapping metafield value for the "old" shortcode checkout
					add_action('woocommerce_checkout_update_order_meta', array(
						$this,
						'wf_mbe_wooCommerce_shipping_custom_mapping_meta_field'
					) );

					// Set Custom Mapping metafield value for the "new" blocks checkout
					add_action('woocommerce_store_api_checkout_update_order_meta', array(
						$this,
						'wf_mbe_wooCommerce_shipping_custom_mapping_meta_field_checkout_block'
					) );

					// Add track ID to transational emails
					add_action( 'woocommerce_email_order_details', array(
						$this,
						'mbe_woocommerce_email_track_id'
					), 20, 4 );

                    // Check the pickup request mode <=> shipping mode
					add_action('woocommerce_settings_page_init', array( $this, 'mbe_eship_check_pickup_request_mode'));

					// Check if the tax and duties must be disabled
					add_action('woocommerce_settings_page_init', array( $this, 'mbe_eship_check_tax_and_duties'));

                    // Set or update pickup data on shipment creation
					add_action(MBE_ESHIP_ID.'_before_create_pickup', array($this, 'mbe_edit_pickup_data' ));

                    // Show Tax and Duties in Checkout shortcodes
                    add_action('woocommerce_review_order_before_submit', array($this, 'mbe_show_tax_and_duties_checkout_message' ));

                    add_action( 'woocommerce_cart_calculate_fees', array($this, 'mbe_eship_add_tax_and_duties_fee' ) );
				}
			}

            function mbe_gel_proximity_scripts() {
                $helper = new Mbe_Shipping_Helper_Data();
	            wp_enqueue_script(MBE_ESHIP_ID.'-gel-proxmity-map-js', Mbe_Shipping_Helper_Data::GEL_PROXIMITY_URL . '/sdk/latest.js','', rand());
	            wp_enqueue_script(MBE_ESHIP_ID.'-gel-proxmity-map-initialization' , MBE_ESHIP_PLUGIN_URL . '/lib/js/gel-proximity-map-initilization.js','',rand());
	            wp_add_inline_script( MBE_ESHIP_ID.'-gel-proxmity-map-initialization', 'const '. 'mbe_gel_proxmity_data = ' . json_encode( array(
			            'urlEndUser' => $this->helper->getGelProxymityUrlEndUser(),
                        'merchantCode' => $this->helper->getGelProximityMerchantCode(),
			            'apiKey' => $this->helper->getGelProxymityApiKey(),
			            'locale' => get_user_locale(),
                        'debug' => $this->helper->debug()
		            ) ), 'before' );
            }

            function mbe_helper_scripts() {
                wp_enqueue_script(MBE_ESHIP_ID.'-helper-scripts-js', MBE_ESHIP_PLUGIN_URL . '/lib/js/mbe-helper-scripts.js','',rand());
            }

			function load_custom_settings_tab( $settings ) {
//				if( ! class_exists(Mbe_Settings::class)) {
//					$settings[] = include __DIR__ . '/includes/class-mbe-settings-page.php';
//				} else {
//					$settings = Mbe_Settings();
//				}

                $mbeSettings = include __DIR__ . '/includes/class-mbe-settings-page.php';
				$settings[] = $mbeSettings;

                do_action('mbe_eship_custom_settings_loaded', $mbeSettings);

				return $settings;

			}

			function mbe_eship_create_options_default($mbeSettingsPage) {
				// Check if it's needed and not already running
				if(!(get_option( MBE_SCHEMA_FLAG_OPTION ) === 'no') && !get_transient( MBE_ESHIP_ID . '_set_options_default' ) ) {
					$logger = new Mbe_Shipping_Helper_Logger();
					$logger->log( 'Saving settings default value - Starting', true);
					// set transient to flag it's running.
					set_transient( MBE_ESHIP_ID . '_set_options_default', 'yes', MINUTE_IN_SECONDS * 5 );

					// Include settings so that we can run through defaults.
					if ( ! class_exists( WC_Settings_Page::class ) ) {
						require_once( WP_PLUGIN_DIR . '/woocommerce/includes/admin/settings/class-wc-settings-page.php' );
					}

//					$mbeSettingsPage = include_once __DIR__ . '/includes/class-mbe-settings-page.php';
		            $sections        = $mbeSettingsPage->get_sections();
					// Remove the Welcome page from the list
					unset($sections['']);

					$subsections = array_unique( array_keys( $sections ) );

					foreach ( $subsections as $subsection ) {
						foreach ( $mbeSettingsPage->get_settings_for_section( $subsection ) as $value ) {
							if ( isset( $value['default'] ) && isset( $value['id'] ) ) {
								add_option( $value['id'], $value['default']);
							}
						}
					}

					update_option( MBE_SCHEMA_FLAG_OPTION, 'no' );
					delete_transient( MBE_ESHIP_ID . '_set_options_default' );
					$logger->log( 'Saving settings default value - Done', true);
				}
			}

			function mbe_eship_check_pickup_default_options() {
				$helper = new Mbe_Shipping_Helper_Data();
				$logger = new Mbe_Shipping_Helper_Logger();
				// Check if Activation flag exists and if not run the default value setter
				if(!$helper->hasOption(Mbe_Shipping_Helper_Data::XML_PATH_PICKUP_REQUEST_ENABLED) && $helper->isEnabledThirdPartyPickups()) {
                    update_option( MBE_SCHEMA_FLAG_OPTION, 'yes' );
					$logger->log('Updating schema flag to run default value setter for Pickup request');
				}
			}

			function mbe_eship_check_pickup_request_mode() {
				$logger = new Mbe_Shipping_Helper_Logger();

                // Disable pickup if the user doesn't have the rights set in MOL
                // Disable pickup if Shipping mode is not coherent
//                $disablePickup = !$this->helper->isEnabledThirdPartyPickups() || (
//                        $this->helper->getShipmentsCreationMode() === Mbe_Shipping_Helper_Data::MBE_CREATION_MODE_AUTOMATICALLY
//                        && $this->helper->getPickupRequestMode() === Mbe_Shipping_Helper_Data::MBE_PICKUP_REQUEST_MANUAL
//                );

				$disablePickup = !$this->helper->isEnabledThirdPartyPickups();

                if ( $disablePickup && $this->helper->getPickupRequestEnabled() ) {
                    $this->helper->setOption( Mbe_Shipping_Helper_Data::XML_PATH_PICKUP_REQUEST_ENABLED, 0 );
//                    WC_Admin_Settings::add_error(
//                        __( 'Pickup request cannot be enabled, please check the settings', 'mail-boxes-etc' )
//                        . ':' . __( 'Shipments creation in MBE Online - Mode', 'mail-boxes-etc' )
//                        . ' or '
//                        . __( 'Pickup Request - Mode', 'mail-boxes-etc' )
//                        . ' ' . __( 'or the MBE Online options for third party pickup', 'mail-boxes-etc' )
//                    );
//                    $logger->log( 'Pickup mode and Shipping mode are not set correctly, disabling pickup' );
	                WC_Admin_Settings::add_error(
		                __( 'Pickup request cannot be enabled, please check the settings', 'mail-boxes-etc' )
		                . ' ' . __( 'for third party pickup in MBE Online', 'mail-boxes-etc' )
	                );
	                $logger->log( 'Pickup is disabled in MOL, disabling pickup' );
                }

                // Disable pickup if the pickup mode is auto and a default address is not set
                if($this->helper->getPickupRequestEnabled()
                   && !$this->helper->hasDefaultPickupAddress()
                   && $this->helper->getPickupRequestMode() === Mbe_Shipping_Helper_Data::MBE_PICKUP_REQUEST_AUTOMATIC
                ) {
	                $this->helper->setOption(Mbe_Shipping_Helper_Data::XML_PATH_PICKUP_REQUEST_ENABLED, 0);
	                WC_Admin_Settings::add_error( __('Pickup request cannot be enabled, please set a default pickup address', 'mail-boxes-etc'));
	                $logger->log('Deafult pickup address missing, disabling pickup');
                }

                // If Pickup is still enabled and shipping mode is automatic but pickup mode is still manual, set pickup mode to automatic too
				if( $this->helper->getPickupRequestEnabled()
                    && $this->helper->getShipmentsCreationMode() === Mbe_Shipping_Helper_Data::MBE_CREATION_MODE_AUTOMATICALLY
                    && $this->helper->getPickupRequestMode() === Mbe_Shipping_Helper_Data::MBE_PICKUP_REQUEST_MANUAL
                ) {
					$this->helper->setOption(Mbe_Shipping_Helper_Data::XML_PATH_PICKUP_REQUEST_MODE, Mbe_Shipping_Helper_Data::MBE_PICKUP_REQUEST_AUTOMATIC);
                }
			}


			function mbe_error_notice() {
				require_once( ABSPATH . 'wp-admin/includes/screen.php' );
				if ( is_admin() ) {
					if ( ! isset( get_current_screen()->id ) || get_current_screen()->id !== 'woocommerce_page_wc-settings' ) {
						return;
					} //only show on 'order' pages
					echo '<div class="notice notice-error"><p>Warning - If you modify template files this could cause problems with your website.</p></div>';
				}
			}

			/**
			 * Plugin cron job
			 */
			function automatic_closure() {
				$logger = new Mbe_Shipping_Helper_Logger();
				$logger->log( 'Cron automatic_closure' );
				include_once 'includes/cron.php';
			}

			function remove_cron_job() {
				wp_clear_scheduled_hook( 'closure_event' );
			}

			public function add_cron_job() {
				$logger = new Mbe_Shipping_Helper_Logger();
				$time = $this->helper->getShipmentsClosureTime();
				if ( $this->helper->isEnabled() && $this->helper->isClosureAutomatically() ) {
					$closureTime = strtotime( "today $time ".wp_timezone_string());
					$cron_jobs = get_option( 'cron' );
					$array     = array_column( $cron_jobs, 'closure_event' );
					if ( ! empty( $array ) ) {
						$index         = array_search( array( 'closure_event' => $array[0] ), $cron_jobs );
						$scheduledTime = date( 'H:i:s', $index );
						if ( $scheduledTime != $time ) {
							wp_clear_scheduled_hook( 'closure_event' );
							wp_schedule_event($closureTime, 'daily', 'closure_event' );
							$logger->log( "Cron automatic closure set at: $time ".wp_timezone_string() );
						}
					} else {
						wp_schedule_event($closureTime, 'daily', 'closure_event' );
						$logger->log( "Cron automatic closure set at: $time ".wp_timezone_string() );
					}
				} else {
					wp_clear_scheduled_hook( 'closure_event' );
				}
			}

			/**
			 * Add settings link in the plugin list
			 */

			public function plugin_action_links( $links ) {
				$plugin_links = array(
					'<a href="' . admin_url( 'admin.php?page=' . mbe_e_link_get_settings_url() . '&tab=' . MBE_ESHIP_ID ) . '">' . __( 'Settings', 'mail-boxes-etc' ) . '</a>',
				);

				return array_merge( $plugin_links, $links );
			}

			/**
			 * Add shipping method to WooCommerce
			 */

			public function wf_mbe_wooCommerce_shipping_methods( $methods ) {
				$methods[] = 'mbe_shipping_method';

				return $methods;
			}

			public function wf_mbe_wooCommerce_shipping_init() {
				include_once( 'includes/class-mbe-shipping.php' );
			}

			/**
			 * Add box in the order detail
			 */

			public function wf_mbe_wooCommerce_shipping_init_box() {
				include_once( 'includes/class-mbe-tracking-admin.php' );
			}

			/**
			 * Add menu tab in WooCommerce
			 */

			function add_mbe_tab() {
				add_submenu_page( 'woocommerce', __( 'MBE Shipments List', 'mail-boxes-etc' ), __( 'MBE Shipments List', 'mail-boxes-etc' ), 'manage_woocommerce', WOOCOMMERCE_MBE_TABS_PAGE, array(
					$this,
					'add_order_list'
				) );

				add_submenu_page( null, __( 'MBE CSV Editor', 'mail-boxes-etc' ), __( 'MBE CSV Editor', 'mail-boxes-etc' ), 'manage_woocommerce', 'woocommerce_mbe_csv_tabs', array(
					$this,
					'add_csv_list'
				) );

				add_submenu_page( null, __( 'Add new', 'mail-boxes-etc' ), __( 'Add new', 'mail-boxes-etc' ), 'activate_plugins', MBE_ESHIP_ID . '_csv_edit_form', array(
					$this,
					'csv_form_page_handler'
				) );

                // PICKUP Pages
                if($this->helper->isEnabledThirdPartyPickups()) {
	                add_submenu_page( null, __( 'MBE Pickup Batches List', 'mail-boxes-etc' ), __( 'Pickup', 'mail-boxes-etc' ), 'manage_woocommerce', MBE_ESHIP_ID . '_' . WOOCOMMERCE_MBE_TABS_PICKUP_PAGE, array(
		                $this,
		                'add_pickup_batches_list'
	                ) );


	                add_submenu_page( null, __( 'MBE Pickup Data Editor', 'mail-boxes-etc' ), __( 'MBE Pickup Data Editor', 'mail-boxes-etc' ), 'manage_woocommerce', MBE_ESHIP_ID . '_pickup_data_tabs', array(
		                $this,
		                'pickup_data_form_page_handler'
	                ) );
                }
			}

			/**
			 * Add order list in MBE tab
			 */

			function add_order_list() {
				require_once 'includes/class-mbe-order.php';
				$orders = new Mbe_E_Link_Order_List_Table();
				$orders->prepare_items();
                ?>

                <nav class="nav-tab-wrapper woo-nav-tab-wrapper">
                    <a href="#" class="nav-tab nav-tab-active">
                        <h2><?php esc_html_e( 'MBE Shipments List', 'mail-boxes-etc' ) ?></h2>
                    </a>
                    <?php if($this->helper->isEnabledThirdPartyPickups()) { ?>
                    <a href="<?php echo esc_url(get_admin_url(get_current_blog_id(), 'admin.php?page=' . MBE_ESHIP_ID . '_' . WOOCOMMERCE_MBE_TABS_PICKUP_PAGE)) ?>" class="nav-tab ">
                        <h2><?php esc_html_e( 'MBE Manual Pickup', 'mail-boxes-etc' ) ?></h2>
                    </a>
                    <?php } ?>
                </nav>
				<div class="wrap">
					<?php $this->mbe_eship_wp_notification() ?>

					<form id="certificates-filter" method="get">
						<input type="hidden" name="page"
						       value="<?php echo ( ! empty( $_REQUEST['page'] ) ) ? esc_attr( wp_unslash( $_REQUEST['page'] ) ) : ''; ?>"
						/>

						<?php
                        $orders->display();
                        ?>
					</form>
				</div>

				<?php
				$downloadFile = $_GET['reload-download'] ?? null;
                if(!empty($downloadFile)) {
                    $actionUrl = sprintf( "%sadmin-post.php?action=mbe_download_multiple_waybill&downloadFile=%s&nonce=%s", get_admin_url(), urlencode($downloadFile), wp_create_nonce( 'mbe_download_multiple_waybill' ) );
                    echo "<script>jQuery(document).ready(mbeButtonAction(this, " . json_encode($actionUrl) . ",'_blank', 'false'))</script>";
                }
			}

            function mbe_download_multiple_waybill() {
	            if ( isset( $_REQUEST['nonce'] ) && wp_verify_nonce( $_REQUEST['nonce'], 'mbe_download_multiple_waybill' ) ) {
			        // Unset the session variable
		            $downloadFile = sanitize_url($_REQUEST['downloadFile']);
		            $this->helper->mbe_download_file( $downloadFile, "mbe-labels.pdf", 'application/pdf', true );
	            }
            }

            function add_pickup_batches_list() {
	            require_once 'includes/class-mbe-pickup-batches.php';
	            $ordersPickupBatches = new Mbe_E_Link_Pickup_Batches_List_Table();
	            $ordersPickupBatches->prepare_items();
	            ?>

                <nav class="nav-tab-wrapper woo-nav-tab-wrapper">
                    <a href="<?php echo esc_url(get_admin_url(get_current_blog_id(), 'admin.php?page=' . WOOCOMMERCE_MBE_TABS_PAGE)) ?>" class="nav-tab ">
                        <h2><?php esc_html_e( 'MBE Shipments List', 'mail-boxes-etc' ) ?></h2>
                    </a>
	                <?php if($this->helper->isEnabledThirdPartyPickups()) { ?>
                    <a href="#" class="nav-tab nav-tab-active">
                        <h2><?php esc_html_e( 'MBE Manual Pickup', 'mail-boxes-etc' ) ?></h2>
                    </a>
	                <?php } ?>
                </nav>
                <div class="wrap">
                    <?php $this->mbe_eship_wp_notification() ?>

<!--                    <form id="certificates-filter" method="get">
                        <input type="hidden" name="page"
                               value="<?php /*echo ( ! empty( $_REQUEST['page'] ) ) ? esc_attr( wp_unslash( $_REQUEST['page'] ) ) : ''; */?>"
                        />
-->
                    <?php $a = $ordersPickupBatches->display() ?>
<!--                    </form>-->
                </div>

	            <?php
            }

			function add_csv_list() {
				$csvType    = sanitize_text_field($_REQUEST['csv'] ?? '');
				$csvFactory = new Mbe_Csv_Editor_Model_Factory();
				$csv        = $csvFactory->create( $csvType );
				$message    = sanitize_text_field($_REQUEST['message'] ?? '');
				if ( ! empty( $csv ) ) {
					$title = $csv->get_title( false );
					$csv->prepare_items();
					if ( 'delete' === $csv->current_action() ) {
						$message = sprintf( __( 'Items deleted: %d', 'mail-boxes-etc' ), count( (array) $_REQUEST['id'] ) );
					}
					?>

					<div class="wrap">
						<h2><?php esc_html_e('MBE ' . $title . ' ' . __( 'Editor' )) ?>
							<a class="add-new-h2"
							   href="<?php echo esc_url(get_admin_url( get_current_blog_id(), 'admin.php?page=' . mbe_e_link_get_settings_url() . '&tab=' . MBE_ESHIP_ID . '&section='. $csv->get_backlink() )); ?>">
								<?php esc_html_e( 'Back to settings', 'mail-boxes-etc' ) ?>
							</a>
						</h2>

						<?php $this->mbe_eship_wp_notification() ?>
						<?php if ( ! empty( $notice ) ): ?>
                            <div id="notice" class="notice notice-error is-dismissible"><p><?php echo wp_kses_post($notice) ?></p></div>
						<?php endif; ?>
						<?php if ( ! empty( $message ) ): ?>
                            <div id="message" class="notice notice-success is-dismissible"><p><?php echo wp_kses_post($message) ?></p></div>
						<?php endif; ?>

						<form id="certificates-filter" method="get">
							<?php //Fields to be sent with request and bulk actions ?>
							<input type="hidden" name="page"
							       value="<?php echo ( ! empty( $_REQUEST['page'] ) ) ? esc_attr( wp_unslash( $_REQUEST['page'] ) ) : ''; ?>"
							/>
							<input type="hidden" name="csv"
							       value="<?php esc_attr_e($csvType) ?>"
							/>
							<?php $csv->display() ?>
						</form>
					</div>
					<?php
				} else {
					?>
					<div class="wrap">
						<h2><?php esc_html_e( 'Missing or wrong csv type', 'mail-boxes-etc' ) ?>
							<a class="add-new-h2"
							   href="<?php echo esc_url(get_admin_url( get_current_blog_id(), 'admin.php?page=' . mbe_e_link_get_settings_url() . '&tab=' . MBE_ESHIP_ID . '&section=mbe_general' )); ?>">
								<?php esc_html_e( 'Back to settings', 'mail-boxes-etc' ) ?>
							</a>
						</h2>
					</div>
					<?php
				}
			}

			/**
			 * Creation shipping manually action
			 */

			function add_order_meta_box_actions( $actions, $order ) {
				if ( is_array( $actions ) ) {
//                    $this->>helper = new Mbe_Shipping_Helper_Data();
					if ( $order ) {
						if ( $this->helper->isMbeShipping( $order ) ) {
							if ( ! $this->helper->isCreationAutomatically() && ! $this->helper->hasTracking($order->get_id()) ) {
								$actions['mbe_shipping_creation'] = __( 'Create MBE shipping', 'mail-boxes-etc' );
							}
						}
					}
				}

				return $actions;
			}

			function process_order_meta_box_actions( $order ) {
				if ( $this->helper->isMbeShipping( $order ) ) {
					include_once 'includes/class-mbe-tracking-factory.php';
					$orderId = $this->helper->getOrderId( $order );
					mbe_tracking_factory::create( $orderId );
				}
			}


			public function mbe_update_tracking( $order_id ) {
				$order = wc_get_order( $order_id );
				if ( $this->helper->isEnabled() && $this->helper->isMbeShipping( $order ) && $this->helper->isCreationAutomatically() && empty( $this->helper->getTrackings( $order_id ) ) ) {
//					include_once 'includes/class-mbe-tracking-factory.php';
//					mbe_tracking_factory::create( $order_id );
                    $pickupInfo = [];
                    if( $this->helper->getPickupRequestEnabled()
                        && Mbe_Shipping_Helper_Data::MBE_PICKUP_REQUEST_AUTOMATIC === $this->helper->getPickupRequestMode()
                    ) {
	                    $pickupInfo = [
		                    'is-pickup'      => true,
		                    'is-batch'       => false,
		                    'custom-data-id' => null,
	                    ];
                    }
					$post_ids = array_map('absint', (array)$order_id);
                    $this->processShipmentCreation($post_ids, $pickupInfo);
				}
			}

			/**
			 * Set Free Label to Carriers
			 */

			public function wf_mbe_wooCommerce_set_free_label( $full_label, $method ) {
				if ( (float) $method->cost == 0.0 ) {
					return $full_label . ': ' . __( "Free", 'mail-boxes-etc' );
				} else {
					return $full_label;
				}
			}

			public function wan_load_wf_shipping_mbe() {
				load_plugin_textdomain( 'mail-boxes-etc', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
			}

			/*
			 * Add front tracking
			 */

			public function woocommerce_order_details_tracking( $order ) {
//                $this->>helper = new Mbe_Shipping_Helper_Data();
				$orderId      = $this->helper->getOrderId( $order );
				$trackings    = $this->helper->getTrackings( $orderId );
				if ( \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled() ) {
					$tracking_url = $order->get_meta('woocommerce_mbe_tracking_url');
					$trackingName = $this->tracking_name = $order->get_meta('woocommerce_mbe_tracking_name');
				} else {
					$tracking_url = get_post_meta( $orderId, 'woocommerce_mbe_tracking_url', true );
					$trackingName = $this->tracking_name = get_post_meta( $orderId, 'woocommerce_mbe_tracking_name', true );
				}

				if ( $this->helper->isMbeShipping( $order ) && ! empty( $trackings ) ) {
					echo "<h2>" . esc_html__( 'Mail Boxes ETC Tracking', 'mail-boxes-etc' ) . "</h2>";
					echo '
					<table class="shop_table tracking_details">
						<thead>
							<tr>
								<th class="service-name">' . esc_html__( 'Service', 'mail-boxes-etc' ) . '</th>
								<th class="tracking-link">' . esc_html__( 'Tracking', 'mail-boxes-etc' ) . '</th>
							</tr>
						</thead>
						<tbody>';
					foreach ( $trackings as $track ) {
						echo '<tr class="order_item">
								<td class="tracking-name">' . esc_html($trackingName) . '</td>
								<td class="product-total"><a target="_blank" href="' . esc_attr($tracking_url . $track) . '">' . esc_html($track) . '</a></td>
							  </tr>';
					}
					echo '</tbody></table>';
				}
			}

			/**
			 * Adds GEL delivery point map to the WooCommerce shipping method
			 *
			 * @param object $method The WooCommerce shipping method object
			 */
			public function wf_mbe_wooCommerce_shipping_gel_delivery_point_map( $method ) {
				$mbeServiceSelected = (
				    $this->isDeliveryPointServiceMethod(WC()->session->get( 'chosen_shipping_methods' ))
					&& $method->id === WC()->session->get( 'chosen_shipping_methods' )[0]
				);

				if ( $mbeServiceSelected && is_checkout() ) {
					// Generate the delivery point GEL services list (NET_xxx codes) for GEL map initialization
//					$gelServices = '';
//					foreach ( $method->meta_data['delivery_point_services']['courier'] as $gel_service ) {
//						$gelServices .= "'" . $gel_service ."'" . ',';
//					}

                    // Generate the prices parameter for GEL map initialization
                    $prenegotiatedFound = false;
                    $labelingFound = false;
                    $deliveryPointPrices = ['currencyCode' => get_woocommerce_currency()];
					foreach ( array_combine($method->meta_data['delivery_point_services']['mol'], $method->meta_data['delivery_point_services']['prices']) as $key=>$value ) {
                        if( !$prenegotiatedFound && in_array($key, MBE_ESTIMATE_DELIVERY_POINT_PRENEGOTIATED_SERVICES) ) {
	                        $deliveryPointPrices ['prenegotiated'] = (float)$value;
                            $prenegotiatedFound = true;
                            if ($labelingFound) break;
                        }

                        if( !$labelingFound && in_array($key, MBE_ESTIMATE_DELIVERY_POINT_LABELING_SERVICES) ) {
							$deliveryPointPrices ['labeling'] = (float)$value;
							$labelingFound = true;
							if ($prenegotiatedFound) break;
						}
                    }

					if(wc_tax_enabled() && WC()->cart->display_prices_including_tax()) {
						$taxPercentage = $this->getShippingTaxPercentageFromCart();

						if ( $taxPercentage > 0 ) {
							foreach ( array_intersect_key($deliveryPointPrices, ['prenegotiated'=>'', 'labeling'=>'']) as $key=>$price ) {
								$deliveryPointPrices[$key] += $this->helper->round($price * $taxPercentage / 100);
							}
						}
                    }

                    // Add the form field to the section and set value in case of delivery point selection price refresh
					echo '<div style="margin-bottom: 0.5em; padding-left: 20px;">';
					$sessionDeliveryPoint = WC()->session->get('mbe_delivery_point')??null;
					$gelProximitySelectionLabel = null;
					$gelProximitySelection = null;

                    if (!empty($sessionDeliveryPoint)) {
                        $gelProximitySelection = json_encode((array)$sessionDeliveryPoint);
                        $gelProximitySelectionLabel = $sessionDeliveryPoint->networkName . ', ' . $sessionDeliveryPoint->address . ', ' . $sessionDeliveryPoint->zipCode .', '. $sessionDeliveryPoint->city;
                    }

					woocommerce_form_field( 'gel-proximity-selection-label', array(
							'id' => 'gel-proximity-selection-label',
                            'class' => 'woocommerce-validated',
							'type'        => 'text',
							'required'    => true,
							'custom_attributes' => array(
								    'style' => 'font-size: 0.8em;',
                                    'readonly'=>'readonly',
                                    'onclick'=>'openGELProximityModal()'
                            ),
                            'placeholder'       => __('Click to select a delivery point', 'mail-boxes-etc'),
						), $gelProximitySelectionLabel
					);

                    woocommerce_form_field( 'gel-proximity-selection', array(
                            'type'        => 'hidden',
		                    'id' => 'gel-proximity-selection',
                        ), $gelProximitySelection
                    );

					woocommerce_form_field( 'gel-proximity-selection-update', array(
						'type'        => 'hidden',
						'id' => 'gel-proximity-selection-update',
					    ), false // alway set the field to false, tha value must be true only when posting from gel-proximity-map-initilization.js
					);

					echo  '<script type="text/javascript">
                                mbeGelSDK.options.networks = '. json_encode($method->meta_data['delivery_point_services']['courier']) .'
                                mbeGelSDK.options.prices = ' . json_encode($deliveryPointPrices) . '
                         </script>';

					echo '</div>';
				}

			}

            function wf_mbe_delivery_point_validation($data, $errors) {
                if ( isset( $_POST['gel-proximity-selection'] ) && empty( $data['mbe_delivery_point_data'] ) ) {
                    $errors->add('validation',  __( 'Select a delivery point to proceed', 'mail-boxes-etc' ));
                }
            }

            function wf_mbe_delivery_point_show_meta_field( $order ) {
                $order_meta_delvery_point = $this->helper->getOrderDeliveryPointShipment($order->get_id());
                if ( ! empty( $order_meta_delvery_point ) ) {
                    echo '<p><strong>' . esc_html__( 'Delivery Point' ) . ':</strong> ' . esc_html__( $order_meta_delvery_point, 'mail-boxes-etc' ) . '</p>';
                }
            }

            function wf_mbe_delivery_point_set_meta_field( $order_id, $data ) {
                $logger = new Mbe_Shipping_Helper_Logger();
                $order = wc_get_order($order_id);
                if ( $this->helper->isMbeShipping( $order ) ) {
                    if ( ! empty( $data['mbe_delivery_point_data'] ) ) {
                        $this->helper->setOrderDeliveryPointShipment($order_id, 'Yes');
                        $this->helper->setOrderDeliveryPointCustomData($order_id, $data['mbe_delivery_point_data'] );
                    } else {
                        $logger->log(__('Missing Access Point ID in $_POST'));
                        $this->helper->setOrderDeliveryPointShipment($order_id, 'No');
                    }
                    $order->save();
                }
            }

			function wf_mbe_delivery_point_update_package_for_cost_recalculation ($packages) {

				$deliveryPoint = WC()->session->get( 'mbe_delivery_point' ) ?? null;
				if ( ! empty( $deliveryPoint ) ) {
                    if(wc_tax_enabled() && WC()->cart->display_prices_including_tax() && !empty(WC()->cart->get_shipping_taxes()) && (WC()->session->get('mbe_delivery_point_update')??false) ) {
	                    $logger = new Mbe_Shipping_Helper_Logger();
	                    $logger->logVar($deliveryPoint, 'Delivery Point recalculation - removing taxes from map cost');
	                    $deliveryPoint->cost = $this->helper->round($this->helper->calculateNetPriceFromGross( $deliveryPoint->cost, $this->getShippingTaxPercentageFromCart() ));
	                    $logger->logVar($deliveryPoint, 'Delivery Point recalculation - taxes removed from map cost');
	                    WC()->session->set('mbe_delivery_point', $deliveryPoint );
	                    WC()->session->set('mbe_delivery_point_update', null);
	                    WC()->session->set('mbe_delivery_point_markup', $this->helper->getHandlingFee());
                    }

                    // Always check if markup/handling fee has been changed (valid only in the same session) and update the delivery point cost and the session option
                    if($this->helper->getHandlingFee() !== WC()->session->get('mbe_delivery_point_markup')) {
	                    $deliveryPoint->cost = $deliveryPoint->cost - WC()->session->get('mbe_delivery_point_markup') + $this->helper->getHandlingFee();
	                    WC()->session->set('mbe_delivery_point', $deliveryPoint );
	                    WC()->session->set('mbe_delivery_point_markup', $this->helper->getHandlingFee());
                    }
					foreach ( $packages as $packageKey => $packageValue ) {
						$packages[ $packageKey ]['delivery_point'] = $deliveryPoint;
					}
				}
				return $packages;
			}

			function wf_mbe_delivery_point_set_meta_field_review( $data ) {
				$logger = new Mbe_Shipping_Helper_Logger();
				WC()->session->set('mbe_delivery_point', null);
				$postData = array();
				parse_str($data, $postData);
				$deliveryPoint = json_decode($postData['gel-proximity-selection']??'');
				$deliveryPointUpdate = json_decode($postData['gel-proximity-selection-update']??false);

                if(!empty($deliveryPoint) && $this->isDeliveryPointServiceMethod( $postData['shipping_method'] )) {
                    $logger->logVar($deliveryPoint, 'Delivery point selection, refresh rates');
	                WC()->session->set('mbe_delivery_point', $deliveryPoint);
	                WC()->session->set('mbe_delivery_point_update', $deliveryPointUpdate);
                }

                // Check if the session variable is empty due to a json_decode() error and log an error
                if(!empty($postData['gel-proximity-selection']) && empty($deliveryPoint)) {
                    $message = __('Possible issue with delivery point content/escaping. This delivery point cannot be correctly selected');
                    $logger->logVar($postData['gel-proximity-selection'], $message);
	                WC()->session->set('mbe_delivery_point', null); // remove the delivery point to be sure it's not retaining an old value
                    wc_add_notice($message, 'error');
                }
			}

            function wf_mbe_delivery_point_set_shipping_address( $data ) {
                $logger = new Mbe_Shipping_Helper_Logger();
                // convert $data['shipping_method'] to array to avoid issues with empty (virtual products) or string values
                $shippingMethod = is_array( $data['shipping_method'] ) ? $data['shipping_method'] : [ $data['shipping_method'] ];
	            $serviceOK      = $this->isDeliveryPointServiceMethod( $shippingMethod );
	            $logger->logVar($shippingMethod, 'wf_mbe_wooCommerce_shipping_uap_address - Shipping Method');
                if ( ! empty( $_POST['gel-proximity-selection'] ) && $serviceOK ) {
                    $deliveryPointAddress = json_decode( stripslashes( $_POST['gel-proximity-selection'] ) );
                    $logger->logVar( $deliveryPointAddress->code, 'wf_mbe_gel_delivery_point_set_shipping_address - GEL PUDO');
                    // Set the Delivery Point address as shipping address
//									$_POST['mbe_delivery_point_data'] = sanitize_text_field( $deliveryPointAddress->pickupPointId );
                    $mbeDeliveryPointData = wp_json_encode( $deliveryPointAddress );
                    $data['mbe_delivery_point_data'] = $mbeDeliveryPointData !== false ? $mbeDeliveryPointData : ''; // encode to json again to run some sanitization
                    $data['shipping_company']   = sanitize_text_field( $deliveryPointAddress->description );
                    $data['shipping_address_1'] = sanitize_text_field( $deliveryPointAddress->address );
                    $data['shipping_postcode']  = sanitize_text_field( $deliveryPointAddress->zipCode );
                    $data['shipping_city']      = sanitize_text_field( $deliveryPointAddress->city );
                    $data['shipping_country']   = sanitize_text_field( $deliveryPointAddress->country );
                    $data['shipping_state']     = array_search( ucfirst( strtolower( sanitize_text_field( $deliveryPointAddress->city ) ) ), WC()->countries->get_states( sanitize_text_field( $deliveryPointAddress->country ) ) );
                }

                return $data;
            }

			/**
			 * Remove the delivery point session variable
			 *
			 * This method removes the custom WC_Session variable 'mbe_delivery_point' that is used to store
			 * the selected delivery point during checkout process.
			 *
			 * @return void
			 */
            function wf_mbe_remove_delivery_point_session_variable() {
				// Remove the custom WC_Session variable
				WC()->session->set('mbe_delivery_point', null);
			}

			function wf_mbe_wooCommerce_shipping_custom_mapping_meta_field( $order_id ) {
				if ( $this->helper->isMbeShippingCustomMapping( $this->helper->getShippingMethod( wc_get_order( $order_id ) ) ) ) {
					if ( \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled() ) {
                        $order = wc_get_order($order_id);
                        $order->update_meta_data(woocommerce_mbe_tracking_admin::SHIPMENT_SOURCE_TRACKING_CUSTOM_MAPPING, 'yes');
                        $order->save();
                    } else {
						update_post_meta( $order_id, woocommerce_mbe_tracking_admin::SHIPMENT_SOURCE_TRACKING_CUSTOM_MAPPING, 'yes' );
					}
				}
			}

			function wf_mbe_wooCommerce_shipping_custom_mapping_meta_field_checkout_block( WC_Order $order ) {
				if ( $this->helper->isMbeShippingCustomMapping( $this->helper->getShippingMethod( $order ) ) ) {
					if ( \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled() ) {
						$order->update_meta_data(woocommerce_mbe_tracking_admin::SHIPMENT_SOURCE_TRACKING_CUSTOM_MAPPING, 'yes');
						$order->save();
					} else {
						update_post_meta( $order->get_id(), woocommerce_mbe_tracking_admin::SHIPMENT_SOURCE_TRACKING_CUSTOM_MAPPING, 'yes' );
					}
				}
            }


			function mbe_woocommerce_email_track_id( $order, $sent_to_admin, $plain_text, $email ) {
				$mailArray   = [ 'customer_invoice', 'customer_completed_order' ];
				if ( \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled() ) {
					$trackingUrl = $order->get_meta(woocommerce_mbe_tracking_admin::SHIPMENT_SOURCE_TRACKING_URL);
				} else {
					$trackingUrl = get_post_meta( $order->get_id(), woocommerce_mbe_tracking_admin::SHIPMENT_SOURCE_TRACKING_URL, true );
				}

				$trackId     = $this->helper->getTrackingsString( $order->get_id() );
				if ( ! empty( $trackId ) && in_array( $email->id, $mailArray ) && $this->helper->getTrackingSetting() ) {
					echo '<table id="track_id" style="width: 100%; vertical-align: top; margin-bottom: 40px; padding: 0px; border:0px; border-spacing: 0; border-collapse: collapse;">
                            <tbody>
                                <tr>
                                    <td style="vertical-align: top; width: 40%; text-align: left; font-family: \'Helvetica Neue\', Helvetica, Roboto, Arial, sans-serif; border: 0; padding: 0;">
                                        <h2 style="color: #96588a; display: block; font-family: &quot;Helvetica Neue&quot;, Helvetica, Roboto, Arial, sans-serif; font-size: 18px; font-weight: bold; line-height: 130%; margin: 0 0 18px; text-align: left;"> ' . esc_html__( woocommerce_mbe_tracking_admin::TRACKING_TITLE_DISPLAY, 'mail-boxes-etc' ) . ' </h2>        
                                    </td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: top; width: 40%; text-align: left; font-family: \'Helvetica Neue\', Helvetica, Roboto, Arial, sans-serif; border: 0; padding: 0;">
                                        <strong>' . esc_html__( 'Tracking id: ', 'mail-boxes-etc' ) . '</strong>
                                    </td>
                                    <td style="vertical-align: top; width: 40%; text-align: left; font-family: \'Helvetica Neue\', Helvetica, Roboto, Arial, sans-serif; border: 0; padding: 0;">
                                        <a href="' . esc_url_raw( $trackingUrl . $trackId ) . '">
                                            ' . esc_html( $trackId ) . '
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                         </table>';
				}
			}

			function mbe_add_query_vars( $vars ) {
				$vars[] = "mbe_filetype";

				return $vars;
			}

			public function mbe_download_standard_package_file() {
				$fileType = sanitize_text_field( $_GET['mbe_filetype'] );
				if ( isset( $_REQUEST['nonce'] ) && wp_verify_nonce( $_REQUEST['nonce'], 'mbe_download_standard_package_file' ) ) {

					switch ( $fileType ) {
						case 'package':
							$this->helper->mbe_download_file( $this->helper->getCurrentCsvPackagesDir() );
							break;
						case 'package-template':
							$this->helper->mbe_download_file( $this->helper->getCsvTemplatePackagesDir() );
							break;
						case 'package-product':
							$this->helper->mbe_download_file( $this->helper->getCurrentCsvPackagesProductDir() );
							break;
						case 'package-product-template':
							$this->helper->mbe_download_file( $this->helper->getCsvTemplatePackagesProductDir() );
							break;
						default:
							break;
					}
				}
			}

			public function mbe_download_shipping_file() {
				if ( isset( $_REQUEST['nonce'] ) && wp_verify_nonce( $_REQUEST['nonce'], 'mbe_download_shipping_file' ) ) {
					$fileType = sanitize_text_field( $_GET['mbe_filetype'] );

					switch ( $fileType ) {
						case 'shipping':
							$this->helper->mbe_download_file( $this->helper->getShipmentsCsvFileDir() );
							break;
						case 'shipping-template':
							$this->helper->mbe_download_file( $this->helper->getShipmentsCsvTemplateFileDir() );
							break;
						default:
							break;
					}
				}
			}

			public function mbe_download_log_files() {
				if ( isset( $_REQUEST['nonce'] ) && wp_verify_nonce( $_REQUEST['nonce'], 'mbe_download_log_files' ) ) {
					$zipfilepath    = trailingslashit($this->helper->getMbeLogDir()) . 'log.zip';
					$wslogfilepath  = $this->helper->getLogWsPath();
					$pluginfilepath = $this->helper->getLogPluginPath();
					$logZip         = new \ZipArchive();
					if ( $logZip->open( $zipfilepath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE ) === true ) {
						$logZip->addFile( $wslogfilepath, substr( $wslogfilepath, strrpos( $wslogfilepath, '/' ) + 1 ) );
						$logZip->addFile( $pluginfilepath, substr( $pluginfilepath, strrpos( $pluginfilepath, '/' ) + 1 ) );
						if ( $logZip->count() > 0 && $logZip->close() ) {
							$this->helper->mbe_download_file( $zipfilepath, 'mbe_log.zip', 'application/zip', true );
						}
					}
					$logZip = null;
					error_log( __( 'MBE Download log - files not found' ) );
				}
				exit;
			}


			public function mbe_delete_log_files() {
				if ( isset( $_REQUEST['nonce'] ) && wp_verify_nonce( $_REQUEST['nonce'], 'mbe_delete_log_files' ) ) {
					try {
						$wslogfilepath  = $this->helper->getLogWsPath();
						$pluginfilepath = $this->helper->getLogPluginPath();
						if ( file_exists( $wslogfilepath ) ) {
							wp_delete_file( $wslogfilepath );
						}
						if ( file_exists( $pluginfilepath ) ) {
							wp_delete_file( $pluginfilepath );
						}
					} catch ( \Exception $e ) {
						error_log( __( 'MBE Delete log - Unexpected error' ) . ' - ' . $e->getMessage() );
					}
				}
				wp_redirect( wp_get_referer() );
			}


			public function mbe_goto_advanced_login() {
				if ( isset( $_REQUEST['nonce'] ) && wp_verify_nonce( $_REQUEST['nonce'], 'mbe_goto_advanced_login' ) ) {
					$this->helper->setLoginMode( false );
					$this->helper->setOption( Mbe_Shipping_Helper_Data::XML_PATH_LOGIN_LINK_ADV, true );
				}
				wp_redirect( wp_get_referer() );
			}

			public function mbe_sign_in() {
				if ( isset( $_REQUEST['nonce'] ) && wp_verify_nonce( $_REQUEST['nonce'], 'mbe_sign_in' ) ) {
				$mbeUser    = $_GET[ MBE_ESHIP_ID . '_' . Mbe_Shipping_Helper_Data::XML_PATH_MBE_USERNAME ];
				$mbePwd     = $_GET[ MBE_ESHIP_ID . '_' . Mbe_Shipping_Helper_Data::XML_PATH_MBE_PASSWORD ];
				$mbeCountry = $_GET[ MBE_ESHIP_ID . '_' . Mbe_Shipping_Helper_Data::XML_PATH_COUNTRY ];
				$this->helper->setOption( Mbe_Shipping_Helper_Data::XML_PATH_LOGIN_LINK_ADV, false );

				if ( ! empty( $mbePwd ) && ! empty( $mbeUser ) ) {
					// Set the option in case it wasn't saved before
					$this->helper->setOption( Mbe_Shipping_Helper_Data::XML_PATH_MBE_USERNAME, $mbeUser );
					$this->helper->setOption( Mbe_Shipping_Helper_Data::XML_PATH_MBE_PASSWORD, $mbePwd );
					$this->helper->setOption( Mbe_Shipping_Helper_Data::XML_PATH_COUNTRY, $mbeCountry );
					$this->helper->setWsUrl( $mbeCountry );

					if ( $this->mbe_generate_api_key() ) {
						// set advanced
						$this->helper->setLoginMode( false );
						update_option( 'mbe_shipping_admin_messages', [
							'message' => urlencode( __( 'Logged in', 'mail-boxes-etc' ) ),
							'status'  => urlencode( 'success' )
						] );
					} else {
						update_option( 'mbe_shipping_admin_messages', [
							'message' => urlencode( __( 'Error Logging in. Please enable debug and check the log files for more details', 'mail-boxes-etc' ) ),
							'status'  => urlencode( 'error' )
						] );
					}
				} else {
					update_option( 'mbe_shipping_admin_messages', [
						'message' => urlencode( __( 'Missing MBE Online Login Information', 'mail-boxes-etc' ) ),
						'status'  => urlencode( 'error' )
					] );
				}
                }

				wp_redirect( wp_get_referer() );
			}

			public function mbe_reset_login() {
				if ( isset( $_REQUEST['nonce'] ) && wp_verify_nonce( $_REQUEST['nonce'], 'mbe_reset_login' ) ) {
					$this->helper->setLoginMode();
					$this->helper->setOption( Mbe_Shipping_Helper_Data::XML_PATH_WS_USERNAME, '' );
					$this->helper->setOption( Mbe_Shipping_Helper_Data::XML_PATH_WS_PASSWORD, '' );
					$this->helper->setOption( Mbe_Shipping_Helper_Data::XML_PATH_WS_URL, '' );
				}
				wp_redirect( wp_get_referer() );
			}

			public function mbe_generate_api_key() {
				$logger = new Mbe_Shipping_Helper_Logger();
				$logger->log( 'MBE Generate API KEY - Start' );
				$status = 'success';
				try {
					$ws       = new MbeWs( true );
					$response = $ws->generateApiKey( $this->helper->getMbeUsername(), $this->helper->getMbePassword() );
					if ( ! empty( $response ) ) {
						$this->helper->setWsUsername( $response->apiKey );
						$this->helper->setWsPassword( $response->apiSecret );
						$logger->log( 'MBE Generate API KEY - Done' );
						$message = __( 'eShip credentials generated correctly', 'mail-boxes-etc' );
					} else {
						$logger->log( 'MBE Generate API KEY - Empty response' );
						$message = __( 'eShip credentials not generated - Empty response', 'mail-boxes-etc' );
						$status  = 'error';
					}

				} catch ( \Exception $e ) {
					$logger->log( __( 'MBE Generate API KEY' ) . ' - ' . $e->getMessage() );
					$message = __( 'eShip credentials not generated. Check the log files for more details', 'mail-boxes-etc' );
					$status  = 'error';
				}
				update_option( 'mbe_shipping_admin_messages', [
					'message' => urlencode( $message ),
					'status'  => urlencode( $status )
				] );

				if ( $status === 'success' ) {
					return true;
				} else {
					return false;
				}
//	            wp_redirect(wp_get_referer());

			}

			public function mbe_shipping_admin_notices() {
//              Manage WC environment notification
				$apiKeyMessage = maybe_unserialize( get_option( 'mbe_shipping_admin_messages' ) );
				if ( ! empty( $apiKeyMessage ) ) {
					$messageStatus = urldecode( $apiKeyMessage['status'] );
					$messageText   = urldecode( $apiKeyMessage['message'] );
					if ( $messageStatus === 'error' ) {
						WC_Admin_Settings::add_error( $messageText );
					} else {
						WC_Admin_Settings::add_message( $messageText );
					}
				}
				delete_option( 'mbe_shipping_admin_messages' );
			}

			// Set the local pickup default data
			public function mbe_woocommerce_get_pickup_default_data() {
				$ws = new Mbe_Shipping_Model_Ws();
                // get MOL default data
				$pickupData = $ws->getPickupDefaultData();
                // set local default data
				$this->helper->setLocalPickupDefaultData($pickupData);
			}

			// Send the local pickup default data to MOL
            public function mbe_woocommerce_set_pickup_default_data() {
	            $ws = new Mbe_Shipping_Model_Ws();
	            $ws->setPickupDefaultData($this->helper->getLocalPickupDefaultData());
            }

			public function mbe_download_pickup_manifest() {
				$ws     = new Mbe_Shipping_Model_Ws();
				$logger = new Mbe_Shipping_Helper_Logger();

				if ( isset( $_REQUEST['nonce'] ) && wp_verify_nonce( $_REQUEST['nonce'], 'mbe_download_pickup_manifest' ) ) {
					try {
						$postId = sanitize_text_field( $_REQUEST['mbe_pickup_postid'] );

						if ( ! $this->helper->isPickupShipped( $postId ) ) {
							throw new \MbeExceptions\ValidationException( __( 'Please select only pickup batches', 'mail-boxes-etc' ) );
						}

						if ( ! $this->helper->hasTracking( $postId ) ) {
							throw new \MbeExceptions\ValidationException( sprintf( __( 'Order %s does not have a tracking number', 'mail-boxes-etc' ), $postId ) );
						}

						if ( \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled() ) {
							$order = wc_get_order($postId);
							$masterTrackingNumber = $order->get_meta(woocommerce_mbe_tracking_admin::SHIPMENT_SOURCE_TRACKING_NUMBER);
						} else {
							$masterTrackingNumber = get_post_meta( $postId, woocommerce_mbe_tracking_admin::SHIPMENT_SOURCE_TRACKING_NUMBER,true );
						}

						$response             = $ws->getPickupManifest( $masterTrackingNumber );

						$outputPdf     = $response->Label->Stream;
						$outputPdfPath = $this->helper->mbeUploadDir() . DIRECTORY_SEPARATOR . current_datetime()->getTimestamp() . rand( 0, 999 ) . '.pdf';

						if ( file_put_contents( $outputPdfPath, $outputPdf ) !== false ) {
							$this->helper->mbe_download_file( $outputPdfPath, "pickup_manifest_$postId.pdf", 'application/pdf', true );
						} else {
							$errMess = __( 'MBE Download pickup manifest - no document to download', 'mail-boxes-etc' );
                            $this->helper->logErrorAndSetWpAdminMessage($errMess, $logger);
						}
					} catch ( \MbeExceptions\ValidationException $e ) {
						$errMess = __( 'MBE Download pickup manifest - ' . $e->getMessage(), 'mail-boxes-etc' );
						$this->helper->logErrorAndSetWpAdminMessage($errMess, $logger);
					} catch ( \Exception $e ) {
						$errMess = __( 'MBE Download pickup manifest - Unexpected error', 'mail-boxes-etc' ) . ' - ' . $e->getMessage();
						$this->helper->logErrorAndSetWpAdminMessage($errMess, $logger);
					}
				}
				wp_redirect(wp_get_referer());
			}

			public function mbe_download_delivery_point_waybill() {
				$ws     = new Mbe_Shipping_Model_Ws();
				$logger = new Mbe_Shipping_Helper_Logger();

				if ( isset( $_REQUEST['nonce'] ) && wp_verify_nonce( $_REQUEST['nonce'], 'mbe_download_delivery_point_waybill' ) ) {
					try {
						$postId = sanitize_text_field( $_REQUEST['mbe_delivery_point_postid'] );

						if ( ! $this->helper->isDeliveryPointOrder( $postId ) ) {
							throw new \MbeExceptions\ValidationException( __( 'Please select only delivery point orders', 'mail-boxes-etc' ) );
						}

						if ( ! $this->helper->hasTracking( $postId ) ) {
							throw new \MbeExceptions\ValidationException( sprintf( __( 'Order %s does not have a tracking number', 'mail-boxes-etc' ), $postId ) );
						}

						if ( \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled() ) {
                            $order = wc_get_order($postId);
							$masterTrackingNumber = $order->get_meta(woocommerce_mbe_tracking_admin::SHIPMENT_SOURCE_TRACKING_NUMBER );
						} else {
							$masterTrackingNumber = get_post_meta( $postId, woocommerce_mbe_tracking_admin::SHIPMENT_SOURCE_TRACKING_NUMBER, true);
						}

						$response = $ws->getDeliveryPointShippingDocument( $masterTrackingNumber );

						if ( $response !== false && empty( $response->Errors ) ) {
							$outputPdf     = file_get_contents($response->CourierWaybill); // load the remote pdf
							$outputPdfPath = $this->helper->mbeUploadDir() . DIRECTORY_SEPARATOR . current_datetime()->getTimestamp() . rand( 0, 999 ) . '.pdf';

							if ( file_put_contents( $outputPdfPath, $outputPdf ) !== false )
							{
								$this->helper->mbe_download_file( $outputPdfPath, "delivery_point_waybill_$postId.pdf", 'application/pdf', true );
							}
							else
							{
								$errMsg = __( 'Download waybill - no document to download. Please, retry later', 'mail-boxes-etc' );
								$this->helper->logErrorAndSetWpAdminMessage($errMsg, $logger);
							}
						} else {
							$errMsg = __( "Download waybill", 'mail-boxes-etc' ) . " $postId - " . __($response->Errors->Error->Description??'', 'mail-boxes-etc' );
							$this->helper->logErrorAndSetWpAdminMessage( $errMsg, $logger );
						}
					} catch ( \MbeExceptions\ValidationException $e ) {
						$errMess = __( 'Download waybill - ' . $e->getMessage(), 'mail-boxes-etc' );
						$this->helper->logErrorAndSetWpAdminMessage($errMess, $logger);
					} catch (\MbeExceptions\ShippingDocumentException $e) {
						$errMsg = sprintf( __( "Download waybill", 'mail-boxes-etc' ) . " - " . __("%s", 'mail-boxes-etc' ), $e->getMessage() );
						$this->helper->logErrorAndSetWpAdminMessage( $errMsg, $logger );
					} catch ( \Exception $e ) {
						$errMess = __( 'MBE Download pickup manifest - Unexpected error', 'mail-boxes-etc' ) . ' - ' . $e->getMessage();
						$this->helper->logErrorAndSetWpAdminMessage($errMess, $logger);
					}
				}
				wp_redirect(wp_get_referer());
			}

            public function mbe_eship_wp_notification() {
	            // Manage WP environment notification
	            $wpAdminMessages = maybe_unserialize( get_option( 'mbe_shipping_wp_admin_messages' ) )?:[];
	            delete_option( 'mbe_shipping_wp_admin_messages' );
                $printf = '';
                foreach ( $wpAdminMessages as $adminMessage ) {
                    $messageStatus = urldecode( $adminMessage['status'] );
                    $messageText   = urldecode( $adminMessage['message'] );
                    switch ( $messageStatus ) {
                        case 'error':
                            $printf .= printf( '<div class="notice notice-error is-dismissible"><p>' . esc_html__( 'Error' ) . ': %s</p></div>', esc_html( $messageText ) );
                            break;
                        case 'warning':
                            $printf .= printf( '<div class="notice notice-warning is-dismissible"><p>%s</p></div>', esc_html( $messageText ) );
                            break;
                        default:
                            $printf .= printf( '<div class="notice notice-info is-dismissible"><p>%s</p></div>', esc_html( $messageText ) );
                            break;
                    }
                }
                return $printf;
            }

			function csv_form_page_handler() {
				global $wpdb;

				$csvType    = esc_attr($_REQUEST['csv']) ?: null;
				$csvFactory = new Mbe_Csv_Editor_Model_Factory();
				$csv        = $csvFactory->create( $csvType );
				if ( ! empty( $csv ) ) {
					$message = '';
					$notice  = '';
					$backPage = 'page=' . (isset( $_REQUEST['backpage'] ) ? sanitize_text_field( $_REQUEST['backpage'] ) : 'woocommerce_mbe_csv_tabs&csv=' . $csvType);

					// default $item data to be used for new records
					$default = $csv->get_defaults();

					$table_name = $csv->get_tablename();

					// check post back and correct nonce
					if ( isset( $_REQUEST['nonce'] ) && wp_verify_nonce( $_REQUEST['nonce'], basename( __FILE__ ) ) ) {
						// combine default and request params
						$item = shortcode_atts( $default, $_REQUEST );
						// data validation
						$item_valid = $csv->validate_row( $item );
						try {
							if ( $item_valid === true ) {
								if ( empty($item[$csv->get_ID()]) ) {
									do_action(MBE_ESHIP_ID . '_csv_editor_adding', $item);
                                    if(!$csv->get_isRemote()) {
	                                    $result     = $wpdb->insert( $table_name, $item ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.SchemaChange
	                                    $item[$csv->get_ID()] = $wpdb->insert_id;
                                    }
									if ( $result !== false ) {
										$message = __( 'Item successfully saved', 'mail-boxes-etc' );
										do_action(MBE_ESHIP_ID . '_csv_editor_added', $item);
										wp_redirect(esc_url_raw(get_admin_url( get_current_blog_id(),'admin.php?'. $backPage. '&message='.urlencode($message) )) );
										exit;
									} else {
										$notice = __( 'There was an error while saving the item', 'mail-boxes-etc' ) . ': ' . $wpdb->last_error;
									}
								} else {
									do_action(MBE_ESHIP_ID . '_csv_editor_updating', $item);
									if(!$csv->get_isRemote()) {
										$result = $wpdb->update( $table_name, $item, array( 'id' => $item[$csv->get_ID()] ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.SchemaChange
									}
									if ( $result !== false ) {
										$message = __( 'Item successfully updated', 'mail-boxes-etc' );
										do_action(MBE_ESHIP_ID . '_csv_editor_updated', $item);
										wp_redirect(get_admin_url( get_current_blog_id(), 'admin.php?page=woocommerce_mbe_csv_tabs&csv=' . $csvType. '&message='.urlencode($message) ));
										exit;
									} else {
										$notice = __( 'There was an error while updating the item', 'mail-boxes-etc' ) . ': ' . $wpdb->last_error;
									}
								}
							} else {
								$notice = $item_valid;
							}
						} catch ( Exception $e ) {
							$notice = $e->getMessage();
						}
					} else {
						$item = $default;
						if ( isset( $_REQUEST['id'] ) ) {
                            if ($csv->get_isRemote()) {
                                $item = $csv->get_remoteRow($_REQUEST['id']);
                            } else {
	                            $item = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d", $_REQUEST['id'] ), ARRAY_A ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.SchemaChange
                            }
                            if ( ! $item ) {
								$item = $default;
//								$notice = __( 'Item not found', 'mail-boxes-etc' );
							}
						}
					}

					// custom meta box is a method of $csv class
					add_meta_box( MBE_ESHIP_ID . '_csv_form_meta_box', $csv->get_title(),
						array( $csv, 'form_meta_box_handler' ),
						'csv-' . $csvType, 'normal', 'default' );
					?>

					<div class="wrap">
						<div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
						<h2><?php esc_html_e('Edit' ) . ' ' . $csv->get_title( false ) ?>
							<a class="add-new-h2"
							   href="<?php echo esc_url(get_admin_url( get_current_blog_id(), 'admin.php?'.$backPage )); ?>">
								<?php esc_html_e( 'Back to list', 'mail-boxes-etc' ) ?>
							</a>
						</h2>

                        <?php $this->mbe_eship_wp_notification() ?>
						<?php if ( ! empty( $notice ) ): ?>
							<div id="notice" class="notice notice-error is-dismissible"><p><?php echo wp_kses_post($notice) ?></p></div>
						<?php endif; ?>
						<?php if ( ! empty( $message ) ): ?>
							<div id="message" class="notice notice-success is-dismissible"><p><?php echo wp_kses_post($message) ?></p></div>
						<?php endif; ?>

						<form id="form" method="POST">
							<input type="hidden" name="nonce"
							       value="<?php esc_attr_e(wp_create_nonce( basename( __FILE__ ) ) )?>"/>
							<?php /* storing id to check if we need to add or update the item */ ?>
							<input type="hidden" name="id" value="<?php esc_attr_e($item[$csv->get_ID()]) ?>"/>

							<div class="metabox-holder" id="poststuff">
								<div id="post-body">
									<div id="post-body-content">
										<?php /* render meta box */ ?>
										<?php do_meta_boxes( 'csv-' . $csvType, 'normal', $item ); ?>
										<input type="submit" value="<?php esc_attr_e( 'Save', 'mail-boxes-etc' ) ?>" id="submit"
										       class="button-primary" name="submit">
									</div>
								</div>
							</div>
						</form>
					</div>
					<?php
				} else {
					?>
					<div class="wrap">
						<h2><?php esc_html_e( 'Missing or wrong csv type', 'mail-boxes-etc' ) ?>
							<a class="add-new-h2"
							   href="<?php echo esc_url(get_admin_url( get_current_blog_id(), 'admin.php?page=' . mbe_e_link_get_settings_url() . '&tab=' . MBE_ESHIP_ID . '&section=mbe_general' )); ?>">
								<?php esc_html_e( 'Back to settings', 'mail-boxes-etc' ) ?>
							</a>
						</h2>
					</div>
					<?php
				}
			}

			public function pickup_data_form_page_handler() {
                $pickupModel = new Mbe_Shipping_Model_Pickup_Custom_Data();
				$orderIds    = isset($_REQUEST['orderids'])?json_decode($_REQUEST['orderids']) : null;
				$backPage = 'page=' . (isset( $_REQUEST['backpage'] ) ? sanitize_text_field( $_REQUEST['backpage'] ) :  MBE_ESHIP_ID . '_' . WOOCOMMERCE_MBE_TABS_PICKUP_PAGE);
				if ( ! empty( $orderIds ) ) {
					$message = '';
					$notice  = '';
//					$backPage = 'page=' . (isset( $_REQUEST['backpage'] ) ? sanitize_text_field( $_REQUEST['backpage'] ) :  MBE_ESHIP_ID . '_' . WOOCOMMERCE_MBE_TABS_PICKUP_PAGE);

					// default $item pickup data to be used for new records,
					// get local ones to avoid a call to the WS since very probably data will be verified and updated
					$default = $this->helper->getLocalPickupDefaultData();

					// initialize the row id to be merged while editing an existing row
					$default['id'] = null;

					// check post back and correct nonce
					if ( isset( $_REQUEST['nonce'] ) && wp_verify_nonce( $_REQUEST['nonce'], basename( __FILE__ ) ) ) {
						$default['date'] = '';
						$default['pickup_address_id'] = '';
						$default['pickup_batch_id'] = null;

						$item = shortcode_atts( $default, $_REQUEST );

						$item_valid = $pickupModel->validate_row( $item );

                        if($item_valid === true) {
	                        // Save the custom pickup data
	                        $batchId = null;
                            // If not updating
	                        if ( empty( $_REQUEST['id'] ) ) {
		                        if ( count( $orderIds ) > 1 ) {
			                        $now                     = current_datetime();
			                        $batchId                 = $this->helper->getWsUsername() . '_' . $now->getTimestamp();
			                        $item['pickup_batch_id'] = $batchId;
		                        }

                                // Additional check to avoid creating a new custom data row if orders are already linked to pickup data
                                $list=[];
		                        foreach ($orderIds as $orderId ) {
			                        if (!empty($this->helper->getOrderPickupBatchId($orderId))) {
				                        $list[] = $orderId;
			                        }
		                        }
                                if(!empty($list)) {
	                                $errMess = sprintf(__('The orders %s are already linked to pickup data'), implode(',', $list));
	                                $this->helper->logErrorAndSetWpAdminMessage($errMess);
	                                wp_redirect( esc_url_raw(admin_url('admin.php?'.$backPage ) ) );
	                                exit;
                                }

                                // Insert pickup custom data row
		                        $pickupCustomDataId = $pickupModel->insertRow( $item );

		                        // Set order items meta fields
		                        foreach ( $orderIds as $orderId ) {
			                        // Additional check to avoid updating field for already existing shipments
			                        if ( ! $this->helper->hasTracking( $orderId ) ) {
                                        $this->helper->setOrderPickupCustomDataId($orderId, $pickupCustomDataId);
                                        $this->helper->setOrderPickupBatchId($orderId, $batchId);
			                        }
		                        }
		                        $action = 'created';
	                        } else {
		                        // Update the custom pickup data
		                        $pickupModel->updateRow( $item['id'], $item );
		                        $batchId = $item['pickup_batch_id'];
                                $action = 'updated';
	                        }
                            // Updating the backpage to the pickup batch list to show the newly created batch, but not if we have a single shipment
//                            if(count($orderIds)>1) {
                                $backPage = 'page=' . MBE_ESHIP_ID . '_' . WOOCOMMERCE_MBE_TABS_PICKUP_PAGE;
//                            }

	                        if ( isset( $_REQUEST['submit_save'] ) ) {
		                        $this->helper->setWpAdminMessages( [
			                        'message' => urlencode(sprintf(__( 'The pickup %s has been %s', 'mail-boxes-etc' ) , (!empty($batchId)?'batch '.$batchId:''), __($action, 'mail-boxes-etc'))),
			                        'status'  => urlencode( 'success' )
		                        ] );
	                        } elseif ( isset( $_REQUEST['submit_savesend'] ) ) {
		                        $this->createPickup($orderIds);
	                        }
	                        wp_redirect( esc_url_raw(admin_url('admin.php?'.$backPage ) ) );
	                        exit;
                        } else {
	                        $notice = $item_valid;
                        }
					} else {
						$item = $default;

						if ( isset( $_REQUEST['id'] ) ) {
							$item = $pickupModel->getRow($_REQUEST['id']);
							if ( ! $item ) {
								$item = $default;
//								$notice = __( 'Item not found', 'mail-boxes-etc' );
							}
						}
					}

					$item['order_ids'] = is_array($orderIds)?$orderIds:[$orderIds];

                    add_meta_box( MBE_ESHIP_ID . '_pickup_data_form_meta_box',' ',
                        array( $pickupModel, 'form_meta_box' ),
                        'pickup-data-editor','normal','default'
                    );
                    ?>
                    <div class="wrap">
                            <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
                            <h2><?php esc_html_e('Pickup orders management', 'mail-boxes-etc') ?>
                                <a class="add-new-h2"
                                   href="<?php echo esc_url(get_admin_url( get_current_blog_id(), 'admin.php?'.$backPage )); ?>">
                                    <?php esc_html_e( 'Back to list', 'mail-boxes-etc' ) ?>
                                </a>
                            </h2>

                            <?php $this->mbe_eship_wp_notification() ?>
                            <?php if ( ! empty( $notice ) ): ?>
                                <div id="notice" class="notice notice-error is-dismissible"><p><?php echo wp_kses_post($notice) ?></p></div>
                            <?php endif; ?>
                            <?php if ( ! empty( $message ) ): ?>
                                <div id="message" class="notice notice-success is-dismissible"><p><?php echo wp_kses_post($message) ?></p></div>
                            <?php endif; ?>
                            <form id="form" method="POST">
                                <input type="hidden" name="nonce" value="<?php esc_attr_e(wp_create_nonce( basename( __FILE__ ) ) )?>"/>
                                <input type="hidden" name="id" value="<?php esc_attr_e($item['id']) ?>"
                                <div class="metabox-holder" id="poststuff">
                                    <div id="post-body">
                                        <div id="post-body-content">
                                            <?php do_meta_boxes('pickup-data-editor', 'normal' , $item)?>
                                        </div>
                                    </div>
                                </div>
                            </form>
                    </div>
                    <?php
				} else {
                    wp_redirect( esc_url_raw(admin_url('admin.php?'.$backPage ) ) );
                }
            }


			public function mbe_edit_pickup_data($post_ids) {
                if($this->helper->getPickupRequestEnabled()) {
	                switch ( $this->helper->getPickupRequestMode() ) {
		                case Mbe_Shipping_Helper_Data::MBE_PICKUP_REQUEST_MANUAL :
			                // Open default data editor and create a row for the batch ( pre-fill with default values from MOL ) and link it to the selected orders

			                $oneShipment = (1 === count( $post_ids ));

			                // If we are managing just one already sent shipment, don't open the form
//			                $openForm = ( !$oneShipment  || ! $this->helper->hasTracking( $post_ids[0] ) );
//
//			                if ( $openForm ) {
//                                $backPage = $oneShipment?'&backpage='.urlencode( WOOCOMMERCE_MBE_TABS_PAGE):'';
//				                wp_redirect( admin_url( 'admin.php?page=' . MBE_ESHIP_ID . '_pickup_data_tabs&orderids=' . urlencode( json_encode( $post_ids ) ).$backPage ) );
//			                } else {
//				                $this->helper->setWpAdminMessages( [
//					                'message' => urlencode( __( 'The shipment has already been sent', 'mail-boxes-etc' ) ),
//					                'status'  => urlencode( 'warning' )
//				                ] );
//			                }

			                $backPage = '&backpage='.urlencode( WOOCOMMERCE_MBE_TABS_PAGE);
			                wp_redirect( esc_url_raw(admin_url( 'admin.php?page=' . MBE_ESHIP_ID . '_pickup_data_tabs&orderids=' . urlencode( json_encode( $post_ids ) ).$backPage ) ) );
			                break;
		                case Mbe_Shipping_Helper_Data::MBE_PICKUP_REQUEST_AUTOMATIC :
			                $this->createPickup( $post_ids );
			                wp_redirect( esc_url_raw(admin_url('admin.php?page=' . WOOCOMMERCE_MBE_TABS_PAGE ) ) );
			                break;
	                }
                }
            }


			public function mbe_delete_pickup_custom_data() {
				$logger           = new \Mbe_Shipping_Helper_Logger();
				if ( isset( $_REQUEST['nonce'] ) && wp_verify_nonce( $_REQUEST['nonce'], 'mbe_delete_pickup_custom_data' ) ) {
					global $wpdb;
					$id       = sanitize_text_field( $_REQUEST['id'] );
					$backPage = 'page=' . (isset( $_REQUEST['backpage'] ) ? sanitize_text_field( $_REQUEST['backpage'] ) :  MBE_ESHIP_ID . '_' . WOOCOMMERCE_MBE_TABS_PICKUP_PAGE);

					$pickupCustomData = new Mbe_Shipping_Model_Pickup_Custom_Data();
					$row              = $pickupCustomData->getRow( $id );

//					if ( 'ready' === strtolower( $row['status'] ) && isset( $row['pickup_batch_id'] ) ) {
                    if ( 'ready' === strtolower( $row['status'] ) ) {
						// Detach all the order linked
						$selectOrderIds = $this->helper->select_pickup_orders_ids( $id );
						$orders      = $wpdb->get_results($selectOrderIds, ARRAY_A ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.SchemaChange
						$orderIds       = array_map( 'absint', array_column( $orders, 'order_id' ) );
						$notDetached = [];
						foreach ( $orderIds as $orderId ) {
							if(!$this->helper->detachOrderFromPickupBatch( $orderId )) {
                                $notDetached[] = $orderId;
							}
						}
                        if (empty($notDetached)) {
	                        // Delete the custom data row
	                        $pickupCustomData->deleteRow( $id );
                        } else {
                            $message = sprintf(__('Cannot delete the pickup custom data row since order(s) %s cannot be detached as it seems to be a shipped pickup(s)', 'mail-boxes-etc'), implode(',', $notDetached));
	                        $this->helper->logErrorAndSetWpAdminMessage($message, $logger);
                        }

					}

					wp_redirect( esc_url_raw(get_admin_url( get_current_blog_id(), 'admin.php?' . $backPage ) ) );
				}
			}

			public function mbe_detach_order_from_batch() {
				$logger = new \Mbe_Shipping_Helper_Logger();

				if ( isset( $_REQUEST['nonce'] ) && wp_verify_nonce( $_REQUEST['nonce'], 'mbe_detach_order_from_batch' ) ) {
					$orderId  = sanitize_text_field( $_REQUEST['mbe_pickup_postid'] );
					$backPage = 'page=' . (isset( $_REQUEST['backpage'] ) ? sanitize_text_field( $_REQUEST['backpage'] ) :  MBE_ESHIP_ID . '_' . WOOCOMMERCE_MBE_TABS_PICKUP_PAGE);
                    $pickupCustomDataId = $this->helper->getOrderPickupCustomDataId($orderId);

					if ($this->helper->detachOrderFromPickupBatch( $orderId )) {
						do_action(MBE_ESHIP_ID.'_after_detach', $pickupCustomDataId);
					} else {
						$message = sprintf(__('Order %d cannot be detached as it is a shipped pickup', 'mail-boxes-etc'), $orderId);
						$this->helper->logErrorAndSetWpAdminMessage($message, $logger);
					}
				}
				wp_redirect( esc_url_raw(get_admin_url( get_current_blog_id(), 'admin.php?' . $backPage ) ) );
            }

			/**
             * Delete pickup batch if there are no items left
             *
			 * @return void
			 */
//			public function mbe_after_detach_check_pickup_batch_items($batchId) {
//                $pickupCustomData = new Mbe_Shipping_Model_Pickup_Custom_Data();
//
//                if(empty($pickupCustomData->getBatchIdOrders($batchId))) {
//	                $pickupCustomData->deleteRow( $pickupCustomData->getBatchIdRow($batchId)->id );
//                }
//            }

			/**
			 * Delete pickup if there are no items left
			 *
			 * @return void
			 */
			public function mbe_after_detach_check_pickup_items($pickupCustomDataId) {
				$pickupCustomData = new Mbe_Shipping_Model_Pickup_Custom_Data();

				if(empty($pickupCustomData->getPickupIdOrders($pickupCustomDataId))) {
					$pickupCustomData->deleteRow( $pickupCustomDataId );
				}
			}


			public function mbe_send_pickup() {
				$logger = new \Mbe_Shipping_Helper_Logger();
                $logger->log('Send pickup Action - Start');
				$backPage = 'page=' . (isset( $_REQUEST['backpage'] ) ? sanitize_text_field( $_REQUEST['backpage'] ) :  MBE_ESHIP_ID . '_' . WOOCOMMERCE_MBE_TABS_PICKUP_PAGE);
				if ( isset( $_REQUEST['nonce'] ) && wp_verify_nonce( $_REQUEST['nonce'], 'mbe_send_pickup' ) && isset($_REQUEST['mbe_pickup_id'] ) && $this->helper->getPickupRequestEnabled())  {
					$logger->log('Send pickup Action - Create pickup');
					global $wpdb;
					$pickupCustomDataId = sanitize_text_field( $_REQUEST['mbe_pickup_id'] );
					$pickupCustomData = new Mbe_Shipping_Model_Pickup_Custom_Data();
                    $hasPickupBatch = !empty($pickupCustomData->getRow($pickupCustomDataId)['pickup_batch_id']);

//					$orders   = $wpdb->get_results( $this->helper->select_pickup_batch_orders_ids( $pickupBatchId ), ARRAY_A );
					$orders   = $wpdb->get_results( $this->helper->select_pickup_orders_ids( $pickupCustomDataId ), ARRAY_A ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.SchemaChange
					$orderIds = array_map( 'absint', array_column( $orders, 'order_id' ) );
					$this->createPickup( $orderIds, $hasPickupBatch );
				}
                if(!$this->helper->getPickupRequestEnabled()) {
	                $message = __( 'Pickup request cannot be enabled, please check the settings', 'mail-boxes-etc' );
	                $this->helper->logErrorAndSetWpAdminMessage($message, $logger);
                }
				$logger->log('Send pickup Action - End');
				wp_redirect( esc_url_raw(get_admin_url( get_current_blog_id(), 'admin.php?' . $backPage ) ) );
			}

			public function mbe_create_return_shipment() {
				$backPage = 'page=' . (isset( $_REQUEST['backpage'] ) ? sanitize_text_field( $_REQUEST['backpage'] ) :  MBE_ESHIP_ID . '_' . WOOCOMMERCE_MBE_TABS_PICKUP_PAGE);
				if ( isset( $_REQUEST['nonce'] ) && wp_verify_nonce( $_REQUEST['nonce'], 'mbe_create_return_shipment' ) && isset( $_REQUEST['mbe_post_id'] ) ) {
					$postId = array_map('absint', (array)$_REQUEST['mbe_post_id']);
                    $this->createReturnShipment($postId);
				}
				wp_redirect( esc_url_raw(get_admin_url( get_current_blog_id(), 'admin.php?' . $backPage ) ) );
			}

			public function mbe_after_delete_pickup_address_disable_pickup_if_no_default_address($ids, $csvType) {
				$logger = new \Mbe_Shipping_Helper_Logger();
				$helper = new Mbe_Shipping_Helper_Data();
				if ( 'pickup-addresses' === $csvType
                     && ! $helper->hasDefaultPickupAddress()
				     && $helper->getPickupRequestEnabled()
				     && $helper->getPickupRequestMode() === Mbe_Shipping_Helper_Data::MBE_PICKUP_REQUEST_AUTOMATIC
				) {
					$helper->setOption( Mbe_Shipping_Helper_Data::XML_PATH_PICKUP_REQUEST_ENABLED, 0 );
					$message = __( 'Pickup request cannot be enabled, please set a default pickup address', 'mail-boxes-etc' );
					$this->helper->logErrorAndSetWpAdminMessage($message);
					$logger->log( 'Default pickup address missing, disabling pickup' );
				}
			}

			function mbe_eship_check_tax_and_duties_default_options() {
				$helper = new Mbe_Shipping_Helper_Data();
				$logger = new Mbe_Shipping_Helper_Logger();
				// Check if Activation flag exists and if the default value setter didn't already run
				if(!$helper->hasOption(Mbe_Shipping_Helper_Data::XML_PATH_TAX_DUTIES_ENABLED) && $helper->getPermissionEnabledTaxAndDuties()) {
					update_option( MBE_SCHEMA_FLAG_OPTION, 'yes' );
					$logger->log('Updating schema flag to run default value setter for Tax and Duties');
				}
			}

			function mbe_eship_add_tax_and_duties_fee($data) {
				$selectedMethod = WC()->session->get('chosen_shipping_methods');
				WC()->session->set( 'mbe_tax_and_duties_show_info_text', '');

				if($this->helper->isMBEShippingMethod($selectedMethod) ) {
					$rateData = $this->getRateData($selectedMethod[0]);

                    $rateMetaData = $rateData->get_meta_data();
                    if(!empty($rateData) && !empty($rateMetaData)) {
	                    $customDutyGuaranteed = $rateMetaData['tax_and_duties_data']['custom_duties_guaranteed'] ?? false;

	                    if ( $customDutyGuaranteed && $this->helper->addTaxAndDutiesToTotal() ) {
		                    WC()->session->set( 'mbe_tax_and_duties_show_info_text', 'DDP');
	                    } else if ( $this->helper->isEnabledTaxAndDuties() ) {
		                    WC()->session->set( 'mbe_tax_and_duties_show_info_text', 'DAP');
	                    }
	                    WC()->session->set( 'mbe_tax_and_duties_info_text_value', $rateMetaData['tax_and_duties_data']['net_tax_and_duty_total_price']??0);
                    }

                }
			}

			function mbe_show_tax_and_duties_checkout_message () {
                // If selected method is mbe and Tax&duty is not guaranteed or not DDP, show the message
				$taxAndDutiesDAP = WC()->session->get( 'mbe_tax_and_duties_show_info_text' )??'DAP';
                $taxAndDutiesValue = WC()->session->get( 'mbe_tax_and_duties_info_text_value' )??null;

				switch ($taxAndDutiesDAP) {
                    case 'DAP':
                        echo wp_kses_post('<div style="font-size: 1rem; padding: 1rem; background: #eee;">
                                    '. sprintf(__('As for all international shipments, customs requires a payment to clear goods through customs. It is a cost independent of our policies and tariffs. The <strong><span>%s</span></strong> figure shown may vary depending on the legislation of the country of destination.' , 'mail-boxes-etc'), wp_kses_post(wc_price($taxAndDutiesValue))) .'
                              </div>');
                        break;
                    case 'DDP':
                        echo wp_kses_post('<div style="font-size: 1rem; padding: 1rem; background: #eee;">
                                '. sprintf(__('As with for all international shipments, customs requires a payment to clear goods through customs. It is a cost independent of our policies and tariffs, but the figure of <strong><span>%s</span></strong> charged ensures customs clearance, taken care of by us.' , 'mail-boxes-etc'), wp_kses_post(wc_price($taxAndDutiesValue))) .'
                          </div>');
                        break;
                    default:
                        break;
				}
			}

            function mbe_eship_check_tax_and_duties() {
                if($this->helper->mustDisableTaxAndDuties() && $this->helper->isEnabledTaxAndDuties()) {
                    $this->helper->setEnabledTaxAndDuties(0);
                    $this->helper->logErrorAndSetWCAdminMessage(__( 'Tax and Duties cannot be enabled, please check the pickup mode and courier and services settings', 'mail-boxes-etc' ), new Mbe_Shipping_Helper_Logger());
                }
            }

			public function wf_mbe_wooCommerce_shipping_hide_custom_order_meta_keys( $formatted_meta, $thisa ) {
				foreach($formatted_meta as $key => $meta){
					if(in_array($meta->key, array(
                            Mbe_Shipping_Helper_Data::META_FIELD_DELIVERY_POINT_CUSTOM_DATA,
                            Mbe_Shipping_Helper_Data::META_FIELD_DELIVERY_POINT_SHIPMENT,
						    Mbe_Shipping_Helper_Data::META_FIELD_PICKUP_CUSTOM_DATA_ID,
						    Mbe_Shipping_Helper_Data::META_FIELD_IS_PICKUP_SHIPPING,
                    ))) {
						unset($formatted_meta[$key]);
					}
				}
				return $formatted_meta;
			}

			/**
			 * @param array $shippingMethod
			 *
			 * @return bool
			 */
			protected function isDeliveryPointServiceMethod( array $shippingMethod ): bool {
				$serviceOK = ! empty( preg_grep( '/^' . MBE_DELIVERY_POINT_SERVICE_METHOD . '/', $shippingMethod ) );

				return $serviceOK;
			}

			/**
			 * @return array
			 */
			protected function getShippingTaxPercentageFromCart(): float {
				$taxPercentage = 0;
				foreach ( WC()->cart->get_shipping_taxes() as $key => $shipping_tax ) {
					$taxPercentage += WC_Tax::get_rate_percent_value( $key );
				}

				return $taxPercentage;
			}

			protected function getRateData($chosen_method_id) {
				$found_rate  = null;
				$packages    = WC()->cart->get_shipping_packages();

				foreach ($packages as $package_key => $package) {
					$shipping_for_package = WC()->session->get('shipping_for_package_' . $package_key);

					if (! empty($shipping_for_package['rates'][$chosen_method_id])) {
						$found_rate = $shipping_for_package['rates'][$chosen_method_id];
						break;
					}
				}

				return $found_rate;
			}

		}
	}
	new mbe_e_link_wooCommerce_shipping_setup();

}