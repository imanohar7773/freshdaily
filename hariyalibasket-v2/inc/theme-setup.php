<?php
/**
 * Theme Setup
 */
if ( ! defined( 'ABSPATH' ) ) exit;

function hb_theme_setup() {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', [ 'search-form', 'comment-form', 'gallery', 'caption' ] );
    add_theme_support( 'automatic-feed-links' );
    register_nav_menus( [
        'primary' => __( 'Primary Menu', 'hariyalibasket' ),
    ] );

    // Image size for product thumbnails
    add_image_size( 'hb-product', 400, 400, true );
}
add_action( 'after_setup_theme', 'hb_theme_setup' );
