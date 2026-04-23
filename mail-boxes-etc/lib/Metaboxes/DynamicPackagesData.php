<?php

namespace Metaboxes;

abstract class DynamicPackagesData {

    public static function add( $item, $screens = [] ) {
        $screens = ! is_array( $screens ) ? [ $screens ] : $screens;
        add_meta_box( MBE_ESHIP_ID . '_dynamic_packages_data_form_meta_box',
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

        if ( count( $orderIds ) === 1 ) {
            $orderPackagesData = array_values(json_decode( $helper->getOrderDynamicPackageData( $orderIds[0] ), ARRAY_A )??[]);
        }

        // Default data if none is found
        if ( empty( $orderPackagesData ) ) {
            $orderPackagesData = [
                0 => [
                    'weight'  => '1.00',
                    'length'  => '0.00',
                    'width'   => '0.00',
                    'height'  => '0.00',
                    'parcels' => '1',
                ]
            ];
        }

        ?>
        <div class="mbe-dynamic-packages-wrapper">
            <table class="form-table">
                <tbody>
                <tr class="form-field">
                    <th scope="row">
                        <label for="address"><?php esc_html_e( 'Order list', 'mail-boxes-etc' ) ?></label>
                    </th>
                    <td>
                        <div class="mbe-order-list-container">
                            <?php foreach ( $orderIds as $order ) : ?>
                                <span class="mbe-order-tag">
                                    <?php echo esc_html( $order ) ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </td>
                </tr>
                <tr class="mbe-packages-row-separator">
                    <td colspan="2">
                        <div id="mbe-dynamic-packages-container">
                            <div class="mbe-section-title"><?php esc_html_e( 'Parcels', 'mail-boxes-etc' ) ?></div>
                            <table class="mbe-dynamic-packages-table">
                                <thead>
                                <tr>
                                    <th><?php esc_html_e( 'Template name', 'mail-boxes-etc' ) ?></th>
                                    <th><?php esc_html_e( 'Weight', 'mail-boxes-etc' ) ?> (Kg)</th>
                                    <th><?php esc_html_e( 'Length', 'mail-boxes-etc' ) ?> (Cm)</th>
                                    <th><?php esc_html_e( 'Width', 'mail-boxes-etc' ) ?> (Cm)</th>
                                    <th><?php esc_html_e( 'Height', 'mail-boxes-etc' ) ?> (Cm)</th>
                                    <th style="width: 5%"><?php esc_html_e( 'Parcels', 'mail-boxes-etc' ) ?></th>
                                    <th style="width: 1%;"></th>
                                </tr>
                                </thead>
                                <tbody id="mbe-packages-body">
                                <?php foreach ( $orderPackagesData as $packageId => $packageData ) : ?>
                                    <?php self::render_package_row( $packageId, $packageData ); ?>
                                <?php endforeach; ?>
                                </tbody>
                            </table>

                            <div class="mbe-actions-container">
                                <button type="button" id="mbe-add-package" class="button">
                                    + <?php esc_html_e( 'Add package', 'mail-boxes-etc' ) ?>
                                </button>
                            </div>

                            <div class="mbe-submit-container">
                                <button type="submit" class="button button-primary" id="submit-save">
                                    <?php esc_html_e( 'Update packages list', 'mail-boxes-etc' ) ?>
                                </button>
                            </div>
                        </div>

                        <template id="mbe-package-row-template">
                            <?php self::render_package_row( '{{INDEX}}' ); ?>
                        </template>

                        <script type="text/javascript">
                            document.addEventListener('DOMContentLoaded', function () {
                                const container = document.getElementById('mbe-packages-body');
                                const addButton = document.getElementById('mbe-add-package');
                                const rowTemplate = document.getElementById('mbe-package-row-template').innerHTML;

                                let rowCount = container.getElementsByClassName('mbe-package-row').length;

                                addButton.addEventListener('click', function () {
                                    const newRowHtml = rowTemplate.replace(/{{INDEX}}/g, rowCount++);
                                    container.insertAdjacentHTML('beforeend', newRowHtml);
                                });

                                container.addEventListener('click', function (e) {
                                    const removeBtn = e.target.closest('.mbe-remove-package');
                                    if (removeBtn) {
                                        const rows = container.getElementsByClassName('mbe-package-row');
                                        if (rows.length > 0) {
                                            removeBtn.closest('tr').remove();
                                            rowCount--
                                        }
                                    }
                                });

                                container.addEventListener('change', function (e) {
                                    const select = e.target.closest('.mbe-package-package-template-selection');
                                    if (!select) return;

                                    if (!select.value) return; // "Select package" option

                                    const selectedOption = JSON.parse(select.value);

                                    let selectId = "mbe_packages[" + parseInt(selectedOption[0]) + "]";

                                    document.getElementsByName(selectId + '[length]')[0].value = selectedOption[1];
                                    document.getElementsByName(selectId + '[width]')[0].value = selectedOption[2];
                                    document.getElementsByName(selectId + '[height]')[0].value = selectedOption[3];
                                });

                            });
                        </script>
                        <style>
                            .mbe-dynamic-packages-wrapper { width: 100%; }
                            .mbe-order-list-container { width: 95%; height: auto; }
                            .mbe-order-tag {
                                font-size: smaller;
                                float: left;
                                color: #0a4b78;
                                border: solid 1px #0a4b78;
                                padding: 4px;
                                text-align: center;
                                border-radius: 5px;
                                margin: 3px;
                            }
                            .mbe-packages-row-separator { border-top: 1px solid #c6c6c6; }
                            #mbe-dynamic-packages-container {
                                background-color: #ffffff;
                                padding: 20px;
                                margin-top: 10px;
                            }
                            .mbe-section-title { font-weight: bold; margin-bottom: 10px; }
                            .mbe-dynamic-packages-table {
                                width: 100%;
                                border-spacing: 10px;
                                border-collapse: separate;
                            }
                            .mbe-dynamic-packages-table th {
                                text-align: center;
                                font-size: 12px;
                                font-weight: bold;
                                color: #333;
                            }
                            .mbe-dynamic-packages-table td { padding: 0; }
                            .mbe-package-row input,
                            .mbe-package-row select {
                                width: 100%;
                                text-align: center;
                                padding: 10px;
                                border: 1px solid #ddd;
                            }
                            .mbe-remove-package {
                                background: black !important;
                                color: white !important;
                                border: none !important;
                                padding: 10px 15px !important;
                                cursor: pointer;
                                line-height: 1 !important;
                                height: auto !important;
                            }
                            .mbe-actions-container { text-align: center; margin-top: 20px; }
                            #mbe-add-package {
                                background: #555;
                                color: white;
                                border: none;
                                padding: 10px 20px;
                                text-transform: uppercase;
                                font-weight: bold;
                                height: auto;
                            }
                            .mbe-submit-container { text-align: right; margin-top: 20px; }
                            #submit-save {
                                background: #e66b55;
                                border-color: #e66b55;
                                padding: 10px 20px;
                                height: auto;
                                font-weight: bold;
                                text-transform: uppercase;
                            }
                        </style>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <?php
    }

    private static function render_package_row( $index, $data = [] ) {
        $csv_package_model = new \Mbe_Shipping_Model_Csv_Package();
        $data = wp_parse_args( $data, [
            'weight'  => '1.00',
            'length'  => '0.00',
            'width'   => '0.00',
            'height'  => '0.00',
            'parcels' => '1',
        ] );
        ?>
        <tr class="mbe-package-row">
            <td><select id="mbe-package-package-template-selection-<?php echo esc_attr( $index ) ?>" class="mbe-package-package-template-selection">
                    <option value=""><?php esc_html_e( 'Select package', 'mail-boxes-etc' ) ?></option>
                    <?php foreach ( $csv_package_model->getStandardPackagesFullData() as $option ) : ?>
                        <option value="<?php echo esc_attr( json_encode([$index, $option['length'], $option['width'], $option['height']]) ) ?>"><?php echo esc_html( $option['package_label'] ) ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
            <td><input type="number" name="mbe_packages[<?php echo esc_attr( $index ) ?>][weight]" step="0.01" required min="0.01" value="<?php echo esc_attr( $data['weight'] ) ?>"></td>
            <td><input type="number" name="mbe_packages[<?php echo esc_attr( $index ) ?>][length]" step="0.01" required min="0.01" value="<?php echo esc_attr( $data['length'] ) ?>"></td>
            <td><input type="number" name="mbe_packages[<?php echo esc_attr( $index ) ?>][width]" step="0.01" required min="0.01" value="<?php echo esc_attr( $data['width'] ) ?>"></td>
            <td><input type="number" name="mbe_packages[<?php echo esc_attr( $index ) ?>][height]" step="0.01" required min="0.01" value="<?php echo esc_attr( $data['height'] ) ?>"></td>
            <td><input type="number" name="mbe_packages[<?php echo esc_attr( $index ) ?>][parcels]" step="1" required min="1" value="<?php echo esc_attr( $data['parcels'] ) ?>"></td>
            <td>
                <button type="button" class="mbe-remove-package">X</button>
            </td>
        </tr>
        <?php
    }

}