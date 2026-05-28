<?php if ( ! defined( 'ABSPATH' ) ) exit;
$min = intval( hb_get( 'hb_free_delivery_min', 199 ) );
$fee = intval( hb_get( 'hb_delivery_fee', 69 ) );
?>
<section id="hb-section-faq" class="hb-modal-source">
  <div class="hb-section-hdr"><h2>❓ Aapke <span>Sawaal-Jawab</span></h2><small>Aapke common questions ke jawab</small></div>
  <div class="hb-faq-list">
    <div class="hb-faq-item">
      <div class="hb-faq-q">🚚 Delivery kab hoti hai? <span class="hb-faq-arrow">▼</span></div>
      <div class="hb-faq-a">Order karo aaj raat 10 PM tak — delivery hogi kal 4 PM se pehle. Hum Jaipur ke major societies aur colonies mein deliver karte hain.</div>
    </div>
    <div class="hb-faq-item">
      <div class="hb-faq-q">💰 Minimum order kitna hai? <span class="hb-faq-arrow">▼</span></div>
      <div class="hb-faq-a">Koi minimum order nahi hai! Lekin ₹<?php echo $min; ?> se upar order karne par FREE delivery milegi. Usse kam par ₹<?php echo $fee; ?> delivery charge lagega.</div>
    </div>
    <div class="hb-faq-item">
      <div class="hb-faq-q">💳 Payment kaise kare? <span class="hb-faq-arrow">▼</span></div>
      <div class="hb-faq-a">Cash on Delivery (COD) aur UPI dono accepted hain. GPay, PhonePe, Paytm sab chalega.</div>
    </div>
    <div class="hb-faq-item">
      <div class="hb-faq-q">🌿 Vegetables kitni fresh hoti hain? <span class="hb-faq-arrow">▼</span></div>
      <div class="hb-faq-a">📦 Har Roz Naya Stock — Packed With Care, Delivered Fresh. Market ki sabzi 2-3 din purani hoti hai, hamari hamesha fresh.</div>
    </div>
    <div class="hb-faq-item">
      <div class="hb-faq-q">🔄 Agar quality achhi na lage toh? <span class="hb-faq-arrow">▼</span></div>
      <div class="hb-faq-a">100% replacement guarantee hai! WhatsApp pe photo bhejo — agla order mein replace ho jaayega ya refund milega.</div>
    </div>
    <div class="hb-faq-item">
      <div class="hb-faq-q">📍 Kahan deliver karte ho? <span class="hb-faq-arrow">▼</span></div>
      <div class="hb-faq-a">Jaipur ke <?php echo esc_html( hb_get( 'hb_delivery_areas', 'Hanging Garden, Vaishali Nagar, Malviya Nagar, Mansarovar' ) ); ?> aur surrounding areas mein deliver karte hain.</div>
    </div>
    <div class="hb-faq-item">
      <div class="hb-faq-q">🎁 New user offer kya hai? <span class="hb-faq-arrow">▼</span></div>
      <div class="hb-faq-a">Pehle order par 1 Kg Aloo bilkul FREE! Bas WhatsApp pe order karte waqt mention karo ki aap new customer ho.</div>
    </div>
  </div>
</section>
