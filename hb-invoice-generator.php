<?php
/**
 * Plugin Name: HariyaliBasket Invoice Generator
 * Description: WordPress admin se invoice banao — fixed HariyaliBasket format mein. Print, PDF, WhatsApp share — sab kuch ek click pe.
 * Version:     1.0
 * Author:      HariyaliBasket
 * Text Domain: hb-invoice
 *
 * USAGE:
 * 1. Is file ko /wp-content/plugins/ folder mein upload karo
 * 2. WordPress Admin → Plugins → "HariyaliBasket Invoice Generator" → Activate karo
 * 3. Left sidebar mein "🧾 HB Invoice" menu aa jaayega — wahaan se bill banao
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Direct access band

/* ============================================================
   1. ADMIN MENU
   ============================================================ */
add_action( 'admin_menu', 'hb_invoice_add_menu' );

function hb_invoice_add_menu() {
    add_menu_page(
        'HB Invoice Generator',          // Page title
        '🧾 HB Invoice',                 // Menu title
        'manage_options',                // Capability
        'hb-invoice',                    // Slug
        'hb_invoice_render_page',        // Callback
        'dashicons-media-spreadsheet',   // Icon
        25                               // Position
    );

    add_submenu_page(
        'hb-invoice',
        'Saved Invoices',
        '📋 Saved Bills',
        'manage_options',
        'hb-invoice-list',
        'hb_invoice_list_page'
    );
}

/* ============================================================
   2. CUSTOM POST TYPE FOR SAVED BILLS
   ============================================================ */
add_action( 'init', 'hb_invoice_register_cpt' );

function hb_invoice_register_cpt() {
    register_post_type( 'hb_invoice', [
        'label'           => 'HB Invoices',
        'public'          => false,
        'show_ui'         => false,
        'show_in_menu'    => false,
        'supports'        => [ 'title', 'custom-fields' ],
        'capability_type' => 'post',
    ]);
}

/* ============================================================
   3. SAVE INVOICE (AJAX)
   ============================================================ */
add_action( 'wp_ajax_hb_save_invoice', 'hb_save_invoice_ajax' );

function hb_save_invoice_ajax() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( 'Permission denied' );
    }
    check_ajax_referer( 'hb_invoice_nonce', 'nonce' );

    $billno   = sanitize_text_field( $_POST['billno'] ?? '' );
    $name     = sanitize_text_field( $_POST['name'] ?? '' );
    $phone    = sanitize_text_field( $_POST['phone'] ?? '' );
    $society  = sanitize_text_field( $_POST['society'] ?? '' );
    $block    = sanitize_text_field( $_POST['block'] ?? '' );
    $flat     = sanitize_text_field( $_POST['flat'] ?? '' );
    $date     = sanitize_text_field( $_POST['date'] ?? '' );
    $pay      = sanitize_text_field( $_POST['pay'] ?? '' );
    $delivery = floatval( $_POST['delivery'] ?? 0 );
    $discount = floatval( $_POST['discount'] ?? 0 );
    $notes    = sanitize_text_field( $_POST['notes'] ?? '' );
    $items    = isset( $_POST['items'] ) ? json_decode( stripslashes( $_POST['items'] ), true ) : [];
    $total    = floatval( $_POST['total'] ?? 0 );

    $post_id = wp_insert_post([
        'post_type'   => 'hb_invoice',
        'post_status' => 'publish',
        'post_title'  => $billno . ' — ' . $name,
    ]);

    if ( is_wp_error( $post_id ) || ! $post_id ) {
        wp_send_json_error( 'Save failed' );
    }

    update_post_meta( $post_id, '_hb_billno',   $billno );
    update_post_meta( $post_id, '_hb_name',     $name );
    update_post_meta( $post_id, '_hb_phone',    $phone );
    update_post_meta( $post_id, '_hb_society',  $society );
    update_post_meta( $post_id, '_hb_block',    $block );
    update_post_meta( $post_id, '_hb_flat',     $flat );
    update_post_meta( $post_id, '_hb_date',     $date );
    update_post_meta( $post_id, '_hb_pay',      $pay );
    update_post_meta( $post_id, '_hb_delivery', $delivery );
    update_post_meta( $post_id, '_hb_discount', $discount );
    update_post_meta( $post_id, '_hb_notes',    $notes );
    update_post_meta( $post_id, '_hb_items',    wp_json_encode( $items ) );
    update_post_meta( $post_id, '_hb_total',    $total );

    wp_send_json_success([ 'id' => $post_id, 'billno' => $billno ]);
}

/* ============================================================
   4. GET WOOCOMMERCE PRODUCTS (FOR DROPDOWN)
   ============================================================ */
add_action( 'wp_ajax_hb_get_products', 'hb_get_products_ajax' );

function hb_get_products_ajax() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error();
    }

    $products = [];

    // hb_product (custom post type)
    $hb_posts = get_posts([
        'post_type'      => 'hb_product',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
    ]);
    foreach ( $hb_posts as $p ) {
        $sp  = (float) get_post_meta( $p->ID, '_hb_sp', true );
        $uom = get_post_meta( $p->ID, '_hb_uom', true ) ?: 'Kg';
        if ( $sp > 0 ) {
            $products[] = [
                'name' => $p->post_title,
                'rate' => $sp,
                'uom'  => $uom,
            ];
        }
    }

    // WooCommerce products (fallback)
    if ( function_exists( 'wc_get_products' ) ) {
        $woo = wc_get_products([ 'limit' => -1, 'status' => 'publish' ]);
        foreach ( $woo as $w ) {
            $rate = (float) ( $w->get_sale_price() ?: $w->get_regular_price() );
            if ( $rate > 0 ) {
                $products[] = [
                    'name' => $w->get_name(),
                    'rate' => $rate,
                    'uom'  => 'pc',
                ];
            }
        }
    }

    wp_send_json_success( $products );
}

/* ============================================================
   5. NEXT BILL NUMBER
   ============================================================ */
function hb_get_next_billno() {
    $last = get_option( 'hb_last_billno', 0 );
    return 'HB-' . str_pad( $last + 1, 4, '0', STR_PAD_LEFT );
}

add_action( 'wp_ajax_hb_increment_billno', 'hb_increment_billno_ajax' );
function hb_increment_billno_ajax() {
    if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error();
    $last = (int) get_option( 'hb_last_billno', 0 );
    update_option( 'hb_last_billno', $last + 1 );
    wp_send_json_success( hb_get_next_billno() );
}

/* ============================================================
   6. MAIN INVOICE PAGE
   ============================================================ */
function hb_invoice_render_page() {
    $next_bill = hb_get_next_billno();
    $today     = date( 'Y-m-d' );
    $nonce     = wp_create_nonce( 'hb_invoice_nonce' );
    ?>
    <div class="wrap">
        <h1 style="display:flex;align-items:center;gap:10px;color:#1a4d2e">
            🌿 HariyaliBasket — Invoice Generator
        </h1>
        <p style="color:#666">Sirf details bharo, bill auto-generate ho jaayega same format mein.</p>

        <div id="hb-app" style="display:flex;gap:20px;flex-wrap:wrap;margin-top:16px">

            <!-- ====== FORM ====== -->
            <div id="hb-form-card" style="background:#0f2d18;color:#fff;border-radius:14px;padding:20px;flex:1;min-width:340px;max-width:500px">
                <h2 style="color:#f4a228;font-size:16px;margin:0 0 12px">📝 Bill Details</h2>

                <div class="hb-field">
                    <label>👤 Customer Name *</label>
                    <input id="f-name" type="text" placeholder="Pura naam">
                </div>

                <div class="hb-field">
                    <label>📞 Phone *</label>
                    <input id="f-phone" type="tel" placeholder="10-digit number">
                </div>

                <div class="hb-field">
                    <label>🏘️ Society / Colony</label>
                    <input id="f-society" type="text" placeholder="Society name">
                </div>

                <div class="hb-row2">
                    <div class="hb-field">
                        <label>🏢 Block</label>
                        <input id="f-block" type="text" placeholder="A / B">
                    </div>
                    <div class="hb-field">
                        <label>🚪 Flat No.</label>
                        <input id="f-flat" type="text" placeholder="101">
                    </div>
                </div>

                <div class="hb-row2">
                    <div class="hb-field">
                        <label>📅 Date</label>
                        <input id="f-date" type="date" value="<?php echo esc_attr( $today ); ?>">
                    </div>
                    <div class="hb-field">
                        <label>🧾 Bill No.</label>
                        <input id="f-billno" type="text" value="<?php echo esc_attr( $next_bill ); ?>">
                    </div>
                </div>

                <div class="hb-field">
                    <label>💳 Payment Method</label>
                    <select id="f-pay">
                        <option value="Cash on Delivery">💵 Cash on Delivery</option>
                        <option value="UPI / Online">📲 UPI / Online</option>
                        <option value="Paid">✅ Paid</option>
                    </select>
                </div>

                <div class="hb-field">
                    <label>🛒 Items (Naam · Qty · UOM · Rate)</label>
                    <div id="items-list"></div>
                    <button type="button" class="hb-add-item" onclick="addItemRow()">➕ Item Add Karo</button>
                </div>

                <div class="hb-row2">
                    <div class="hb-field">
                        <label>🚚 Delivery Charge (₹)</label>
                        <input id="f-delivery" type="number" value="0" min="0">
                    </div>
                    <div class="hb-field">
                        <label>🎁 Discount (₹)</label>
                        <input id="f-discount" type="number" value="0" min="0">
                    </div>
                </div>

                <div class="hb-field">
                    <label>📝 Notes (optional)</label>
                    <input id="f-notes" type="text" placeholder="e.g. Kal subah deliver karna">
                </div>

                <button type="button" class="hb-btn-primary" onclick="generateBill()">🧾 Bill Generate Karo</button>
                <button type="button" class="hb-btn-secondary" onclick="resetForm()">🔄 Form Reset</button>
            </div>

            <!-- ====== INVOICE PREVIEW ====== -->
            <div id="hb-invoice-wrap" style="flex:1;min-width:340px;max-width:500px;display:none">
                <div class="invoice" id="invoice">
                    <div class="inv-head">
                        <div class="inv-logo">🌿</div>
                        <div class="inv-brand">Hariyali<span>Basket</span></div>
                        <div class="inv-tagline">FARM TO DOORSTEP</div>
                        <div class="inv-contact">📱 +91 80003 44554 · Jaipur</div>
                    </div>

                    <div class="inv-title-bar">
                        <span>🧾 INVOICE</span>
                        <span id="inv-billno">HB-0001</span>
                    </div>

                    <div class="inv-meta">
                        <div><b>Date:</b> <span id="inv-date">—</span></div>
                        <div style="text-align:right"><b>Time:</b> <span id="inv-time">—</span></div>
                    </div>

                    <div class="inv-cust">
                        <div class="inv-cust-title">🚚 DELIVER TO</div>
                        <div class="inv-cust-name" id="inv-name">—</div>
                        <div class="inv-cust-detail" id="inv-address">—</div>
                        <div class="inv-cust-detail">📞 <span id="inv-phone">—</span></div>
                    </div>

                    <table class="inv-items">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Item</th>
                                <th class="num">Qty</th>
                                <th class="num">Rate</th>
                                <th class="num">Amount</th>
                            </tr>
                        </thead>
                        <tbody id="inv-items-body"></tbody>
                    </table>

                    <div class="inv-totals">
                        <div class="row"><span>Subtotal</span><span id="inv-subtotal">₹0</span></div>
                        <div class="row" id="inv-discount-row"><span>🎁 Discount</span><span id="inv-discount">- ₹0</span></div>
                        <div class="row"><span>🚚 Delivery</span><span id="inv-delivery">₹0</span></div>
                        <div class="row grand"><span>GRAND TOTAL</span><span id="inv-total">₹0</span></div>
                        <div class="savings" id="inv-savings" style="display:none"></div>
                    </div>

                    <div class="inv-pay">
                        <b>💳 Payment:</b> <span id="inv-pay">—</span>
                        <div id="inv-notes-wrap" style="display:none;margin-top:4px"><b>📝 Note:</b> <span id="inv-notes"></span></div>
                    </div>

                    <div class="inv-foot">
                        <div class="ty">🙏 Dhanyawaad! Aapka order mila!</div>
                        <div>🌿 100% Farm Fresh Guarantee · Free Replacement</div>
                        <div>WhatsApp: +91 80003 44554 · UPI: imanohar07773@ybl</div>
                    </div>
                </div>

                <div class="hb-actions">
                    <button class="act-print" onclick="printBill()">🖨️ Print / PDF</button>
                    <button class="act-wa" onclick="sendWhatsApp()">📱 WhatsApp</button>
                    <button class="act-save" onclick="saveBill()">💾 Save Bill</button>
                    <button class="act-edit" onclick="editBill()">✏️ Edit</button>
                    <button class="act-new" onclick="newBill()">🆕 Naya Bill</button>
                </div>
            </div>

        </div>
    </div>

    <style>
    /* ===== ADMIN PAGE STYLES ===== */
    #hb-app .hb-field { margin-bottom: 10px; }
    #hb-app .hb-field label { font-size: 11px; color: #8ed46a; font-weight: 700; display: block; margin-bottom: 4px; }
    #hb-app .hb-field input,
    #hb-app .hb-field select,
    #hb-app .hb-field textarea {
        width: 100%;
        padding: 9px 12px;
        border: 1px solid #2d6b42;
        border-radius: 8px;
        background: #1a4d2e;
        color: #fff;
        font-size: 13px;
        outline: none;
        box-sizing: border-box;
    }
    #hb-app .hb-field input:focus,
    #hb-app .hb-field select:focus { border-color: #8ed46a; }
    #hb-app .hb-row2 { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
    #hb-app .item-row {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr 1fr auto;
        gap: 6px;
        margin-bottom: 6px;
        align-items: end;
    }
    #hb-app .item-row input { padding: 8px 10px; font-size: 12px; }
    #hb-app .item-row .del-btn {
        background: #e74c3c; color: #fff; border: none;
        width: 32px; height: 36px; border-radius: 8px;
        cursor: pointer; font-size: 14px; font-weight: bold;
    }
    #hb-app .item-row .item-suggest {
        position: relative;
    }
    #hb-app .suggest-list {
        position: absolute;
        top: 100%; left: 0; right: 0;
        background: #fff; color: #1a1a1a;
        border: 1px solid #1a4d2e;
        border-radius: 8px;
        max-height: 180px; overflow-y: auto;
        z-index: 1000;
        font-size: 12px;
    }
    #hb-app .suggest-list .sg-item {
        padding: 8px 12px; cursor: pointer; border-bottom: 1px solid #eee;
    }
    #hb-app .suggest-list .sg-item:hover { background: #f0fff4; }
    #hb-app .hb-add-item {
        width: 100%; background: transparent;
        border: 2px dashed #8ed46a; color: #8ed46a;
        padding: 9px; border-radius: 8px;
        cursor: pointer; font-size: 12px; font-weight: 700;
        margin-top: 6px;
    }
    #hb-app .hb-btn-primary {
        width: 100%; background: #25d366; color: #fff;
        border: none; padding: 13px; border-radius: 10px;
        font-size: 14px; font-weight: 800; cursor: pointer; margin-top: 12px;
    }
    #hb-app .hb-btn-secondary {
        width: 100%; background: #1a4d2e; color: #8ed46a;
        border: 1px solid #2d6b42; padding: 11px; border-radius: 10px;
        font-size: 13px; font-weight: 700; cursor: pointer; margin-top: 6px;
    }

    /* ===== INVOICE STYLES ===== */
    #hb-app .invoice {
        background: #fff; color: #1a1a1a;
        border-radius: 14px; overflow: hidden;
        box-shadow: 0 4px 24px rgba(0,0,0,0.15);
        font-family: 'Segoe UI', Arial, sans-serif;
    }
    #hb-app .inv-head {
        background: linear-gradient(135deg, #1a4d2e, #0f2d18);
        color: #fff; padding: 18px 20px; text-align: center;
    }
    #hb-app .inv-logo { font-size: 32px; }
    #hb-app .inv-brand { font-size: 22px; font-weight: 900; }
    #hb-app .inv-brand span { color: #f4a228; }
    #hb-app .inv-tagline { font-size: 9px; color: #8ed46a; letter-spacing: 2px; margin-top: 2px; }
    #hb-app .inv-contact { font-size: 10px; color: #d4ffea; margin-top: 6px; }
    #hb-app .inv-title-bar {
        background: #f4a228; color: #fff; padding: 8px 20px;
        display: flex; justify-content: space-between; align-items: center;
        font-size: 13px; font-weight: 800;
    }
    #hb-app .inv-meta {
        padding: 12px 20px; border-bottom: 1px dashed #ddd;
        display: grid; grid-template-columns: 1fr 1fr;
        gap: 6px; font-size: 11px;
    }
    #hb-app .inv-meta b { color: #1a4d2e; }
    #hb-app .inv-cust { padding: 12px 20px; background: #f9fff9; border-bottom: 1px dashed #ddd; }
    #hb-app .inv-cust-title { font-size: 10px; color: #888; font-weight: 700; letter-spacing: 1px; }
    #hb-app .inv-cust-name { font-size: 15px; font-weight: 800; color: #1a4d2e; margin-top: 4px; }
    #hb-app .inv-cust-detail { font-size: 11px; color: #555; margin-top: 3px; }
    #hb-app .inv-items { width: 100%; border-collapse: collapse; font-size: 11px; }
    #hb-app .inv-items thead { background: #1a4d2e; color: #fff; }
    #hb-app .inv-items th, #hb-app .inv-items td { padding: 8px 10px; text-align: left; }
    #hb-app .inv-items th { font-size: 10px; }
    #hb-app .inv-items td { border-bottom: 1px solid #eee; }
    #hb-app .inv-items .num { text-align: right; }
    #hb-app .inv-items td.iname { font-weight: 600; color: #1a4d2e; }
    #hb-app .inv-totals { padding: 12px 20px; background: #fff; }
    #hb-app .inv-totals .row { display: flex; justify-content: space-between; padding: 5px 0; font-size: 12px; color: #444; }
    #hb-app .inv-totals .grand {
        margin-top: 6px; padding: 10px 0 6px;
        border-top: 2px solid #1a4d2e;
        font-size: 16px; font-weight: 900; color: #1a4d2e;
    }
    #hb-app .inv-totals .grand span:last-child { color: #f4a228; }
    #hb-app .inv-totals .savings {
        background: #fff8e1; border: 1px solid #f4a228;
        border-radius: 8px; padding: 6px 10px; margin-top: 8px;
        font-size: 11px; color: #b36000; font-weight: 700; text-align: center;
    }
    #hb-app .inv-pay { padding: 10px 20px; background: #f0fff4; font-size: 11px; color: #1a4d2e; }
    #hb-app .inv-foot {
        background: #1a4d2e; color: #fff;
        padding: 12px 20px; text-align: center;
        font-size: 10px; line-height: 1.7;
    }
    #hb-app .inv-foot .ty { font-size: 13px; font-weight: 800; color: #f4a228; margin-bottom: 3px; }

    /* ===== ACTIONS ===== */
    #hb-app .hb-actions {
        margin-top: 12px;
        display: grid; grid-template-columns: 1fr 1fr;
        gap: 8px;
    }
    #hb-app .hb-actions button {
        padding: 12px; border: none; border-radius: 10px;
        font-size: 13px; font-weight: 700; cursor: pointer;
    }
    #hb-app .act-print { background: #1a4d2e; color: #fff; }
    #hb-app .act-wa    { background: #25d366; color: #fff; }
    #hb-app .act-save  { background: #2196f3; color: #fff; }
    #hb-app .act-edit  { background: #f4a228; color: #fff; }
    #hb-app .act-new   { background: #e74c3c; color: #fff; grid-column: 1 / -1; }

    /* ===== PRINT ===== */
    @media print {
        body * { visibility: hidden !important; }
        #invoice, #invoice * { visibility: visible !important; }
        #invoice {
            position: absolute; left: 0; top: 0;
            width: 100%; box-shadow: none; border-radius: 0;
        }
        #adminmenuback, #adminmenuwrap, #wpadminbar,
        #hb-form-card, .hb-actions, #wpfooter { display: none !important; }
    }
    </style>

    <script>
    const HB_AJAX  = '<?php echo esc_js( admin_url( 'admin-ajax.php' ) ); ?>';
    const HB_NONCE = '<?php echo esc_js( $nonce ); ?>';
    let HB_PRODUCTS = [];

    /* Load products from server (hb_product + WooCommerce) */
    fetch(HB_AJAX + '?action=hb_get_products')
        .then(r => r.json())
        .then(j => { if (j.success) HB_PRODUCTS = j.data; });

    function addItemRow(name, qty, uom, rate) {
        const list = document.getElementById('items-list');
        const div = document.createElement('div');
        div.className = 'item-row';
        div.innerHTML = `
            <div class="item-suggest">
                <input type="text" placeholder="Item naam" value="${name || ''}" class="it-name" oninput="suggestItems(this)" onblur="setTimeout(() => closeSuggest(this), 200)">
            </div>
            <input type="number" placeholder="Qty" value="${qty || 1}" min="0" step="0.5" class="it-qty">
            <input type="text" placeholder="kg/pc" value="${uom || 'Kg'}" class="it-uom">
            <input type="number" placeholder="₹ Rate" value="${rate || ''}" min="0" class="it-rate">
            <button class="del-btn" onclick="this.parentNode.remove()">✕</button>
        `;
        list.appendChild(div);
    }

    function suggestItems(input) {
        const q = input.value.toLowerCase().trim();
        const wrap = input.parentNode;
        let list = wrap.querySelector('.suggest-list');
        if (!q || q.length < 2) { if (list) list.remove(); return; }
        const matches = HB_PRODUCTS.filter(p => p.name.toLowerCase().includes(q)).slice(0, 6);
        if (!matches.length) { if (list) list.remove(); return; }
        if (!list) {
            list = document.createElement('div');
            list.className = 'suggest-list';
            wrap.appendChild(list);
        }
        list.innerHTML = matches.map((p, i) => `
            <div class="sg-item" onclick="pickItem(this, ${HB_PRODUCTS.indexOf(p)})">
                <b>${p.name}</b> — ₹${p.rate} / ${p.uom}
            </div>
        `).join('');
    }

    function pickItem(el, idx) {
        const p = HB_PRODUCTS[idx];
        const row = el.closest('.item-row');
        row.querySelector('.it-name').value = p.name;
        row.querySelector('.it-uom').value  = p.uom;
        row.querySelector('.it-rate').value = p.rate;
        closeSuggest(row.querySelector('.it-name'));
    }

    function closeSuggest(input) {
        const list = input.parentNode.querySelector('.suggest-list');
        if (list) list.remove();
    }

    function getItems() {
        const rows = document.querySelectorAll('#items-list .item-row');
        const items = [];
        rows.forEach(r => {
            const name = r.querySelector('.it-name').value.trim();
            const qty  = parseFloat(r.querySelector('.it-qty').value) || 0;
            const uom  = r.querySelector('.it-uom').value.trim() || 'pc';
            const rate = parseFloat(r.querySelector('.it-rate').value) || 0;
            if (name && qty > 0 && rate > 0) {
                items.push({ name, qty, uom, rate, amount: qty * rate });
            }
        });
        return items;
    }

    function todayStr() {
        const d = new Date();
        return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
    }

    function fmtDate(s) {
        if (!s) return '—';
        const d = new Date(s);
        return d.toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' });
    }

    function fmtTime() {
        return new Date().toLocaleTimeString('en-IN', { hour: '2-digit', minute: '2-digit', hour12: true });
    }

    let currentBill = null;

    function generateBill() {
        const name = document.getElementById('f-name').value.trim();
        const phone = document.getElementById('f-phone').value.trim();
        const items = getItems();

        if (!name) { alert('⚠️ Customer ka naam bharo!'); return; }
        if (!phone) { alert('⚠️ Phone number bharo!'); return; }
        if (!items.length) { alert('⚠️ Kam se kam ek item add karo!'); return; }

        const society = document.getElementById('f-society').value.trim();
        const block = document.getElementById('f-block').value.trim();
        const flat = document.getElementById('f-flat').value.trim();
        const date = document.getElementById('f-date').value || todayStr();
        const billno = document.getElementById('f-billno').value.trim();
        const pay = document.getElementById('f-pay').value;
        const delivery = parseFloat(document.getElementById('f-delivery').value) || 0;
        const discount = parseFloat(document.getElementById('f-discount').value) || 0;
        const notes = document.getElementById('f-notes').value.trim();

        document.getElementById('inv-billno').textContent = billno;
        document.getElementById('inv-date').textContent = fmtDate(date);
        document.getElementById('inv-time').textContent = fmtTime();
        document.getElementById('inv-name').textContent = name;
        const addr = [society, block ? 'Block ' + block : '', flat ? 'Flat ' + flat : ''].filter(Boolean).join(' · ');
        document.getElementById('inv-address').textContent = addr || '—';
        document.getElementById('inv-phone').textContent = phone;
        document.getElementById('inv-pay').textContent = pay;

        if (notes) {
            document.getElementById('inv-notes').textContent = notes;
            document.getElementById('inv-notes-wrap').style.display = 'block';
        } else {
            document.getElementById('inv-notes-wrap').style.display = 'none';
        }

        document.getElementById('inv-items-body').innerHTML = items.map((it, i) => `
            <tr>
                <td>${i + 1}</td>
                <td class="iname">${it.name}</td>
                <td class="num">${it.qty} ${it.uom}</td>
                <td class="num">₹${it.rate}</td>
                <td class="num"><b>₹${it.amount}</b></td>
            </tr>
        `).join('');

        const subtotal = items.reduce((s, it) => s + it.amount, 0);
        const total = subtotal - discount + delivery;

        document.getElementById('inv-subtotal').textContent = '₹' + subtotal;
        document.getElementById('inv-delivery').textContent = delivery > 0 ? '₹' + delivery : 'FREE';
        document.getElementById('inv-discount').textContent = '- ₹' + discount;
        document.getElementById('inv-discount-row').style.display = discount > 0 ? 'flex' : 'none';
        document.getElementById('inv-total').textContent = '₹' + total;

        const savingsArr = [];
        if (discount > 0) savingsArr.push('💰 ₹' + discount + ' bachaye');
        if (delivery === 0 && subtotal >= 499) savingsArr.push('🎉 FREE Delivery');
        const sEl = document.getElementById('inv-savings');
        if (savingsArr.length) {
            sEl.textContent = savingsArr.join(' · ');
            sEl.style.display = 'block';
        } else {
            sEl.style.display = 'none';
        }

        currentBill = { billno, name, phone, society, block, flat, date, pay, delivery, discount, notes, items, total };
        document.getElementById('hb-invoice-wrap').style.display = 'block';
        document.getElementById('hb-invoice-wrap').scrollIntoView({ behavior: 'smooth' });
    }

    function printBill() { window.print(); }

    function editBill() {
        document.getElementById('hb-invoice-wrap').style.display = 'none';
        document.getElementById('hb-form-card').scrollIntoView({ behavior: 'smooth' });
    }

    function newBill() {
        if (!confirm('Naya bill banayein? Current bill clear ho jaayega.')) return;

        // Increment bill number on server
        const fd = new FormData();
        fd.append('action', 'hb_increment_billno');
        fetch(HB_AJAX, { method: 'POST', body: fd })
            .then(r => r.json())
            .then(j => {
                if (j.success) document.getElementById('f-billno').value = j.data;
            });

        resetForm();
        document.getElementById('hb-invoice-wrap').style.display = 'none';
    }

    function resetForm() {
        ['f-name','f-phone','f-society','f-block','f-flat','f-notes'].forEach(id => {
            document.getElementById(id).value = '';
        });
        document.getElementById('f-date').value = todayStr();
        document.getElementById('f-delivery').value = 0;
        document.getElementById('f-discount').value = 0;
        document.getElementById('f-pay').selectedIndex = 0;
        document.getElementById('items-list').innerHTML = '';
        addItemRow();
    }

    function saveBill() {
        if (!currentBill) { alert('Pehle bill generate karo!'); return; }
        const fd = new FormData();
        fd.append('action', 'hb_save_invoice');
        fd.append('nonce', HB_NONCE);
        Object.keys(currentBill).forEach(k => {
            fd.append(k, k === 'items' ? JSON.stringify(currentBill[k]) : currentBill[k]);
        });
        fetch(HB_AJAX, { method: 'POST', body: fd })
            .then(r => r.json())
            .then(j => {
                if (j.success) alert('✅ Bill save ho gaya! (' + j.data.billno + ')');
                else alert('❌ Save fail: ' + (j.data || 'unknown'));
            });
    }

    function sendWhatsApp() {
        if (!currentBill) { alert('Pehle bill generate karo!'); return; }
        const c = currentBill;
        let msg = `🌿 *HariyaliBasket — Bill ${c.billno}*\n\n`;
        msg += `👤 ${c.name}\n📅 ${fmtDate(c.date)}\n\n`;
        msg += `*Items:*\n`;
        c.items.forEach((it, i) => {
            msg += `${i + 1}. ${it.name} — ${it.qty} ${it.uom} × ₹${it.rate} = ₹${it.amount}\n`;
        });
        const subtotal = c.items.reduce((s, it) => s + it.amount, 0);
        msg += `\nSubtotal: ₹${subtotal}\n`;
        if (c.discount > 0) msg += `Discount: -₹${c.discount}\n`;
        msg += `Delivery: ${c.delivery > 0 ? '₹' + c.delivery : 'FREE'}\n`;
        msg += `*Grand Total: ₹${c.total}*\n`;
        msg += `💳 ${c.pay}\n\n`;
        msg += `🙏 Dhanyawaad! 🌿\n📱 +91 80003 44554`;

        const phone = c.phone.replace(/\D/g, '');
        const waPhone = phone.length === 10 ? '91' + phone : phone;
        window.open('https://wa.me/' + waPhone + '?text=' + encodeURIComponent(msg), '_blank');
    }

    /* Init */
    addItemRow();
    </script>
    <?php
}

/* ============================================================
   7. SAVED BILLS LIST PAGE
   ============================================================ */
function hb_invoice_list_page() {
    $bills = get_posts([
        'post_type'      => 'hb_invoice',
        'posts_per_page' => 50,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
    ]);
    ?>
    <div class="wrap">
        <h1>📋 Saved Bills</h1>
        <p>Aaj tak ke saare bills yahan dikhenge.</p>
        <table class="wp-list-table widefat striped">
            <thead>
                <tr>
                    <th>Bill No.</th>
                    <th>Customer</th>
                    <th>Phone</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th>Payment</th>
                </tr>
            </thead>
            <tbody>
                <?php if ( empty( $bills ) ) : ?>
                    <tr><td colspan="6" style="text-align:center;padding:20px;color:#999">Abhi tak koi bill save nahi hua.</td></tr>
                <?php else : foreach ( $bills as $b ) : ?>
                    <tr>
                        <td><b><?php echo esc_html( get_post_meta( $b->ID, '_hb_billno', true ) ); ?></b></td>
                        <td><?php echo esc_html( get_post_meta( $b->ID, '_hb_name', true ) ); ?></td>
                        <td><?php echo esc_html( get_post_meta( $b->ID, '_hb_phone', true ) ); ?></td>
                        <td><?php echo esc_html( get_post_meta( $b->ID, '_hb_date', true ) ); ?></td>
                        <td>₹<?php echo esc_html( get_post_meta( $b->ID, '_hb_total', true ) ); ?></td>
                        <td><?php echo esc_html( get_post_meta( $b->ID, '_hb_pay', true ) ); ?></td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}
