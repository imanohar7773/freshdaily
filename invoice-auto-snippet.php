<?php
/**
 * ================================================================
 * HARIYALIBASKET - AUTO INVOICE ON ORDER PLACEMENT
 * ================================================================
 *
 * INSTALLATION:
 * Is poori file ka content (sirf neeche wala HTML+JS+CSS block)
 * apne main PHP template mein paste karo, BILKUL NEECHE jahan ye line hai:
 *
 *     <?php wp_footer(); ?>
 *
 * USE THIS BEFORE THAT LINE.
 *
 * KYA HOTA HAI:
 * - Customer jab "Order on WhatsApp" dabayega
 * - Pehle data Google Sheet mein save hoga (already existing)
 * - Phir WhatsApp khulega (already existing)
 * - SAATH HI Invoice Modal show hoga - same HariyaliBasket format mein
 * - Customer Print / PDF / Save Image kar sakta hai
 * - Aapke paas bhi record rahega
 * ================================================================
 */
?>

<!-- ============================================================
     INVOICE AUTO-GENERATION MODAL + SCRIPT
     (Paste this entire block at the bottom of your template)
     ============================================================ -->

<div id="hb-invoice-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.85);z-index:999999;overflow-y:auto;padding:20px 0">
  <div id="hb-invoice-box" style="max-width:480px;margin:0 auto;background:#fff;border-radius:18px;overflow:hidden;box-shadow:0 20px 80px rgba(0,0,0,0.5);font-family:'Segoe UI',Arial,sans-serif">

    <!-- Header -->
    <div style="background:linear-gradient(135deg,#1a4d2e,#0f2d18);color:#fff;padding:22px 20px;text-align:center;position:relative">
      <button onclick="hbCloseInvoice()" class="no-print" style="position:absolute;top:12px;right:14px;background:rgba(255,255,255,0.15);border:none;color:#fff;width:30px;height:30px;border-radius:50%;font-size:16px;cursor:pointer;line-height:1">&#10005;</button>
      <div style="font-size:34px;line-height:1">&#127807;</div>
      <div style="font-size:22px;font-weight:900;letter-spacing:0.5px;margin-top:4px">Hariyali<span style="color:#f4a228">Basket</span></div>
      <div style="font-size:9px;color:#8ed46a;letter-spacing:2.5px;margin-top:2px">FARM TO DOORSTEP</div>
      <div style="font-size:10px;color:#d4ffea;margin-top:6px">&#128241; +91 80003 44554 &middot; Jaipur</div>
    </div>

    <!-- Bill Title -->
    <div style="background:#f4a228;color:#fff;padding:9px 20px;display:flex;justify-content:space-between;align-items:center;font-size:13px;font-weight:800;letter-spacing:0.5px">
      <span>&#129534; INVOICE / BILL</span>
      <span id="hbi-billno">HB-00000</span>
    </div>

    <!-- Success message -->
    <div style="background:#e8f8ee;color:#1a4d2e;padding:10px 20px;text-align:center;font-size:12px;font-weight:700;border-bottom:1px dashed #b2dfdb">
      &#9989; Aapka order successfully place ho gaya!
    </div>

    <!-- Meta -->
    <div style="padding:12px 20px;border-bottom:1px dashed #ddd;display:grid;grid-template-columns:1fr 1fr;gap:6px;font-size:11px;color:#444">
      <div><b style="color:#1a4d2e">Date:</b> <span id="hbi-date">&mdash;</span></div>
      <div style="text-align:right"><b style="color:#1a4d2e">Time:</b> <span id="hbi-time">&mdash;</span></div>
    </div>

    <!-- Customer -->
    <div style="padding:12px 20px;background:#f9fff9;border-bottom:1px dashed #ddd">
      <div style="font-size:10px;color:#888;font-weight:700;letter-spacing:1px">&#128666; DELIVER TO</div>
      <div style="font-size:15px;font-weight:800;color:#1a4d2e;margin-top:4px" id="hbi-name">&mdash;</div>
      <div style="font-size:11px;color:#555;margin-top:3px;line-height:1.5" id="hbi-address">&mdash;</div>
      <div style="font-size:11px;color:#555;margin-top:3px">&#128222; <span id="hbi-phone">&mdash;</span></div>
    </div>

    <!-- Items Table -->
    <table style="width:100%;border-collapse:collapse;font-size:11px">
      <thead style="background:#1a4d2e;color:#fff">
        <tr>
          <th style="padding:8px 10px;text-align:left;font-size:10px;font-weight:700">#</th>
          <th style="padding:8px 10px;text-align:left;font-size:10px;font-weight:700">Item</th>
          <th style="padding:8px 10px;text-align:right;font-size:10px;font-weight:700">Qty</th>
          <th style="padding:8px 10px;text-align:right;font-size:10px;font-weight:700">Rate</th>
          <th style="padding:8px 10px;text-align:right;font-size:10px;font-weight:700">Amount</th>
        </tr>
      </thead>
      <tbody id="hbi-items"></tbody>
    </table>

    <!-- Totals -->
    <div style="padding:12px 20px;background:#fff">
      <div style="display:flex;justify-content:space-between;padding:5px 0;font-size:12px;color:#444">
        <span>Subtotal</span><span id="hbi-subtotal">&#8377;0</span>
      </div>
      <div style="display:flex;justify-content:space-between;padding:5px 0;font-size:12px;color:#444">
        <span>&#128666; Delivery</span><span id="hbi-delivery">&#8377;0</span>
      </div>
      <div style="display:flex;justify-content:space-between;margin-top:6px;padding:10px 0 6px;border-top:2px solid #1a4d2e;font-size:17px;font-weight:900;color:#1a4d2e">
        <span>GRAND TOTAL</span><span id="hbi-total" style="color:#f4a228">&#8377;0</span>
      </div>
      <div id="hbi-savings" style="display:none;background:#fff8e1;border:1px solid #f4a228;border-radius:8px;padding:8px 10px;margin-top:10px;font-size:11px;color:#b36000;font-weight:700;text-align:center"></div>
    </div>

    <!-- Payment -->
    <div style="padding:10px 20px;background:#f0fff4;font-size:11px;color:#1a4d2e;border-top:1px dashed #ccc">
      <b>&#128179; Payment:</b> <span id="hbi-pay">&mdash;</span>
      <div id="hbi-txn-row" style="display:none;margin-top:3px"><b>&#128273; Txn ID:</b> <span id="hbi-txn"></span></div>
    </div>

    <!-- Footer -->
    <div style="background:#1a4d2e;color:#fff;padding:14px 20px;text-align:center;font-size:10px;line-height:1.7">
      <div style="font-size:13px;font-weight:800;color:#f4a228;margin-bottom:3px">&#128591; Dhanyawaad! Aapka Order Mil Gaya!</div>
      <div>&#128230; Kal 4 PM tak delivery hogi</div>
      <div>&#127807; 100% Farm Fresh Guarantee &middot; Free Replacement</div>
      <div>WhatsApp: +91 80003 44554 &middot; UPI: imanohar07773@ybl</div>
    </div>

    <!-- Action Buttons -->
    <div class="no-print" style="padding:14px 16px 16px;display:grid;grid-template-columns:1fr 1fr;gap:8px;background:#fafafa">
      <button onclick="hbPrintInvoice()" style="background:#1a4d2e;color:#fff;border:none;padding:12px;border-radius:10px;font-size:12px;font-weight:800;cursor:pointer;font-family:inherit">&#128424; Print / PDF</button>
      <button onclick="hbShareInvoice()" style="background:#25d366;color:#fff;border:none;padding:12px;border-radius:10px;font-size:12px;font-weight:800;cursor:pointer;font-family:inherit">&#128241; WhatsApp Bhejo</button>
      <button onclick="hbCloseInvoice(true)" style="grid-column:1/-1;background:#f4a228;color:#fff;border:none;padding:11px;border-radius:10px;font-size:12px;font-weight:800;cursor:pointer;font-family:inherit">&#9989; OK &middot; Naya Order</button>
    </div>

  </div>
</div>

<style>
@media print {
  body * { visibility: hidden !important; }
  #hb-invoice-modal,
  #hb-invoice-modal * { visibility: visible !important; }
  #hb-invoice-modal {
    position: absolute !important;
    inset: 0 !important;
    background: #fff !important;
    padding: 0 !important;
  }
  #hb-invoice-box {
    box-shadow: none !important;
    border-radius: 0 !important;
    margin: 0 !important;
    max-width: 100% !important;
  }
  #hb-invoice-modal .no-print { display: none !important; }
}
@keyframes hb-inv-pop {
  0% { transform: scale(0.85); opacity: 0; }
  100% { transform: scale(1); opacity: 1; }
}
#hb-invoice-modal[data-show="1"] #hb-invoice-box {
  animation: hb-inv-pop 0.35s ease;
}
</style>

<script>
(function() {
  'use strict';

  /* ---- BILL NUMBER GENERATOR ---- */
  function nextBillNo() {
    var d = new Date();
    var ymd = d.getFullYear().toString().slice(-2) +
              String(d.getMonth() + 1).padStart(2, '0') +
              String(d.getDate()).padStart(2, '0');
    var rand = String(Math.floor(Math.random() * 900) + 100);
    return 'HB-' + ymd + '-' + rand;
  }

  /* ---- COLLECT ORDER DATA FROM CURRENT FORM + CART ---- */
  function collectOrderData() {
    var $ = function(id) { return document.getElementById(id); };

    var name    = ($('o-name')    || {}).value || '';
    var phone   = ($('o-phone')   || {}).value || '';
    var society = ($('o-society') || {}).value || '';
    var block   = ($('o-block')   || {}).value || '';
    var flat    = ($('o-flat')    || {}).value || '';
    var txnId   = ($('o-txn')     || {}).value || '';

    name = name.trim(); phone = phone.trim(); society = society.trim();
    block = block.trim(); flat = flat.trim(); txnId = txnId.trim();

    if (!name || !society || !phone) return null;
    if (!/^[6-9]\d{9}$/.test(phone))  return null;
    if (typeof cart === 'undefined' || !Object.keys(cart).length) return null;
    if (typeof payMethod !== 'undefined' && payMethod === 'upi' && !txnId) return null;

    var items = [];
    var subtotal = 0;
    var savings = 0;

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

    return {
      billno:   nextBillNo(),
      name:     name,
      phone:    phone,
      society:  society,
      block:    block,
      flat:     flat,
      txnId:    txnId,
      payMethod: (typeof payMethod !== 'undefined' && payMethod === 'upi') ? 'UPI / Online' : 'Cash on Delivery',
      items:    items,
      subtotal: subtotal,
      delivery: delivery,
      total:    total,
      savings:  savings
    };
  }

  /* ---- SHOW INVOICE MODAL ---- */
  function showInvoice(data) {
    var $ = function(id) { return document.getElementById(id); };
    var d = new Date();

    $('hbi-billno').textContent = data.billno;
    $('hbi-date').textContent   = d.toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' });
    $('hbi-time').textContent   = d.toLocaleTimeString('en-IN', { hour: '2-digit', minute: '2-digit', hour12: true });
    $('hbi-name').textContent   = data.name;
    $('hbi-phone').textContent  = data.phone;

    var addr = [data.society, data.block ? 'Block ' + data.block : '', data.flat ? 'Flat ' + data.flat : '']
                 .filter(Boolean).join(' \u00b7 ');
    $('hbi-address').textContent = addr || '\u2014';

    $('hbi-pay').textContent = data.payMethod;
    if (data.txnId) {
      $('hbi-txn').textContent       = data.txnId;
      $('hbi-txn-row').style.display = 'block';
    } else {
      $('hbi-txn-row').style.display = 'none';
    }

    $('hbi-items').innerHTML = data.items.map(function(it, i) {
      return '<tr>' +
        '<td style="padding:8px 10px;border-bottom:1px solid #eee">' + (i + 1) + '</td>' +
        '<td style="padding:8px 10px;border-bottom:1px solid #eee;font-weight:600;color:#1a4d2e">' + it.name + '</td>' +
        '<td style="padding:8px 10px;border-bottom:1px solid #eee;text-align:right">' + it.qty + ' ' + it.uom + '</td>' +
        '<td style="padding:8px 10px;border-bottom:1px solid #eee;text-align:right">\u20b9' + it.rate + '</td>' +
        '<td style="padding:8px 10px;border-bottom:1px solid #eee;text-align:right;font-weight:700">\u20b9' + it.amount + '</td>' +
        '</tr>';
    }).join('');

    $('hbi-subtotal').textContent = '\u20b9' + data.subtotal;
    $('hbi-delivery').textContent = data.delivery > 0 ? '\u20b9' + data.delivery : 'FREE';
    $('hbi-total').textContent    = '\u20b9' + data.total;

    var savingsTxts = [];
    if (data.savings > 0)    savingsTxts.push('\ud83d\udcb0 \u20b9' + data.savings + ' bachaye');
    if (data.delivery === 0) savingsTxts.push('\ud83c\udf89 FREE Delivery');
    var sEl = $('hbi-savings');
    if (savingsTxts.length) {
      sEl.textContent   = savingsTxts.join(' \u00b7 ');
      sEl.style.display = 'block';
    } else {
      sEl.style.display = 'none';
    }

    var modal = $('hb-invoice-modal');
    modal.style.display = 'block';
    modal.setAttribute('data-show', '1');
    document.body.style.overflow = 'hidden';

    // Save last invoice for share/print
    window.__hbLastInvoice = data;
  }

  /* ---- CLOSE INVOICE ---- */
  window.hbCloseInvoice = function(clearCartFlag) {
    var modal = document.getElementById('hb-invoice-modal');
    if (!modal) return;
    modal.style.display = 'none';
    modal.removeAttribute('data-show');
    document.body.style.overflow = '';
    if (clearCartFlag && typeof window.clearCart === 'function') {
      window.clearCart();
    }
  };

  /* ---- PRINT INVOICE ---- */
  window.hbPrintInvoice = function() {
    window.print();
  };

  /* ---- SEND INVOICE ON WHATSAPP ---- */
  window.hbShareInvoice = function() {
    var d = window.__hbLastInvoice;
    if (!d) return;
    var msg = '\ud83c\udf3f *HariyaliBasket \u2014 Bill ' + d.billno + '*\n\n';
    msg += '\ud83d\udc64 ' + d.name + '\n';
    msg += '\ud83d\udcde ' + d.phone + '\n';
    var addr = [d.society, d.block ? 'Block ' + d.block : '', d.flat ? 'Flat ' + d.flat : ''].filter(Boolean).join(' \u00b7 ');
    if (addr) msg += '\ud83c\udfd8\ufe0f ' + addr + '\n';
    msg += '\n*Items:*\n';
    d.items.forEach(function(it, i) {
      msg += (i + 1) + '. ' + it.name + ' \u2014 ' + it.qty + ' ' + it.uom + ' \u00d7 \u20b9' + it.rate + ' = \u20b9' + it.amount + '\n';
    });
    msg += '\nSubtotal: \u20b9' + d.subtotal + '\n';
    msg += 'Delivery: ' + (d.delivery > 0 ? '\u20b9' + d.delivery : 'FREE') + '\n';
    msg += '*Grand Total: \u20b9' + d.total + '*\n';
    msg += '\ud83d\udcb3 ' + d.payMethod + (d.txnId ? ' (TXN: ' + d.txnId + ')' : '') + '\n';
    if (d.savings > 0) msg += '\ud83d\udcb0 Aapne \u20b9' + d.savings + ' bachaye!\n';
    msg += '\n\ud83d\udce6 Kal 4 PM tak delivery\n\ud83d\ude4f Dhanyawaad!';

    var biz  = (typeof WA !== 'undefined') ? WA : '918000344554';
    window.open('https://wa.me/' + biz + '?text=' + encodeURIComponent(msg), '_blank');
  };

  /* ---- HOOK INTO EXISTING sendWA() ---- */
  function attachInvoiceHook() {
    if (typeof window.sendWA !== 'function') {
      // Try again later - sendWA may not be defined yet
      return setTimeout(attachInvoiceHook, 300);
    }
    if (window.__hbInvoiceHooked) return;
    window.__hbInvoiceHooked = true;

    var _orig = window.sendWA;
    window.sendWA = function() {
      // Snapshot data BEFORE original (because original may close form / clear cart)
      var data = collectOrderData();

      // Call original (validates, sends to Sheet, opens WhatsApp, closes order modal)
      _orig.apply(this, arguments);

      // If validation passed (data exists), show invoice after WA opens
      if (data) {
        setTimeout(function() {
          showInvoice(data);
        }, 500);
      }
    };
  }

  // Wait for DOM + scripts to load, then hook
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() { setTimeout(attachInvoiceHook, 800); });
  } else {
    setTimeout(attachInvoiceHook, 800);
  }

})();
</script>

<!-- ============================================================
     END OF INVOICE AUTO-GENERATION SNIPPET
     ============================================================ -->
