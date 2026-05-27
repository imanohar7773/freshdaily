<?php
/**
 * WooCommerce Price Sync — improved matching (BUG FIX #5)
 *
 * Match priority:
 *   1. _hb_woo_id meta (explicit link)
 *   2. _hb_sku meta vs WC SKU (exact)
 *   3. Slugified name comparison (fuzzy, case-insensitive)
 *   4. Levenshtein distance ≤ 2 (typo tolerance)
 */
if ( ! defined( 'ABSPATH' ) ) exit;

// Fallback for non-WooCommerce sites
if ( ! function_exists( 'wc_get_product' ) ) {
    function wc_get_product( $id ) { return false; }
}
if ( ! function_exists( 'wc_get_product_id_by_sku' ) ) {
    function wc_get_product_id_by_sku( $sku ) { return 0; }
}

/**
 * Normalize product name for comparison.
 */
function hb_norm_name( $name ) {
    $n = strtolower( $name );
    $n = preg_replace( '/[^a-z0-9]+/', ' ', $n );
    return trim( $n );
}

/**
 * Find hb_product matching given WooCommerce product.
 *
 * @return int|false hb_product post ID or false if no good match.
 */
function hb_find_hb_for_woo( $woo ) {
    if ( ! $woo ) return false;
    $woo_id   = $woo->get_id();
    $woo_sku  = $woo->get_sku();
    $woo_name = $woo->get_name();

    // 1. Explicit link via _hb_woo_id meta
    $explicit = get_posts( [
        'post_type'   => 'hb_product',
        'post_status' => 'publish',
        'numberposts' => 1,
        'fields'      => 'ids',
        'meta_query'  => [ [ 'key' => '_hb_woo_id', 'value' => $woo_id ] ],
    ] );
    if ( ! empty( $explicit ) ) return $explicit[0];

    // 2. SKU match (exact)
    if ( $woo_sku ) {
        $by_sku = get_posts( [
            'post_type'   => 'hb_product',
            'post_status' => 'publish',
            'numberposts' => 1,
            'fields'      => 'ids',
            'meta_query'  => [ [ 'key' => '_hb_sku', 'value' => $woo_sku ] ],
        ] );
        if ( ! empty( $by_sku ) ) return $by_sku[0];
    }

    // 3. Normalised name match
    $norm_woo = hb_norm_name( $woo_name );
    $all_hb   = get_posts( [
        'post_type'   => 'hb_product',
        'post_status' => 'publish',
        'numberposts' => -1,
        'fields'      => 'ids',
    ] );

    $best_id   = false;
    $best_dist = PHP_INT_MAX;
    foreach ( $all_hb as $hb_id ) {
        $hb_norm = hb_norm_name( get_the_title( $hb_id ) );
        if ( $hb_norm === $norm_woo ) return $hb_id;
        // Substring match (either direction)
        if ( strlen( $hb_norm ) > 4 && strlen( $norm_woo ) > 4 ) {
            if ( strpos( $norm_woo, $hb_norm ) !== false || strpos( $hb_norm, $norm_woo ) !== false ) {
                return $hb_id;
            }
        }
        // Levenshtein on first word (typo tolerance)
        $first_woo = explode( ' ', $norm_woo )[0];
        $first_hb  = explode( ' ', $hb_norm )[0];
        if ( strlen( $first_woo ) >= 4 && strlen( $first_hb ) >= 4 ) {
            $d = levenshtein( $first_woo, $first_hb );
            if ( $d <= 2 && $d < $best_dist ) {
                $best_dist = $d;
                $best_id   = $hb_id;
            }
        }
    }
    if ( $best_id !== false ) return $best_id;

    return false;
}

/**
 * Auto-sync WooCommerce price → hb_product
 */
function hb_sync_woo_to_hb( $product_id ) {
    $woo = wc_get_product( $product_id );
    if ( ! $woo ) return;

    $sale = (float) $woo->get_sale_price();
    $reg  = (float) $woo->get_regular_price();
    $sp   = $sale > 0 ? $sale : $reg;
    $mrp  = $reg > 0 ? $reg : $sp;
    if ( $sp <= 0 ) return;

    $hb_id = hb_find_hb_for_woo( $woo );
    if ( ! $hb_id ) return;

    update_post_meta( $hb_id, '_hb_sp',     $sp );
    update_post_meta( $hb_id, '_hb_mrp',    $mrp );
    update_post_meta( $hb_id, '_hb_woo_id', $product_id ); // remember link for next time
}
add_action( 'woocommerce_update_product', 'hb_sync_woo_to_hb', 10, 1 );

/**
 * Bulk sync trigger
 * URL: /wp-admin/?hb_bulk_sync=1
 */
function hb_bulk_sync_handler() {
    if ( ! isset( $_GET['hb_bulk_sync'] ) ) return;
    if ( ! current_user_can( 'manage_options' ) ) return;

    $woo_products = get_posts( [
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
    ] );

    foreach ( $woo_products as $p ) {
        hb_sync_woo_to_hb( $p->ID );
    }

    wp_die(
        '<h2 style="font-family:sans-serif;color:green">✅ Sync Complete!</h2>' .
        '<p style="font-family:sans-serif">' . count( $woo_products ) . ' products synced.</p>' .
        '<p><a href="/wp-admin/">← Back to Admin</a></p>'
    );
}
add_action( 'admin_init', 'hb_bulk_sync_handler' );
