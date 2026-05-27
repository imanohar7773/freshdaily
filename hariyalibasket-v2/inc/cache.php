<?php
/**
 * Cache clearing on price/product update
 */
if ( ! defined( 'ABSPATH' ) ) exit;

function hb_clear_cache_on_save( $post_id ) {
    wp_cache_delete( $post_id, 'post_meta' );
    wp_cache_delete( 'hb_all_products', 'hariyali' );

    if ( function_exists( 'wp_cache_post_change' ) ) wp_cache_post_change( $post_id );
    if ( function_exists( 'w3tc_flush_post' ) )      w3tc_flush_post( $post_id );
    if ( function_exists( 'rocket_clean_post' ) )    rocket_clean_post( $post_id );
    do_action( 'litespeed_purge_post', $post_id );

    clean_post_cache( get_option( 'page_on_front' ) );
}
add_action( 'save_post_hb_product', 'hb_clear_cache_on_save', 20, 1 );
add_action( 'save_post_hb_order',   'hb_clear_cache_on_save', 20, 1 );
