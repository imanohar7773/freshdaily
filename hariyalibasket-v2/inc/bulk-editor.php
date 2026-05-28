<?php
/**
 * Admin Bulk Price Editor
 * URL: /wp-admin/admin.php?page=hb_bulk_price_editor
 */
if ( ! defined( 'ABSPATH' ) ) exit;

function hb_register_bulk_editor() {
    if ( ! is_user_logged_in() ) return;
    $user = wp_get_current_user();
    if ( ! in_array( 'administrator', (array) $user->roles, true ) ) return;

    add_menu_page(
        'HariyaliBasket Bulk Price Editor',
        '🌿 HB Prices',
        'manage_options',
        'hb_bulk_price_editor',
        'hb_render_bulk_editor',
        'dashicons-tag',
        26
    );
}
add_action( 'admin_menu', 'hb_register_bulk_editor' );

function hb_render_bulk_editor() {
    if ( ! current_user_can( 'manage_options' ) ) wp_die( '❌ Access Denied' );

    $message = '';
    if ( isset( $_POST['hb_bulk_save_nonce'] ) && wp_verify_nonce( $_POST['hb_bulk_save_nonce'], 'hb_bulk_save' ) ) {
        $ids = isset( $_POST['hb_ids'] ) ? (array) $_POST['hb_ids'] : [];
        $saved = 0;
        foreach ( $ids as $id ) {
            $id = intval( $id );
            if ( ! $id ) continue;
            if ( isset( $_POST[ 'hb_sp_' . $id ] ) )  update_post_meta( $id, '_hb_sp',  floatval( $_POST[ 'hb_sp_' . $id ] ) );
            if ( isset( $_POST[ 'hb_mrp_' . $id ] ) ) update_post_meta( $id, '_hb_mrp', floatval( $_POST[ 'hb_mrp_' . $id ] ) );
            if ( isset( $_POST[ 'hb_uom_' . $id ] ) ) update_post_meta( $id, '_hb_uom', sanitize_text_field( $_POST[ 'hb_uom_' . $id ] ) );
            hb_clear_cache_on_save( $id );
            $saved++;
        }
        $message = '<div class="notice notice-success" style="padding:12px;font-weight:700">✅ ' . $saved . ' products updated! Cache cleared.</div>';
    }

    $products = get_posts( [
        'post_type' => 'hb_product', 'posts_per_page' => -1, 'post_status' => 'publish', 'orderby' => 'title', 'order' => 'ASC',
    ] );

    $grouped = [];
    foreach ( $products as $p ) {
        $terms = wp_get_post_terms( $p->ID, 'hb_category' );
        $cat   = ! empty( $terms ) ? $terms[0]->name : 'General';
        $grouped[ $cat ][] = $p;
    }
    ksort( $grouped );
    ?>
    <div class="wrap" style="max-width:1000px;font-family:-apple-system,Segoe UI,sans-serif">
      <h1>🌿 HariyaliBasket — Bulk Price Editor</h1>
      <p style="color:#666">Saari prices ek jagah se update karo.</p>
      <?php echo $message; ?>

      <form method="post">
        <?php wp_nonce_field( 'hb_bulk_save', 'hb_bulk_save_nonce' ); ?>
        <?php foreach ( $grouped as $cat => $items ) : ?>
          <div style="background:#fff;border-radius:10px;margin-bottom:20px;box-shadow:0 1px 3px rgba(0,0,0,.05);overflow:hidden">
            <h3 style="background:#1a4d2e;color:#fff;padding:12px 18px;margin:0;font-size:14px">
              📂 <?php echo esc_html( $cat ); ?>
              <span style="color:#8ed46a;font-size:12px;font-weight:400">(<?php echo count( $items ); ?> items)</span>
            </h3>
            <table class="widefat" style="margin:0">
              <thead style="background:#f9fafb"><tr>
                <th style="padding:10px">Product</th><th>UOM</th><th>MRP (₹)</th><th>Sell Price (₹)</th><th>Discount</th>
              </tr></thead>
              <tbody>
                <?php foreach ( $items as $p ) :
                    $mrp = (float) get_post_meta( $p->ID, '_hb_mrp', true );
                    $sp  = (float) get_post_meta( $p->ID, '_hb_sp', true );
                    $uom = get_post_meta( $p->ID, '_hb_uom', true ) ?: 'Kg';
                    $disc = ( $mrp > 0 && $sp > 0 ) ? round( ( 1 - $sp / $mrp ) * 100 ) : 0;
                ?>
                  <tr>
                    <input type="hidden" name="hb_ids[]" value="<?php echo esc_attr( $p->ID ); ?>">
                    <td style="padding:8px 12px;font-weight:600"><?php echo esc_html( $p->post_title ); ?></td>
                    <td><input type="text" name="hb_uom_<?php echo $p->ID; ?>" value="<?php echo esc_attr( $uom ); ?>" style="width:60px"></td>
                    <td><input type="number" step="0.5" min="0" name="hb_mrp_<?php echo $p->ID; ?>" value="<?php echo esc_attr( $mrp ); ?>" style="width:80px"></td>
                    <td><input type="number" step="0.5" min="0" name="hb_sp_<?php echo $p->ID; ?>" value="<?php echo esc_attr( $sp ); ?>" style="width:80px;font-weight:700;color:#1a4d2e"></td>
                    <td><span style="background:<?php echo $disc > 0 ? '#ef4444' : '#ccc'; ?>;color:#fff;font-size:11px;font-weight:800;padding:3px 8px;border-radius:10px"><?php echo $disc > 0 ? $disc . '%' : '—'; ?></span></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endforeach; ?>

        <div style="position:sticky;bottom:0;background:#fff;padding:14px;border-top:2px solid #1a4d2e">
          <button type="submit" class="button button-primary" style="background:#1a4d2e;border-color:#1a4d2e;padding:10px 28px;font-size:14px">💾 Save All Prices</button>
        </div>
      </form>
    </div>
    <?php
}
