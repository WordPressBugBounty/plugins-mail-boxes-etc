<?php

namespace Metaboxes;

abstract class DepartmentAddressData {

	public static function add( $item, $screens = [] ) {
		$screens = ! is_array( $screens ) ? [ $screens ] : $screens;
		add_meta_box( MBE_ESHIP_ID . '_department_address_data_form_meta_box',
			' ',
			[ self::class, 'html' ],
			$screens,
			'normal',
			'default',
			$item
		);
	}

	public static function html( $orderIds ) {
		$helper = new \Mbe_Shipping_Helper_Data();
        $departmentsAddressList = $helper->getMolDepartmentsAddress();
        $selectedAddress = count($orderIds)===1?$helper->getOrderDepartmentAddressId($orderIds[0]):null; // set the selected value for single order
		?>
        <table style="width: 100%;" class="form-table">
            <tbody>
            <tr class="form-field">
                <th scope="row">
                    <label for="address"><?php esc_html_e( 'Order list', 'mail-boxes-etc' ) ?></label>
                </th>
                <td>
                    <div style="width: 95%; height: auto">
						<?php
						foreach ( $orderIds as $order ) {
							?>
                            <span style="font-size: smaller; float: left; color:#0a4b78; border: solid 1px #0a4b78; padding:4px ;text-align: center;border-radius: 5px; margin: 3px;">
                                <?php esc_attr_e( $order ) ?>
                            </span>
							<?php
						}
						?>
                    </div>
                </td>
            </tr>
            <tr class="form-field">
                <th scope="row">
                    <label for="address"><?php esc_html_e( 'Department address', 'mail-boxes-etc' ) ?></label>
                    <span style="color: #c03939">*</span>
                </th>
                <td>
                    <select id="department_address_id" name="department_address_id" required>
						<?php
						foreach ( $departmentsAddressList as $key=>$value ) {
							?>
                            <option value=<?php echo esc_attr($key) ?> <?php echo $key==$selectedAddress?'selected':'' ?> >
								<?php esc_html_e($value) ?>
                            </option>
							<?php
						}
						?>
                    </select>
                </td>
            </tr>
            </tbody>
        </table>
		<?php
	}

}