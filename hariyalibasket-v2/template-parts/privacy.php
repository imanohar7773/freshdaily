<?php if ( ! defined( 'ABSPATH' ) ) exit;
$min = intval( hb_get( 'hb_free_delivery_min', 199 ) );
$fee = intval( hb_get( 'hb_delivery_fee', 69 ) );
?>
<section id="hb-section-privacy" class="hb-modal-source">
  <div class="hb-section-hdr"><h2>📋 Privacy &amp; <span>Terms</span></h2><small>Hamare policies aur terms</small></div>
  <div class="hb-faq-list">
    <div class="hb-faq-item">
      <div class="hb-faq-q">🔒 Privacy Policy <span class="hb-faq-arrow">▼</span></div>
      <div class="hb-faq-a">Aapka naam, address aur phone number sirf delivery ke liye use hota hai. Hum kisi third party ko aapka data nahi dete.</div>
    </div>
    <div class="hb-faq-item">
      <div class="hb-faq-q">📜 Terms &amp; Conditions <span class="hb-faq-arrow">▼</span></div>
      <div class="hb-faq-a">
        <ul>
          <li>Order 10 AM tak place karna hoga next-day delivery ke liye.</li>
          <li>Prices 7 din ke andar change ho sakte hain.</li>
          <li>Delivery area limited hai — apna area pehle confirm karo.</li>
          <li>Bulk orders available hain — WhatsApp pe contact karo.</li>
        </ul>
      </div>
    </div>
    <div class="hb-faq-item">
      <div class="hb-faq-q">🔄 Refund &amp; Replacement <span class="hb-faq-arrow">▼</span></div>
      <div class="hb-faq-a">Delivery ke 2 ghante ke andar WhatsApp pe photo bhejein. Free replacement ya full refund — aapki choice.</div>
    </div>
    <div class="hb-faq-item">
      <div class="hb-faq-q">🚚 Delivery Policy <span class="hb-faq-arrow">▼</span></div>
      <div class="hb-faq-a">Next day 4 PM tak delivery guaranteed. ₹<?php echo $min; ?> se upar FREE. Usse kam par ₹<?php echo $fee; ?> delivery charge.</div>
    </div>
  </div>
</section>
