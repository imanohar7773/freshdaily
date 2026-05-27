<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div id="hb-success-screen">
  <div class="hb-success-bg">
    <div class="hb-success-top">
      <div class="hb-success-ring"><span>✅</span></div>
      <div class="hb-success-title">Order Placed!</div>
      <div class="hb-success-sub">Delivery kal 4 PM se pehle 🚚</div>
    </div>
    <div class="hb-success-card">
      <div class="hb-oid-row">
        <div>
          <div class="hb-oid-label">ORDER ID</div>
          <div class="hb-oid-val" id="hb-s-orderid"></div>
        </div>
        <button class="hb-oid-copy" onclick="HBCheckout.copyOrderId()">Copy</button>
      </div>
      <div id="hb-s-details"></div>
    </div>
    <div class="hb-success-actions">
      <button class="hb-wa-notify-btn" id="hb-wa-btn" onclick="HBCheckout.notifyWA()">💬 Also Notify on WhatsApp</button>
      <button class="hb-continue-btn" onclick="HBCheckout.closeSuccess()">🛒 Continue Shopping</button>
    </div>
  </div>
</div>
