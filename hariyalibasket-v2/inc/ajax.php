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

    // ── 1. Rate limit by IP ──
    if ( function_exists( 'hb_rate_limit_check' ) && ! hb_rate_limit_check() ) {
        wp_send_json_error( [ 'msg' => 'Bahut zyada orders. Ek ghante baad try karo.' ], 429 );
    }

    // ── 2. Sanitize inputs ──
    $name    = sanitize_text_field( $_POST['name'] ?? '' );
    $phone   = sanitize_text_field( $_POST['phone'] ?? '' );
    $society = sanitize_text_field( $_POST['society'] ?? '' );
    $block   = sanitize_text_field( $_POST['block'] ?? '' );
    $flat    = sanitize_text_field( $_POST['flat'] ?? '' );
    $payment = sanitize_text_field( $_POST['payment'] ?? 'cod' );
    $txn     = sanitize_text_field( $_POST['txn'] ?? '' );
    $items   = isset( $_POST['items'] ) ? json_decode( wp_unslash( $_POST['items'] ), true ) : [];
    $total   = floatval( $_POST['total'] ?? 0 );
    $cap_token  = sanitize_text_field( $_POST['captcha_token']  ?? '' );
    $cap_answer = sanitize_text_field( $_POST['captcha_answer'] ?? '' );

    // ── 3. Basic validation ──
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

    // ── 4. CAPTCHA — server-side token verification ──
    if ( function_exists( 'hb_captcha_verify' ) ) {
        if ( ! hb_captcha_verify( $cap_token, $cap_answer ) ) {
            wp_send_json_error( [ 'msg' => 'CAPTCHA galat ya expire ho gaya. Please try again.', 'captcha_failed' => true ] );
        }
    }

    // ── 5. Phone-specific rate limit (BUG FIX: was missing) ──
    if ( function_exists( 'hb_rate_limit_phone_check' ) && ! hb_rate_limit_phone_check( $phone ) ) {
        wp_send_json_error( [ 'msg' => 'Iss mobile se aaj ke 3 orders ho chuke hain. Kal try karo ya WhatsApp pe contact karo.' ], 429 );
    }

    // ── 6. Server-side cart validation (prevent price tampering) ──
    if ( function_exists( 'hb_validate_cart_items' ) ) {
        $validated = hb_validate_cart_items( $items );
        if ( $validated === false ) {
            wp_send_json_error( [ 'msg' => 'Cart items invalid ya price tampered. Refresh karke try karo.' ] );
        }
        $items = $validated['items'];
        // Recompute server-trusted total
        $min_free = intval( get_theme_mod( 'hb_free_delivery_min', 199 ) );
        $fee      = intval( get_theme_mod( 'hb_delivery_fee', 69 ) );
        $subtotal = $validated['subtotal'];
        $delivery = $subtotal >= $min_free ? 0 : $fee;
        $total    = $subtotal + $delivery;
    }

    // ── 7. Save order ──
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
            if ( function_exists( 'hb_rate_limit_record' ) ) hb_rate_limit_record( $phone );

            wp_send_json_success( [
                'order_id' => $order_id['code'],
                'post_id'  => $order_id['post_id'],
                'total'    => $total, // server-trusted total
            ] );
        }
    }

    wp_send_json_error( [ 'msg' => 'Order save nahi hua. Try again.' ] );
}
add_action( 'wp_ajax_hb_place_order',        'hb_ajax_place_order' );
add_action( 'wp_ajax_nopriv_hb_place_order', 'hb_ajax_place_order' );

/**
 * Get fresh CAPTCHA challenge — token-based, server-state
 */
function hb_ajax_captcha() {
    if ( ! function_exists( 'hb_captcha_generate' ) ) {
        wp_send_json_error( [ 'msg' => 'CAPTCHA module not loaded' ] );
    }
    wp_send_json_success( hb_captcha_generate() );
}
add_action( 'wp_ajax_hb_captcha',        'hb_ajax_captcha' );
add_action( 'wp_ajax_nopriv_hb_captcha', 'hb_ajax_captcha' );
