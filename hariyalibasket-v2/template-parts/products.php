<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<section id="hb-products" class="hb-products-section">

  <!-- Search -->
  <div class="hb-search-section">
    <div class="hb-search-box">
      <span class="hb-search-icon">🔍</span>
      <input id="hb-search-inp" type="text" placeholder="Aloo, Tamatar, Pyaaz dhundo..." autocomplete="off">
      <button class="hb-clear-btn" id="hb-clear-btn">✕</button>
    </div>
    <div id="hb-search-suggest" class="hb-search-suggest"></div>
  </div>

  <!-- Delivery strip -->
  <div class="hb-delivery-strip">
    <div class="hb-dstrip-pill active"><span>📍 <?php echo esc_html( explode( ',', hb_get( 'hb_delivery_areas', 'Jaipur' ) )[0] ); ?></span></div>
    <div class="hb-dstrip-pill"><span>🕐 Kal 4 PM</span></div>
    <div class="hb-dstrip-pill"><span>🚚 Free on ₹<?php echo intval( hb_get( 'hb_free_delivery_min', 199 ) ); ?>+</span></div>
    <div class="hb-dstrip-pill"><span>🌿 100% Fresh</span></div>
  </div>

  <!-- Filter chips -->
  <div class="hb-filter-bar" id="hb-filter-bar"></div>

  <!-- Section header -->
  <div class="hb-section-hdr">
    <h2>Aaj ke <span>Deals</span> 🔥</h2>
    <small id="hb-prod-count">0 items</small>
  </div>

  <!-- Product grid -->
  <div id="hb-product-grid"></div>
</section>
