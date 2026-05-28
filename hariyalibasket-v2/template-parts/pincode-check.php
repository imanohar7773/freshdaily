<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<section class="hb-pincode-section">
  <div class="hb-pin-box">
    <div class="hb-pin-icon">📍</div>
    <h3>Delivery Area Check Karo</h3>
    <p>Apna pincode ya colony name type karo</p>
    <div class="hb-pin-row">
      <input id="hb-pin-input" type="text" placeholder="Pincode ya Colony" maxlength="80">
      <button onclick="HBExtras.checkPincode()">Check ✓</button>
    </div>
    <div id="hb-pin-result"></div>
  </div>
</section>
