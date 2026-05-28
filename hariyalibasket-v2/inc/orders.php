<?php
/**
 * NEW ADDITION #1: Real Order Saving System
 * CPT: hb_order
 *
 * Stores: customer name, phone, address, items, payment, status
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Register hb_order CPT
 */
function hb_register_order_cpt() {
    register_post_type( 'hb_order', [
        'label'        => 'Orders',
        'labels'       => [
            'name'          => 'Orders',
            'singular_name' => 'Order',
            'all_items'     => 'All Orders',
            'edit_item'     => 'View Order',
        ],
        'public'       => false,
        'show_ui'      => true,
        'show_in_menu' => true,
        'menu_icon'    => 'dashicons-cart',
        'menu_position' => 25,
        'supports'     => [ 'title' ],
        'capability_type' => 'post',
        'capabilities' => [
            'create_posts' => 'do_not_allow', // Orders only via frontend
        ],
        'map_meta_cap' => true,
    ] );
}
add_action( 'init', 'hb_register_order_cpt' );

/**
 * Order statuses
 */
function hb_order_statuses() {
    return [
        'new'       => [ 'label' => '🆕 New',          'color' => '#3b82f6' ],
        'confirmed' => [ 'label' => '✅ Confirmed',    'color' => '#10b981' ],
        'packing'   => [ 'label' => '📦 Packing',      'color' => '#f59e0b' ],
        'out'       => [ 'label' => '🚚 Out for Delivery', 'color' => '#8b5cf6' ],
        'delivered' => [ 'label' => '🎉 Delivered',    'color' => '#22c55e' ],
        'cancelled' => [ 'label' => '❌ Cancelled',    'color' => '#ef4444' ],
    ];
}

/**
 * Create new order from frontend
 */
function hb_create_order( $data ) {
    $code = hb_generate_order_id();

    $items_str = '';
    if ( ! empty( $data['items'] ) && is_array( $data['items'] ) ) {
        foreach ( $data['items'] as $it ) {
            $items_str .= sprintf(
                "%s × %d = ₹%s\n",
                $it['name'] ?? '?',
                intval( $it['qty'] ?? 0 ),
                number_format( floatval( $it['amount'] ?? 0 ), 0 )
            );
        }
    }

    $title = sprintf( '%s — %s (₹%s)',
        $code,
        $data['name'],
        number_format( floatval( $data['total'] ), 0 )
    );

    $post_id = wp_insert_post( [
        'post_title'  => $title,
        'post_type'   => 'hb_order',
        'post_status' => 'publish',
        'post_content' => $items_str,
    ] );

    if ( is_wp_error( $post_id ) || ! $post_id ) return false;

    update_post_meta( $post_id, '_hb_order_code',    $code );
    update_post_meta( $post_id, '_hb_order_status',  'new' );
    update_post_meta( $post_id, '_hb_cust_name',     $data['name'] );
    update_post_meta( $post_id, '_hb_cust_phone',    $data['phone'] );
    update_post_meta( $post_id, '_hb_cust_society',  $data['society'] );
    update_post_meta( $post_id, '_hb_cust_block',    $data['block'] );
    update_post_meta( $post_id, '_hb_cust_flat',     $data['flat'] );
    update_post_meta( $post_id, '_hb_payment',       $data['payment'] );
    update_post_meta( $post_id, '_hb_txn',           $data['txn'] );
    update_post_meta( $post_id, '_hb_payment_status', $data['payment'] === 'cod' ? 'pending' : 'paid' );
    update_post_meta( $post_id, '_hb_total',         floatval( $data['total'] ) );
    update_post_meta( $post_id, '_hb_items',         wp_json_encode( $data['items'] ) );
    update_post_meta( $post_id, '_hb_ip',            hb_client_ip() );
    update_post_meta( $post_id, '_hb_created',       current_time( 'mysql' ) );

    do_action( 'hb_order_created', $post_id, $data );

    return [ 'code' => $code, 'post_id' => $post_id ];
}

/**
 * Add columns to Orders list view
 */
function hb_order_columns( $cols ) {
    return [
        'cb'           => $cols['cb'] ?? '',
        'hb_code'      => 'Order #',
        'hb_customer'  => 'Customer',
        'hb_phone'     => 'Phone',
        'hb_total'     => 'Total',
        'hb_payment'   => 'Payment',
        'hb_status'    => 'Status',
        'date'         => 'Date',
    ];
}
add_filter( 'manage_hb_order_posts_columns', 'hb_order_columns' );

function hb_order_column_value( $col, $post_id ) {
    $statuses = hb_order_statuses();
    switch ( $col ) {
        case 'hb_code':
            $code = get_post_meta( $post_id, '_hb_order_code', true );
            echo '<strong><a href="' . esc_url( admin_url( 'post.php?post=' . $post_id . '&action=edit' ) ) . '">' . esc_html( $code ) . '</a></strong>';
            break;
        case 'hb_customer':
            echo esc_html( get_post_meta( $post_id, '_hb_cust_name', true ) );
            $soc = get_post_meta( $post_id, '_hb_cust_society', true );
            if ( $soc ) echo '<br><small style="color:#666">📍 ' . esc_html( $soc ) . '</small>';
            break;
        case 'hb_phone':
            $ph = get_post_meta( $post_id, '_hb_cust_phone', true );
            echo '<a href="https://wa.me/91' . esc_attr( $ph ) . '" target="_blank">📱 ' . esc_html( $ph ) . '</a>';
            break;
        case 'hb_total':
            echo '<strong style="color:#1a4d2e">₹' . number_format( (float) get_post_meta( $post_id, '_hb_total', true ), 0 ) . '</strong>';
            break;
        case 'hb_payment':
            $pm = get_post_meta( $post_id, '_hb_payment', true );
            $ps = get_post_meta( $post_id, '_hb_payment_status', true );
            echo '<span style="font-weight:700">' . ( $pm === 'upi' ? '📲 UPI' : '💵 COD' ) . '</span>';
            echo '<br><small style="color:' . ( $ps === 'paid' ? '#22c55e' : '#f59e0b' ) . '">' . esc_html( ucfirst( $ps ) ) . '</small>';
            break;
        case 'hb_status':
            $s = get_post_meta( $post_id, '_hb_order_status', true );
            $st = $statuses[ $s ] ?? [ 'label' => $s, 'color' => '#666' ];
            echo '<span style="display:inline-block;padding:3px 10px;border-radius:12px;background:' . esc_attr( $st['color'] ) . ';color:#fff;font-size:11px;font-weight:700">' . esc_html( $st['label'] ) . '</span>';
            break;
    }
}
add_action( 'manage_hb_order_posts_custom_column', 'hb_order_column_value', 10, 2 );

/**
 * Add Order details meta box
 */
function hb_order_meta_boxes() {
    add_meta_box( 'hb_order_details', '🌿 Order Details', 'hb_order_details_cb', 'hb_order', 'normal', 'high' );
    add_meta_box( 'hb_order_actions', '⚡ Status & Actions', 'hb_order_actions_cb', 'hb_order', 'side', 'high' );
}
add_action( 'add_meta_boxes', 'hb_order_meta_boxes' );

function hb_order_details_cb( $post ) {
    $items = json_decode( get_post_meta( $post->ID, '_hb_items', true ), true );
    ?>
    <table style="width:100%;border-collapse:collapse" class="widefat">
      <tr><th style="text-align:left;padding:8px">Customer</th><td><?php echo esc_html( get_post_meta( $post->ID, '_hb_cust_name', true ) ); ?></td></tr>
      <tr><th style="text-align:left;padding:8px">Phone</th><td><a href="https://wa.me/91<?php echo esc_attr( get_post_meta( $post->ID, '_hb_cust_phone', true ) ); ?>" target="_blank">📱 <?php echo esc_html( get_post_meta( $post->ID, '_hb_cust_phone', true ) ); ?></a></td></tr>
      <tr><th style="text-align:left;padding:8px">Society</th><td><?php echo esc_html( get_post_meta( $post->ID, '_hb_cust_society', true ) ); ?></td></tr>
      <tr><th style="text-align:left;padding:8px">Block / Flat</th><td>
        <?php
        $block = get_post_meta( $post->ID, '_hb_cust_block', true );
        $flat  = get_post_meta( $post->ID, '_hb_cust_flat', true );
        echo esc_html( trim( $block . ' / ' . $flat, ' /' ) );
        ?>
      </td></tr>
      <tr><th style="text-align:left;padding:8px">Payment</th><td>
        <?php $pm = get_post_meta( $post->ID, '_hb_payment', true ); ?>
        <?php echo $pm === 'upi' ? '📲 UPI / Online' : '💵 Cash on Delivery'; ?>
        <?php $txn = get_post_meta( $post->ID, '_hb_txn', true ); if ( $txn ) echo ' · TXN: <code>' . esc_html( $txn ) . '</code>'; ?>
      </td></tr>
      <tr><th style="text-align:left;padding:8px">Total</th><td><strong style="font-size:18px;color:#1a4d2e">₹<?php echo number_format( (float) get_post_meta( $post->ID, '_hb_total', true ), 0 ); ?></strong></td></tr>
    </table>
    <h3 style="margin-top:20px">🛒 Items</h3>
    <table class="widefat striped">
      <thead><tr><th>Product</th><th>Qty</th><th>Price</th><th>Total</th></tr></thead>
      <tbody>
        <?php if ( $items && is_array( $items ) ) foreach ( $items as $it ) : ?>
        <tr>
          <td><?php echo esc_html( $it['name'] ?? '' ); ?></td>
          <td><?php echo intval( $it['qty'] ?? 0 ); ?></td>
          <td>₹<?php echo number_format( floatval( $it['sp'] ?? 0 ), 0 ); ?></td>
          <td><strong>₹<?php echo number_format( floatval( $it['amount'] ?? 0 ), 0 ); ?></strong></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php
}

function hb_order_actions_cb( $post ) {
    wp_nonce_field( 'hb_save_order', 'hb_order_nonce' );
    $current  = get_post_meta( $post->ID, '_hb_order_status', true ) ?: 'new';
    $pay_st   = get_post_meta( $post->ID, '_hb_payment_status', true ) ?: 'pending';
    $statuses = hb_order_statuses();
    $phone    = get_post_meta( $post->ID, '_hb_cust_phone', true );
    ?>
    <p><strong>Order Status:</strong></p>
    <select name="hb_status" style="width:100%;padding:8px">
      <?php foreach ( $statuses as $key => $st ) : ?>
        <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $current, $key ); ?>><?php echo esc_html( $st['label'] ); ?></option>
      <?php endforeach; ?>
    </select>

    <p style="margin-top:14px"><strong>Payment Status:</strong></p>
    <select name="hb_payment_status" style="width:100%;padding:8px">
      <option value="pending" <?php selected( $pay_st, 'pending' ); ?>>⏳ Pending</option>
      <option value="paid"    <?php selected( $pay_st, 'paid' ); ?>>✅ Paid</option>
      <option value="failed"  <?php selected( $pay_st, 'failed' ); ?>>❌ Failed</option>
    </select>

    <hr style="margin:14px 0">
    <p><a class="button button-primary" href="https://wa.me/91<?php echo esc_attr( $phone ); ?>" target="_blank" style="width:100%;text-align:center">💬 Message on WhatsApp</a></p>
    <p><a class="button" href="tel:<?php echo esc_attr( $phone ); ?>" style="width:100%;text-align:center">📞 Call Customer</a></p>
    <?php
}

function hb_order_save_meta( $post_id ) {
    if ( ! isset( $_POST['hb_order_nonce'] ) || ! wp_verify_nonce( $_POST['hb_order_nonce'], 'hb_save_order' ) ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    if ( isset( $_POST['hb_status'] ) ) {
        update_post_meta( $post_id, '_hb_order_status', sanitize_text_field( $_POST['hb_status'] ) );
    }
    if ( isset( $_POST['hb_payment_status'] ) ) {
        update_post_meta( $post_id, '_hb_payment_status', sanitize_text_field( $_POST['hb_payment_status'] ) );
    }
}
add_action( 'save_post_hb_order', 'hb_order_save_meta' );
