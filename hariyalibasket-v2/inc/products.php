<?php
/**
 * Products data layer — loaded once per page render
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Get all hb_product as JSON-ready array
 */
function hb_get_all_products() {
    static $cache = null;
    if ( $cache !== null ) return $cache;

    $posts = get_posts( [
        'post_type'      => 'hb_product',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'orderby'        => 'menu_order title',
        'order'          => 'ASC',
    ] );

    $out = [];
    foreach ( $posts as $p ) {
        $terms = wp_get_post_terms( $p->ID, 'hb_category' );
        $cat   = ! empty( $terms ) ? html_entity_decode( $terms[0]->name, ENT_QUOTES, 'UTF-8' ) : 'General';

        $sp  = (float) ( get_post_meta( $p->ID, '_hb_sp', true ) ?: 0 );
        $mrp = (float) ( get_post_meta( $p->ID, '_hb_mrp', true ) ?: 0 );
        $uom = get_post_meta( $p->ID, '_hb_uom', true ) ?: 'Kg';

        $out[] = [
            'id'       => $p->ID,
            'name'     => $p->post_title,
            'cat'      => $cat,
            'uom'      => $uom,
            'mrp'      => $mrp,
            'sp'       => $sp,
            'img'      => has_post_thumbnail( $p->ID ) ? get_the_post_thumbnail_url( $p->ID, 'hb-product' ) : '',
            'variants' => hb_get_product_variants( $p->ID, $cat, $sp, $mrp, $uom ),
            'stock'    => hb_get_product_stock( $p->ID ),
        ];
    }

    $cache = $out;
    return $out;
}

/**
 * BUG FIX #6: Server-side cart validation
 *
 * Re-fetches actual prices from DB and validates client-submitted cart.
 * Prevents browser-console price tampering.
 *
 * @param array $client_items Raw items from $_POST
 * @return array|false { items: [validated], subtotal: float } or false on invalid
 */
function hb_validate_cart_items( $client_items ) {
    if ( empty( $client_items ) || ! is_array( $client_items ) ) return false;

    $all = hb_get_all_products();
    $by_id = [];
    foreach ( $all as $p ) $by_id[ $p['id'] ] = $p;

    $validated = [];
    $subtotal  = 0.0;

    foreach ( $client_items as $item ) {
        if ( ! is_array( $item ) ) continue;
        $pid = intval( $item['pid'] ?? 0 );
        $qty = max( 1, intval( $item['qty'] ?? 0 ) );
        $key = sanitize_text_field( $item['key'] ?? '' );

        if ( ! isset( $by_id[ $pid ] ) ) return false; // unknown product

        $product = $by_id[ $pid ];

        // Out-of-stock check
        if ( $product['stock'] === 'out' ) return false;

        // Resolve variant from key (e.g. "12_1" => variant index 1)
        $sp  = $product['sp'];
        $mrp = $product['mrp'];
        $uom = $product['uom'];
        $size_label = '';

        $parts = explode( '_', $key );
        if ( count( $parts ) > 1 ) {
            $vi = intval( $parts[ count( $parts ) - 1 ] );
            if ( ! empty( $product['variants'][ $vi ] ) ) {
                $v = $product['variants'][ $vi ];
                $sp  = (float) $v['sp'];
                $mrp = (float) $v['mrp'];
                $uom = $v['size'];
                $size_label = $v['size'];
            }
        }

        // Reasonable qty cap
        if ( $qty > 99 ) return false;

        $amount = $sp * $qty;
        $subtotal += $amount;

        $validated[] = [
            'pid'    => $pid,
            'key'    => $key,
            'name'   => $product['name'] . ( $size_label ? ' · ' . $size_label : '' ),
            'qty'    => $qty,
            'sp'     => $sp,
            'mrp'    => $mrp,
            'uom'    => $uom,
            'amount' => $amount,
        ];
    }

    if ( empty( $validated ) ) return false;

    return [
        'items'    => $validated,
        'subtotal' => $subtotal,
    ];
}

/**
 * Determine variants for a product
 */
function hb_get_product_variants( $post_id, $cat, $sp, $mrp, $uom ) {
    // 1. Manual variants override
    $v = get_post_meta( $post_id, '_hb_variants', true );
    if ( $v ) {
        $out = [];
        foreach ( explode( ',', $v ) as $chunk ) {
            $parts = explode( ':', trim( $chunk ) );
            if ( count( $parts ) >= 2 ) {
                $out[] = [
                    'size' => trim( $parts[0] ),
                    'sp'   => (float) $parts[1],
                    'mrp'  => isset( $parts[2] ) ? (float) $parts[2] : (float) $parts[1],
                ];
            }
        }
        return $out;
    }

    // 2. Skip variants for Pc/Pcs UOM
    $uom_lower = strtolower( $uom );
    if ( in_array( $uom_lower, [ 'pc', 'pcs', 'piece', 'pieces', 'pkt', 'packet', 'bunch', 'dozen' ] ) ) return [];

    // 3. Skip Banana Leaf and other special items
    $title_lower = strtolower( get_the_title( $post_id ) );
    $skip_keywords = [ 'banana leaf', 'mushroom', 'dragon fruit', 'kiwi', 'avocado', 'watermelon', 'coconut', 'pineapple' ];
    foreach ( $skip_keywords as $kw ) {
        if ( strpos( $title_lower, $kw ) !== false ) return [];
    }

    // 4. Auto-generate for Sabzi / Veg / Herbs only (fruits intentionally excluded —
    //    user wants single 1 Kg pricing for fruits, no 250g/500g splits)
    $cat_lower  = strtolower( $cat );
    if ( strpos( $cat_lower, 'fruit' ) !== false ) return [];
    $auto_cats  = [ 'root', 'green', 'herb', 'leafy', 'sabzi', 'vegetable' ];
    $is_auto    = false;
    foreach ( $auto_cats as $ac ) {
        if ( strpos( $cat_lower, $ac ) !== false ) { $is_auto = true; break; }
    }
    if ( ! $is_auto ) return [];

    return [
        [ 'size' => '250g', 'sp' => round( $sp / 4 ), 'mrp' => round( $mrp / 4 ) ],
        [ 'size' => '500g', 'sp' => round( $sp / 2 ), 'mrp' => round( $mrp / 2 ) ],
        [ 'size' => '1 Kg', 'sp' => $sp,             'mrp' => $mrp ],
    ];
}

/**
 * Stock state: 'in' or 'out'
 */
function hb_get_product_stock( $post_id ) {
    $manual = get_post_meta( $post_id, '_hb_stock', true );
    if ( $manual === 'out' ) return 'out';
    if ( $manual === 'in' ) return 'in';

    if ( function_exists( 'wc_get_product' ) ) {
        $sku = get_post_meta( $post_id, '_hb_sku', true );
        if ( $sku && function_exists( 'wc_get_product_id_by_sku' ) ) {
            $woo_id = wc_get_product_id_by_sku( $sku );
            if ( $woo_id ) {
                $woo = wc_get_product( $woo_id );
                if ( $woo ) return $woo->is_in_stock() ? 'in' : 'out';
            }
        }
    }
    return 'in';
}
