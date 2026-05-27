<?php
/**
 * WooCommerce Price Sync
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

    // Try SKU match
    $sku = $woo->get_sku();
    if ( $sku ) {
        $hb = get_posts( [
            'post_type'   => 'hb_product',
            'post_status' => 'publish',
            'numberposts' => 1,
            'meta_query'  => [ [ 'key' => '_hb_sku', 'value' => $sku ] ],
        ] );
        if ( ! empty( $hb ) ) {
            update_post_meta( $hb[0]->ID, '_hb_sp', $sp );
            update_post_meta( $hb[0]->ID, '_hb_mrp', $mrp );
            return;
        }
    }

    // Fallback: name match
    $name_match = get_posts( [
        'post_type'   => 'hb_product',
        'post_status' => 'publish',
        'title'       => $woo->get_name(),
        'numberposts' => 1,
    ] );
    if ( ! empty( $name_match ) ) {
        update_post_meta( $name_match[0]->ID, '_hb_sp', $sp );
        update_post_meta( $name_match[0]->ID, '_hb_mrp', $mrp );
    }
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
