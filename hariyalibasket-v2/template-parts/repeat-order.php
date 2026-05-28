<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<!-- NEW: Repeat Order Button (only visible if last order exists in localStorage) -->
<div id="hb-repeat-bar" style="display:none">
  <div class="hb-rb-text">
    <div class="hb-rb-label">🔁 Pichla Order</div>
    <div class="hb-rb-items" id="hb-rb-items"></div>
  </div>
  <button class="hb-rb-btn" onclick="HBExtras.repeatOrder()">Repeat</button>
</div>
