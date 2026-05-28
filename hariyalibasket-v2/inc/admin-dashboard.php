<?php
/**
 * NEW ADDITION #2: Admin Order Dashboard
 * URL: /wp-admin/admin.php?page=hb_dashboard
 */
if ( ! defined( 'ABSPATH' ) ) exit;

function hb_register_dashboard() {
    if ( ! current_user_can( 'manage_options' ) ) return;

    add_menu_page(
        '🌿 HariyaliBasket Dashboard',
        '🌿 HB Dashboard',
        'manage_options',
        'hb_dashboard',
        'hb_render_dashboard',
        'dashicons-chart-area',
        3
    );
}
add_action( 'admin_menu', 'hb_register_dashboard' );

function hb_render_dashboard() {
    if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Access Denied' );

    // Counts by status
    $statuses = hb_order_statuses();
    $counts   = [];
    $total    = 0;

    foreach ( $statuses as $key => $st ) {
        $q = new WP_Query( [
            'post_type'      => 'hb_order',
            'posts_per_page' => 1,
            'meta_key'       => '_hb_order_status',
            'meta_value'     => $key,
            'fields'         => 'ids',
            'no_found_rows'  => false,
        ] );
        $counts[ $key ] = $q->found_posts;
        $total += $q->found_posts;
    }

    // Today's revenue
    $today_start = strtotime( 'today' );
    $today_orders = get_posts( [
        'post_type'      => 'hb_order',
        'posts_per_page' => -1,
        'date_query'     => [ [ 'after' => date( 'Y-m-d 00:00:00', $today_start ) ] ],
    ] );
    $today_revenue = 0;
    $today_count   = count( $today_orders );
    foreach ( $today_orders as $o ) {
        $today_revenue += (float) get_post_meta( $o->ID, '_hb_total', true );
    }

    // Recent 10 orders
    $recent = get_posts( [
        'post_type'      => 'hb_order',
        'posts_per_page' => 10,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ] );
    ?>
    <div class="wrap" style="max-width:1200px;margin:20px auto;font-family:-apple-system,Segoe UI,sans-serif">
      <h1 style="display:flex;align-items:center;gap:10px;margin-bottom:6px">
        🌿 HariyaliBasket — Order Dashboard
      </h1>
      <p style="color:#666;margin-bottom:24px">Real-time order management. Refresh to update.</p>

      <!-- Today's Stats -->
      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:14px;margin-bottom:30px">
        <div style="background:linear-gradient(135deg,#1a4d2e,#0d3320);color:#fff;border-radius:14px;padding:20px;box-shadow:0 4px 16px rgba(26,77,46,.25)">
          <div style="font-size:11px;color:#8ed46a;font-weight:700;letter-spacing:1px;margin-bottom:6px">📅 AAJ KE ORDERS</div>
          <div style="font-size:32px;font-weight:900"><?php echo $today_count; ?></div>
          <div style="font-size:13px;color:#8ed46a;margin-top:4px">orders today</div>
        </div>
        <div style="background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff;border-radius:14px;padding:20px;box-shadow:0 4px 16px rgba(245,158,11,.25)">
          <div style="font-size:11px;color:#fde68a;font-weight:700;letter-spacing:1px;margin-bottom:6px">💰 AAJ KA REVENUE</div>
          <div style="font-size:32px;font-weight:900">₹<?php echo number_format( $today_revenue, 0 ); ?></div>
          <div style="font-size:13px;color:#fde68a;margin-top:4px">total earnings</div>
        </div>
        <div style="background:linear-gradient(135deg,#3b82f6,#1e40af);color:#fff;border-radius:14px;padding:20px;box-shadow:0 4px 16px rgba(59,130,246,.25)">
          <div style="font-size:11px;color:#bfdbfe;font-weight:700;letter-spacing:1px;margin-bottom:6px">📦 ALL TIME ORDERS</div>
          <div style="font-size:32px;font-weight:900"><?php echo $total; ?></div>
          <div style="font-size:13px;color:#bfdbfe;margin-top:4px">total orders</div>
        </div>
        <div style="background:linear-gradient(135deg,#22c55e,#15803d);color:#fff;border-radius:14px;padding:20px;box-shadow:0 4px 16px rgba(34,197,94,.25)">
          <div style="font-size:11px;color:#bbf7d0;font-weight:700;letter-spacing:1px;margin-bottom:6px">🎉 DELIVERED</div>
          <div style="font-size:32px;font-weight:900"><?php echo $counts['delivered']; ?></div>
          <div style="font-size:13px;color:#bbf7d0;margin-top:4px">successful</div>
        </div>
      </div>

      <!-- Status breakdown -->
      <h2 style="font-size:18px;margin:24px 0 14px">📊 Order Status Breakdown</h2>
      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:10px;margin-bottom:30px">
        <?php foreach ( $statuses as $key => $st ) : ?>
          <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=hb_order&hb_filter_status=' . $key ) ); ?>"
             style="background:#fff;border-left:5px solid <?php echo esc_attr( $st['color'] ); ?>;border-radius:10px;padding:14px;text-decoration:none;color:#333;box-shadow:0 1px 3px rgba(0,0,0,.05);transition:transform .15s">
            <div style="font-size:12px;color:#666;font-weight:700;margin-bottom:4px"><?php echo esc_html( $st['label'] ); ?></div>
            <div style="font-size:24px;font-weight:900;color:<?php echo esc_attr( $st['color'] ); ?>"><?php echo intval( $counts[ $key ] ); ?></div>
          </a>
        <?php endforeach; ?>
      </div>

      <!-- Recent orders -->
      <h2 style="font-size:18px;margin:24px 0 14px">🕐 Recent Orders (last 10)</h2>
      <div style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.05)">
        <table class="widefat striped" style="margin:0">
          <thead style="background:#f9fafb">
            <tr>
              <th style="padding:12px">Order #</th>
              <th>Customer</th>
              <th>Phone</th>
              <th>Total</th>
              <th>Payment</th>
              <th>Status</th>
              <th>Date</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if ( empty( $recent ) ) : ?>
              <tr><td colspan="8" style="text-align:center;padding:30px;color:#999">No orders yet 🌿</td></tr>
            <?php else : foreach ( $recent as $o ) :
                $code = get_post_meta( $o->ID, '_hb_order_code', true );
                $name = get_post_meta( $o->ID, '_hb_cust_name', true );
                $phone = get_post_meta( $o->ID, '_hb_cust_phone', true );
                $total_o = get_post_meta( $o->ID, '_hb_total', true );
                $pm = get_post_meta( $o->ID, '_hb_payment', true );
                $st_key = get_post_meta( $o->ID, '_hb_order_status', true ) ?: 'new';
                $st = $statuses[ $st_key ] ?? [ 'label' => $st_key, 'color' => '#666' ];
            ?>
              <tr>
                <td><strong><a href="<?php echo esc_url( admin_url( 'post.php?post=' . $o->ID . '&action=edit' ) ); ?>"><?php echo esc_html( $code ); ?></a></strong></td>
                <td><?php echo esc_html( $name ); ?></td>
                <td><a href="https://wa.me/91<?php echo esc_attr( $phone ); ?>" target="_blank">📱 <?php echo esc_html( $phone ); ?></a></td>
                <td><strong style="color:#1a4d2e">₹<?php echo number_format( (float) $total_o, 0 ); ?></strong></td>
                <td><?php echo $pm === 'upi' ? '📲 UPI' : '💵 COD'; ?></td>
                <td><span style="display:inline-block;padding:3px 10px;border-radius:12px;background:<?php echo esc_attr( $st['color'] ); ?>;color:#fff;font-size:10px;font-weight:700"><?php echo esc_html( $st['label'] ); ?></span></td>
                <td><?php echo human_time_diff( strtotime( $o->post_date ), current_time( 'timestamp' ) ); ?> ago</td>
                <td>
                  <a class="button button-small" href="<?php echo esc_url( admin_url( 'post.php?post=' . $o->ID . '&action=edit' ) ); ?>">View</a>
                </td>
              </tr>
            <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>

      <p style="margin-top:20px"><a class="button button-primary" href="<?php echo esc_url( admin_url( 'edit.php?post_type=hb_order' ) ); ?>">📋 View All Orders</a></p>
    </div>
    <?php
}

/**
 * Filter orders by status (used in dashboard links)
 */
function hb_filter_orders_by_status( $query ) {
    global $pagenow;
    if ( ! is_admin() || $pagenow !== 'edit.php' ) return;
    if ( ! isset( $_GET['post_type'] ) || $_GET['post_type'] !== 'hb_order' ) return;
    if ( ! isset( $_GET['hb_filter_status'] ) ) return;

    $query->set( 'meta_key', '_hb_order_status' );
    $query->set( 'meta_value', sanitize_text_field( $_GET['hb_filter_status'] ) );
}
add_action( 'pre_get_posts', 'hb_filter_orders_by_status' );
