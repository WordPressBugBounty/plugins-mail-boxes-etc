<?php

use Dompdf\Dompdf;
use \iio\libmergepdf\Merger;
use \iio\libmergepdf\Driver\TcpdiDriver;

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class Mbe_E_Link_Pickup_Batches_List_Table extends WP_List_Table
{

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

	// Override the WP_List one to avoid creation of the nonce fields ( no parameter to display_tablenav )
	public function display() {
		$singular = $this->_args['singular'];

		$this->display_tablenav( 'bottom' );

		$this->screen->render_screen_reader_content( 'heading_list' );
		?>
		<table class="wp-list-table <?php esc_attr_e(implode( ' ', $this->get_table_classes() )); ?>">
			<?php $this->print_table_description(); ?>
			<thead>
			<tr>
				<?php $this->print_column_headers(); ?>
			</tr>
			</thead>

			<tbody id="the-list"
				<?php
				if ( $singular ) {
					esc_attr_e( " data-wp-lists='list:$singular'");
				}
				?>
			>
			<?php $this->display_rows_or_placeholder(); ?>
			</tbody>

			<tfoot>
			<tr>
				<?php $this->print_column_headers( false ); ?>
			</tr>
			</tfoot>

		</table>
		<?php
		$this->display_tablenav( 'bottom' );
	}

	public function display_rows() {
		global $wpdb;
        $pickupCustomModel = new Mbe_Shipping_Model_Pickup_Custom_Data();

		$backPage = urlencode(MBE_ESHIP_ID . '_' . WOOCOMMERCE_MBE_TABS_PICKUP_PAGE);

		$orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'id';
		$order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'desc';
		$orderClause = sanitize_sql_orderby($orderby . ' ' . $order);

		foreach ( $this->items as $item ) {

			// get shipments for the pickup batch id
			$shipments = $pickupCustomModel->getPickupIdOrders($item['id'], $orderClause);

			$orderIds = array_map('absint', array_column($shipments, 'orderid'));

			$editButton = '<a href="'.admin_url('admin.php?page=' . MBE_ESHIP_ID . '_pickup_data_tabs&id=' . urlencode( $item['id'] ) .'&orderids=' . urlencode( json_encode($orderIds))).'&backpage='.$backPage.'">
							<button type="button" style="height:35px; width:35px; margin: 0px 5px 0px 5px; padding:4px 6px 4px 6px; color:#778899FF; cursor:pointer;" title="'.__('Edit pickup batch', 'mail-boxes-etc').'">
								<svg style="height: 16px;width: 16px;" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="-1 -1 21 21">
							    	<path d="m13.835 7.578-.005.007-7.137 7.137 2.139 2.138 7.143-7.142-2.14-2.14Zm-10.696 3.59 2.139 2.14 7.138-7.137.007-.005-2.141-2.141-7.143 7.143Zm1.433 4.261L2 12.852.051 18.684a1 1 0 0 0 1.265 1.264L7.147 18l-2.575-2.571Zm14.249-14.25a4.03 4.03 0 0 0-5.693 0L11.7 2.611 17.389 8.3l1.432-1.432a4.029 4.029 0 0 0 0-5.689Z"/>
							  	</svg>
						   </button></a>';

			$sendButton = '<form action="' . esc_url( admin_url( 'admin-post.php' ) ) .'" method="post" id="mbe_send_pickup" style="display: inline-flex">
								<input type="hidden" name="action" value="mbe_send_pickup"/>
								<input type="hidden" name="nonce" value="'.wp_create_nonce('mbe_send_pickup') .'"/>
								<input type="hidden" name="mbe_pickup_id" value="'. urlencode( $item['id'] ).'"/>
							    <button type="submit" style="height:35px; width:35px; margin: 0px 5px 0px 5px; padding:4px 6px 4px 6px; color:#778899FF; cursor:pointer;" title="'.__('Send', 'mail-boxes-etc').'">
							        <svg style="transform:rotate(45deg); height: 16px;width: 16px; " aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="-2 1 20 20">
								        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 17 8 2L9 1 1 19l8-2Zm0 0V9"/>
								    </svg>
							   	</button>
						   </form>';

			$deleteButton = '<form action="' . esc_url( admin_url( 'admin-post.php' ) ) .'" method="post" id="mbe_delete_pickup_custom_data" style="display: inline-flex">
								<input type="hidden" name="action" value="mbe_delete_pickup_custom_data"/>
								<input type="hidden" name="nonce" value="'.wp_create_nonce('mbe_delete_pickup_custom_data') .'"/>
								<input type="hidden" name="id" value="'. urlencode( $item['id'] ).'"/>
								
								<button type="submit" style="height:35px; width:35px; margin: 0px 5px 0px 5px; padding:4px 6px 4px 6px; color:#778899FF; cursor:pointer;" title="'.__('Destroy pickup batch', 'mail-boxes-etc').'">
									<svg style="height: 17px;width: 17px;" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 20">
									    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h16M7 8v8m4-8v8M7 1h4a1 1 0 0 1 1 1v3H6V2a1 1 0 0 1 1-1ZM3 5h12v13a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V5Z"/>
									  </svg>
								</button>
								</form>';

			$manifestButton = '<form action="' . esc_url( admin_url( 'admin-post.php' ) ) .'" method="post" id="mbe_download_pickup_manifest" style="display: inline-flex">
									<input type="hidden" name="action" value="mbe_download_pickup_manifest"/>
									<input type="hidden" name="nonce" value="'.wp_create_nonce('mbe_download_pickup_manifest') .'"/>
									<input type="hidden" name="mbe_pickup_postid" value="'. urlencode( !empty($orderIds)?$orderIds[0]:'' ).'"/>
									
									<button type="submit" style="height:35px; width:35px; margin: 0px 5px 0px 5px; padding:4px 6px 4px 6px; color:#778899FF; cursor:pointer;" title="'.__('Download manifest', 'mail-boxes-etc').'">
										<svg style="height: 16px;width: 16px;" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
								            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.5 10.25a2.25 2.25 0 1 0 0 4.5 2.25 2.25 0 0 0 0-4.5Zm0 0a2.225 2.225 0 0 0-1.666.75H12m3.5-.75a2.225 2.225 0 0 1 1.666.75H19V7m-7 4V3h5l2 4m-7 4H6.166a2.225 2.225 0 0 0-1.666-.75M12 11V2a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v9h1.834a2.225 2.225 0 0 1 1.666-.75M19 7h-6m-8.5 3.25a2.25 2.25 0 1 0 0 4.5 2.25 2.25 0 0 0 0-4.5Z"/>
										</svg>
									</button>
							    </form>';


			// Shows the buttons based on the batch status only if pickup mode is manual, since you can't send pickup if it's automatic
            if(Mbe_Shipping_Helper_Data::MBE_PICKUP_REQUEST_MANUAL === $this->helper->getPickupRequestMode()) {
	            switch ( strtolower( $item['status'] ) ) {
		            case 'ready':
			            $actionButtons = $editButton . $sendButton . $deleteButton;
			            break;
		            case 'initialized':
			            $actionButtons = $sendButton;
			            break;
		            case 'sent':
		            case 'confirmed':
			            $actionButtons = $manifestButton;
			            break;
		            default:
			            $actionButtons = '';
			            break;
	            }
            } else {
                // Enable manifest and the delete button that can be used to detach the orders, so it can be managed differently
	            switch ( strtolower( $item['status'] ) ) {
		            case 'ready':
			            $actionButtons = $deleteButton;
			            break;
		            case 'confirmed':
			            $actionButtons = $manifestButton;
			            break;
		            default:
			            $actionButtons = '';
			            break;
	            }
            }

			// Add display to the safe display propetries. To be used when escaping actionbuttons
            add_filter( 'safe_style_css', function( $styles ) {
				$styles[] = 'display';
				return $styles;
			} );

			echo '<style>
                    .actions.column-actions {
                        vertical-align: middle;
                    }
                  </style>
                  <tr style="background-color: #e2f3db">
					<th style="text-transform: capitalize;" colspan="' . count($this->get_columns()) . '">
					<div style="float: left; margin: 0px 15px 0px 5px;">'.
			     wp_kses_post(empty($item['pickup_batch_id'])?'<strong>' . __('Single order pickup', 'mail-boxes-etc') . '</strong>':'	<strong>' . __('batch id', 'mail-boxes-etc') . ': </strong>' . $item['pickup_batch_id']) .
					'</div>
					<div style="float: left; margin: 0px 15px 0px 5px;">
						<strong>' . esc_html__('Status', 'mail-boxes-etc') . ': </strong>' . wp_kses_post(strtoupper($item['status'])) . '
					</div>
					<div style="float: right; margin: 0px 5px 0px 5px;text-align: right">
						' . wp_kses( $actionButtons, [
                                'a'      => [ 'href'=>true, 'class'=> true, 'style' => true],
                                'form'   => [ 'action' => true, 'method' => true, 'id'=>true, 'style' => true],
                                'input'  => [ 'type' => true, 'name' => true, 'value' => true ],
                                'button' => [ 'type' => true, 'style' => true, 'title' => true ],
                                'svg'    => [
                                    'style'       => true,
                                    'fill'        => true,
                                    'viewbox'     => true,
                                    'role'        => true,
                                    'aria-hidden' => true,
                                    'focusable'   => true,
                                    'height'      => true,
                                    'width'       => true,
                                    'xmlns'       => true,
                                ],
                                'path'   => [
                                    'd'    => true,
                                    'fill' => true,
	                                'stroke'=> true,
                                    'stroke-linecap'=> true,
                                    'stroke-linejoin'=> true,
                                    'stroke-width'=> true,
                                 ],
                            ] ) . '
					</div>
					</th>
				  </tr>';

			foreach ( $shipments as $shipment ) {
				$this->single_row( $shipment );
			}
		}
	}

	public function single_row( $item ) {
		echo '<tr>';
		$this->single_row_columns( $item );
		echo '</tr>';
	}

    function column_ID($item)
    {

        $actions = array(
            'edit' => sprintf('<a href="' . get_home_url() . '/wp-admin/post.php?post=%s&action=edit">%s</a>', $item['orderid'], __('Edit', 'mail-boxes-etc')),
        );
        return sprintf('%s %s',
            $item['orderid'],
            $this->row_actions($actions)
        );

    }

    function column_post_author($item)
    {
        $order = wc_get_order($item['orderid']);

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
        $order = wc_get_order($item["orderid"]);
        $serviceName = $this->helper->getServiceName($order);
        return $serviceName;
    }


    function column_tracking($item)
    {
        $trackings = $this->helper->getTrackings($item['orderid']);
        if (empty($trackings)) {
            return '';
        }
        else {
            $html = '';
	        if ( \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled() ) {
		        $order = wc_get_order($item['orderid']);
		        $url = $order->get_meta('woocommerce_mbe_tracking_url');
	        } else {
		        $url = get_post_meta($item['orderid'], 'woocommerce_mbe_tracking_url', true);
	        }

            $trackingString = $this->helper->getTrackingsString($item['orderid']);
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
        $order = wc_get_order($item['orderid']);
		$date = new DateTimeImmutable($order->get_date_created());
	    return wp_date( get_option( 'date_format' ), $date->getTimestamp() );
    }

    function column_total($item)
    {
        $order = wc_get_order($item['orderid']);
        return $order->get_total() . ' &euro;';
    }

    function column_payment($item)
    {
        $order = wc_get_order($item['orderid']);
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
        return $this->helper->isShippingOpen($item['orderid']) ? __('Opened', 'mail-boxes-etc') : __('Closed', 'mail-boxes-etc');
    }

    function column_files($item)
    {
        $files = $this->helper->getFileNames($item['orderid']);

        $trackings = $this->helper->getTrackings($item['orderid']);
        if (empty($files)) {
            return '';
        }
        else {
            $html = '';
            for ($i = 0; $i < count($files); $i++) {
                $filename = __('Label', 'mail-boxes-etc') . " " . ($i + 1);
                $path = $this->helper->mbeUploadUrl(). DIRECTORY_SEPARATOR . $files[$i];
                $html .= "<a target='_blank' href=" . $path . " style='margin-bottom:5px;display: inline-block;'>" . $filename . "</a></br>";
            }
            if (isset($trackings[0]) && !$this->helper->isTrackingOpen($trackings[0])) {
                $path = $this->helper->mbeUploadUrl(). DIRECTORY_SEPARATOR . 'MBE_' . $trackings[0] . "_closed.pdf";
                $html .= "<a target='_blank' href=" . $path . " style='margin-bottom:5px;display: inline-block;'>" . __('Closure file', 'mail-boxes-etc') . "</a></br>";
            }
            return $html;
        }
    }

    function column_cb($item)
    {
//        return sprintf(
//            '<input type="checkbox" name="id[]" value="%s" />',
//            $item['orderid']
//        );
	    return '';
    }

	function column_actions($item) {

        $returnButton = '<form action="' . esc_url( admin_url( 'admin-post.php' ) ) .'" method="post" id="mbe_create_return_shipment" style="display: inline-flex">
								<input type="hidden" name="action" value="mbe_create_return_shipment"/>
								<input type="hidden" name="nonce" value="'.wp_create_nonce('mbe_create_return_shipment') .'"/>
								<input type="hidden" name="mbe_post_id" value="'. urlencode( $item['orderid'] ).'"/>
	                            <button type="submit" class="mbe_pickup_order_button" title="'.__('Create return shipment', 'mail-boxes-etc').'">
									<svg style="transform:rotate(180deg); height: 16px;width: 16px; padding-top: 4px" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 4 16 14">
									    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m12 7 3-3-3-3m0 12H5.5a4.5 4.5 0 1 1 0-9H14"/>
									</svg>
								</button>
						</form>';

		$detachButton = '<form action="' . esc_url( admin_url( 'admin-post.php' ) ) .'" method="post" id="mbe_detach_order_from_batch" style="display: inline-flex">
								<input type="hidden" name="action" value="mbe_detach_order_from_batch"/>
								<input type="hidden" name="nonce" value="'.wp_create_nonce('mbe_detach_order_from_batch') .'"/>
								<input type="hidden" name="mbe_pickup_postid" value="'. urlencode( $item['orderid'] ).'"/>
								<button type="submit" class="mbe_pickup_order_button" title="'.__('Detach from Pickup Batch', 'mail-boxes-etc').'">
									<svg style="height: 16px;width: 16px; padding-top: 4px" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
										<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m13 7-6 6m0-6 6 6m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
									</svg>
								</button>
						</form>';

		return '<div style="float: right">' .
		       '<style>' .
		       'button.mbe_pickup_order_button {
                    margin: 0px 5px 0px 5px;
                    color:#778899FF;
                    border: 0;
                    background: none;
                    box-shadow: none;
                    border-radius: 0px;
                    cursor:pointer;
                  } 
                  button.mbe_pickup_order_button:hover {
                    background-color:#e3e3e3;
                  }' .
		       '</style>' .
		       ($this->helper->hasTracking($item['orderid'])?$returnButton:$detachButton) .
		       '</div>';

	}

    function get_columns()
    {
	    $columns = array('cb' => '');
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
	    $columns['actions'] = __('', 'mail-boxes-etc');

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
/*        $actions = array();
        if (!$this->helper->isCreationAutomatically()) {
            $actions['creation'] = __('Create shipments in MBE Online', 'mail-boxes-etc'). __( '(no pickup data)', 'mail-boxes-etc' );
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

		// To be used for single pickup shipment (manual only)
		if($this->helper->getPickupRequestEnabled() && !$this->helper->isCreationAutomatically() ) {
			$actions['creation_pickup'] = __( 'Create shipments in MBE Online', 'mail-boxes-etc' ) . __( '(with pickup data)', 'mail-boxes-etc' );
		}

        return $actions;
*/
		return null;
    }

    function prepare_items()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . MBE_ESHIP_ID . '_pickup_custom_data';
	    $this->mustCloseShipments = $this->ws->mustCloseShipments(); // Use a local parameter to avoid calling the function multiple times for each row

	    $per_page = 5;
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);

        $this->process_bulk_action();

        $paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged']) - 1) : 0;
        $paged = $paged * $per_page;

	    $total_items = $wpdb->get_var( $wpdb->prepare("SELECT COUNT(DISTINCT(ID)) FROM %i", $table_name));  // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.SchemaChange

	    if (isset($_REQUEST["order_search"]) && $_REQUEST["order_search"] != "") {
            $search = esc_sql($_REQUEST["order_search"]);

            $query = $wpdb->prepare("SELECT pcd.*, pcd.ID FROM $table_name AS pcd
                                       GROUP BY pcd.ID
                                       ORDER BY id DESC LIMIT %d OFFSET %d", array($per_page, $paged));
            $this->items = $wpdb->get_results($query, ARRAY_A);  // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.SchemaChange
        }
        else {
	        // {$order_filter}
            
	        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.SchemaChange
            $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name
                                                        ORDER by id desc
                                                        LIMIT %d OFFSET %d", array($per_page, $paged)), ARRAY_A);
        }

        $this->set_pagination_args(array(
            'total_items' => $total_items, // total items defined above
            'per_page'    => $per_page, // per page constant defined at top of method
            'total_pages' => ceil($total_items / $per_page) // calculate pages count
        ));
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