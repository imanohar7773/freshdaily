<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<!-- NEW: Sticky Bottom Navigation (mobile-app feel) -->
<nav class="hb-bottom-nav" id="hb-bottom-nav">
  <a class="hb-bn-item active" data-target="home" onclick="HBNav.scrollTo('home')">
    <div class="hb-bn-icon">🏠</div>
    <div class="hb-bn-label">Home</div>
  </a>
  <a class="hb-bn-item" data-target="categories" onclick="HBNav.scrollTo('categories')">
    <div class="hb-bn-icon">🛒</div>
    <div class="hb-bn-label">Shop</div>
  </a>
  <a class="hb-bn-item hb-bn-cart" onclick="HBCart.toggleDrawer()">
    <div class="hb-bn-icon">🧺<span class="hb-bn-badge" id="hb-bn-cart-badge">0</span></div>
    <div class="hb-bn-label">Cart</div>
  </a>
  <a class="hb-bn-item" onclick="HBNav.openSection('faq')">
    <div class="hb-bn-icon">📦</div>
    <div class="hb-bn-label">Orders</div>
  </a>
  <a class="hb-bn-item" onclick="HBNav.openSection('contact')">
    <div class="hb-bn-icon">👤</div>
    <div class="hb-bn-label">Account</div>
  </a>
</nav>
