<?php
/**
 * NEW ADDITION #3: Rate Limiting
 * Prevents spam orders.
 *
 * Limits:
 *  - 5 orders / hour per IP
 *  - 3 orders / hour per phone
 */
if ( ! defined( 'ABSPATH' ) ) exit;

define( 'HB_RATE_IP_MAX',    5 );
define( 'HB_RATE_PHONE_MAX', 3 );
define( 'HB_RATE_WINDOW',    HOUR_IN_SECONDS );

/**
 * Check if current IP is rate-limited
 */
function hb_rate_limit_check() {
    $ip  = hb_client_ip();
    $key = 'hb_rl_ip_' . md5( $ip );
    $count = (int) get_transient( $key );
    if ( $count >= HB_RATE_IP_MAX ) return false;

    set_transient( $key, $count + 1, HB_RATE_WINDOW );
    return true;
}

/**
 * Record phone usage (called after successful order)
 */
function hb_rate_limit_record( $phone ) {
    $key = 'hb_rl_ph_' . md5( $phone );
    $count = (int) get_transient( $key );
    set_transient( $key, $count + 1, HB_RATE_WINDOW );
}

/**
 * Check phone-specific limit (called before order)
 */
function hb_rate_limit_phone_check( $phone ) {
    $key = 'hb_rl_ph_' . md5( $phone );
    $count = (int) get_transient( $key );
    return $count < HB_RATE_PHONE_MAX;
}
