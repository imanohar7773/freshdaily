<?php
/**
 * NEW ADDITION #4: Math CAPTCHA
 * Simple anti-bot — "What is 5 + 3?"
 *
 * Stored as transient: hb_captcha_{token} = correct_answer
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Generate a fresh challenge
 */
function hb_captcha_generate() {
    $a = wp_rand( 1, 9 );
    $b = wp_rand( 1, 9 );
    $token = wp_generate_password( 12, false, false );
    set_transient( 'hb_cap_' . $token, [ 'a' => $a, 'b' => $b, 'sum' => $a + $b ], 10 * MINUTE_IN_SECONDS );

    return [
        'token'    => $token,
        'a'        => $a,
        'b'        => $b,
        'question' => sprintf( '%d + %d = ?', $a, $b ),
    ];
}

/**
 * Verify a challenge response
 *
 * @param int $a User-submitted A
 * @param int $b User-submitted B
 * @param int $val User's answer
 * @return bool
 */
function hb_captcha_verify( $a, $b, $val ) {
    return ( intval( $val ) === intval( $a ) + intval( $b ) ) && intval( $a ) > 0 && intval( $b ) > 0;
}
