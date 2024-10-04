<?php

class Mbe_Shipping_Model_Pickup_Custom_Data implements Mbe_Shipping_Entity_Model_Interface {

	use \Traits\Mbe_Entity_Model_Trait {
        insertRow as protected traitInserRow;
		truncate as protected traitTruncate;
        tableExists as protected traitTableExists;
		insertRow as protected traitInsertRow;
		updateRow as protected traitUpdateRow;
		deleteRow as protected traitDeleteRow;
    }

	public function tableExists() {
		return $this->traitTableExists($this->getTableName());
	}

	public function truncate() {
		return $this->traitTruncate($this->getTableName());
	}

	public function updateRow($id, $row ) {
		return $this->traitUpdateRow($this->getTableName(), $id, $row);
	}

	public function deleteRow( $id ) {
		return $this->traitDeleteRow($this->getTableName(), $id);
    }

	/**
	 * Validate a row based on specific criteria
	 *
	 * @param array $item The row data to be validated
	 *
	 * @return true|string Returns true if the row is valid, or a string with error messages if the row is invalid
	 */
	public function validate_row($item) {
		$helper = new Mbe_Shipping_Helper_Data();
        $ws = new Mbe_Shipping_Model_Ws();
		$messages = array();

        // address exists
        $pickupAddress = $ws->getPickupAddressById($item['pickup_address_id']);
        if(empty($pickupAddress)) $messages[] = __("Please check that the address selected exists", 'mail-boxes-etc' );

        // start time >= end time
		if(!$helper->checkStartEndTime($item['preferred_from'], $item['preferred_to'])) $messages[] = __("Maximum pickup preferred time should be greater than minimum", 'mail-boxes-etc' );
		if(!$helper->checkStartEndTime($item['alternative_from'], $item['alternative_to'])) $messages[] = __("Maximum pickup alternative time should be greater than minimum", 'mail-boxes-etc' );

        // Pickup date not in the past
		$pickupDate = new DateTime($item['date']);
		$today = new DateTime('today');
        if($pickupDate < $today) $messages[] = __("Pickup date can't be set to the past", 'mail-boxes-etc' );

		if (empty($messages)) return true;
		return implode('<br />', $messages);
	}

    // Override the trait to return the new row id
    public function insertRow( $row ) {
	    global $wpdb;
	    try {
		    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.SchemaChange
		    if (!$wpdb->insert($this->getTableName(), $row)) {
			    $this->helper->setWpAdminMessages( [
				    'message' => urlencode( $wpdb->last_error ),
				    'status'  => urlencode( 'error' )
			    ] );
			    \WC_Admin_Settings::add_error(__('Error while adding data to the table ') . $this->getTableName() . ' :' . $wpdb->last_error);
			    return false;
		    }
	    } catch (\Exception $e) {
		    $this->helper->setWpAdminMessages( [
			    'message' => urlencode( $e->getMessage() ),
			    'status'  => urlencode( 'error' )
		    ] );
		    \WC_Admin_Settings::add_error(__('Unexpected error') . ': ' . $e->getMessage());
	    }
	    return $wpdb->insert_id;
    }

	public function getTableName() {
		global $wpdb;
		return $wpdb->prefix . Mbe_Shipping_Helper_Data::MBE_PICKUP_CUSTOM_DATA_TABLE_NAME;
	}

	public function getSqlCreate() {
		global $wpdb;
		$charset = $wpdb->get_charset_collate();

		return "CREATE TABLE " . $this->getTableName() . " (
        id                          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY ,
        pickup_batch_id             varchar(255)                                    NULL,
        pickup_address_id           varchar(256)                                    NULL,
		cutoff                      varchar(20)                                     NULL,
		preferred_from              varchar(5)                                      NULL,
		preferred_to                varchar(5)                                      NULL,
		alternative_from            varchar(5)                                      NULL,
		alternative_to              varchar(5)                                      NULL,
		notes                       text                                            NULL,
		date                        date                                            NULL,
	    status                      varchar(20)                     DEFAULT 'ready' NOT NULL,
	    INDEX  " . $this->getTableName() . "_pickup_batch_id_index (pickup_batch_id)
    )  $charset;";

	}

	public function getRow( $rowId ) {
		global $wpdb;
		return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . $this->getTableName() . " where id = %d", $rowId), ARRAY_A ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.SchemaChange
	}

	/**
	 * Get the row for a specific pickupBatchId
	 * @param $pickupBatchId
	 */
	public function getBatchIdRow( $pickupBatchId ) {
		global $wpdb;
		return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM %i where pickup_batch_id = %s", $this->getTableName(), $pickupBatchId )); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.SchemaChange
	}

	public function getBatchIdOrders( $pickupBatchId, $orderClause = '' ) {
		global $wpdb;
		$orderItemTable = $wpdb->prefix . 'woocommerce_order_items';
		$orderItemMetaTable = $wpdb->prefix . 'woocommerce_order_itemmeta';

		if ( \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled() ) {
			$postTable = $wpdb->prefix . 'wc_orders';
		} else {
			$postTable = $wpdb->prefix . 'posts';
		}

        $metakey = Mbe_Shipping_Helper_Data::META_FIELD_PICKUP_BATCH_ID;
        $orderSql = $orderClause?"ORDER BY $orderClause":"";

		$query = $wpdb->prepare("SELECT DISTINCT p.*,p.ID orderid FROM $postTable AS p
                    INNER JOIN $orderItemTable AS oi ON p.id = oi.order_id
                    LEFT JOIN $orderItemMetaTable AS oim ON oi.order_item_id = oim.order_item_id
					WHERE oi.order_item_type='shipping'
					AND oim.meta_key = %s
					AND oim.meta_value = %s 
					$orderSql",
                array($metakey, $pickupBatchId)
		);
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.SchemaChange
		return $wpdb->get_results($query, ARRAY_A);
    }

	public function getPickupIdOrders( $pickupCustomDataId, $orderClause = '' ) {
		global $wpdb;
		$orderItemTable = $wpdb->prefix . 'woocommerce_order_items';
		$orderItemMetaTable = $wpdb->prefix . 'woocommerce_order_itemmeta';

		if ( \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled() ) {
			$postTable = $wpdb->prefix . 'wc_orders';
		} else {
			$postTable = $wpdb->prefix . 'posts';
		}
		$metakey = Mbe_Shipping_Helper_Data::META_FIELD_PICKUP_CUSTOM_DATA_ID;
		$orderSql = $orderClause?"ORDER BY $orderClause":"";

		$query = $wpdb->prepare("SELECT DISTINCT p.*,p.ID orderid FROM $postTable AS p
                    INNER JOIN $orderItemTable AS oi ON p.id = oi.order_id
                    LEFT JOIN $orderItemMetaTable AS oim ON oi.order_item_id = oim.order_item_id
					WHERE oi.order_item_type='shipping'
					AND oim.meta_key = %s
					AND %s = oim.meta_value 
					$orderSql",
			array($metakey, $pickupCustomDataId)
		);
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.SchemaChange
		return $wpdb->get_results($query, ARRAY_A);
	}


	public function getStatus( $pickupBatchId ) {
		$row = $this->getBatchIdRow( $pickupBatchId );
		if($row) {
			return $row->status;
		}
		return false;
	}

	public function setStatus( $rowId, $value = 'ready' ) {
		global $wpdb;
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.SchemaChange
		$wpdb->update(
			$this->getTableName(),
			['status' => $value],
			['id' => $rowId]
		);
	}

	public function form_meta_box($item) {
        $helper = new Mbe_Shipping_Helper_Data();
		require_once( MBE_ESHIP_PLUGIN_DIR . 'includes/class-mbe-csv-editor-pickup-addresses.php' );
		$csvPickupAddresses = new Mbe_Shipping_Csv_Editor_Pickup_Addresses();
		$tomorrow = new DateTime('tomorrow');
        $today = new DateTime('today');
//        $pickupBatchIdInput = isset($item['pickup_batch_id'])?'<input type="hidden" name="pickup_batch_id" id="pickup_batch_id" value="' . esc_attr($item['pickup_batch_id']) . '"/>':''
		?>

		<table style="width: 100%;" class="form-table">
			<tbody>
            <?php echo (isset($item['pickup_batch_id'])?'<input type="hidden" name="pickup_batch_id" id="pickup_batch_id" value="' . esc_attr($item['pickup_batch_id']) . '"/>':'') ?>
            <tr class="form-field">
                <th scope="row">
                    <label for="address"><?php esc_html_e( 'Order list', 'mail-boxes-etc' ) ?></label>
                </th>
                <td>
                    <div style="width: 95%; height: auto">
                        <?php
                        foreach ( $item['order_ids'] as $order ) {
                            ?>
                            <span style="font-size: smaller; float: left; color:#0a4b78; border: solid 1px #0a4b78; padding:4px ;text-align: center;border-radius: 5px; margin: 3px;">
                                <?php esc_attr_e($order)?>
                            </span>
                            <?php
                        }
                        ?>
                    </div>
                </td>
            </tr>
            <tr class="form-field">
                <th scope="row">
                    <label for="address"><?php esc_html_e( 'Pickup address', 'mail-boxes-etc' ) ?></label>
                    <span style="color: #c03939">*</span>
                </th>
                <td>
                    <select id="pickup_address_id" name="pickup_address_id">
                        <?php
                        foreach ( $csvPickupAddresses->get_remoteRows() as $address ) {
                            ?>
                            <option value="<?php esc_attr_e($address['pickup_address_id']) ?>" <?php echo $address['pickup_address_id']==($item['pickup_address_id']??'')?"selected":"" ?>>
                                <?php esc_html_e($address['trade_name'] .', ' . $address['address_1'] .', ' . $address['zip_code'] .', ' . $address['city']) ?>
                            </option>
                            <?php
                        }
                        ?>
                    </select>
                    <p style="margin-top:5px; margin-bottom: 5px;">
                        <?php
//                        $returnLink = urlencode_deep(esc_url(MBE_ESHIP_ID . "_pickup_data_tabs&orderids=".json_encode($item['order_ids'])));
//                        $buttonLink = esc_url(sprintf("admin.php?backpage=%s&page=' . MBE_ESHIP_ID . '_csv_edit_form&action=new&csv=pickup-addresses&id=%s", $returnLink, null));
//                        echo '<a class="button" href="'.$buttonLink.'">'.__('Add New Pickup Address', 'mail-boxes-etc').'</a>';
                        $returnLink = urlencode(MBE_ESHIP_ID . "_pickup_data_tabs&orderids=".json_encode($item['order_ids']));
                        $hrefUrl = get_admin_url(null, "admin.php?backpage=".$returnLink."&page=" . MBE_ESHIP_ID . "_csv_edit_form&action=new&csv=pickup-addresses&id=");
                        echo wp_kses_post(sprintf( '<a class="button" href="%s">%s</a>',$hrefUrl, __('Add New Pickup Address', 'mail-boxes-etc')));
                        ?>
                    </p>
                </td>
            </tr>
<!--            Pickup Date-->
			<tr class="form-field">
				<th scope="row">
					<label for="date"><?php esc_html_e( 'Pickup Date', 'mail-boxes-etc' ) ?></label>
					<span style="color: #c03939">*</span>
				</th>
				<td>
					<input id="date" name="date" type="date" style="width: 95%"
					       value="<?php esc_html_e($item['date']??$tomorrow->format('Y-m-d')) ?>"
                           min="<?php  esc_html_e($today->format('Y-m-d')) ?>"
					       required>
                    <p class="description">
                        <?php esc_html_e('Pickup for PUDO shipments will be booked automatically after shipments closure for the next working day', 'mail-boxes-etc') ?>
                    </p>
				</td>
			</tr>
<!--            Preferred time-->
            <tr class="form-field">
                <th scope="row">
                    <label for="preferred">
                        <span><?php esc_html_e('Pickup Time - Preferred', 'mail-boxes-etc') ?></span>
                    </label>
                    <span style="color: #c03939">*</span>
                </th>
                <td class="forminp forminp-time">
                    <fieldset>
                        <legend class="screen-reader-text"><span><?php __('Pickup Time - Preferred from', 'mail-boxes-etc') ?></span></legend>
                        <input class="input-text regular-input"
                               type="time" name="preferred_from"
                               id="preferred_from"
                               value="<?php echo esc_attr($item['preferred_from']) ?>"
                        />
                        <legend class="screen-reader-text"><span><?php __('Pickup Time - Preferred to', 'mail-boxes-etc') ?></span></legend>
                        <input class="input-text regular-input"
                               type="time" name="preferred_to"
                               id="preferred-"
                               value="<?php echo esc_attr($item['preferred_to']) ?>"
			            />
                        <p class="description">
                            <?php esc_html_e('Minimum and maximum pickup time that will be communicated to the courier(N.B. pickup time is approximate and may not be observed by the final courier)', 'mail-boxes-etc') ?>
                        </p>
                    </fieldset>
                </td>
            </tr>
<!--            Alternative time-->
            <tr class="form-field">
                <th scope="row">
                    <label for="alternative">
                        <span><?php esc_html_e('Pickup Time - Alternative', 'mail-boxes-etc') ?></span>
                    </label>
                </th>

                <td class="forminp forminp-time">
                    <fieldset>
                        <legend class="screen-reader-text"><span><?php __('Pickup Time - Alternative from', 'mail-boxes-etc') ?></span></legend>
                        <input class="input-text regular-input"
                               type="time" name="alternative_from"
                               id="alternative_from"
                               value="<?php echo esc_attr($item['alternative_from']) ?>"
                        />
                        <legend class="screen-reader-text"><span><?php __('Pickup Time - Alternative to', 'mail-boxes-etc') ?></span></legend>
                        <input class="input-text regular-input"
                               type="time" name="alternative_to"
                               id="alternative_to"
                               value="<?php echo esc_attr($item['alternative_to']) ?>"
                        />
                        <p class="description">
							<?php esc_html_e('Alternative minimum and maximum pickup time that will be communicated to the courier (N.B. pickup time is approximate and may not be observed by the final courier)', 'mail-boxes-etc') ?>
                        </p>
                    </fieldset>
                </td>
            </tr>
<!--            Note -->
            <tr class="form-field">
                <th scope="row">
                    <label for="notes"><?php esc_html_e( 'Pickup notes', 'mail-boxes-etc' ) ?></label>
                </th>
                <td>
                    <input id="notes" name="notes" type="text" style="width: 95%"
                           value="<?php echo esc_attr( $item['notes'] ) ?>"
                           required>
                </td>
            </tr>

            <tr class="form-field">
                <th></th>
                <td>
                    <div style="float:right;">
                        <?php /*
                        if( Mbe_Shipping_Helper_Data::MBE_PICKUP_REQUEST_MANUAL === $helper->getPickupRequestMode() &&
                            Mbe_Shipping_Helper_Data::MBE_CREATION_MODE_MANUALLY === $helper->getShipmentsCreationMode() &&
                            (count($item['order_ids']) > 1 || !empty($item['pickup_batch_id']))
                        ) {
                        */ ?>
                            <input type="submit" value="<?php esc_html_e( 'Save', 'mail-boxes-etc' ) ?>" id="submit_save"
                                   class="button-primary" name="submit_save">
                        <?php /* } */?>
                        <?php if($helper->getPickupRequestEnabled()) { ?>
                        <input type="submit" value="<?php esc_html_e( 'Save and send', 'mail-boxes-etc' ) ?>" id="submit_savesend"
                               class="button-primary" name="submit_savesend">
			            <?php } ?>
                    </div>
                </td>

            </tr>
			</tbody>
		</table>


	<?php
	}

}