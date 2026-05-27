<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<!-- Hamburger drawer + Section modal -->
<div id="hb-nav-overlay" onclick="HBNav.close()"></div>

<aside id="hb-nav-drawer">
  <div class="hb-nd-head">
    <div class="hb-nd-brand">Hariyali<span>Basket</span></div>
    <button class="hb-nd-close" onclick="HBNav.close()">✕</button>
  </div>
  <nav class="hb-nd-links">
    <button class="hb-nd-link" onclick="HBNav.scrollTo('home')"><span>🏠</span> Home</button>
    <button class="hb-nd-link" onclick="HBNav.scrollTo('products')"><span>🛒</span> Products</button>
    <button class="hb-nd-link" onclick="HBNav.openSection('about')"><span>🌿</span> About Us</button>
    <button class="hb-nd-link" onclick="HBNav.openSection('blog')"><span>📝</span> Blog &amp; Recipes</button>
    <button class="hb-nd-link" onclick="HBNav.openSection('wishlist')"><span>❤️</span> Wishlist</button>
    <button class="hb-nd-link" onclick="HBNav.openSection('faq')"><span>❓</span> FAQ</button>
    <button class="hb-nd-link" onclick="HBNav.openSection('contact')"><span>📞</span> Contact</button>
    <button class="hb-nd-link" onclick="HBNav.openSection('privacy')"><span>📋</span> Privacy &amp; T&amp;C</button>
    <a class="hb-nd-link hb-nd-wa" href="https://wa.me/<?php echo esc_attr( hb_get( 'hb_wa_number', '918000344554' ) ); ?>" target="_blank"><span>💬</span> WhatsApp Order</a>
  </nav>
  <div class="hb-nd-foot">© <?php echo date( 'Y' ); ?> HariyaliBasket 🌿</div>
</aside>

<div id="hb-section-modal" onclick="HBNav.checkModalClose(event)">
  <div class="hb-section-inner">
    <div class="hb-modal-bar">
      <span id="hb-modal-title">About Us</span>
      <button onclick="HBNav.closeSection()">✕</button>
    </div>
    <div id="hb-modal-body"></div>
  </div>
</div>
