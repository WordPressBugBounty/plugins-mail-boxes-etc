<?php

namespace Metaboxes;

use DateTime;
use Mbe_Shipping_Csv_Editor_Pickup_Addresses;
use Mbe_Shipping_Helper_Data;

abstract class AdvancedReturnAddressData {

	public static function add( $item, $screens = [] ) {
		$screens = ! is_array( $screens ) ? [ $screens ] : $screens;
		add_meta_box( MBE_ESHIP_ID . '_advanced_return_address_data_form_meta_box',
			' ',
			[ self::class, 'html' ],
			$screens,
			'normal',
			'default',
			$item
		);
	}

	public static function html( $orderId ) {

		$helper = new Mbe_Shipping_Helper_Data();
        $ws = new \Mbe_Shipping_Model_Ws();

        $originalShipmentData = $ws->getShipmentItems( $orderId );
        $originalShipmentSender = $originalShipmentData->ShipmentsFullInfo->ShipmentFullInfo->SenderInfo;
        $originalShipmentReceiver = $originalShipmentData->ShipmentsFullInfo->ShipmentFullInfo->ReceiverInfo;

		$nextMonday                = new DateTime( 'next monday' );
		$today                     = new DateTime( 'today' );
		$fifteenMinutesBeforeNoon  = new DateTime( 'front of 10AM' );
		$noon                      = new DateTime( '10AM' );
		$countries                 = new \WC_Countries();
//		$shopCountry               = $helper->getCountry();
		?>

        <div style="width: 100%; overflow: hidden;">
<!--Sender            -->
            <div style="float: left; width: 28%; margin-right: 2%;"><h3><?php esc_html_e( 'Sender Address', 'mail-boxes-etc' ); ?></h3>

                <div style="margin-bottom: 15px;">
                    <label for="sender_trade_name" style="display: block; font-weight: bold;"><?php esc_html_e( 'Trade Name', 'mail-boxes-etc' ); ?> <span
                                style="color: #c03939">*</span></label>
                    <input type="text" name="sender_trade_name" id="sender_trade_name" class="regular-text" maxlength="100"
                           style="width: 100%;" value="<?php echo $originalShipmentReceiver->CompanyName??'' ?>" required>
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="sender_address_1" style="display: block; font-weight: bold;"><?php esc_html_e( 'Address 1', 'mail-boxes-etc' ); ?> <span
                                style="color: #c03939">*</span></label>
                    <input type="text" name="sender_address_1" id="sender_address_1" class="regular-text" maxlength="200"
                           style="width: 100%;" value="<?php echo $originalShipmentReceiver->Address??'' ?>" required>
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="sender_address_2" style="display: block; font-weight: bold;"><?php esc_html_e( 'Address 2', 'mail-boxes-etc' ); ?></label>
                    <input type="text" name="sender_address_2" id="sender_address_2" class="regular-text" maxlength="100"
                           style="width: 100%;">
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="sender_address_3" style="display: block; font-weight: bold;"><?php esc_html_e( 'Address 3', 'mail-boxes-etc' ); ?></label>
                    <input type="text" name="sender_address_3" id="sender_address_3" class="regular-text" maxlength="100"
                           style="width: 100%;">
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="sender_postcode" style="display: block; font-weight: bold;"><?php esc_html_e( 'Postcode', 'mail-boxes-etc' ); ?> <span
                                style="color: #c03939">*</span></label>
                    <input type="text" name="sender_postcode" id="sender_postcode" class="regular-text" maxlength="12"
                           style="width: 100%;" value="<?php echo $originalShipmentReceiver->ZipCode??'' ?>" required>
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="sender_city" style="display: block; font-weight: bold;"><?php esc_html_e( 'City', 'mail-boxes-etc' ); ?> <span
                                style="color: #c03939">*</span></label>
                    <input type="text" name="sender_city" id="sender_city" class="regular-text" style="width: 100%;" maxlength="100"
                           value="<?php echo $originalShipmentReceiver->City??'' ?>" required>
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="sender_province" style="display: block; font-weight: bold;"><?php esc_html_e( 'Province', 'mail-boxes-etc' ); ?> <span
                                style="color: #c03939"><?php echo $helper->getCountry()==='IT'?'*':''?></span></label>
                    <input type="text" name="sender_province" id="sender_province" class="regular-text" maxlength="2"
                           style="width: 100%;" value="<?php echo substr($originalShipmentReceiver->State??'', 0 ,2) ?>" <?php echo $helper->getCountry()==='IT'?'required':''?> >
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="sender_country" style="display: block; font-weight: bold;"><?php esc_html_e( 'Country', 'mail-boxes-etc' ); ?> <span
                                style="color: #c03939">*</span></label>
                    <select id="sender_country" name="sender_country" style="width: 100%" required>
		                <?php foreach ( $countries->get_countries() as $code=>$label ) {
			                echo '<option value="'.esc_attr($code).'" '. (strtolower($originalShipmentReceiver->Country) === strtolower($label)?'selected':'') .'>'.esc_html($label).'</option>';
		                } ?>
                    </select>
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="sender_reference" style="display: block; font-weight: bold;"><?php esc_html_e( 'Reference', 'mail-boxes-etc' ); ?> <span
                                style="color: #c03939">*</span></label>
                    <input type="text" name="sender_reference" id="sender_reference" class="regular-text" maxlength="100"
                           style="width: 100%;" value="<?php echo $originalShipmentReceiver->CompanyName??'' ?>" required>
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="sender_telephone_1" style="display: block; font-weight: bold;"><?php esc_html_e( 'Telephone 1', 'mail-boxes-etc' ); ?> <span
                                style="color: #c03939">*</span></label>
                    <input type="text" name="sender_telephone_1" id="sender_telephone_1" class="regular-text" maxlength="50"
                           style="width: 100%;" value="<?php echo $originalShipmentReceiver->Phone??'' ?>" required>
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="sender_email_1" style="display: block; font-weight: bold;"><?php esc_html_e( 'E-mail 1', 'mail-boxes-etc' ); ?> <span
                                style="color: #c03939">*</span></label>
                    <input type="email" name="sender_email_1" id="sender_email_1" class="regular-text" maxlength="75"
                           style="width: 100%;" value="<?php echo $originalShipmentReceiver->Emails??'' ?>" required>
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="sender_fax" style="display: block; font-weight: bold;"><?php esc_html_e( 'Fax', 'mail-boxes-etc' ); ?></label>
                    <input type="text" name="sender_fax" id="sender_fax" class="regular-text" style="width: 100%;">
                </div>
            </div>
<!--            Recipient-->
            <div style="float: left; width: 28%; margin-right: 2%;"><h3><?php esc_html_e( 'Recipient address', 'mail-boxes-etc' ); ?></h3>

                <div style="margin-bottom: 15px;">
                    <label for="receiver_trade_name" style="display: block; font-weight: bold;"><?php esc_html_e( 'Trade Name', 'mail-boxes-etc' ); ?> <span
                                style="color: #c03939">*</span></label>
                    <input type="text" name="receiver_trade_name" id="receiver_trade_name" class="regular-text" maxlength="100"
                           style="width: 100%;" value="<?php echo $originalShipmentSender->CompanyName??'' ?>" required>
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="receiver_address_1" style="display: block; font-weight: bold;"><?php esc_html_e( 'Address 1', 'mail-boxes-etc' ); ?> <span
                                style="color: #c03939">*</span></label>
                    <input type="text" name="receiver_address_1" id="receiver_address_1" class="regular-text" maxlength="200"
                           style="width: 100%;" value="<?php echo $originalShipmentSender->Address??'' ?>" required>
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="receiver_address_2" style="display: block; font-weight: bold;"><?php esc_html_e( 'Address 2', 'mail-boxes-etc' ); ?></label>
                    <input type="text" name="receiver_address_2" id="receiver_address_2" class="regular-text" maxlength="100"
                           style="width: 100%;">
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="receiver_address_3" style="display: block; font-weight: bold;"><?php esc_html_e( 'Address 3', 'mail-boxes-etc' ); ?></label>
                    <input type="text" name="receiver_address_3" id="receiver_address_3" class="regular-text" maxlength="100"
                           style="width: 100%;">
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="receiver_postcode" style="display: block; font-weight: bold;"><?php esc_html_e( 'Postcode', 'mail-boxes-etc' ); ?> <span
                                style="color: #c03939">*</span></label>
                    <input type="text" name="receiver_postcode" id="receiver_postcode" class="regular-text" maxlength="12"
                           style="width: 100%;" value="<?php echo $originalShipmentSender->ZipCode??'' ?>" required>
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="receiver_city" style="display: block; font-weight: bold;"><?php esc_html_e( 'City', 'mail-boxes-etc' ); ?> <span
                                style="color: #c03939">*</span></label>
                    <input type="text" name="receiver_city" id="receiver_city" class="regular-text" style="width: 100%;" maxlength="100"
                           value="<?php echo $originalShipmentSender->City??'' ?>" required>
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="receiver_province" style="display: block; font-weight: bold;"><?php esc_html_e( 'Province', 'mail-boxes-etc' ); ?> <span
                                style="color: #c03939"><?php echo $helper->getCountry()==='IT'?'*':''?></span></label>
                    <input type="text" name="receiver_province" id="receiver_province" class="regular-text" maxlength="2"
                           style="width: 100%;" value="<?php echo substr($originalShipmentSender->State??'',0 ,2) ?>" <?php echo $helper->getCountry()==='IT'?'required':''?> >
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="receiver_country" style="display: block; font-weight: bold;"><?php esc_html_e( 'Country', 'mail-boxes-etc' ); ?> <span
                                style="color: #c03939">*</span></label>
                    <select id="receiver_country" name="receiver_country" style="width: 100%" required>
		                <?php foreach ( $countries->get_countries() as $code=>$label ) {
			                echo '<option value="'.esc_attr($code).'" '. (strtolower($originalShipmentSender->Country) === strtolower($label)?'selected':'') .'>'.esc_html($label).'</option>';
		                } ?>
                    </select>
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="receiver_reference" style="display: block; font-weight: bold;"><?php esc_html_e( 'Reference', 'mail-boxes-etc' ); ?> <span
                                style="color: #c03939">*</span></label>
                    <input type="text" name="receiver_reference" id="receiver_reference" class="regular-text" maxlength="100"
                           style="width: 100%;" value="<?php echo $originalShipmentSender->CompanyName??''?>" required>
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="receiver_telephone_1" style="display: block; font-weight: bold;"><?php esc_html_e( 'Telephone 1', 'mail-boxes-etc' ); ?> <span
                                style="color: #c03939">*</span></label>
                    <input type="text" name="receiver_telephone_1" id="receiver_telephone_1" class="regular-text" maxlength="50"
                           style="width: 100%;" value="<?php echo $originalShipmentSender->Phone??'' ?>" required>
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="receiver_email_1" style="display: block; font-weight: bold;"><?php esc_html_e( 'E-mail 1', 'mail-boxes-etc' ); ?> <span
                                style="color: #c03939">*</span></label>
                    <input type="email" name="receiver_email_1" id="receiver_email_1" class="regular-text" maxlength="75"
                           style="width: 100%;" value="<?php echo $originalShipmentSender->Emails??'' ?>" required>
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="receiver_fax" style="display: block; font-weight: bold;"><?php esc_html_e( 'Fax', 'mail-boxes-etc' ); ?></label>
                    <input type="text" name="receiver_fax" id="receiver_fax" class="regular-text" style="width: 100%;">
                </div>
            </div>
<!--            Pickup data-->
            <div style="float: left; width: 38%; "><h3><?php esc_html_e( 'Pickup Details', 'mail-boxes-etc' ); ?></h3>

                <div style="margin-bottom: 15px;">
                    <label for="pickup_date" style="display: block; font-weight: bold;"><?php esc_html_e( 'Pickup Date', 'mail-boxes-etc' ); ?> <span
                                style="color: #c03939">*</span></label>
                    <input id="pickup_date" name="pickup_date" type="date" style="width: 95%"
                           value="<?php esc_html_e($item['date']??$nextMonday->format('Y-m-d')) ?>"
                           min="<?php  esc_html_e($today->format('Y-m-d')) ?>"
                           required>
                    <p class="description" style="font-size: 0.9em; color: #555; margin-top: 5px;"><?php esc_html_e( 'Select the date on which you would like courier pickup to take place. This date will be communicated directly to the courier. N.B. It is highly recommended that you choose a working day', 'mail-boxes-etc' ); ?></p>
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="pickup_time_preferred_from" style="display: block; font-weight: bold;"><?php esc_html_e( 'Pickup Time - Preferred from', 'mail-boxes-etc' ); ?> <span style="color: #c03939">*</span></label>
                    <input type="time" name="pickup_time_preferred_from" id="pickup_time_preferred_from" value="<?php echo esc_attr($fifteenMinutesBeforeNoon->format('H:i')) ?>"
                           style="width: 100%;" required>
                    <p class="description" style="font-size: 0.9em; color: #555; margin-top: 5px;"><?php esc_html_e( 'Minimum pickup time that will be communicated to the courier (N.B. pickup time is approximate and may not be observed by the final courier)', 'mail-boxes-etc' ); ?></p>
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="pickup_time_pickup_time_preferred_to" style="display: block; font-weight: bold;"><?php esc_html_e( 'Pickup Time - Preferred to', 'mail-boxes-etc' ); ?> <span style="color: #c03939">*</span></label>
                    <input type="time" name="pickup_time_preferred_to" id="pickup_time_preferred_to" value="<?php echo esc_attr($noon->format('H:i')) ?>"
                           style="width: 100%;" required>
                    <p class="description" style="font-size: 0.9em; color: #555; margin-top: 5px;"><?php esc_html_e( 'Maximum pickup time that will be communicated to the courier (N.B. pickup time is approximate and may not be observed by the final courier)', 'mail-boxes-etc' ); ?></p>
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="pickup_time_alternative_from" style="display: block; font-weight: bold;"><?php esc_html_e( 'Pickup Time - Alternative from', 'mail-boxes-etc' ); ?></label>
                    <input type="time" name="pickup_time_alternative_from" id="pickup_time_alternative_from"
                           style="width: 100%;">
                    <p class="description" style="font-size: 0.9em; color: #555; margin-top: 5px;"><?php esc_html_e( 'Alternative minimum pickup time that will be communicated to the courier (N.B. pickup time is approximate and may not be observed by the final courier)', 'mail-boxes-etc' ); ?></p>
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="pickup_time_alternative_to" style="display: block; font-weight: bold;"><?php esc_html_e( 'Pickup Time - Alternative to', 'mail-boxes-etc' ); ?></label>
                    <input type="time" name="pickup_time_alternative_to" id="pickup_time_alternative_to"
                           style="width: 100%;">
                    <p class="description" style="font-size: 0.9em; color: #555; margin-top: 5px;"><?php esc_html_e( 'Alternative maximum pickup time that will be communicated to the courier (N.B. pickup time is approximate and may not be observed by the final courier)', 'mail-boxes-etc' ); ?></p>
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="pickup_notes" style="display: block; font-weight: bold;"><?php esc_html_e( 'Pickup notes', 'mail-boxes-etc' ); ?></label>
                    <input type="text" name="pickup_notes" id="pickup_notes" class="regular-text" style="width: 100%;">
                    <p class="description" style="font-size: 0.9em; color: #555; margin-top: 5px;"><?php esc_html_e( 'Notes to be included within the pickup request and that will be forwarded to the final carrier', 'mail-boxes-etc' ); ?></p>
                </div>
            </div>

        </div>

        <div style="clear: both;"></div>


		<?php
	}

}