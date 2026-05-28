<?php
/**
 * Custom Taxonomy: hb_category
 */
if ( ! defined( 'ABSPATH' ) ) exit;

function hb_register_category_taxonomy() {
    register_taxonomy( 'hb_category', 'hb_product', [
        'label'        => 'Product Category',
        'hierarchical' => true,
        'show_in_rest' => true,
        'rewrite'      => false,
    ] );
}
add_action( 'init', 'hb_register_category_taxonomy' );
