<?php
/**
 * AJAX endpoints
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Get all products via AJAX (for live updates)
 */
function hb_ajax_get_products() {
    wp_send_json_success( hb_get_all_products() );
}
add_action( 'wp_ajax_hb_get_products',        'hb_ajax_get_products' );
add_action( 'wp_ajax_nopriv_hb_get_products', 'hb_ajax_get_products' );

/**
 * Place order — saves to hb_order CPT
 */
function hb_ajax_place_order() {
    check_ajax_referer( 'hb_nonce', 'nonce' );

    // Rate limit check
    if ( function_exists( 'hb_rate_limit_check' ) && ! hb_rate_limit_check() ) {
        wp_send_json_error( [ 'msg' => 'Too many orders. Please try again in an hour.' ], 429 );
    }

    $name    = sanitize_text_field( $_POST['name'] ?? '' );
    $phone   = sanitize_text_field( $_POST['phone'] ?? '' );
    $society = sanitize_text_field( $_POST['society'] ?? '' );
    $block   = sanitize_text_field( $_POST['block'] ?? '' );
    $flat    = sanitize_text_field( $_POST['flat'] ?? '' );
    $payment = sanitize_text_field( $_POST['payment'] ?? 'cod' );
    $txn     = sanitize_text_field( $_POST['txn'] ?? '' );
    $items   = isset( $_POST['items'] ) ? json_decode( wp_unslash( $_POST['items'] ), true ) : [];
    $total   = floatval( $_POST['total'] ?? 0 );

    // Validation
    if ( empty( $name ) || empty( $society ) ) {
        wp_send_json_error( [ 'msg' => 'Naam aur society zaroori hain' ] );
    }
    if ( ! preg_match( '/^[6-9]\d{9}$/', $phone ) ) {
        wp_send_json_error( [ 'msg' => 'Valid 10-digit mobile number chahiye' ] );
    }
    if ( empty( $items ) || ! is_array( $items ) ) {
        wp_send_json_error( [ 'msg' => 'Cart khaali hai' ] );
    }
    if ( $payment === 'upi' && empty( $txn ) ) {
        wp_send_json_error( [ 'msg' => 'UPI Transaction ID zaroori hai' ] );
    }

    // CAPTCHA check
    if ( function_exists( 'hb_captcha_verify' ) ) {
        $cap_a = intval( $_POST['captcha_a'] ?? 0 );
        $cap_b = intval( $_POST['captcha_b'] ?? 0 );
        $cap_v = intval( $_POST['captcha_v'] ?? -1 );
        if ( ! hb_captcha_verify( $cap_a, $cap_b, $cap_v ) ) {
            wp_send_json_error( [ 'msg' => 'CAPTCHA galat hai. Please try again.' ] );
        }
    }

    // Save order
    if ( function_exists( 'hb_create_order' ) ) {
        $order_id = hb_create_order( [
            'name'    => $name,
            'phone'   => $phone,
            'society' => $society,
            'block'   => $block,
            'flat'    => $flat,
            'payment' => $payment,
            'txn'     => $txn,
            'items'   => $items,
            'total'   => $total,
        ] );

        if ( $order_id ) {
            // Mark phone for rate limit
            if ( function_exists( 'hb_rate_limit_record' ) ) hb_rate_limit_record( $phone );

            wp_send_json_success( [
                'order_id' => $order_id['code'],
                'post_id'  => $order_id['post_id'],
            ] );
        }
    }

    wp_send_json_error( [ 'msg' => 'Order save nahi hua. Try again.' ] );
}
add_action( 'wp_ajax_hb_place_order',        'hb_ajax_place_order' );
add_action( 'wp_ajax_nopriv_hb_place_order', 'hb_ajax_place_order' );

/**
 * Get fresh CAPTCHA challenge
 */
function hb_ajax_captcha() {
    if ( function_exists( 'hb_captcha_generate' ) ) {
        wp_send_json_success( hb_captcha_generate() );
    }
    wp_send_json_error();
}
add_action( 'wp_ajax_hb_captcha',        'hb_ajax_captcha' );
add_action( 'wp_ajax_nopriv_hb_captcha', 'hb_ajax_captcha' );
