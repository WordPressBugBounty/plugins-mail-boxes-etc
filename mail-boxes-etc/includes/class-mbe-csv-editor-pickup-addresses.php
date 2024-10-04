<?php

if (!class_exists('Mbe_Shipping_Csv_Editor')) {
	require_once(__DIR__ . '/class-mbe-csv-editor.php');
}

class Mbe_Shipping_Csv_Editor_Pickup_Addresses extends Mbe_Shipping_Csv_Editor {

	function __construct( $args = array(
		'singular' => 'pickup address',
		'plural'   => 'pickup addresses'
	) ) {

		$this->tableName =  Mbe_Shipping_Helper_Data::MBE_CSV_PICKUP_ADDRESSES_TABLE_NAME;

        $this->idColumn = 'pickup_address_id';

		$this->columns = [
			'cb' => '<input type="checkbox" />',
			'id' => __('Pickup address id', 'mail-boxes-etc'),
//			'pickup_address_id' => __('Pickup address id', 'mail-boxes-etc'),
			'trade_name' => __('Trade Name', 'mail-boxes-etc'),
			'address_1' =>  __('Address', 'mail-boxes-etc'),
			'zip_code' =>  __('Postcode', 'mail-boxes-etc'),
			'city' =>  __('City', 'mail-boxes-etc'),
			'province' =>  __('Province', 'mail-boxes-etc'),
			'country' =>  __('Country', 'mail-boxes-etc'),
			'reference' => __('Reference', 'mail-boxes-etc'),
			'phone_1' => __('Telephone 1', 'mail-boxes-etc'),
			'email_1' => __('Email', 'mail-boxes-etc'),
            'is_default' => __('Default', 'mail-boxes-etc'),
		];

		$this->sortable_columns = [
			'id'           => ['id', true],
//			'pickup_address_id' => ['pickup_address_id', true],
            'trade_name' => ['trade_name', true],
            'city' => ['city', true],
            'reference' => ['reference', true],

		];

		$this->csvType = 'pickup-addresses';

		$this->default = [
//			'id' =>'0' ,
			'pickup_address_id' => '',
			'trade_name' => '',
            'address_1' => '',
			'address_2' => '',
			'address_3' => '',
            'zip_code' => '',
            'city' => '',
            'province' => '',
            'country' => '',
            'reference' => '',
            'phone_1' => '',
			'phone_2' => '',
            'email_1' => '',
			'email_2' => '',
            'fax' => '',
            'res' => 0,
            'mmr' => 0,
            'ltz' => 0,
            'is_default' => 0,
		];

        $this->backLink = 'mbe_pickup';

        $this->isRemote = true;

		// Send new pickup address to MOL
        add_action(MBE_ESHIP_ID . '_csv_editor_adding', array($this, 'mbe_woocommerce_add_pickup_address_data'));

		// Send updated pickup address to MOL
		add_action(MBE_ESHIP_ID . '_csv_editor_updating', array($this, 'mbe_woocommerce_update_pickup_address_data'));

        // Delete the pickup address in MOL
        add_action(MBE_ESHIP_ID . '_csv_editor_deleting', array($this, 'mbe_woocommerce_delete_pickup_address_data'));


		parent::__construct( $args );
	}

	/**
	 * @throws \MbeExceptions\ApiRequestException
	 */
	public function get_remoteRows($orderBy = null, $order = 'asc'): array {
        $orderBy = $orderBy ?? $this->get_ID();
	    $ws = new Mbe_Shipping_Model_Ws();
        $addresses = $ws->getPickupAddressesData();
        $remoteAddresses = [];
        if(!empty($addresses)) {
	        foreach ( $addresses as $address ) {
		        $remoteAddresses[] = $this->toLocalArrayFormat( $address );
	        }
	        return $this->sortRemoteRows($remoteAddresses, $orderBy, $order);
        } else {
            return [];
        }

	}

	/**
	 * @throws \MbeExceptions\ApiRequestException
	 */
	public function get_remoteRow($id) {
		$ws = new Mbe_Shipping_Model_Ws();
        $address = $ws->getPickupAddressById($id);
        return !empty($address)?$this->toLocalArrayFormat($address):null;
    }

    public function toLocalArrayFormat($address) {
	    return [
		    'pickup_address_id' => $address['PickupAddressId'],
		    'trade_name'        => $address['TradeName'],
		    'address_1'         => $address['Address1'],
		    'address_2'         => $address['Address2'] ?? '',
		    'address_3'         => $address['Address3'] ?? '',
		    'zip_code'          => $address['ZipCode'],
		    'city'              => $address['City'],
		    'province'          => $address['Province'],
		    'country'           => $address['Country'],
		    'reference'         => $address['Reference'],
		    'phone_1'           => $address['Phone1'],
		    'phone_2'           => $address['Phone2'] ?? '',
		    'email_1'           => $address['Email1'],
		    'email_2'           => $address['Email2'] ?? '',
		    'fax'               => $address['Fax'] ?? '',
		    'res'               => $address['RES'],
		    'mmr'               => $address['MMR'],
		    'ltz'               => $address['LTZ'],
		    'is_default'        => $address['IsDefault'],
	    ];
    }

	/** Send created address to MOL
	 * @throws \MbeExceptions\ApiRequestException
	 */
	public function mbe_woocommerce_add_pickup_address_data($address) {
		remove_action(MBE_ESHIP_ID . '_csv_editor_adding', array($this, 'mbe_woocommerce_add_pickup_address_data'));
		$ws = new Mbe_Shipping_Model_Ws();
		$result = $ws->addPickupAddressData($address);
//        $pickupAddressId = $result->PickupAddressId;

        // Set the action to update the address row with the generated Pickup Address ID
//        add_action('mbe_eship_csv_editor_added', function($address) use($pickupAddressId) {
//            global $wpdb;
//            $address['pickup_address_id'] = $pickupAddressId;
//	        $wpdb->update( $this->tableName, $address, array( 'id' => $address['id'] ) );
//        });
	}

	/** Send updated address to MOL
	/**
	 * @throws \MbeExceptions\ApiRequestException
	 */
	public function mbe_woocommerce_update_pickup_address_data($address) {
		remove_action(MBE_ESHIP_ID . '_csv_editor_updating', array($this, 'mbe_woocommerce_update_pickup_address_data'));
		$ws = new Mbe_Shipping_Model_Ws();
		$result = $ws->updatePickupAddressData( $address );
	}

	/**
	 * @throws \MbeExceptions\ApiRequestException
	 */
	public function mbe_woocommerce_delete_pickup_address_data($ids) {
		remove_action(MBE_ESHIP_ID . '_csv_editor_deleting', array($this, 'mbe_woocommerce_delete_pickup_address_data'));
		$ws = new Mbe_Shipping_Model_Ws();
		foreach ( $ids as $id ) {
		    $result = $ws->deletePickupAddressData( $id );
        }
	}

	public function get_title($singular = true){
		if($singular) {
			return ucwords(__('pickup address','mail-boxes-etc'));
		}
		return ucwords(__('pickup addresses','mail-boxes-etc'));
	}

	function column_is_default($item)
	{
		return $item['is_default'] ? '<div  style="align-self:center"><span class="dashicons dashicons-yes"></span></div>':'';
	}

    function column_country( $item ) {
        $countries = new WC_Countries();
        $countries = $countries->get_countries();

        return $countries[$item['country']]??$item['country'];
    }

	public function validate_row($item) {

		$messages = array();

		if (empty(trim($item['trade_name']))) $messages[] = __('trade name', 'mail-boxes-etc') . ' ' . __('required');
		if (empty(trim($item['address_1']))) $messages[] = __('address 1', 'mail-boxes-etc') . ' ' . __('required');
		if (empty(trim($item['zip_code']))) $messages[] = __('zipcode', 'mail-boxes-etc') . ' ' . __('required');
		if (empty(trim($item['city']))) $messages[] = __('city', 'mail-boxes-etc') . ' ' . __('required');
		if (empty(trim($item['province']))) $messages[] = __('province', 'mail-boxes-etc') . ' ' . __('required');
		if (empty(trim($item['country']))) $messages[] = __('country', 'mail-boxes-etc') . ' ' . __('required');
		if (empty(trim($item['reference']))) $messages[] = __('reference', 'mail-boxes-etc') . ' ' . __('required');
		if (empty(trim($item['phone_1']))) $messages[] = __('phone 1', 'mail-boxes-etc') . ' ' . __('required');
		if (empty(trim($item['email_1']))) $messages[] = __('email 1', 'mail-boxes-etc') . ' ' . __('required');
//		if (empty(trim($item['res']))) $messages[] = __('res', 'mail-boxes-etc') . ' ' . __('required');
//		if (empty(trim($item['mmr']))) $messages[] = __('mmr', 'mail-boxes-etc') . ' ' . __('required');
//		if (empty(trim($item['ltz']))) $messages[] = __('ltz', 'mail-boxes-etc') . ' ' . __('required');

		if (empty($messages)) return true;

		return implode('<br />', $messages);
	}

	public function form_meta_box_handler( $item ) {
        $helper = new Mbe_Shipping_Helper_Data();
		$countries = new WC_Countries();

		?>
		<table style="width: 100%;" class="form-table">
			<tbody>
			<?php if($item[$this->get_ID()]!=='') { ?>
<!--                <tr class="form-field">-->
<!--                    <th scope="row">-->
<!--                        <label for="id">--><?php //_e( 'ID', 'mail-boxes-etc' ) ?><!--</label>-->
<!--                    </th>-->
<!--                    <td>-->
<!--                        <label id="id" style="width: 95%">--><?php //echo esc_attr( $item[$this->get_ID()] ) ?><!--</label>-->
<!--                    </td>-->
<!--                </tr>-->
                <tr class="form-field">
                    <th scope="row">
                        <label for="pickup_address_id_label"><?php esc_html_e( 'Pickup Address ID', 'mail-boxes-etc' ) ?></label>
                    </th>
                    <td>
                        <label id="pickup_address_id_label" style="width: 95%"><?php echo esc_attr( $item['pickup_address_id'] ) ?></label>
                        <input id="pickup_address_id" name="pickup_address_id" type="hidden"
                               value="<?php esc_attr_e( $item['pickup_address_id'] ) ?>">
                    </td>
                </tr>
			<?php } ?>
            <tr class="form-field">
                <th scope="row">
                    <label for="trade_name"><?php esc_html_e( 'Trade Name', 'mail-boxes-etc' ) ?></label>
                    <span style="color: #c03939">*</span>
                </th>
                <td>
                    <input id="trade_name" name="trade_name" type="text" style="width: 95%"
                           value="<?php echo esc_attr( $item['trade_name'] ) ?>"
                           placeholder="<?php esc_attr_e( 'Trade Name', 'mail-boxes-etc' ) ?>"
                           required>
                </td>
            </tr>

            <tr class="form-field">
                <th scope="row">
                    <label for="address_1"><?php esc_html_e( 'Address', 'mail-boxes-etc' ) ?> 1</label>
                    <span style="color: #c03939">*</span>
                </th>
                <td>
                    <input id="address_1" name="address_1" type="text" style="width: 95%"
                           value="<?php echo esc_attr( $item['address_1'] ) ?>"
                           placeholder="<?php esc_attr_e( 'Address', 'mail-boxes-etc' ) ?>"
                           required>
                </td>
            </tr>

            <tr class="form-field">
                <th scope="row">
                    <label for="address_2"><?php esc_html_e( 'Address', 'mail-boxes-etc' ) ?> 2</label>
                </th>
                <td>
                    <input id="address_2" name="address_2" type="text" style="width: 95%"
                           value="<?php echo esc_attr( $item['address_2'] ) ?>"
                           placeholder="<?php esc_attr_e( 'Address', 'mail-boxes-etc' ) ?>"
                           >
                </td>
            </tr>

            <tr class="form-field">
                <th scope="row">
                    <label for="address_3"><?php esc_html_e( 'Address', 'mail-boxes-etc' ) ?> 3</label>
                </th>
                <td>
                    <input id="address_3" name="address_3" type="text" style="width: 95%"
                           value="<?php echo esc_attr( $item['address_3'] ) ?>"
                           placeholder="<?php esc_attr_e( 'Address', 'mail-boxes-etc' ) ?>"
                           >
                </td>
            </tr>

            <tr class="form-field">
                <th scope="row">
                    <label for="zip_code"><?php esc_html_e( 'Postcode', 'mail-boxes-etc' ) ?></label>
                    <span style="color: #c03939">*</span>
                </th>
                <td>
                    <input id="zip_code" name="zip_code" type="text" style="width: 95%"
                           value="<?php echo esc_attr( $item['zip_code'] ) ?>"
                           placeholder="<?php esc_attr_e( 'Postcode', 'mail-boxes-etc' ) ?>"
                           required>
                </td>
            </tr>

            <tr class="form-field">
                <th scope="row">
                    <label for="city"><?php esc_html_e( 'City', 'mail-boxes-etc' ) ?></label>
                    <span style="color: #c03939">*</span>
                </th>
                <td>
                    <input id="city" name="city" type="text" style="width: 95%"
                           value="<?php echo esc_attr( $item['city'] ) ?>"
                           placeholder="<?php esc_attr_e( 'City', 'mail-boxes-etc' ) ?>"
                           required>
                </td>
            </tr>

            <tr class="form-field">
                <th scope="row">
                    <label for="province"><?php esc_html_e( 'Province', 'mail-boxes-etc' ) ?></label>
                    <span style="color: #c03939">*</span>
                </th>
                <td>
                    <input id="province" name="province" type="text" style="width: 95%"
                           value="<?php echo esc_attr( $item['province'] ) ?>"
                           placeholder="<?php esc_attr_e( 'Province', 'mail-boxes-etc' ) ?>"
                           required>
                </td>
            </tr>

            <tr class="form-field">
                <th scope="row">
                    <label for="country"><?php esc_html_e( 'Country', 'mail-boxes-etc' ) ?></label>
                    <span style="color: #c03939">*</span>
                </th>
                <td>
                    <select id="country" name="country" style="width: 95%" required>
                        <?php foreach ( $countries->get_countries() as $code=>$label ) {
                            echo '<option value="'.esc_attr($code).'" '. (strtolower($item['country']) === strtolower($code)?'selected':'') .'>'.esc_html($label).'</option>';
                        } ?>
                    </select>
                </td>
            </tr>

            <tr class="form-field">
                <th scope="row">
                    <label for="reference"><?php esc_html_e( 'Reference', 'mail-boxes-etc' ) ?></label>
                    <span style="color: #c03939">*</span>
                </th>
                <td>
                    <input id="reference" name="reference" type="text" style="width: 95%"
                           value="<?php echo esc_attr( $item['reference'] ) ?>"
                           placeholder="<?php esc_attr_e( 'Reference', 'mail-boxes-etc' ) ?>"
                           required>
                </td>
            </tr>

            <tr class="form-field">
                <th scope="row">
                    <label for="phone_1"><?php esc_html_e( 'Telephone', 'mail-boxes-etc' ) ?> 1</label>
                    <span style="color: #c03939">*</span>
                </th>
                <td>
                    <input id="phone_1" name="phone_1" type="tel" style="width: 95%"
                           pattern="^\s*(?:\+?(\d{1,3}))?([-. (]*(\d{3})[-. )]*)?((\d{3})[-. ]*(\d{2,4})(?:[-.x ]*(\d+))?)\s*$"
                           value="<?php echo esc_attr( $item['phone_1'] ) ?>"
                           placeholder="<?php esc_attr_e( 'Telephone', 'mail-boxes-etc' ) ?>"
                           required>
                </td>
            </tr>

            <tr class="form-field">
                <th scope="row">
                    <label for="phone_2"><?php esc_html_e( 'Telephone', 'mail-boxes-etc' ) ?> 2</label>
                </th>
                <td>
                    <input id="phone_2" name="phone_2" type="tel" style="width: 95%"
                           pattern="^\s*(?:\+?(\d{1,3}))?([-. (]*(\d{3})[-. )]*)?((\d{3})[-. ]*(\d{2,4})(?:[-.x ]*(\d+))?)\s*$"
                           value="<?php echo esc_attr( $item['phone_2'] ) ?>"
                           placeholder="<?php esc_attr_e( 'Telephone', 'mail-boxes-etc' ) ?>"
                           >
                </td>
            </tr>

            <tr class="form-field">
                <th scope="row">
                    <label for="email_1"><?php esc_html_e( 'Email', 'mail-boxes-etc' ) ?> 1</label>
                    <span style="color: #c03939">*</span>
                </th>
                <td>
                    <input id="email_1" name="email_1" type="email" style="width: 95%"
                           value="<?php echo esc_attr( $item['email_1'] ) ?>"
                           placeholder="<?php esc_attr_e( 'email', 'mail-boxes-etc' ) ?>"
                           required>
                </td>
            </tr>

            <tr class="form-field">
                <th scope="row">
                    <label for="email_2"><?php esc_html_e( 'Email', 'mail-boxes-etc' ) ?> 2</label>
                </th>
                <td>
                    <input id="email_2" name="email_2" type="email" style="width: 95%"
                           value="<?php echo esc_attr( $item['email_2'] ) ?>"
                           placeholder="<?php esc_attr_e( 'Email', 'mail-boxes-etc' ) ?>"
                           >
                </td>
            </tr>

            <tr class="form-field">
                <th scope="row">
                    <label for="fax"><?php esc_html_e( 'Fax', 'mail-boxes-etc' ) ?></label>
                </th>
                <td>
                    <input id="fax" name="fax" type="tel" style="width: 95%"
                           pattern="^\s*(?:\+?(\d{1,3}))?([-. (]*(\d{3})[-. )]*)?((\d{3})[-. ]*(\d{2,4})(?:[-.x ]*(\d+))?)\s*$"
                           value="<?php echo esc_attr( $item['fax'] ) ?>"
                           placeholder="<?php esc_attr_e( 'Fax', 'mail-boxes-etc' ) ?>"
                           >
                </td>
            </tr>
            <?php if( !($helper->getCountry() === "FR" || $helper->getCountry() === "DE") ) {?>
                <tr class="form-field">
                    <th scope="row">
                        <label for="res"><?php esc_html_e( 'RES', 'mail-boxes-etc' ) ?></label>
                    </th>
                    <td>
                        <input id="res" name="res" class="form-check-input" type="checkbox"
                               value="1" <?php echo esc_attr( $item['res']?'checked':'' ) ?>>
                    </td>
                </tr>

                <tr class="form-field">
                    <th scope="row">
                        <label for="mmr"><?php esc_html_e( 'MMR', 'mail-boxes-etc' ) ?></label>
                    </th>
                    <td>
                        <input id="mmr" name="mmr" class="form-check-input" type="checkbox"
                               value="1" <?php echo esc_attr( $item['mmr']?'checked':'' ) ?>>
                    </td>
                </tr>

                <tr class="form-field">
                    <th scope="row">
                        <label for="ltz"><?php esc_html_e( 'LTZ', 'mail-boxes-etc' ) ?></label>
                    </th>
                    <td>
                        <input id="ltz" name="ltz" class="form-check-input" type="checkbox"
                               value="1" <?php echo esc_attr( $item['ltz']?'checked':'' ) ?>>
                    </td>
                </tr>
            <?php } ?>
            <tr class="form-field">
                <th scope="row">
                    <label for="is_default"><?php esc_html_e( 'Default', 'mail-boxes-etc' ) ?></label>
                </th>
                <td>
                    <input id="is_default" name="is_default" class="form-check-input" type="checkbox"
                           value="1" <?php echo esc_attr( $item['is_default']?'checked':'' ) ?>>
                </td>
            </tr>
			</tbody>
		</table>


		<?php
	}
}