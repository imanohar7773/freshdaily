<?php
/**
 * Pincode Check — TOP-of-page prominent variant (Shop-First v2 layout).
 * Replaces the bottom pincode-check section. Reuses existing hb-pin-input/hb-pin-result IDs
 * so extras.js HBExtras.checkPincode() works without changes.
 */
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<section class="hb-pincode-top">
  <div class="hb-pin-row">
    <span class="hb-pin-label">📍 Delivery?</span>
    <input id="hb-pin-input" type="text" placeholder="Pincode ya colony name" maxlength="80" autocomplete="off">
    <button onclick="HBExtras.checkPincode()">Check ✓</button>
  </div>
  <div id="hb-pin-result"></div>
</section>
