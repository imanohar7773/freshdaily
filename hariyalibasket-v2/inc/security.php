<?php
/**
 * Security headers
 */
if ( ! defined( 'ABSPATH' ) ) exit;

function hb_security_headers() {
    if ( is_admin() ) return;
    header( 'X-Content-Type-Options: nosniff' );
    header( 'X-Frame-Options: SAMEORIGIN' );
    header( 'X-XSS-Protection: 1; mode=block' );
    header( 'Referrer-Policy: strict-origin-when-cross-origin' );
    header( 'Permissions-Policy: camera=(), microphone=(), geolocation=()' );
}
add_action( 'send_headers', 'hb_security_headers' );

/**
 * Disable XML-RPC (common attack vector)
 */
add_filter( 'xmlrpc_enabled', '__return_false' );

/**
 * Remove WordPress version from <head>
 */
remove_action( 'wp_head', 'wp_generator' );
