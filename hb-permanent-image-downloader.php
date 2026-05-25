<?php
/**
 * ═══════════════════════════════════════════════════
 * HariyaliBasket — Permanent Image Downloader
 * Saari images ek baar server pe download ho jayengi
 * Pexels/Wikipedia se permanent azaadi!
 *
 * INSTALLATION:
 * 1. Is file ko /wp-content/plugins/ folder mein upload karo
 *    (ya theme ke functions.php mein paste kar do — dono kaam karenge)
 * 2. Plugins mein activate karo
 * 3. Browser mein kholo: https://YOURSITE.com/wp-admin/?hb_img_download=1
 * 4. 30-60 second mein saari 71 images download ho jayengi
 *
 * BAAD MEIN:
 * - Images aapke WordPress Media Library mein hain
 * - Har hb_product ke `_hb_img` meta mein local URL save hai
 * - Template mein _hb_img read karke <img> dikhana hai
 * ═══════════════════════════════════════════════════
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'admin_init', 'hb_run_image_download' );

function hb_run_image_download() {
    if ( ! isset( $_GET['hb_img_download'] ) ) return;
    if ( ! current_user_can( 'manage_options' ) ) return;

    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    @set_time_limit( 600 );
    @ini_set( 'memory_limit', '512M' );

    $products = [
        // FRUITS
        'HB-FR-001' => ['name' => 'Apple Green Imp.',        'url' => 'https://images.pexels.com/photos/9427265/pexels-photo-9427265.jpeg'],
        'HB-FR-002' => ['name' => 'Apple Himachal',          'url' => 'https://images.pexels.com/photos/34844892/pexels-photo-34844892.jpeg'],
        'HB-FR-003' => ['name' => 'Apple Imp.',              'url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a6/Pink_lady_and_cross_section.jpg/960px-Pink_lady_and_cross_section.jpg'],
        'HB-FR-004' => ['name' => 'Avocado Local',           'url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/f2/Persea_americana_fruit_2.JPG/960px-Persea_americana_fruit_2.JPG'],
        'HB-FR-005' => ['name' => 'Banana',                  'url' => 'https://images.pexels.com/photos/6848574/pexels-photo-6848574.jpeg'],
        'HB-FR-006' => ['name' => 'Chikoo',                  'url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/00/%E0%B4%B8%E0%B4%AA%E0%B5%8D%E0%B4%AA%E0%B5%8B%E0%B4%9F%E0%B5%8D%E0%B4%9F.jpg/960px-%E0%B4%B8%E0%B4%AA%E0%B5%8D%E0%B4%AA%E0%B5%8B%E0%B4%9F%E0%B5%8D%E0%B4%9F.jpg'],
        'HB-FR-007' => ['name' => 'Coconut Dhab',            'url' => 'https://images.pexels.com/photos/34528211/pexels-photo-34528211.jpeg'],
        'HB-FR-008' => ['name' => 'Coconut Fresh',           'url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/32/Cocos_nucifera_-_K%C3%B6hler%E2%80%93s_Medizinal-Pflanzen-187.jpg/960px-Cocos_nucifera_-_K%C3%B6hler%E2%80%93s_Medizinal-Pflanzen-187.jpg'],
        'HB-FR-009' => ['name' => 'Dragon Fruit',            'url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/4/43/Pitaya_cross_section_ed2.jpg/960px-Pitaya_cross_section_ed2.jpg'],
        'HB-FR-010' => ['name' => 'Guava (Desi)',            'url' => 'https://images.pexels.com/photos/8668727/pexels-photo-8668727.jpeg'],
        'HB-FR-011' => ['name' => 'Guava Imp.',              'url' => 'https://images.pexels.com/photos/5945840/pexels-photo-5945840.jpeg'],
        'HB-FR-012' => ['name' => 'Jamun',                   'url' => 'https://images.pexels.com/photos/37043794/pexels-photo-37043794.jpeg'],
        'HB-FR-013' => ['name' => 'Kiwi Fresh',              'url' => 'https://images.pexels.com/photos/6411505/pexels-photo-6411505.jpeg'],
        'HB-FR-014' => ['name' => 'Mango Alphonso',          'url' => 'https://images.pexels.com/photos/4687187/pexels-photo-4687187.jpeg'],
        'HB-FR-015' => ['name' => 'Mango Langra',            'url' => 'https://images.pexels.com/photos/33079558/pexels-photo-33079558.jpeg'],
        'HB-FR-016' => ['name' => 'Mango Safeda',            'url' => 'https://images.pexels.com/photos/37744364/pexels-photo-37744364.jpeg'],
        'HB-FR-017' => ['name' => 'Papaya Fresh',            'url' => 'https://images.pexels.com/photos/28613331/pexels-photo-28613331.jpeg'],
        'HB-FR-018' => ['name' => 'Pineapple Fresh',         'url' => 'https://images.pexels.com/photos/12471181/pexels-photo-12471181.jpeg'],
        'HB-FR-019' => ['name' => 'Pomegranate (Anar)',      'url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/6a/Pomegranate_Juice_%282019%29.jpg/960px-Pomegranate_Juice_%282019%29.jpg'],
        'HB-FR-020' => ['name' => 'Raw Papaya',              'url' => 'https://images.pexels.com/photos/13306351/pexels-photo-13306351.jpeg'],
        'HB-FR-021' => ['name' => 'Sweet Lime (Mosambi)',    'url' => 'https://images.pexels.com/photos/16105462/pexels-photo-16105462.jpeg'],
        'HB-FR-022' => ['name' => 'Watermelon (3-4 Kg)',     'url' => 'https://images.pexels.com/photos/15975999/pexels-photo-15975999.jpeg'],
        // ROOT VEGETABLES
        'HB-RV-001' => ['name' => 'Beetroot',                'url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/ae/Detroitdarkredbeets.png/960px-Detroitdarkredbeets.png'],
        'HB-RV-002' => ['name' => 'Carrot',                  'url' => 'https://images.pexels.com/photos/1306559/pexels-photo-1306559.jpeg'],
        'HB-RV-003' => ['name' => 'Garlic',                  'url' => 'https://images.pexels.com/photos/12955789/pexels-photo-12955789.jpeg'],
        'HB-RV-004' => ['name' => 'Garlic Peeled',           'url' => 'https://images.pexels.com/photos/750948/pexels-photo-750948.jpeg'],
        'HB-RV-005' => ['name' => 'Ginger',                  'url' => 'https://images.pexels.com/photos/7657087/pexels-photo-7657087.jpeg'],
        'HB-RV-006' => ['name' => 'Onion',                   'url' => 'https://images.pexels.com/photos/12999831/pexels-photo-12999831.jpeg'],
        'HB-RV-007' => ['name' => 'Onion Small',             'url' => 'https://images.pexels.com/photos/34961607/pexels-photo-34961607.jpeg'],
        'HB-RV-008' => ['name' => 'Potato',                  'url' => 'https://images.pexels.com/photos/144248/potatoes-vegetables-erdfrucht-bio-144248.jpeg'],
        'HB-RV-009' => ['name' => 'Potato Small',            'url' => 'https://images.pexels.com/photos/35595249/pexels-photo-35595249.jpeg'],
        'HB-RV-010' => ['name' => 'Raw Mango',               'url' => 'https://images.pexels.com/photos/37490138/pexels-photo-37490138.jpeg'],
        'HB-RV-011' => ['name' => 'Red Potato',              'url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/ab/Patates.jpg/960px-Patates.jpg'],
        // VEGETABLES
        'HB-VG-001' => ['name' => 'Arbi (Colocasia)',        'url' => 'https://images.pexels.com/photos/7543152/pexels-photo-7543152.jpeg'],
        'HB-VG-002' => ['name' => 'Baby Corn Fresh',         'url' => 'https://upload.wikimedia.org/wikipedia/commons/a/a1/Baby_corn.jpg'],
        'HB-VG-003' => ['name' => 'Bitter Gourd (Karela)',   'url' => 'https://images.pexels.com/photos/28909474/pexels-photo-28909474.jpeg'],
        'HB-VG-004' => ['name' => 'Bottle Gourd (Lauki)',    'url' => 'https://upload.wikimedia.org/wikipedia/commons/a/a2/Courge_encore_verte.jpg'],
        'HB-VG-005' => ['name' => 'Brinjal (Baingan)',       'url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/76/Solanum_melongena_24_08_2012_%281%29.JPG/960px-Solanum_melongena_24_08_2012_%281%29.JPG'],
        'HB-VG-006' => ['name' => 'Broccoli',                'url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/03/Broccoli_and_cross_section_edit.jpg/960px-Broccoli_and_cross_section_edit.jpg'],
        'HB-VG-007' => ['name' => 'Cabbage Green',           'url' => 'https://images.pexels.com/photos/37404971/pexels-photo-37404971.jpeg'],
        'HB-VG-008' => ['name' => 'Capsicum Green',          'url' => 'https://images.pexels.com/photos/36935461/pexels-photo-36935461.jpeg'],
        'HB-VG-009' => ['name' => 'Cauliflower',             'url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/2f/Chou-fleur_02.jpg/960px-Chou-fleur_02.jpg'],
        'HB-VG-010' => ['name' => 'Chola Fali',              'url' => 'https://images.pexels.com/photos/17975550/pexels-photo-17975550.jpeg'],
        'HB-VG-011' => ['name' => 'Corn (Bhutta)',           'url' => 'https://upload.wikimedia.org/wikipedia/commons/7/79/VegCorn.jpg'],
        'HB-VG-012' => ['name' => 'Cucumber Chinese',        'url' => 'https://images.pexels.com/photos/17975573/pexels-photo-17975573.jpeg'],
        'HB-VG-013' => ['name' => 'Curry Leaves',            'url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/ad/Curry_Trees.jpg/960px-Curry_Trees.jpg'],
        'HB-VG-014' => ['name' => 'Drumsticks (Sahjan)',     'url' => 'https://images.pexels.com/photos/20466259/pexels-photo-20466259.jpeg'],
        'HB-VG-015' => ['name' => 'French Beans',            'url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a0/Heaps_of_beans.jpg/960px-Heaps_of_beans.jpg'],
        'HB-VG-016' => ['name' => 'Green Chilli',            'url' => 'https://images.pexels.com/photos/10899475/pexels-photo-10899475.jpeg'],
        'HB-VG-017' => ['name' => 'Green Chilli (Hot)',      'url' => 'https://images.pexels.com/photos/5678084/pexels-photo-5678084.jpeg'],
        'HB-VG-018' => ['name' => 'Guar Phali',              'url' => 'https://upload.wikimedia.org/wikipedia/commons/9/9b/Cluster_bean-guar-Cyamopsis_psoralioides-Cyamopsis_tetragonolobus-TAMIL_NADU73.jpg'],
        'HB-VG-019' => ['name' => 'Jackfruit (Kathal)',      'url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/3b/The_jackfruit_is_holding_on_to_the_tree.jpg/960px-The_jackfruit_is_holding_on_to_the_tree.jpg'],
        'HB-VG-020' => ['name' => 'Kachri',                  'url' => 'https://images.pexels.com/photos/30876616/pexels-photo-30876616.jpeg'],
        'HB-VG-021' => ['name' => 'Kakri',                   'url' => 'https://images.pexels.com/photos/21644440/pexels-photo-21644440.jpeg'],
        'HB-VG-022' => ['name' => 'Lady Finger (Bhindi)',    'url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/95/Hong_Kong_Okra_Aug_25_2012.JPG/960px-Hong_Kong_Okra_Aug_25_2012.JPG'],
        'HB-VG-023' => ['name' => 'Lettuce Green',           'url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/d/da/Iceberg_lettuce_in_SB.jpg/960px-Iceberg_lettuce_in_SB.jpg'],
        'HB-VG-024' => ['name' => 'Parwal',                  'url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/2d/Pointed_gourd.jpg/960px-Pointed_gourd.jpg'],
        'HB-VG-025' => ['name' => 'Red Capsicum',            'url' => 'https://images.pexels.com/photos/34997142/pexels-photo-34997142.jpeg'],
        'HB-VG-026' => ['name' => 'Red Pumpkin',             'url' => 'https://images.pexels.com/photos/18509545/pexels-photo-18509545.jpeg'],
        'HB-VG-027' => ['name' => 'Spinach (Palak)',         'url' => 'https://images.pexels.com/photos/11064917/pexels-photo-11064917.jpeg'],
        'HB-VG-028' => ['name' => 'Spring Onion',            'url' => 'https://images.pexels.com/photos/7456538/pexels-photo-7456538.jpeg'],
        'HB-VG-029' => ['name' => 'Tinda',                   'url' => 'https://upload.wikimedia.org/wikipedia/commons/8/80/Tinda.jpg'],
        'HB-VG-030' => ['name' => 'Tomato',                  'url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/89/Tomato_je.jpg/960px-Tomato_je.jpg'],
        'HB-VG-031' => ['name' => 'Yellow Capsicum',         'url' => 'https://images.pexels.com/photos/32934476/pexels-photo-32934476.jpeg'],
        'HB-VG-032' => ['name' => 'Zucchini Green/Yellow',   'url' => 'https://images.pexels.com/photos/13656392/pexels-photo-13656392.jpeg'],
        'HB-VG-033' => ['name' => 'Mushroom Fresh 200g',     'url' => 'https://images.pexels.com/photos/5950405/pexels-photo-5950405.jpeg'],
        // HERBS
        'HB-HR-001' => ['name' => 'Banana Leaf',             'url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/74/Banana-leaf-1.JPG/960px-Banana-leaf-1.JPG'],
        'HB-HR-002' => ['name' => 'Coriander',               'url' => 'https://upload.wikimedia.org/wikipedia/commons/1/13/Coriandrum_sativum_-_K%C3%B6hler%E2%80%93s_Medizinal-Pflanzen-193.jpg'],
        'HB-HR-003' => ['name' => 'Coriander Fresh',         'url' => 'https://images.pexels.com/photos/12178852/pexels-photo-12178852.jpeg'],
        'HB-HR-004' => ['name' => 'Lemon (Nimbu)',           'url' => 'https://images.pexels.com/photos/37113424/pexels-photo-37113424.jpeg'],
        'HB-HR-005' => ['name' => 'Mint (Pudina)',           'url' => 'https://images.pexels.com/photos/12717629/pexels-photo-12717629.jpeg'],
    ];

    $results = [];
    $success = 0; $skipped = 0; $failed = 0;

    foreach ( $products as $sku => $data ) {
        $existing = get_posts( [
            'post_type'   => 'attachment',
            'post_status' => 'inherit',
            'meta_query'  => [ [ 'key' => '_hb_sku_img', 'value' => $sku ] ],
            'numberposts' => 1,
        ] );

        if ( ! empty( $existing ) ) {
            $local_url = wp_get_attachment_url( $existing[0]->ID );
            hb_update_product_img( $sku, $data['name'], $local_url );
            $skipped++;
            $results[] = [ 'status' => 'skip', 'sku' => $sku, 'name' => $data['name'], 'url' => $local_url ];
            continue;
        }

        $tmp = download_url( $data['url'], 30 );
        if ( is_wp_error( $tmp ) ) {
            $failed++;
            $results[] = [ 'status' => 'fail', 'sku' => $sku, 'name' => $data['name'], 'error' => $tmp->get_error_message() ];
            continue;
        }

        $ext = pathinfo( strtok( $data['url'], '?' ), PATHINFO_EXTENSION ) ?: 'jpg';
        $ext = strtolower( $ext );
        if ( ! in_array( $ext, [ 'jpg', 'jpeg', 'png', 'webp', 'gif' ] ) ) $ext = 'jpg';

        $file = [
            'name'     => sanitize_file_name( $sku . '-' . $data['name'] . '.' . $ext ),
            'tmp_name' => $tmp,
        ];

        $attach_id = media_handle_sideload( $file, 0, $data['name'] );
        @unlink( $tmp );

        if ( is_wp_error( $attach_id ) ) {
            $failed++;
            $results[] = [ 'status' => 'fail', 'sku' => $sku, 'name' => $data['name'], 'error' => $attach_id->get_error_message() ];
            continue;
        }

        update_post_meta( $attach_id, '_hb_sku_img', $sku );
        $local_url = wp_get_attachment_url( $attach_id );
        hb_update_product_img( $sku, $data['name'], $local_url );

        $success++;
        $results[] = [ 'status' => 'ok', 'sku' => $sku, 'name' => $data['name'], 'url' => $local_url ];
    }

    delete_transient( 'hb_products_cache' );

    $html  = '<div style="font-family:sans-serif;max-width:900px;margin:40px auto;padding:0 20px">';
    $html .= '<h2 style="color:#1a4d2e">🌿 HariyaliBasket — Image Download Report</h2>';
    $html .= '<div style="display:flex;gap:16px;margin-bottom:20px;flex-wrap:wrap">';
    $html .= '<div style="background:#d4edda;border-radius:8px;padding:12px 20px;flex:1;text-align:center;min-width:160px"><strong style="font-size:24px;color:green">' . $success . '</strong><br>Downloaded</div>';
    $html .= '<div style="background:#fff3cd;border-radius:8px;padding:12px 20px;flex:1;text-align:center;min-width:160px"><strong style="font-size:24px;color:orange">' . $skipped . '</strong><br>Already Done</div>';
    $html .= '<div style="background:#f8d7da;border-radius:8px;padding:12px 20px;flex:1;text-align:center;min-width:160px"><strong style="font-size:24px;color:red">' . $failed . '</strong><br>Failed</div>';
    $html .= '</div>';
    $html .= '<table style="width:100%;border-collapse:collapse;font-size:13px">';
    $html .= '<tr style="background:#1a4d2e;color:#fff"><th style="padding:8px;text-align:left">SKU</th><th style="padding:8px;text-align:left">Product</th><th style="padding:8px;text-align:left">Status</th><th style="padding:8px;text-align:left">Local URL / Error</th></tr>';
    foreach ( $results as $i => $r ) {
        $bg = $i % 2 === 0 ? '#f9f9f9' : '#fff';
        $icon = $r['status'] === 'ok' ? '✅' : ( $r['status'] === 'skip' ? '⏭️' : '❌' );
        $url_col = $r['status'] === 'fail'
            ? '<span style="color:red">' . esc_html( $r['error'] ) . '</span>'
            : '<a href="' . esc_url( $r['url'] ) . '" target="_blank" style="color:#1a4d2e;word-break:break-all">' . esc_html( $r['url'] ) . '</a>';
        $html .= '<tr style="background:' . $bg . '"><td style="padding:6px 8px">' . esc_html( $r['sku'] ) . '</td><td style="padding:6px 8px">' . esc_html( $r['name'] ) . '</td><td style="padding:6px 8px">' . $icon . '</td><td style="padding:6px 8px">' . $url_col . '</td></tr>';
    }
    $html .= '</table>';
    $html .= '<p style="margin-top:24px;background:#e8f5e9;padding:12px;border-radius:8px">✅ <strong>Ab images permanently aapke server pe hain.</strong></p>';
    $html .= '<p><a href="/wp-admin/" style="background:#1a4d2e;color:#fff;padding:10px 20px;border-radius:8px;text-decoration:none">← Back to Admin</a></p>';
    $html .= '</div>';

    wp_die( $html );
}

function hb_update_product_img( $sku, $name, $local_url ) {
    $posts = get_posts( [
        'post_type'   => 'hb_product',
        'post_status' => 'publish',
        'numberposts' => 1,
        'meta_query'  => [ [ 'key' => '_hb_sku', 'value' => $sku ] ],
    ] );

    if ( empty( $posts ) ) {
        $posts = get_posts( [
            'post_type'   => 'hb_product',
            'post_status' => 'publish',
            'title'       => $name,
            'numberposts' => 1,
        ] );
    }

    if ( ! empty( $posts ) ) {
        update_post_meta( $posts[0]->ID, '_hb_img', esc_url_raw( $local_url ) );
    }
}
