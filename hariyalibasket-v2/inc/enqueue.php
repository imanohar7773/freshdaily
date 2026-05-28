<?php
/**
 * Enqueue CSS & JS
 * NOTE: For production, combine these into single minified file.
 * See README → CDN/Minify section.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

function hb_enqueue_assets() {
    $ver = HB_THEME_VERSION;
    $css = HB_THEME_URI . '/assets/css';
    $js  = HB_THEME_URI . '/assets/js';

    // Google Fonts (Nunito + Sora — new UI fonts)
    wp_enqueue_style(
        'hb-fonts',
        'https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Sora:wght@400;600;700;800&display=swap',
        [],
        null
    );

    // Theme metadata stylesheet (required by WordPress)
    wp_enqueue_style( 'hb-style', get_stylesheet_uri(), [], $ver );

    // Modular CSS files (load order matters)
    wp_enqueue_style( 'hb-base',     "$css/base.css",     [], $ver );
    wp_enqueue_style( 'hb-layout',   "$css/layout.css",   [ 'hb-base' ], $ver );
    wp_enqueue_style( 'hb-products', "$css/products.css", [ 'hb-base' ], $ver );
    wp_enqueue_style( 'hb-cart',     "$css/cart.css",     [ 'hb-base' ], $ver );
    wp_enqueue_style( 'hb-sections', "$css/sections.css", [ 'hb-base' ], $ver );
    wp_enqueue_style( 'hb-modals',   "$css/modals.css",   [ 'hb-base' ], $ver );
    wp_enqueue_style( 'hb-anim',     "$css/animations.css", [ 'hb-base' ], $ver );
    wp_enqueue_style( 'hb-respond',  "$css/responsive.css", [ 'hb-base' ], $ver );

    // Modular JS files
    wp_enqueue_script( 'hb-main',     "$js/main.js",     [], $ver, true );
    wp_enqueue_script( 'hb-products', "$js/products.js", [ 'hb-main' ], $ver, true );
    wp_enqueue_script( 'hb-search',   "$js/search.js",   [ 'hb-main' ], $ver, true );
    wp_enqueue_script( 'hb-cart',     "$js/cart.js",     [ 'hb-main' ], $ver, true );
    wp_enqueue_script( 'hb-checkout', "$js/checkout.js", [ 'hb-cart' ], $ver, true );
    wp_enqueue_script( 'hb-extras',   "$js/extras.js",   [ 'hb-main' ], $ver, true );

    // Pass PHP data to JS
    wp_localize_script( 'hb-main', 'HB', [
        'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
        'nonce'        => wp_create_nonce( 'hb_nonce' ),
        'wa'           => hb_get( 'hb_wa_number', '918000344554' ),
        'upi'          => hb_get( 'hb_upi_id', 'imanohar07773@ybl' ),
        'minFree'      => intval( hb_get( 'hb_free_delivery_min', 199 ) ),
        'deliveryFee'  => intval( hb_get( 'hb_delivery_fee', 69 ) ),
        'sheetUrl'     => hb_get( 'hb_sheet_url', '' ),
        'homeUrl'      => home_url( '/' ),
    ] );
}
add_action( 'wp_enqueue_scripts', 'hb_enqueue_assets' );
