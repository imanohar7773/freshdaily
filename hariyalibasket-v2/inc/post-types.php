<?php
/**
 * Custom Post Type: hb_product
 * (Same as v1 — backward compatible with existing data)
 */
if ( ! defined( 'ABSPATH' ) ) exit;

function hb_register_product_cpt() {
    register_post_type( 'hb_product', [
        'label'  => 'Products',
        'labels' => [
            'name'          => 'Products',
            'singular_name' => 'Product',
            'add_new_item'  => 'Add New Product',
            'edit_item'     => 'Edit Product',
            'all_items'     => 'All Products',
        ],
        'public'       => true,
        'show_in_menu' => true,
        'menu_icon'    => 'dashicons-carrot',
        'supports'     => [ 'title', 'thumbnail' ],
        'show_in_rest' => true,
        'has_archive'  => false,
        'rewrite'      => false,
    ] );
}
add_action( 'init', 'hb_register_product_cpt' );
