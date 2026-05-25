<?php
/**
 * Plugin Name: HariyaliBasket Bulk Image Setter
 * Description: Aapke saare hb_product posts ke liye automatic Wikipedia se images fetch karta hai aur featured image set karta hai. Frontend pe automatic emoji ki jagah real image dikhne lagta hai.
 * Version:     1.0
 * Author:      HariyaliBasket
 * Text Domain: hb-image-setter
 *
 * INSTALLATION:
 * 1. Is file ko /wp-content/plugins/ folder mein upload karo
 * 2. WordPress Admin → Plugins → "HariyaliBasket Bulk Image Setter" → Activate
 * 3. Left sidebar mein "🖼️ HB Images" menu aayega — wahan se 1-click se sab images set karo
 * 4. Frontend pe automatic emoji ki jagah real image dikhne lagega
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* ============================================================
   1. ADMIN MENU
   ============================================================ */
add_action( 'admin_menu', 'hb_bis_admin_menu' );

function hb_bis_admin_menu() {
    add_menu_page(
        'HB Image Setter',
        '🖼️ HB Images',
        'manage_options',
        'hb-image-setter',
        'hb_bis_admin_page',
        'dashicons-format-image',
        26
    );
}

/* ============================================================
   2. PRODUCT NAME → WIKIPEDIA TITLE MAPPING
   ============================================================ */
function hb_bis_get_wiki_title( $product_name ) {
    $name = strtolower( trim( $product_name ) );

    // Specific mappings (longest match wins)
    $mappings = [
        'apple green imp'       => 'Granny_Smith',
        'apple himachal'        => 'Apple',
        'apple imp'             => 'Apple',
        'avocado'               => 'Avocado',
        'banana leaf'           => 'Banana_leaf',
        'banana'                => 'Banana',
        'chikoo'                => 'Manilkara_zapota',
        'sapota'                => 'Manilkara_zapota',
        'coconut dhab'          => 'Coconut',
        'coconut fresh'         => 'Coconut',
        'coconut'               => 'Coconut',
        'dragon fruit'          => 'Pitaya',
        'guava'                 => 'Guava',
        'jamun'                 => 'Syzygium_cumini',
        'kiwi'                  => 'Kiwifruit',
        'mango alphonso'        => 'Alphonso_(mango)',
        'alphonso'              => 'Alphonso_(mango)',
        'mango langra'          => 'Langra_(mango)',
        'langra'                => 'Langra_(mango)',
        'mango safeda'          => 'Mango',
        'safeda'                => 'Mango',
        'raw mango'             => 'Mango',
        'mango'                 => 'Mango',
        'papaya fresh'          => 'Papaya',
        'raw papaya'            => 'Papaya',
        'papaya'                => 'Papaya',
        'pineapple'             => 'Pineapple',
        'pomegranate'           => 'Pomegranate',
        'anar'                  => 'Pomegranate',
        'sweet lime'            => 'Mosambi',
        'mosambi'               => 'Mosambi',
        'watermelon'            => 'Watermelon',

        // Root vegetables
        'beetroot'              => 'Beetroot',
        'carrot'                => 'Carrot',
        'garlic peeled'         => 'Garlic',
        'garlic'                => 'Garlic',
        'ginger'                => 'Ginger',
        'onion small'           => 'Shallot',
        'onion'                 => 'Onion',
        'red potato'            => 'Potato',
        'potato small'          => 'Potato',
        'potato'                => 'Potato',

        // Green vegetables
        'arbi'                  => 'Taro',
        'colocasia'             => 'Taro',
        'baby corn'             => 'Baby_corn',
        'bitter gourd'          => 'Bitter_melon',
        'karela'                => 'Bitter_melon',
        'bottle gourd'          => 'Calabash',
        'lauki'                 => 'Calabash',
        'brinjal'               => 'Eggplant',
        'baingan'               => 'Eggplant',
        'broccoli'              => 'Broccoli',
        'cabbage'               => 'Cabbage',
        'cauliflower'           => 'Cauliflower',
        'red capsicum'          => 'Bell_pepper',
        'yellow capsicum'       => 'Bell_pepper',
        'capsicum'              => 'Bell_pepper',
        'chola fali'            => 'Yardlong_bean',
        'corn'                  => 'Sweet_corn',
        'bhutta'                => 'Sweet_corn',
        'cucumber'              => 'Cucumber',
        'curry leaves'          => 'Curry_tree',
        'curry patta'           => 'Curry_tree',
        'drumsticks'            => 'Moringa_oleifera',
        'sahjan'                => 'Moringa_oleifera',
        'moringa'               => 'Moringa_oleifera',
        'french beans'          => 'Green_bean',
        'green chilli'          => 'Capsicum',
        'guar phali'            => 'Guar',
        'jackfruit'             => 'Jackfruit',
        'kathal'                => 'Jackfruit',
        'kachri'                => 'Cucumis_callosus',
        'kakri'                 => 'Cucumis_melo',
        'lady finger'           => 'Okra',
        'bhindi'                => 'Okra',
        'lettuce'               => 'Lettuce',
        'mushroom'              => 'Agaricus_bisporus',
        'parwal'                => 'Trichosanthes_dioica',
        'red pumpkin'           => 'Pumpkin',
        'pumpkin'               => 'Pumpkin',
        'spinach'               => 'Spinach',
        'palak'                 => 'Spinach',
        'spring onion'          => 'Scallion',
        'tinda'                 => 'Praecitrullus_fistulosus',
        'tomato'                => 'Tomato',
        'zucchini'              => 'Zucchini',

        // Herbs
        'coriander'             => 'Coriander',
        'dhaniya'               => 'Coriander',
        'lemon'                 => 'Lemon',
        'nimbu'                 => 'Lemon',
        'mint'                  => 'Mentha',
        'pudina'                => 'Mentha',
    ];

    // Find longest matching key
    $best_match = null;
    $longest = 0;
    foreach ( $mappings as $key => $wiki_title ) {
        if ( strpos( $name, $key ) !== false && strlen( $key ) > $longest ) {
            $best_match = $wiki_title;
            $longest = strlen( $key );
        }
    }

    if ( $best_match ) return $best_match;

    // Fallback: clean up name
    $clean = preg_replace( '/\([^)]*\)/', '', $product_name );
    $clean = preg_replace( '/\b(Imp\.?|Fresh|Local|Imported|Desi|per\s+\w+)\b/i', '', $clean );
    $clean = trim( preg_replace( '/\s+/', ' ', $clean ) );
    return str_replace( ' ', '_', $clean );
}

/* ============================================================
   3. WIKIPEDIA IMAGE FETCHER
   ============================================================ */
function hb_bis_fetch_wiki_image( $title ) {
    if ( empty( $title ) ) return null;

    $api_url = 'https://en.wikipedia.org/w/api.php?' . http_build_query( [
        'action'      => 'query',
        'format'      => 'json',
        'prop'        => 'pageimages',
        'titles'      => $title,
        'piprop'      => 'thumbnail|original',
        'pithumbsize' => 800,
    ] );

    $response = wp_remote_get( $api_url, [
        'timeout' => 20,
        'headers' => [ 'User-Agent' => 'HariyaliBasket-ImageBot/1.0' ],
    ] );

    if ( is_wp_error( $response ) ) return null;

    $body = json_decode( wp_remote_retrieve_body( $response ), true );
    if ( empty( $body['query']['pages'] ) ) return null;

    foreach ( $body['query']['pages'] as $page ) {
        if ( ! empty( $page['thumbnail']['source'] ) ) return $page['thumbnail']['source'];
        if ( ! empty( $page['original']['source'] ) )  return $page['original']['source'];
    }

    // Fallback: search Wikipedia
    $search_url = 'https://en.wikipedia.org/w/api.php?' . http_build_query( [
        'action'      => 'query',
        'format'      => 'json',
        'generator'   => 'search',
        'gsrsearch'   => str_replace( '_', ' ', $title ),
        'gsrlimit'    => 3,
        'prop'        => 'pageimages',
        'piprop'      => 'thumbnail|original',
        'pithumbsize' => 800,
    ] );

    $response2 = wp_remote_get( $search_url, [
        'timeout' => 20,
        'headers' => [ 'User-Agent' => 'HariyaliBasket-ImageBot/1.0' ],
    ] );

    if ( is_wp_error( $response2 ) ) return null;

    $body2 = json_decode( wp_remote_retrieve_body( $response2 ), true );
    if ( empty( $body2['query']['pages'] ) ) return null;

    foreach ( $body2['query']['pages'] as $page ) {
        if ( ! empty( $page['thumbnail']['source'] ) ) return $page['thumbnail']['source'];
        if ( ! empty( $page['original']['source'] ) )  return $page['original']['source'];
    }

    return null;
}

/* ============================================================
   4. AJAX: FETCH AND SET IMAGE FOR ONE PRODUCT
   ============================================================ */
add_action( 'wp_ajax_hb_bis_set_image', 'hb_bis_set_image_ajax' );

function hb_bis_set_image_ajax() {
    if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error( 'Permission denied' );
    check_ajax_referer( 'hb_bis_nonce', 'nonce' );

    $post_id = intval( $_POST['post_id'] ?? 0 );
    $force   = ! empty( $_POST['force'] );

    if ( ! $post_id ) wp_send_json_error( 'Invalid post' );

    $post = get_post( $post_id );
    if ( ! $post ) wp_send_json_error( 'Post not found' );

    // Skip if already has featured image (unless force)
    if ( ! $force && has_post_thumbnail( $post_id ) ) {
        wp_send_json_success( [
            'url'  => get_the_post_thumbnail_url( $post_id, 'medium' ),
            'note' => 'Already had image (skipped)',
            'skipped' => true,
        ] );
    }

    $title     = $post->post_title;
    $wiki_title = hb_bis_get_wiki_title( $title );
    $img_url   = hb_bis_fetch_wiki_image( $wiki_title );

    if ( ! $img_url ) {
        wp_send_json_error( "Image nahi mili: {$title} (tried: {$wiki_title})" );
    }

    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    $tmp = download_url( $img_url, 30 );
    if ( is_wp_error( $tmp ) ) {
        wp_send_json_error( 'Download failed: ' . $tmp->get_error_message() );
    }

    $extension = pathinfo( parse_url( $img_url, PHP_URL_PATH ), PATHINFO_EXTENSION );
    if ( ! in_array( strtolower( $extension ), [ 'jpg', 'jpeg', 'png', 'webp', 'gif' ] ) ) {
        $extension = 'jpg';
    }

    $file_array = [
        'name'     => sanitize_file_name( $title ) . '.' . $extension,
        'tmp_name' => $tmp,
    ];

    $attach_id = media_handle_sideload( $file_array, $post_id, $title );

    if ( is_wp_error( $attach_id ) ) {
        @unlink( $tmp );
        wp_send_json_error( 'Sideload failed: ' . $attach_id->get_error_message() );
    }

    set_post_thumbnail( $post_id, $attach_id );

    wp_send_json_success( [
        'url'       => wp_get_attachment_image_url( $attach_id, 'medium' ),
        'attach_id' => $attach_id,
    ] );
}

/* ============================================================
   5. ADMIN PAGE
   ============================================================ */
function hb_bis_admin_page() {
    $products = get_posts( [
        'post_type'      => 'hb_product',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'orderby'        => 'title',
        'order'          => 'ASC',
    ] );

    if ( empty( $products ) ) {
        echo '<div class="wrap"><h1>🖼️ HB Image Setter</h1>';
        echo '<p>Koi <code>hb_product</code> posts nahi mile. Pehle products add karo.</p></div>';
        return;
    }

    $nonce = wp_create_nonce( 'hb_bis_nonce' );
    $with_image = 0;
    $without_image = 0;
    foreach ( $products as $p ) {
        if ( has_post_thumbnail( $p->ID ) ) $with_image++;
        else $without_image++;
    }
    ?>
    <div class="wrap">
        <h1 style="display:flex;align-items:center;gap:10px;color:#1a4d2e">
            🖼️ HariyaliBasket Image Setter
        </h1>
        <p style="font-size:14px;color:#555">
            Aapke <b><?php echo count( $products ); ?> products</b> ke liye automatic Wikipedia se images fetch hongi aur featured image set hogi.
            <br>Frontend pe automatic emoji ki jagah real image dikhne lagega.
        </p>

        <div style="background:#fff;border:1px solid #ddd;border-radius:10px;padding:16px;margin-top:14px">
            <div style="display:flex;gap:14px;flex-wrap:wrap;margin-bottom:14px">
                <div style="background:#e8f5e9;padding:10px 16px;border-radius:8px">
                    <b>Total:</b> <?php echo count( $products ); ?>
                </div>
                <div style="background:#e8f5e9;padding:10px 16px;border-radius:8px;color:#2e7d32">
                    ✅ <b>Image set:</b> <span id="hb-stat-with"><?php echo $with_image; ?></span>
                </div>
                <div style="background:#fff3e0;padding:10px 16px;border-radius:8px;color:#e65100">
                    ⏳ <b>Image baaki:</b> <span id="hb-stat-without"><?php echo $without_image; ?></span>
                </div>
            </div>

            <div style="display:flex;gap:10px;flex-wrap:wrap">
                <button class="button button-primary" id="hb-bis-start" style="font-size:14px;padding:10px 20px;height:auto">
                    ▶️ Auto-Fetch All Images (jin mein nahi hai)
                </button>
                <button class="button" id="hb-bis-force" style="font-size:14px;padding:10px 20px;height:auto;background:#f4a228;color:#fff;border-color:#f4a228">
                    🔄 Re-Fetch ALL (existing replace karo)
                </button>
            </div>

            <div id="hb-bis-progress" style="display:none;margin-top:16px">
                <div style="display:flex;justify-content:space-between;margin-bottom:6px;font-size:13px;font-weight:bold">
                    <span id="hb-bis-status">Processing...</span>
                    <span id="hb-bis-counter">0 / <?php echo count( $products ); ?></span>
                </div>
                <progress id="hb-bis-bar" max="<?php echo count( $products ); ?>" value="0" style="width:100%;height:14px;border-radius:7px;overflow:hidden"></progress>
            </div>
        </div>

        <div id="hb-bis-grid" style="margin-top:20px;display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:10px;">
            <?php foreach ( $products as $p ) :
                $thumb = get_the_post_thumbnail_url( $p->ID, 'thumbnail' );
            ?>
            <div class="hb-bis-card" id="hb-card-<?php echo $p->ID; ?>" data-id="<?php echo $p->ID; ?>"
                 style="background:#fff;border:1px solid <?php echo $thumb ? '#a5d6a7' : '#ffcc80'; ?>;border-radius:10px;padding:10px;text-align:center">
                <div class="hb-card-img"
                     style="width:100%;height:120px;background:#f5f5f5;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:34px;overflow:hidden;margin-bottom:8px">
                    <?php if ( $thumb ) : ?>
                        <img src="<?php echo esc_url( $thumb ); ?>" style="width:100%;height:100%;object-fit:cover">
                    <?php else : ?>
                        <span style="opacity:0.4">🌿</span>
                    <?php endif; ?>
                </div>
                <div style="font-size:12px;font-weight:700;color:#1a4d2e;line-height:1.3;min-height:32px">
                    <?php echo esc_html( $p->post_title ); ?>
                </div>
                <div class="hb-card-status" style="font-size:10px;margin-top:4px;font-weight:700;color:<?php echo $thumb ? '#2e7d32' : '#e65100'; ?>">
                    <?php echo $thumb ? '✅ Image set' : '⏳ Pending'; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
    (function() {
        const HB_BIS = {
            ajax:  '<?php echo admin_url( 'admin-ajax.php' ); ?>',
            nonce: '<?php echo $nonce; ?>',
            ids:   <?php echo wp_json_encode( wp_list_pluck( $products, 'ID' ) ); ?>
        };

        function $(id) { return document.getElementById(id); }

        async function processOne(id, force) {
            const fd = new FormData();
            fd.append('action',  'hb_bis_set_image');
            fd.append('nonce',   HB_BIS.nonce);
            fd.append('post_id', id);
            if (force) fd.append('force', '1');

            try {
                const r = await fetch(HB_BIS.ajax, { method: 'POST', body: fd });
                return await r.json();
            } catch (e) {
                return { success: false, data: 'Network error' };
            }
        }

        async function runAll(force) {
            if (!confirm(force
                ? 'Saare products ki images RE-FETCH karein? Existing images replace ho jayengi.'
                : 'Jin products mein image nahi hai unke liye fetch karein?'
            )) return;

            const startBtn = $('hb-bis-start');
            const forceBtn = $('hb-bis-force');
            startBtn.disabled = true;
            forceBtn.disabled = true;
            startBtn.textContent = '⏳ Processing...';

            $('hb-bis-progress').style.display = 'block';
            const bar     = $('hb-bis-bar');
            const status  = $('hb-bis-status');
            const counter = $('hb-bis-counter');

            let success = 0, failed = 0, skipped = 0;
            const total = HB_BIS.ids.length;

            for (let i = 0; i < total; i++) {
                const id = HB_BIS.ids[i];
                const card = $('hb-card-' + id);
                const title = card ? card.querySelector('div[style*="font-weight"]').textContent.trim() : id;

                status.textContent = `Processing: ${title}`;
                counter.textContent = `${i + 1} / ${total}`;

                if (card) {
                    card.querySelector('.hb-card-status').textContent = '⏳ Fetching...';
                    card.style.borderColor = '#ffd54f';
                }

                const result = await processOne(id, force);

                if (result.success) {
                    if (result.data.skipped) {
                        skipped++;
                        if (card) {
                            card.querySelector('.hb-card-status').textContent = '⏭️ Already had image';
                            card.querySelector('.hb-card-status').style.color = '#1976d2';
                            card.style.borderColor = '#a5d6a7';
                        }
                    } else {
                        success++;
                        if (card && result.data.url) {
                            card.querySelector('.hb-card-img').innerHTML =
                                '<img src="' + result.data.url + '" style="width:100%;height:100%;object-fit:cover">';
                            card.querySelector('.hb-card-status').textContent = '✅ Set!';
                            card.querySelector('.hb-card-status').style.color = '#2e7d32';
                            card.style.borderColor = '#a5d6a7';
                        }
                    }
                } else {
                    failed++;
                    if (card) {
                        card.querySelector('.hb-card-status').textContent = '❌ Failed';
                        card.querySelector('.hb-card-status').style.color = '#c62828';
                        card.style.borderColor = '#ef9a9a';
                        card.title = result.data || 'Failed';
                    }
                }

                bar.value = i + 1;

                // Stat counters
                $('hb-stat-with').textContent = parseInt($('hb-stat-with').textContent || 0) +
                    (result.success && !result.data.skipped ? 1 : 0);

                // Delay to avoid rate limit
                await new Promise(r => setTimeout(r, 200));
            }

            status.textContent = `✅ Done! Success: ${success}, Skipped: ${skipped}, Failed: ${failed}`;
            counter.textContent = `${total} / ${total}`;
            startBtn.textContent = '🔄 Run Again';
            startBtn.disabled = false;
            forceBtn.disabled = false;
        }

        $('hb-bis-start').addEventListener('click', () => runAll(false));
        $('hb-bis-force').addEventListener('click', () => runAll(true));
    })();
    </script>
    <?php
}

/* ============================================================
   6. FRONTEND: REPLACE EMOJI WITH FEATURED IMAGE
   ============================================================ */
add_action( 'wp_footer', 'hb_bis_inject_frontend_images', 999 );

function hb_bis_inject_frontend_images() {
    // Only on pages where allProducts is rendered
    $products = get_posts( [
        'post_type'      => 'hb_product',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
    ] );

    $images = [];
    foreach ( $products as $p ) {
        $url = get_the_post_thumbnail_url( $p->ID, 'medium' );
        if ( $url ) {
            $images[ (string) $p->ID ] = $url;
        }
    }

    if ( empty( $images ) ) return;
    ?>
    <style>
    /* Real image styling for product cards */
    .prod-emoji.hb-has-image {
        font-size: 0 !important;
        height: 100px;
        width: 100%;
        margin-bottom: 8px;
        background: #f5f5f5;
        border-radius: 8px;
        overflow: hidden;
        display: block !important;
        text-align: initial;
        line-height: 0;
    }
    .prod-emoji.hb-has-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        transition: transform 0.3s ease;
    }
    .prod-card:hover .prod-emoji.hb-has-image img {
        transform: scale(1.05);
    }
    /* Wishlist cards */
    #wishlist-grid .prod-emoji.hb-has-image {
        height: 80px;
    }
    /* Cart drawer emoji area */
    #cart-items .hb-cart-emoji-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 10px;
    }
    </style>
    <script>
    (function() {
        'use strict';

        const HB_PRODUCT_IMAGES = <?php echo wp_json_encode( $images ); ?>;

        function applyImages() {
            // Wait for allProducts to be defined
            if ( typeof allProducts === 'undefined' || ! Array.isArray( window.allProducts ) ) {
                return setTimeout( applyImages, 200 );
            }

            // Inject image URLs into allProducts
            allProducts.forEach( function( p ) {
                if ( HB_PRODUCT_IMAGES[ String( p.id ) ] ) {
                    p.image = HB_PRODUCT_IMAGES[ String( p.id ) ];
                }
            } );

            if ( typeof window.getEmoji !== 'function' ) {
                return setTimeout( applyImages, 200 );
            }

            if ( window.__hbImageOverrideDone ) return;
            window.__hbImageOverrideDone = true;

            // Override getEmoji to return img tag when product has image
            const _origGetEmoji = window.getEmoji;
            window.getEmoji = function( name ) {
                const product = allProducts.find( function( x ) { return x.name === name; } );
                if ( product && product.image ) {
                    return '<img src="' + product.image + '" alt="' + (name || '').replace(/"/g, '&quot;') + '">';
                }
                return _origGetEmoji.call( this, name );
            };

            // Override renderProducts to add hb-has-image class to spans containing img
            if ( typeof window.renderProducts === 'function' ) {
                const _origRender = window.renderProducts;
                window.renderProducts = function() {
                    _origRender.apply( this, arguments );
                    document.querySelectorAll( '.prod-emoji' ).forEach( function( span ) {
                        if ( span.querySelector( 'img' ) ) {
                            span.classList.add( 'hb-has-image' );
                        }
                    } );
                };

                // Trigger re-render
                window.renderProducts();
            }
        }

        if ( document.readyState === 'loading' ) {
            document.addEventListener( 'DOMContentLoaded', function() {
                setTimeout( applyImages, 600 );
            } );
        } else {
            setTimeout( applyImages, 600 );
        }
    })();
    </script>
    <?php
}
