<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div id="hb-cart-bar">
  <div class="hb-meter-wrap" id="hb-meter-wrap">
    <div class="hb-meter-labels">
      <span id="hb-meter-txt">🚚 Free delivery ke liye add karo</span>
      <span id="hb-meter-pct">0%</span>
    </div>
    <div class="hb-meter-track"><div class="hb-meter-fill" id="hb-meter-fill"></div></div>
  </div>
  <div class="hb-cart-row">
    <div class="hb-cart-info" onclick="HBCart.toggleDrawer()">
      <div class="hb-cart-items-txt" id="hb-cart-items-txt">0 items</div>
      <div class="hb-cart-total-txt" id="hb-cart-total-txt">₹0</div>
    </div>
    <button class="hb-checkout-trigger" onclick="HBCheckout.open()">
      Place Order <span>→</span>
    </button>
  </div>
</div>
