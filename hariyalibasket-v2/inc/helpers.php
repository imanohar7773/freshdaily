<?php
/**
 * Helper functions used across theme
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Customizer mod getter
 */
function hb_get( $key, $default = '' ) {
    return get_theme_mod( $key, $default );
}

/**
 * Get emoji for a product based on its name
 */
function hb_get_emoji( $name ) {
    $emoji_map = [
        'aloo' => '🥔', 'potato' => '🥔', 'pyaaz' => '🧅', 'onion' => '🧅',
        'tamatar' => '🍅', 'tomato' => '🍅', 'gajar' => '🥕', 'carrot' => '🥕',
        'palak' => '🥬', 'spinach' => '🥬', 'gobhi' => '🥦', 'cauliflower' => '🥦',
        'cabbage' => '🥬', 'bhindi' => '🫛', 'okra' => '🫛', 'lady finger' => '🫛',
        'shimla' => '🫑', 'capsicum' => '🫑', 'mango' => '🥭', 'aam' => '🥭',
        'kela' => '🍌', 'banana' => '🍌', 'seb' => '🍎', 'apple' => '🍎',
        'dhaniya' => '🌿', 'coriander' => '🌿', 'pudina' => '🌿', 'mint' => '🌿',
        'methi' => '🌱', 'mushroom' => '🍄', 'broccoli' => '🥦', 'adrak' => '🫚',
        'ginger' => '🫚', 'garlic' => '🧄', 'lahsun' => '🧄', 'beetroot' => '🫀',
        'beet' => '🫀', 'corn' => '🌽', 'bhutta' => '🌽', 'lauki' => '🥒',
        'gourd' => '🥒', 'cucumber' => '🥒', 'kheera' => '🥒', 'kakri' => '🥒',
        'baingan' => '🍆', 'brinjal' => '🍆', 'chilli' => '🌶️', 'mirch' => '🌶️',
        'lemon' => '🍋', 'nimbu' => '🍋', 'mosambi' => '🍋', 'lime' => '🍋',
        'watermelon' => '🍉', 'papaya' => '🫒', 'pineapple' => '🍍',
        'coconut' => '🥥', 'kiwi' => '🥝', 'avocado' => '🥑', 'dragon' => '🐲',
        'guava' => '🍈', 'pomegranate' => '🍑', 'anar' => '🍑', 'jamun' => '🫐',
        'chikoo' => '🟤', 'beans' => '🫘', 'pumpkin' => '🎃',
        'jackfruit' => '🍈', 'kathal' => '🍈', 'arbi' => '🥔',
        'tinda' => '🥒', 'parwal' => '🥒', 'drumstick' => '🌿',
        'banana leaf' => '🍃', 'spring' => '🧅', 'leafy' => '🥬',
        'lettuce' => '🥬', 'zucchini' => '🥒', 'kachri' => '🥒',
        'guar' => '🫘', 'chola' => '🫘',
    ];
    $n = strtolower( $name );
    foreach ( $emoji_map as $key => $em ) {
        if ( strpos( $n, $key ) !== false ) return $em;
    }
    return '🌿';
}

/**
 * Format INR amount
 */
function hb_inr( $amount ) {
    return '₹' . number_format( (float) $amount, 0 );
}

/**
 * Get client IP (for rate limiting)
 */
function hb_client_ip() {
    if ( ! empty( $_SERVER['HTTP_CF_CONNECTING_IP'] ) ) return $_SERVER['HTTP_CF_CONNECTING_IP'];
    if ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
        $ips = explode( ',', $_SERVER['HTTP_X_FORWARDED_FOR'] );
        return trim( $ips[0] );
    }
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

/**
 * Generate short order ID like HB7K9X2A
 */
function hb_generate_order_id() {
    return 'HB' . strtoupper( substr( md5( uniqid( '', true ) ), 0, 6 ) );
}
