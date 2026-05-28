<?php
/**
 * Theme Customizer settings
 * Appearance → Customize → HariyaliBasket Settings
 */
if ( ! defined( 'ABSPATH' ) ) exit;

function hb_customizer_settings( $wp_customize ) {
    $wp_customize->add_section( 'hariyali_settings', [
        'title'    => '🌿 HariyaliBasket Settings',
        'priority' => 30,
    ] );

    $fields = [
        'hb_wa_number'          => [ 'WhatsApp Number',         '918000344554' ],
        'hb_upi_id'             => [ 'UPI ID',                  'imanohar07773@ybl' ],
        'hb_validity'           => [ 'Price Validity',          date( 'd M' ) . ' – ' . date( 'd M Y', strtotime( '+15 days' ) ) ],
        'hb_free_delivery_min'  => [ 'Min Order Free Delivery', '199' ],
        'hb_delivery_fee'       => [ 'Delivery Fee (₹)',        '69' ],
        'hb_delivery_areas'     => [ 'Delivery Areas (comma)',  'Hanging Garden,Vaishali Nagar,Malviya Nagar,Mansarovar' ],
        'hb_sheet_url'          => [ 'Google Sheet URL (logger)', '' ],
        'hb_email'              => [ 'Contact Email',           'hariyalibasket@gmail.com' ],
        'hb_business_address'   => [ 'Business Address',         'Jaipur, Rajasthan' ],
        'hb_owner_name'         => [ 'Owner Name',               'HariyaliBasket Team' ],
    ];

    foreach ( $fields as $key => $data ) {
        $wp_customize->add_setting( $key, [
            'default'           => $data[1],
            'sanitize_callback' => 'sanitize_text_field',
        ] );
        $wp_customize->add_control( $key, [
            'label'   => $data[0],
            'section' => 'hariyali_settings',
            'type'    => 'text',
        ] );
    }
}
add_action( 'customize_register', 'hb_customizer_settings' );
