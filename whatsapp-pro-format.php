<?php
/**
 * ================================================================
 * HARIYALIBASKET - PROFESSIONAL WHATSAPP INVOICE FORMAT
 * ================================================================
 *
 * KYA HOTA HAI:
 * Customer jab "Order on WhatsApp" dabayega, WhatsApp pe plain text
 * ki jagah ek BEAUTIFUL INVOICE-STYLE message jaayega — bilkul
 * professional bill jaisa.
 *
 * INSTALLATION:
 * Apne main PHP template mein <?php wp_footer(); ?> ke OOPAR
 * is poori file ka content paste kar do.
 * ================================================================
 */
?>

<!-- ============================================================
     PROFESSIONAL WHATSAPP INVOICE FORMAT
     Paste at bottom of template (before wp_footer)
     ============================================================ -->

<script>
(function() {
  'use strict';

  /* ============================================================
     1. COLLECT ORDER DATA FROM CART + FORM
     ============================================================ */
  function collectData() {
    var $ = function(id) { return document.getElementById(id); };

    var name    = (($('o-name')    || {}).value || '').trim();
    var phone   = (($('o-phone')   || {}).value || '').trim();
    var society = (($('o-society') || {}).value || '').trim();
    var block   = (($('o-block')   || {}).value || '').trim();
    var flat    = (($('o-flat')    || {}).value || '').trim();
    var txnId   = (($('o-txn')     || {}).value || '').trim();

    if (!name || !phone || !society) return null;
    if (typeof cart === 'undefined' || !Object.keys(cart).length) return null;

    var items = [];
    var subtotal = 0;
    var savings  = 0;

    for (var k in cart) {
      var pts  = String(k).split('_');
      var pid  = pts[0];
      var vIdx = pts[1] !== undefined ? parseInt(pts[1]) : -1;
      var p    = (typeof allProducts !== 'undefined') ?
                  allProducts.find(function(x) { return String(x.id) === String(pid); }) : null;
      if (!p) continue;

      var av  = (vIdx >= 0 && p.variants && p.variants[vIdx]) ? p.variants[vIdx] : null;
      var sp  = av ? av.sp  : p.sp;
      var mrp = av ? av.mrp : p.mrp;
      var uom = av ? av.size : p.uom;
      var amt = cart[k] * sp;
      var sav = cart[k] * Math.max(0, mrp - sp);

      subtotal += amt;
      savings  += sav;

      items.push({
        name: p.name + (av ? ' (' + av.size + ')' : ''),
        qty:  cart[k],
        uom:  uom,
        rate: sp,
        amount: amt
      });
    }

    var minFree  = (typeof MIN_FREE !== 'undefined') ? MIN_FREE : 499;
    var delivery = subtotal >= minFree ? 0 : 69;
    var total    = subtotal + delivery;

    var d = new Date();
    var billno = 'HB-' +
      d.getFullYear().toString().slice(-2) +
      String(d.getMonth() + 1).padStart(2, '0') +
      String(d.getDate()).padStart(2, '0') + '-' +
      String(Math.floor(Math.random() * 900) + 100);

    return {
      billno: billno,
      date:   d.toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' }),
      time:   d.toLocaleTimeString('en-IN', { hour: '2-digit', minute: '2-digit', hour12: true }),
      name:    name,
      phone:   phone,
      society: society,
      block:   block,
      flat:    flat,
      txnId:   txnId,
      payMethod: (typeof payMethod !== 'undefined' && payMethod === 'upi') ? 'UPI / Online' : 'Cash on Delivery',
      items:    items,
      subtotal: subtotal,
      delivery: delivery,
      total:    total,
      savings:  savings
    };
  }

  /* ============================================================
     2. BUILD PROFESSIONAL INVOICE-STYLE MESSAGE
     ============================================================ */
  function buildInvoiceMessage(d) {
    var numEmojis = ['1\u20e3', '2\u20e3', '3\u20e3', '4\u20e3', '5\u20e3',
                     '6\u20e3', '7\u20e3', '8\u20e3', '9\u20e3', '\ud83d\udd1f'];

    var m = '';

    // ===== HEADER =====
    m += '\u2554\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2557\n';
    m += '   \ud83c\udf3f *HARIYALIBASKET* \ud83c\udf3f\n';
    m += '   Farm Fresh \u00b7 Daily\n';
    m += '\u255a\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u255d\n\n';

    // ===== BILL META =====
    m += '\ud83d\udcdd *BILL #' + d.billno + '*\n';
    m += '\ud83d\udcc5 ' + d.date + '  \u00b7  \u23f0 ' + d.time + '\n';

    // ===== CUSTOMER =====
    m += '\n\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\n';
    m += '\ud83d\udc64 *CUSTOMER DETAILS*\n';
    m += '\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\n';
    m += '*Name:* ' + d.name + '\n';
    m += '*Phone:* +91 ' + d.phone + '\n';
    m += '*Society:* ' + d.society + '\n';

    var addr = [];
    if (d.block) addr.push('Block ' + d.block);
    if (d.flat)  addr.push('Flat ' + d.flat);
    if (addr.length) m += '*Address:* ' + addr.join(', ') + '\n';

    // ===== ITEMS =====
    m += '\n\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\n';
    m += '\ud83d\uded2 *ORDER ITEMS*\n';
    m += '\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\n';

    d.items.forEach(function(it, i) {
      var nE = (i < 10) ? numEmojis[i] : (i + 1) + '.';
      m += nE + ' *' + it.name + '*\n';
      m += '   ' + it.qty + ' ' + it.uom + ' \u00d7 \u20b9' + it.rate + ' = *\u20b9' + it.amount + '*\n';
      if (i < d.items.length - 1) m += '\n';
    });

    // ===== SUMMARY =====
    m += '\n\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\n';
    m += '\ud83d\udcb0 *BILL SUMMARY*\n';
    m += '\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\n';
    m += 'Subtotal:        \u20b9' + d.subtotal + '\n';
    m += 'Delivery:        ' + (d.delivery > 0 ? '\u20b9' + d.delivery : '*FREE* \ud83c\udf89') + '\n';
    if (d.savings > 0) m += 'Aapne bachaye:   \u20b9' + d.savings + ' \ud83d\udcb0\n';
    m += '\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\n';
    m += '*\ud83d\udcb5 GRAND TOTAL: \u20b9' + d.total + '*\n';

    // ===== PAYMENT =====
    m += '\n\ud83d\udcb3 *Payment:* ' + d.payMethod + '\n';
    if (d.txnId) m += '\ud83d\udd10 *Txn ID:* `' + d.txnId + '`\n';

    // ===== CONFIRMATION =====
    m += '\n\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\n';
    m += '\u2705 *ORDER CONFIRMED!*\n';
    m += '\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\u2501\n';
    m += '\ud83d\udce6 Delivery: *Kal 4 PM tak*\n';
    m += '\ud83c\udf3f 100% Farm Fresh Guarantee\n';
    m += '\ud83d\udd04 Free Replacement Policy\n';

    // ===== FOOTER =====
    m += '\n\ud83d\ude4f *Thank You!*\n';
    m += '\ud83c\udf3f *HariyaliBasket*\n';
    m += '\ud83d\udcf1 +91 80003 44554\n';
    m += '\ud83d\udc9a Jaipur Ki Taazi Sabzi';

    return m;
  }

  /* ============================================================
     3. HIJACK WHATSAPP URL DURING sendWA() CALL
     ============================================================ */
  function attachHook() {
    if (typeof window.sendWA !== 'function') return setTimeout(attachHook, 300);
    if (window.__hbProFormatHooked) return;
    window.__hbProFormatHooked = true;

    var _origSendWA = window.sendWA;
    window.sendWA = function() {
      // Pre-collect data so we have everything before original runs
      var data = collectData();

      // Hijack window.open temporarily to replace the WhatsApp URL
      var _origOpen = window.open;
      var hijacked = false;

      window.open = function(url, target, features) {
        if (!hijacked && typeof url === 'string' && url.indexOf('wa.me') !== -1 && data) {
          hijacked = true;
          // Extract phone number from original URL
          var m = url.match(/wa\.me\/(\d+)/);
          var phone = m ? m[1] : '918000344554';
          var newMsg = buildInvoiceMessage(data);
          url = 'https://wa.me/' + phone + '?text=' + encodeURIComponent(newMsg);
        }
        return _origOpen.call(window, url, target, features);
      };

      // Call original chain (validation, Sheet, WhatsApp, invoice modal)
      try {
        _origSendWA.apply(this, arguments);
      } finally {
        // Always restore window.open after a delay
        setTimeout(function() { window.open = _origOpen; }, 2000);
      }
    };
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() { setTimeout(attachHook, 1000); });
  } else {
    setTimeout(attachHook, 1000);
  }
})();
</script>

<!-- ============================================================
     END OF PROFESSIONAL WHATSAPP FORMAT SNIPPET
     ============================================================ -->
