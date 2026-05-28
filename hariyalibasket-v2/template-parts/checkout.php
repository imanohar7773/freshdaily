<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div id="hb-overlay" onclick="HBCheckout.close()"></div>

<div id="hb-checkout-sheet">
  <div class="hb-sheet-top">
    <div class="hb-drag-handle"></div>
    <div class="hb-step-bar">
      <div class="hb-step-node">
        <div class="hb-step-circle active" id="hb-sc1">1</div>
        <div class="hb-step-label active" id="hb-sl1">CART</div>
      </div>
      <div class="hb-step-line" id="hb-sline1"></div>
      <div class="hb-step-node">
        <div class="hb-step-circle" id="hb-sc2">2</div>
        <div class="hb-step-label" id="hb-sl2">ADDRESS</div>
      </div>
      <div class="hb-step-line" id="hb-sline2"></div>
      <div class="hb-step-node">
        <div class="hb-step-circle" id="hb-sc3">3</div>
        <div class="hb-step-label" id="hb-sl3">PAYMENT</div>
      </div>
    </div>
    <div class="hb-sheet-hdr">
      <h3 class="hb-sheet-title" id="hb-sheet-title">🛒 Your Cart</h3>
      <button class="hb-sheet-close" onclick="HBCheckout.close()">✕</button>
    </div>
  </div>

  <!-- STEP 1: Cart -->
  <div class="hb-step-panel active" id="hb-panel1">
    <div id="hb-co-items"></div>
    <div class="hb-bill-card">
      <div class="hb-bill-title">📋 BILL SUMMARY</div>
      <div class="hb-bill-row"><span>Item Total (MRP)</span><span id="hb-b-mrp">₹0</span></div>
      <div class="hb-bill-row hb-saving"><span>🎉 You Save</span><span id="hb-b-save">−₹0</span></div>
      <div class="hb-bill-row"><span>Delivery Fee</span><span id="hb-b-del">₹<?php echo intval( hb_get( 'hb_delivery_fee', 69 ) ); ?></span></div>
      <hr class="hb-bill-dash">
      <div class="hb-bill-row hb-grand"><span>Grand Total</span><span id="hb-b-grand">₹0</span></div>
    </div>
    <div class="hb-delivery-tag">
      <span class="hb-dt-icon">⏰</span>
      <div>
        <div class="hb-dt-main">Expected Delivery</div>
        <div class="hb-dt-sub">Tomorrow before 4:00 PM</div>
      </div>
      <span class="hb-dt-fast">FAST</span>
    </div>
  </div>
  <div class="hb-sheet-footer show" id="hb-foot1">
    <button class="hb-primary-btn hb-btn-green" onclick="HBCheckout.go(2)">Proceed to Delivery →</button>
  </div>

  <!-- STEP 2: Address -->
  <div class="hb-step-panel" id="hb-panel2">
    <div class="hb-saved-addr" id="hb-saved-addr" onclick="HBAddress.edit()">
      <div class="hb-sa-row">
        <div>
          <div class="hb-sa-label">📍 SAVED ADDRESS</div>
          <div id="hb-saved-addr-text" class="hb-sa-text"></div>
        </div>
        <span class="hb-sa-change">Change</span>
      </div>
    </div>

    <div id="hb-addr-form">
      <div class="hb-form-section-label">📍 DELIVERY ADDRESS</div>
      <div class="hb-form-group">
        <input id="hb-f-name" class="hb-field" type="text" placeholder="Full Name *" autocomplete="name">
        <div class="hb-soc-wrap">
          <input id="hb-f-soc" class="hb-field" type="text" placeholder="Society / Colony *" readonly onclick="HBAddress.toggleDD()">
          <span class="hb-soc-arrow">▼</span>
          <div class="hb-soc-dropdown" id="hb-soc-dd">
            <div class="hb-soc-search-wrap">
              <input class="hb-soc-search" id="hb-soc-q" type="text" placeholder="🔍 Search colony..." autocomplete="off">
            </div>
            <div class="hb-soc-list" id="hb-soc-list"></div>
            <div class="hb-soc-footer">
              <button class="hb-add-soc-btn" type="button" onclick="HBAddress.addCustom()">+ Add Your Colony</button>
            </div>
          </div>
        </div>
        <div class="hb-field-grid">
          <input id="hb-f-block" class="hb-field" type="text" placeholder="Block / Tower">
          <input id="hb-f-flat" class="hb-field" type="text" placeholder="Flat / House No.">
        </div>
        <input id="hb-f-phone" class="hb-field" type="tel" placeholder="Mobile Number *" maxlength="10" autocomplete="tel">
      </div>
      <label class="hb-save-row">
        <input type="checkbox" id="hb-save-chk" checked>
        <span>Save this address for next time</span>
      </label>
    </div>
  </div>
  <div class="hb-sheet-footer" id="hb-foot2">
    <button class="hb-primary-btn hb-btn-green" onclick="HBAddress.validate()">Choose Payment →</button>
  </div>

  <!-- STEP 3: Payment -->
  <div class="hb-step-panel" id="hb-panel3">
    <div class="hb-form-section-label">💳 PAYMENT METHOD</div>

    <div class="hb-pay-card selected" id="hb-pay-cod" onclick="HBCheckout.pickPay('cod')">
      <div class="hb-pay-inner">
        <div class="hb-pay-icon" style="background:#dcfce7">💵</div>
        <div>
          <div class="hb-pay-title">Cash on Delivery</div>
          <div class="hb-pay-sub">Pay when your order arrives</div>
        </div>
        <div class="hb-pay-radio on" id="hb-radio-cod"><div class="hb-pay-dot"></div></div>
      </div>
    </div>

    <div class="hb-pay-card" id="hb-pay-upi" onclick="HBCheckout.pickPay('upi')">
      <div class="hb-pay-inner">
        <div class="hb-pay-icon" style="background:#ede9fe">📲</div>
        <div>
          <div class="hb-pay-title">UPI / Online Payment</div>
          <div class="hb-pay-sub">GPay · PhonePe · Paytm</div>
        </div>
        <div class="hb-pay-radio" id="hb-radio-upi"></div>
      </div>
    </div>

    <div class="hb-upi-expand" id="hb-upi-expand">
      <div class="hb-upi-step">📲 STEP 1 — Pay to this UPI ID</div>
      <div class="hb-upi-card">
        <div class="hb-upi-hint">UPI ID</div>
        <div class="hb-upi-val"><?php echo esc_html( hb_get( 'hb_upi_id', 'imanohar07773@ybl' ) ); ?></div>
        <button class="hb-copy-btn" type="button" onclick="HBCheckout.copyUPI()">📋 Copy UPI ID</button>
      </div>
      <div class="hb-upi-step">✅ STEP 2 — Enter Transaction ID</div>
      <input id="hb-f-txn" class="hb-field" type="text" placeholder="Transaction ID (e.g. 4352XXXX8932)">
      <div class="hb-upi-tip">💡 Find it in your GPay / PhonePe app after paying</div>
    </div>

    <!-- CAPTCHA -->
    <div class="hb-captcha-wrap">
      <div class="hb-captcha-label">🔒 Bot Check (spam prevention)</div>
      <div class="hb-captcha-row">
        <span class="hb-captcha-q" id="hb-cap-q">5 + 3 = ?</span>
        <input id="hb-cap-v" class="hb-field hb-cap-input" type="number" placeholder="Answer" inputmode="numeric">
      </div>
    </div>

    <div class="hb-order-mini">
      <div class="hb-om-label">ORDER TOTAL</div>
      <div class="hb-om-row">
        <span class="hb-om-count" id="hb-mini-count"></span>
        <span class="hb-om-total" id="hb-mini-total"></span>
      </div>
    </div>
  </div>
  <div class="hb-sheet-footer" id="hb-foot3">
    <button class="hb-primary-btn hb-btn-amber" id="hb-place-btn" onclick="HBCheckout.place()">
      <span id="hb-place-btn-txt">🎉 Place Order</span>
    </button>
  </div>
</div>
