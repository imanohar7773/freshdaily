<?php
/**
 * NEW ADDITION #4: Math CAPTCHA — TOKEN-BASED (server-side state)
 *
 * Flow:
 *  1. Frontend calls hb_captcha (AJAX) → gets {token, question}
 *  2. User answers
 *  3. Submits {token, answer} with order
 *  4. Server validates answer against transient stored under that token
 *  5. Token deleted after first verify (one-time use)
 *
 * This prevents bots from sending fake (a,b,val) pairs that simply add up.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Generate a fresh challenge (server-side state)
 *
 * @return array { token, question }
 *   token    — opaque identifier client sends back
 *   question — human-readable, e.g. "5 + 3 = ?"
 */
function hb_captcha_generate() {
    $a     = wp_rand( 1, 9 );
    $b     = wp_rand( 1, 9 );
    $token = wp_generate_password( 24, false, false );

    // Store ONLY on server (transient = WP options/cache, 10-min TTL)
    set_transient( 'hb_cap_' . $token, [
        'sum'     => $a + $b,
        'created' => time(),
        'ip'      => function_exists( 'hb_client_ip' ) ? hb_client_ip() : ( $_SERVER['REMOTE_ADDR'] ?? '' ),
    ], 10 * MINUTE_IN_SECONDS );

    return [
        'token'    => $token,
        'question' => sprintf( '%d + %d = ?', $a, $b ),
    ];
}

/**
 * Verify a challenge response — uses server-stored sum, not client input.
 *
 * @param string $token  Token returned from generate()
 * @param mixed  $answer User-submitted answer
 * @return bool true if correct, false otherwise
 */
function hb_captcha_verify( $token, $answer ) {
    if ( empty( $token ) || ! is_string( $token ) ) return false;
    $key = 'hb_cap_' . $token;
    $data = get_transient( $key );
    if ( ! is_array( $data ) || ! isset( $data['sum'] ) ) return false;

    // Optional: bind to IP to prevent token reuse from another machine
    $current_ip = function_exists( 'hb_client_ip' ) ? hb_client_ip() : ( $_SERVER['REMOTE_ADDR'] ?? '' );
    if ( ! empty( $data['ip'] ) && $data['ip'] !== $current_ip ) {
        delete_transient( $key );
        return false;
    }

    $ok = ( intval( $answer ) === intval( $data['sum'] ) );

    // ONE-TIME USE: delete after attempt regardless of result
    // (prevents brute-force & replay attacks)
    delete_transient( $key );

    return $ok;
}
