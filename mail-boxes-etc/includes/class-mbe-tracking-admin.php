<?php

class woocommerce_mbe_tracking_admin
{
    const TRACKING_TITLE_DISPLAY = "MBE Shipment Tracking";

    const SHIPMENT_SOURCE_TRACKING_NUMBER = "woocommerce_mbe_tracking_number";
    const SHIPMENT_SOURCE_TRACKING_NAME = "woocommerce_mbe_tracking_name";
    const SHIPMENT_SOURCE_TRACKING_SERVICE = "woocommerce_mbe_tracking_service";
    const SHIPMENT_SOURCE_TRACKING_ZONE = "woocommerce_mbe_tracking_zone";
    const SHIPMENT_SOURCE_TRACKING_URL = "woocommerce_mbe_tracking_url";
    const SHIPMENT_SOURCE_TRACKING_FILENAME = 'woocommerce_mbe_tracking_filename';
	const SHIPMENT_SOURCE_TRACKING_CUSTOM_MAPPING = "woocommerce_mbe_tracking_custom_mapping";
	const TRACKING_METABOX_KEY = "Tracking_Mbe_box";

    protected $helper;
	/**
	 * @var array
	 */
	private array $tracking_files=[];
	/**
	 * @var array|string|string[]
	 */
	private $tracking_string;
	/**
	 * @var array|string
	 */
	private $tracking_name;
	/**
	 * @var array|string
	 */
	private  $tracking_url;
	private string $closure_file;

	function __construct()
    {
	    $this->helper = new Mbe_Shipping_Helper_Data();

        if (is_admin()) {

            $this->init();
        }
    }

    private function init()
    {
        add_action('add_meta_boxes', array($this, 'add_mbe_tracking_metabox'), 15);
    }

    function add_mbe_tracking_metabox()
    {
        $screen = class_exists( '\Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController' ) && wc_get_container()->get( \Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController::class )->custom_orders_table_usage_is_enabled()
            ? wc_get_page_screen_id( 'shop-order' )
            : 'shop_order';

        add_meta_box(
            self::TRACKING_METABOX_KEY,
            __( self::TRACKING_TITLE_DISPLAY, 'mail-boxes-etc' ),
            array( $this, 'tracking_metabox_content' ),
            $screen,
            'side',
            'default'
        );
    }

    function tracking_metabox_content($post_or_order_object)
    {
	    $order = ( $post_or_order_object instanceof WP_Post ) ? wc_get_order( $post_or_order_object->ID ) : $post_or_order_object;

	    if (isset($order)) {
            $order_id      = $order->get_id();
		    $tracking_data = $this->helper->getTrackings($order_id);

		    if ($this->helper->isEnabled() && $this->helper->isMbeShipping($order) && !empty( $tracking_data )) {
			    $this->tracking_string = $this->helper->getTrackingsString($order_id);
			    $this->tracking_files = $this->helper->getFileNames($order_id);
			    if ( \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled() ) {
				    $this->tracking_name = $order->get_meta(self::SHIPMENT_SOURCE_TRACKING_NAME);
				    $this->tracking_url = $order->get_meta(self::SHIPMENT_SOURCE_TRACKING_URL);
			    } else {
				    $this->tracking_name = get_post_meta($order_id, self::SHIPMENT_SOURCE_TRACKING_NAME, true);
				    $this->tracking_url = get_post_meta($order_id, self::SHIPMENT_SOURCE_TRACKING_URL, true);
			    }
			    if ( isset( $tracking_data[0]) && !$this->helper->isTrackingOpen( $tracking_data[0])) {
				    $this->closure_file = $this->helper->mbeUploadUrl().'/MBE_' . $tracking_data[0] . "_closed.pdf";
			    }
		    } else {
                return;
            }
	    } else {
		    return;
        }



	    ?>
		<ul class="order_actions submitbox">
			<li id="actions" class="wide">
				<strong><?php esc_html_e('Carrier name: ', 'mail-boxes-etc') ?></strong> <?php esc_html_e($this->tracking_name) ?>
			</li>
            <?php if ( count( $tracking_data ) > 1) { ?>
				<li id="actions" class="wide">
					<a target="_blank" href="<?php esc_attr_e($this->tracking_url . $this->tracking_string) ?>"><?php esc_html_e('Track all', 'mail-boxes-etc') ?></a>
				</li>
            <?php } ?>

            <?php foreach ( $tracking_data as $t) { ?>
				<li id="actions" class="wide">
					<strong><?php esc_html_e('Tracking id: ', 'mail-boxes-etc') ?></strong> <a target="_blank" href="<?php esc_attr_e($this->tracking_url . $t) ?>"><?php esc_html_e($t) ?></a>
				</li>
            <?php } ?>
            <?php for ($i = 0; $i < count($this->tracking_files); $i++) { ?>
				<li id="actions" class="wide">
					<strong><?php esc_html_e('Label', 'mail-boxes-etc') . " " . ($i + 1) . ": "; ?></strong> <a target="_blank" href="<?php esc_attr_e($this->helper->mbeUploadUrl(). DIRECTORY_SEPARATOR . $this->tracking_files[$i]); ?>"><?php esc_html_e("link", 'mail-boxes-etc'); ?></a>
				</li>
            <?php } ?>
            <?php if (isset($this->closure_file)) { ?>
				<li id="actions" class="wide">
					<strong><?php esc_html_e('Closure file', 'mail-boxes-etc') . ": "; ?></strong> <a target="_blank" href="<?php esc_attr_e($this->closure_file); ?>"><?php esc_html_e("link", 'mail-boxes-etc'); ?></a>
				</li>
            <?php } ?>
		</ul>
		<script>
            // jQuery(document).ready(function ($) {
            //     $("date-picker").datepicker();
            // });

            jQuery("a.woocommerce_shipment_tracking").on("click", function () {
                location.href = this.href + '&wc_track_shipment=' + jQuery('#tracking_shipment_ids').val().replace(/ /g, '') + '&shipping_service=' + jQuery("#shipping_service").val();
                return false;
            });
		</script>
        <?php
    }

    function load_order($orderId)
    {
        if (!class_exists('WC_Order')) {
            return false;
        }
        $order = false;

        try {
            $order = wc_get_order($orderId);
        }
        catch (Exception $e) {
        }
        return $order;
    }
}

new woocommerce_mbe_tracking_admin();

?>