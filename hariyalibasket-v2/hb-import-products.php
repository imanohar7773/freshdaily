<?php
/**
 * HARIYALIBASKET — Product Importer (One-Time + Auto-Lock)
 *
 * SECURITY (BUG FIX #7):
 *   - Requires admin login (manage_options capability)
 *   - Auto-disables itself by writing a lock file after first successful run
 *   - To re-run, manually delete .hb-import-lock file
 *   - To permanently disable, just delete this file
 *
 * USAGE:
 *   1. Upload to: public_html/wp-content/themes/hariyalibasket-v2/
 *   2. Login to WordPress admin first
 *   3. Visit: https://YOURSITE.com/wp-content/themes/hariyalibasket-v2/hb-import-products.php?run=1&clean=1
 *   4. After run, file auto-locks. Delete it for safety.
 */

// Bootstrap WordPress
if ( ! defined( 'ABSPATH' ) ) {
    $wp_load = dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/wp-load.php';
    if ( file_exists( $wp_load ) ) require_once $wp_load;
    else die( 'Could not locate wp-load.php' );
}

// Auth check
if ( ! current_user_can( 'manage_options' ) ) {
    wp_die( '❌ Sirf Administrator yeh script chala sakta hai. Pehle WordPress mein login karo.' );
}

// LOCK CHECK — prevent re-run after success
$lock_file = __DIR__ . '/.hb-import-lock';
$is_locked = file_exists( $lock_file );

$run   = isset( $_GET['run'] )   ? intval( $_GET['run'] )   : 0;
$clean = isset( $_GET['clean'] ) ? intval( $_GET['clean'] ) : 0;
$force = isset( $_GET['force'] ) ? intval( $_GET['force'] ) : 0;

if ( $is_locked && $run && ! $force ) {
    $lock_time = file_get_contents( $lock_file );
    wp_die(
        '<h2 style="font-family:sans-serif;color:#d97706">🔒 Import Already Done</h2>' .
        '<p style="font-family:sans-serif">Import was completed at: <code>' . esc_html( $lock_time ) . '</code></p>' .
        '<p>To re-run, delete <code>.hb-import-lock</code> in theme folder, or use <code>?force=1</code> in URL.</p>' .
        '<p><a href="/wp-admin/edit.php?post_type=hb_product">View Products →</a></p>'
    );
}

// Product List (71 items)
$PRODUCTS = [
    // FRUITS
    [ 'name' => 'Apple Green Imp.',     'uom' => 'Kg',  'mrp' => 280, 'sp' => 220, 'cat' => 'Fruits' ],
    [ 'name' => 'Apple Himachal',       'uom' => 'Kg',  'mrp' => 200, 'sp' => 160, 'cat' => 'Fruits' ],
    [ 'name' => 'Apple Imp.',           'uom' => 'Kg',  'mrp' => 280, 'sp' => 220, 'cat' => 'Fruits' ],
    [ 'name' => 'Avocado Local',        'uom' => 'Pc',  'mrp' => 150, 'sp' => 120, 'cat' => 'Fruits' ],
    [ 'name' => 'Banana',               'uom' => 'Kg',  'mrp' => 50,  'sp' => 40,  'cat' => 'Fruits' ],
    [ 'name' => 'Chikoo',               'uom' => 'Kg',  'mrp' => 80,  'sp' => 60,  'cat' => 'Fruits' ],
    [ 'name' => 'Coconut Dhab',         'uom' => 'Pc',  'mrp' => 100, 'sp' => 70,  'cat' => 'Fruits' ],
    [ 'name' => 'Coconut Fresh',        'uom' => 'Pc',  'mrp' => 60,  'sp' => 45,  'cat' => 'Fruits' ],
    [ 'name' => 'Dragon Fruit',         'uom' => 'Pc',  'mrp' => 110, 'sp' => 90,  'cat' => 'Fruits' ],
    [ 'name' => 'Guava Imp.',           'uom' => 'Kg',  'mrp' => 110, 'sp' => 80,  'cat' => 'Fruits' ],
    [ 'name' => 'Guava (Desi)',         'uom' => 'Kg',  'mrp' => 90,  'sp' => 60,  'cat' => 'Fruits' ],
    [ 'name' => 'Jamun',                'uom' => 'Kg',  'mrp' => 200, 'sp' => 140, 'cat' => 'Fruits' ],
    [ 'name' => 'Kiwi Fresh',           'uom' => 'Pc',  'mrp' => 45,  'sp' => 40,  'cat' => 'Fruits' ],
    [ 'name' => 'Mango Alphonso',       'uom' => 'Kg',  'mrp' => 220, 'sp' => 160, 'cat' => 'Fruits' ],
    [ 'name' => 'Mango Langra',         'uom' => 'Kg',  'mrp' => 130, 'sp' => 90,  'cat' => 'Fruits' ],
    [ 'name' => 'Mango Safeda',         'uom' => 'Kg',  'mrp' => 100, 'sp' => 70,  'cat' => 'Fruits' ],
    [ 'name' => 'Papaya Fresh',         'uom' => 'Kg',  'mrp' => 60,  'sp' => 45,  'cat' => 'Fruits' ],
    [ 'name' => 'Raw Papaya',           'uom' => 'Kg',  'mrp' => 60,  'sp' => 45,  'cat' => 'Fruits' ],
    [ 'name' => 'Pineapple Fresh',      'uom' => 'Pc',  'mrp' => 90,  'sp' => 70,  'cat' => 'Fruits' ],
    [ 'name' => 'Pomegranate (Anar)',   'uom' => 'Kg',  'mrp' => 260, 'sp' => 200, 'cat' => 'Fruits' ],
    [ 'name' => 'Sweet Lime (Mosambi)', 'uom' => 'Kg',  'mrp' => 80,  'sp' => 60,  'cat' => 'Fruits' ],
    [ 'name' => 'Watermelon',           'uom' => 'Pc',  'mrp' => 80,  'sp' => 50,  'cat' => 'Fruits' ],

    // ROOT VEGETABLES
    [ 'name' => 'Beetroot',             'uom' => 'Kg',  'mrp' => 50,  'sp' => 30,  'cat' => 'Root Vegetables' ],
    [ 'name' => 'Carrot',               'uom' => 'Kg',  'mrp' => 50,  'sp' => 30,  'cat' => 'Root Vegetables' ],
    [ 'name' => 'Garlic',               'uom' => 'Kg',  'mrp' => 150, 'sp' => 120, 'cat' => 'Root Vegetables' ],
    [ 'name' => 'Garlic Peeled',        'uom' => 'Kg',  'mrp' => 180, 'sp' => 140, 'cat' => 'Root Vegetables' ],
    [ 'name' => 'Ginger',               'uom' => 'Kg',  'mrp' => 200, 'sp' => 150, 'cat' => 'Root Vegetables' ],
    [ 'name' => 'Onion',                'uom' => 'Kg',  'mrp' => 40,  'sp' => 25,  'cat' => 'Root Vegetables' ],
    [ 'name' => 'Onion Small',          'uom' => 'Kg',  'mrp' => 35,  'sp' => 20,  'cat' => 'Root Vegetables' ],
    [ 'name' => 'Potato',               'uom' => 'Kg',  'mrp' => 30,  'sp' => 14,  'cat' => 'Root Vegetables' ],
    [ 'name' => 'Potato Small',         'uom' => 'Kg',  'mrp' => 20,  'sp' => 10,  'cat' => 'Root Vegetables' ],
    [ 'name' => 'Red Potato',           'uom' => 'Kg',  'mrp' => 40,  'sp' => 25,  'cat' => 'Root Vegetables' ],
    [ 'name' => 'Raw Mango',            'uom' => 'Kg',  'mrp' => 80,  'sp' => 50,  'cat' => 'Root Vegetables' ],

    // GREEN VEGETABLES
    [ 'name' => 'Arbi (Colocasia)',     'uom' => 'Kg',  'mrp' => 50,  'sp' => 40,  'cat' => 'Green Vegetables' ],
    [ 'name' => 'French Beans',         'uom' => 'Kg',  'mrp' => 120, 'sp' => 80,  'cat' => 'Green Vegetables' ],
    [ 'name' => 'Corn (Bhutta)',        'uom' => 'Kg',  'mrp' => 60,  'sp' => 40,  'cat' => 'Green Vegetables' ],
    [ 'name' => 'Brinjal (Baingan)',    'uom' => 'Kg',  'mrp' => 40,  'sp' => 25,  'cat' => 'Green Vegetables' ],
    [ 'name' => 'Cabbage Green',        'uom' => 'Kg',  'mrp' => 40,  'sp' => 20,  'cat' => 'Green Vegetables' ],
    [ 'name' => 'Capsicum Green',       'uom' => 'Kg',  'mrp' => 60,  'sp' => 40,  'cat' => 'Green Vegetables' ],
    [ 'name' => 'Cauliflower',          'uom' => 'Kg',  'mrp' => 70,  'sp' => 50,  'cat' => 'Green Vegetables' ],
    [ 'name' => 'Green Chilli',         'uom' => 'Kg',  'mrp' => 50,  'sp' => 30,  'cat' => 'Green Vegetables' ],
    [ 'name' => 'Green Chilli (Hot)',   'uom' => 'Kg',  'mrp' => 100, 'sp' => 60,  'cat' => 'Green Vegetables' ],
    [ 'name' => 'Chola Fali',           'uom' => 'Kg',  'mrp' => 60,  'sp' => 50,  'cat' => 'Green Vegetables' ],
    [ 'name' => 'Cucumber Chinese',     'uom' => 'Kg',  'mrp' => 70,  'sp' => 40,  'cat' => 'Green Vegetables' ],
    [ 'name' => 'Curry Leaves',         'uom' => 'Kg',  'mrp' => 100, 'sp' => 40,  'cat' => 'Green Vegetables' ],
    [ 'name' => 'Drumsticks (Sahjan)',  'uom' => 'Kg',  'mrp' => 140, 'sp' => 80,  'cat' => 'Green Vegetables' ],
    [ 'name' => 'Guar Phali',           'uom' => 'Kg',  'mrp' => 60,  'sp' => 40,  'cat' => 'Green Vegetables' ],
    [ 'name' => 'Jackfruit (Kathal)',   'uom' => 'Kg',  'mrp' => 50,  'sp' => 40,  'cat' => 'Green Vegetables' ],
    [ 'name' => 'Kachri',               'uom' => 'Kg',  'mrp' => 40,  'sp' => 30,  'cat' => 'Green Vegetables' ],
    [ 'name' => 'Kakri',                'uom' => 'Kg',  'mrp' => 50,  'sp' => 30,  'cat' => 'Green Vegetables' ],
    [ 'name' => 'Bitter Gourd (Karela)','uom' => 'Kg',  'mrp' => 60,  'sp' => 40,  'cat' => 'Green Vegetables' ],
    [ 'name' => 'Lady Finger (Bhindi)', 'uom' => 'Kg',  'mrp' => 50,  'sp' => 40,  'cat' => 'Green Vegetables' ],
    [ 'name' => 'Bottle Gourd (Lauki)', 'uom' => 'Kg',  'mrp' => 40,  'sp' => 30,  'cat' => 'Green Vegetables' ],
    [ 'name' => 'Parwal',               'uom' => 'Kg',  'mrp' => 80,  'sp' => 60,  'cat' => 'Green Vegetables' ],
    [ 'name' => 'Red Pumpkin',          'uom' => 'Kg',  'mrp' => 30,  'sp' => 20,  'cat' => 'Green Vegetables' ],
    [ 'name' => 'Spinach (Palak)',      'uom' => 'Kg',  'mrp' => 60,  'sp' => 40,  'cat' => 'Green Vegetables' ],
    [ 'name' => 'Spring Onion',         'uom' => 'Kg',  'mrp' => 110, 'sp' => 60,  'cat' => 'Green Vegetables' ],
    [ 'name' => 'Tinda',                'uom' => 'Kg',  'mrp' => 50,  'sp' => 40,  'cat' => 'Green Vegetables' ],
    [ 'name' => 'Tomato',               'uom' => 'Kg',  'mrp' => 40,  'sp' => 25,  'cat' => 'Green Vegetables' ],
    [ 'name' => 'Baby Corn Fresh',      'uom' => 'Kg',  'mrp' => 160, 'sp' => 140, 'cat' => 'Green Vegetables' ],
    [ 'name' => 'Broccoli',             'uom' => 'Kg',  'mrp' => 250, 'sp' => 180, 'cat' => 'Green Vegetables' ],
    [ 'name' => 'Lettuce Green',        'uom' => 'Kg',  'mrp' => 300, 'sp' => 220, 'cat' => 'Green Vegetables' ],
    [ 'name' => 'Red Capsicum',         'uom' => 'Kg',  'mrp' => 150, 'sp' => 100, 'cat' => 'Green Vegetables' ],
    [ 'name' => 'Yellow Capsicum',      'uom' => 'Kg',  'mrp' => 150, 'sp' => 100, 'cat' => 'Green Vegetables' ],
    [ 'name' => 'Zucchini Green',       'uom' => 'Kg',  'mrp' => 200, 'sp' => 120, 'cat' => 'Green Vegetables' ],

    // EXOTIC
    [ 'name' => 'Mushroom Fresh 200g',  'uom' => 'Pkt', 'mrp' => 50,  'sp' => 40,  'cat' => 'Exotic & Packed' ],

    // HERBS & LEAFY
    [ 'name' => 'Coriander',            'uom' => 'Kg',  'mrp' => 80,  'sp' => 60,  'cat' => 'Herbs & Leafy' ],
    [ 'name' => 'Coriander Fresh',      'uom' => 'Kg',  'mrp' => 80,  'sp' => 60,  'cat' => 'Herbs & Leafy' ],
    [ 'name' => 'Mint (Pudina)',        'uom' => 'Kg',  'mrp' => 100, 'sp' => 80,  'cat' => 'Herbs & Leafy' ],
    [ 'name' => 'Lemon (Nimbu)',        'uom' => 'Kg',  'mrp' => 160, 'sp' => 120, 'cat' => 'Herbs & Leafy' ],
    [ 'name' => 'Banana Leaf',          'uom' => 'Pc',  'mrp' => 30,  'sp' => 25,  'cat' => 'Herbs & Leafy' ],
];

$total = count( $PRODUCTS );
$deleted = 0;
$inserted = 0;

if ( $run ) {
    if ( $clean ) {
        $existing = get_posts( [ 'post_type' => 'hb_product', 'posts_per_page' => -1, 'post_status' => 'any', 'fields' => 'ids' ] );
        foreach ( $existing as $id ) { wp_delete_post( $id, true ); $deleted++; }
    }

    foreach ( $PRODUCTS as $p ) {
        $term = term_exists( $p['cat'], 'hb_category' );
        if ( ! $term ) $term = wp_insert_term( $p['cat'], 'hb_category' );
        $term_id = is_array( $term ) ? $term['term_id'] : $term;

        $post_id = wp_insert_post( [
            'post_title'  => $p['name'],
            'post_type'   => 'hb_product',
            'post_status' => 'publish',
        ] );

        if ( $post_id && ! is_wp_error( $post_id ) ) {
            update_post_meta( $post_id, '_hb_uom', $p['uom'] );
            update_post_meta( $post_id, '_hb_mrp', $p['mrp'] );
            update_post_meta( $post_id, '_hb_sp',  $p['sp'] );
            wp_set_post_terms( $post_id, [ $term_id ], 'hb_category' );
            $inserted++;
        }
    }

    // AUTO-LOCK after successful run
    @file_put_contents( $lock_file, current_time( 'mysql' ) . ' (by user ID ' . get_current_user_id() . ')' );
}
?>
<!DOCTYPE html>
<html><head><meta charset="UTF-8"><title>HB Importer</title>
<style>
body{font-family:-apple-system,Segoe UI,sans-serif;background:#f0fff4;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;padding:20px}
.box{background:#fff;border-radius:18px;padding:40px;text-align:center;box-shadow:0 8px 32px rgba(0,0,0,.1);max-width:520px;width:100%}
h2{color:#0d3320;margin-bottom:6px}.tag{color:#666;margin-bottom:24px}
.stat{background:#f0fff4;border:1px solid #c3efd4;border-radius:10px;padding:12px;margin:8px 0;font-size:14px;font-weight:700}
a.btn{display:inline-block;background:#0d3320;color:#fff;padding:12px 24px;border-radius:24px;text-decoration:none;font-weight:800;margin:6px 4px}
a.btn-amber{background:#f59e0b;color:#0d3320}
a.btn-red{background:#ef4444;color:#fff}
.warn{background:#fff3cd;border:2px solid #f59e0b;border-radius:10px;padding:14px;margin-top:20px;color:#92400e;font-size:13px;font-weight:700}
.preview{text-align:left;background:#f9fafb;border-radius:10px;padding:14px;font-size:11px;max-height:200px;overflow:auto;margin:14px 0}
.lock{background:#fee2e2;border:2px solid #ef4444;color:#7f1d1d}
</style></head><body>
<div class="box">
<?php if ( $run ) : ?>
  <div style="font-size:60px">✅</div>
  <h2>Import Complete!</h2>
  <p class="tag">Sab kuch ready hai — file auto-lock ho gayi</p>
  <?php if ( $clean ) : ?>
    <div class="stat">🗑️ <strong><?php echo $deleted; ?></strong> purane products delete hue</div>
  <?php endif; ?>
  <div class="stat">📦 <strong><?php echo $inserted; ?></strong> fresh products import hue</div>
  <div class="stat lock">🔒 Importer auto-locked. Re-run karne ke liye <code>.hb-import-lock</code> file delete karo.</div>
  <p style="margin-top:18px">
    <a class="btn" href="/wp-admin/edit.php?post_type=hb_product">Products dekhen →</a>
    <a class="btn btn-amber" href="/">Website dekhen →</a>
  </p>
  <div class="warn">⚠️ <strong>BEST PRACTICE:</strong> Is poori file ko ab DELETE kar do!<br>
  cPanel → File Manager → <code>wp-content/themes/hariyalibasket-v2/hb-import-products.php</code> → Delete</div>
<?php else : ?>
  <div style="font-size:60px">📦</div>
  <h2>HariyaliBasket Product Importer</h2>
  <?php if ( $is_locked ) : ?>
    <div class="stat lock">🔒 Importer pehle se locked hai. Force re-run ke liye <code>?force=1</code> URL mein add karo.</div>
  <?php endif; ?>
  <p class="tag"><strong><?php echo $total; ?> products</strong> import karne ke liye ready hain</p>
  <div class="preview">
    <?php
    $by_cat = [];
    foreach ( $PRODUCTS as $p ) $by_cat[ $p['cat'] ] = ( $by_cat[ $p['cat'] ] ?? 0 ) + 1;
    foreach ( $by_cat as $cat => $count ) echo '<div>📂 <b>' . esc_html( $cat ) . ':</b> ' . $count . ' items</div>';
    ?>
  </div>
  <p>
    <a class="btn btn-red" href="?run=1&clean=1<?php echo $is_locked ? '&force=1' : ''; ?>">🔥 Fresh Reset (DELETE old + import new)</a>
  </p>
  <p>
    <a class="btn btn-amber" href="?run=1&clean=0<?php echo $is_locked ? '&force=1' : ''; ?>">➕ Add Only (keep existing)</a>
  </p>
  <div class="warn">⚠️ <strong>Fresh Reset</strong> SAARE existing products delete kar dega!<br>
  Run hone ke baad file auto-lock ho jayegi.</div>
<?php endif; ?>
</div>
</body></html>
