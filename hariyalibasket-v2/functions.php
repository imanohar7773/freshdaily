<?php
/**
 * HariyaliBasket v2 — Theme Functions
 * Modular structure — all logic split into /inc/ files
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'HB_THEME_VERSION', '2.0.0' );
define( 'HB_THEME_DIR', get_template_directory() );
define( 'HB_THEME_URI', get_template_directory_uri() );

/**
 * Load all /inc/ modules
 */
$hb_modules = [
    'helpers',          // hb_get(), get_emoji(), formatters
    'theme-setup',      // theme support, menus
    'enqueue',          // CSS/JS loading
    'post-types',       // hb_product CPT
    'taxonomies',       // hb_category
    'meta-fields',      // MRP, SP, UOM, variants
    'customizer',       // WhatsApp, UPI, validity, free-del
    'products',         // get_all_products()
    'ajax',             // hb_get_products endpoint
    'woo-sync',         // WooCommerce price/stock sync
    'bulk-editor',      // /wp-admin/?page=hb_bulk_price_editor
    'security',         // security headers
    'cache',            // cache clearing on save
    // ── NEW ADDITIONS ──
    'orders',           // hb_order CPT (real order saving)
    'admin-dashboard',  // Order management dashboard
    'rate-limit',       // Spam prevention
    'captcha',          // Math CAPTCHA
];

foreach ( $hb_modules as $mod ) {
    $file = HB_THEME_DIR . "/inc/{$mod}.php";
    if ( file_exists( $file ) ) require_once $file;
}
