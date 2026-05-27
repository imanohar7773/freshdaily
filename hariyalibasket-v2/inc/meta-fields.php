<?php
/**
 * Meta fields for hb_product:
 *   _hb_uom    — unit of measurement (Kg, Pc, Pkt, Bunch)
 *   _hb_mrp    — original price
 *   _hb_sp     — selling price
 *   _hb_sku    — for WooCommerce sync match
 *   _hb_variants — "size:sp:mrp,size:sp:mrp" format
 *   _hb_stock  — manual override (in/out)
 */
if ( ! defined( 'ABSPATH' ) ) exit;

function hb_register_product_meta_box() {
    add_meta_box(
        'hb_product_details',
        '🌿 Product Details (Price, UOM, Variants)',
        'hb_product_meta_callback',
        'hb_product',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'hb_register_product_meta_box' );

function hb_product_meta_callback( $post ) {
    wp_nonce_field( 'hb_save_meta', 'hb_meta_nonce' );
    $mrp      = get_post_meta( $post->ID, '_hb_mrp', true );
    $sp       = get_post_meta( $post->ID, '_hb_sp', true );
    $uom      = get_post_meta( $post->ID, '_hb_uom', true );
    $sku      = get_post_meta( $post->ID, '_hb_sku', true );
    $variants = get_post_meta( $post->ID, '_hb_variants', true );
    $stock    = get_post_meta( $post->ID, '_hb_stock', true );
    ?>
    <style>.hb-meta td{padding:8px 4px}.hb-meta input,.hb-meta select{width:100%;padding:7px;margin-top:3px}</style>
    <table class="hb-meta" style="width:100%;border-collapse:collapse">
      <tr>
        <td><label><strong>UOM</strong> (Kg / Pc / Pkt / Bunch)<br>
          <input type="text" name="hb_uom" value="<?php echo esc_attr( $uom ); ?>" placeholder="Kg"></label></td>
        <td><label><strong>SKU</strong> (WooCommerce match)<br>
          <input type="text" name="hb_sku" value="<?php echo esc_attr( $sku ); ?>"></label></td>
      </tr>
      <tr>
        <td><label><strong>MRP (₹)</strong><br>
          <input type="number" name="hb_mrp" value="<?php echo esc_attr( $mrp ); ?>" step="0.5" min="0"></label></td>
        <td><label><strong>Selling Price (₹)</strong><br>
          <input type="number" name="hb_sp" value="<?php echo esc_attr( $sp ); ?>" step="0.5" min="0"></label></td>
      </tr>
      <tr>
        <td colspan="2"><label><strong>Variants</strong> (format: <code>250g:12:14,500g:23:28,1Kg:45:55</code>)<br>
          <input type="text" name="hb_variants" value="<?php echo esc_attr( $variants ); ?>" placeholder="Optional — leave empty for auto-generate"></label></td>
      </tr>
      <tr>
        <td colspan="2"><label><strong>Stock Override</strong><br>
          <select name="hb_stock">
            <option value="" <?php selected( $stock, '' ); ?>>Auto (use WooCommerce)</option>
            <option value="in" <?php selected( $stock, 'in' ); ?>>Force In Stock</option>
            <option value="out" <?php selected( $stock, 'out' ); ?>>Force Out of Stock (Coming Soon)</option>
          </select></label></td>
      </tr>
    </table>
    <?php
}

function hb_save_product_meta( $post_id ) {
    if ( ! isset( $_POST['hb_meta_nonce'] ) || ! wp_verify_nonce( $_POST['hb_meta_nonce'], 'hb_save_meta' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    $fields = [
        'hb_uom'      => '_hb_uom',
        'hb_sku'      => '_hb_sku',
        'hb_variants' => '_hb_variants',
        'hb_stock'    => '_hb_stock',
    ];
    foreach ( $fields as $field => $key ) {
        if ( isset( $_POST[ $field ] ) ) {
            update_post_meta( $post_id, $key, sanitize_text_field( $_POST[ $field ] ) );
        }
    }
    if ( isset( $_POST['hb_mrp'] ) ) update_post_meta( $post_id, '_hb_mrp', floatval( $_POST['hb_mrp'] ) );
    if ( isset( $_POST['hb_sp'] ) )  update_post_meta( $post_id, '_hb_sp',  floatval( $_POST['hb_sp'] ) );
}
add_action( 'save_post_hb_product', 'hb_save_product_meta' );
