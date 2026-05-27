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

    // 4. Auto-generate for Sabzi / Veg / Fruits / Herbs
    $cat_lower  = strtolower( $cat );
    $auto_cats  = [ 'root', 'green', 'herb', 'leafy', 'sabzi', 'vegetable', 'fruit' ];
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
