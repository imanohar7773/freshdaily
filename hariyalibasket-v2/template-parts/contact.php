<?php if ( ! defined( 'ABSPATH' ) ) exit;
$wa    = hb_get( 'hb_wa_number', '918000344554' );
$email = hb_get( 'hb_email', 'hariyalibasket@gmail.com' );
?>
<section id="hb-section-contact" class="hb-modal-source">
  <div class="hb-section-hdr"><h2>📞 Humse <span>Baat Karo</span></h2><small>Hum hamesha available hain aapke liye</small></div>
  <div class="hb-contact-list">
    <a href="https://wa.me/<?php echo esc_attr( $wa ); ?>" target="_blank" class="hb-contact-card hb-cc-wa">
      <div class="hb-cc-icon">💬</div>
      <div>
        <div class="hb-cc-title">WhatsApp pe Order</div>
        <div class="hb-cc-sub">+91 <?php echo esc_html( substr( $wa, 2 ) ); ?></div>
        <div class="hb-cc-meta">9 AM – 9 PM · Roz Available</div>
      </div>
      <div class="hb-cc-arrow">→</div>
    </a>
    <a href="tel:<?php echo esc_attr( $wa ); ?>" class="hb-contact-card">
      <div class="hb-cc-icon">📞</div>
      <div>
        <div class="hb-cc-title">Call Karo</div>
        <div class="hb-cc-sub">+91 <?php echo esc_html( substr( $wa, 2 ) ); ?></div>
        <div class="hb-cc-meta">9 AM – 9 PM</div>
      </div>
      <div class="hb-cc-arrow">→</div>
    </a>
    <a href="mailto:<?php echo esc_attr( $email ); ?>" class="hb-contact-card">
      <div class="hb-cc-icon">📧</div>
      <div>
        <div class="hb-cc-title">Email Karo</div>
        <div class="hb-cc-sub"><?php echo esc_html( $email ); ?></div>
        <div class="hb-cc-meta">24 ghante mein reply</div>
      </div>
      <div class="hb-cc-arrow">→</div>
    </a>
    <div class="hb-contact-card">
      <div class="hb-cc-icon">⏰</div>
      <div>
        <div class="hb-cc-title">Order Timing</div>
        <div class="hb-cc-sub">🕙 Order Window: 24 hrs</div>
        <div class="hb-cc-meta">Subah 10 AM se pehle → Aaj 4 PM delivery<br>Subah 10 AM ke baad → Kal 4 PM delivery</div>
      </div>
    </div>
  </div>
</section>
