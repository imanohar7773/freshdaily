<?php
/**
 * Footer
 */
if ( ! defined( 'ABSPATH' ) ) exit;
?>

<!-- ── FOOTER ── -->
<footer class="hb-footer">
  <div class="hb-footer-logo">Hariyali<span>Basket</span></div>
  <div class="hb-footer-tag">🌿 Jaipur Ki Taazi Sabzi · Roz Subah Chuni, Shaam Tak Ghar Pe</div>
  <div class="hb-footer-links">
    <a href="#" onclick="HBNav.openSection('about');return false;">About</a>
    <a href="#" onclick="HBNav.openSection('faq');return false;">FAQ</a>
    <a href="#" onclick="HBNav.openSection('contact');return false;">Contact</a>
    <a href="#" onclick="HBNav.openSection('privacy');return false;">Privacy</a>
  </div>
  <div class="hb-footer-copy">© <?php echo date( 'Y' ); ?> HariyaliBasket · All Rights Reserved</div>
</footer>

<!-- ── TOAST ── -->
<div id="hb-toast"></div>

<!-- ── CONFETTI MOUNT ── -->
<div id="hb-fly-mount" aria-hidden="true"></div>
<div id="hb-leaves-mount" aria-hidden="true"></div>

<?php wp_footer(); ?>
</body>
</html>
