/* ═══ CHECKOUT — 3-step wizard, address, payment, captcha, order placement ═══ */
(function(){
'use strict';

var $ = HBUtils.$;
var App = HBApp;

// ─── ADDRESS ──────────────────────────────────
window.HBAddress = {
  selSoc: '',
  ddOpen: false,

  customSocs: function() {
    return HBUtils.storage.get(App.CSOC_KEY) || [];
  },

  allSocs: function() {
    var defaults = window.HB_SOCIETIES || ['Hanging Garden'];
    return defaults.concat(HBAddress.customSocs());
  },

  loadSaved: function() {
    var d = HBUtils.storage.get(App.ADDR_KEY);
    if (!d || !d.name) return;
    App.savedAddr = d;
    var el = $('hb-saved-addr');
    var tx = $('hb-saved-addr-text');
    tx.innerHTML = '<b>' + HBUtils.esc(d.name) + '</b>' +
                    (d.soc ? ' · ' + HBUtils.esc(d.soc) : '') +
                    (d.phone ? '<br>📞 ' + HBUtils.esc(d.phone) : '');
    el.classList.add('show');
    $('hb-addr-form').style.display = 'none';
  },

  edit: function() {
    $('hb-saved-addr').classList.remove('show');
    $('hb-addr-form').style.display = 'block';
    if (App.savedAddr) {
      var s = App.savedAddr;
      $('hb-f-name').value  = s.name  || '';
      $('hb-f-soc').value   = s.soc   || '';
      $('hb-f-block').value = s.block || '';
      $('hb-f-flat').value  = s.flat  || '';
      $('hb-f-phone').value = s.phone || '';
      HBAddress.selSoc = s.soc || '';
    }
  },

  toggleDD: function() {
    HBAddress.ddOpen = !HBAddress.ddOpen;
    var dd = $('hb-soc-dd');
    if (HBAddress.ddOpen) {
      dd.classList.add('show');
      HBAddress.renderList('');
      setTimeout(function(){ $('hb-soc-q').focus(); }, 60);
    } else {
      dd.classList.remove('show');
    }
  },

  renderList: function(q) {
    var el = $('hb-soc-list');
    var all = HBAddress.allSocs();
    var ql = (q || '').toLowerCase();
    var fil = ql ? all.filter(function(s){ return s.toLowerCase().indexOf(ql) !== -1; }) : all;
    if (!fil.length) {
      el.innerHTML = '<div style="padding:14px;font-size:12px;color:#999;text-align:center">Not found</div>';
      return;
    }
    el.innerHTML = fil.map(function(s){
      var sel = (s === HBAddress.selSoc) ? ' sel' : '';
      var safe = HBUtils.esc(s);
      return '<div class="hb-soc-item' + sel + '" data-name="' + safe + '">' +
        (sel ? '✅' : '🏘️') + ' ' + safe + '</div>';
    }).join('');

    el.onclick = function(e) {
      var item = e.target.closest('.hb-soc-item');
      if (!item) return;
      HBAddress.pick(item.getAttribute('data-name'));
    };
  },

  pick: function(name) {
    HBAddress.selSoc = name;
    $('hb-f-soc').value = name;
    $('hb-soc-dd').classList.remove('show');
    HBAddress.ddOpen = false;
  },

  addCustom: function() {
    var n = prompt('Apni Society / Colony ka naam:');
    if (n && n.trim()) {
      var c = HBAddress.customSocs();
      if (c.indexOf(n.trim()) === -1) {
        c.push(n.trim());
        HBUtils.storage.set(App.CSOC_KEY, c);
      }
      HBAddress.pick(n.trim());
    }
  },

  validate: function() {
    var afShow = $('hb-addr-form').style.display !== 'none';
    var name, phone, soc, block, flat;
    if (!afShow && App.savedAddr && App.savedAddr.name) {
      name = App.savedAddr.name; phone = App.savedAddr.phone; soc = App.savedAddr.soc;
      block = App.savedAddr.block || ''; flat = App.savedAddr.flat || '';
    } else {
      name = $('hb-f-name').value.trim();
      phone = $('hb-f-phone').value.trim();
      soc = $('hb-f-soc').value.trim();
      block = $('hb-f-block').value.trim();
      flat = $('hb-f-flat').value.trim();
    }
    if (!name) { HBUtils.toast('Apna naam enter karo ⚠️'); return; }
    if (!soc) { HBUtils.toast('Society / Colony select karo ⚠️'); return; }
    if (!phone || !/^[6-9]\d{9}$/.test(phone)) {
      HBUtils.toast('Valid 10-digit mobile number chahiye ⚠️');
      return;
    }
    if ($('hb-save-chk').checked) {
      HBUtils.storage.set(App.ADDR_KEY, { name:name, phone:phone, soc:soc, block:block, flat:flat });
      App.savedAddr = { name:name, phone:phone, soc:soc, block:block, flat:flat };
    }
    HBCheckout.go(3);
  }
};

// Wire society search
document.addEventListener('input', function(e){
  if (e.target && e.target.id === 'hb-soc-q') HBAddress.renderList(e.target.value);
});
// Outside click closes dropdown
document.addEventListener('click', function(e){
  if (!HBAddress.ddOpen) return;
  var dd = $('hb-soc-dd');
  var soc = $('hb-f-soc');
  if (dd && !dd.contains(e.target) && e.target !== soc) {
    dd.classList.remove('show');
    HBAddress.ddOpen = false;
  }
});

// ─── CHECKOUT ──────────────────────────────────
var STEP_TITLES = { 1: '🛒 Your Cart', 2: '📍 Delivery Address', 3: '💳 Payment' };

window.HBCheckout = {
  open: function() {
    if (!Object.keys(App.cart).length) {
      HBUtils.toast('Pehle kuch items add karo! 🛒');
      return;
    }
    HBCheckout.go(1);
    $('hb-overlay').style.display = 'block';
    var sh = $('hb-checkout-sheet');
    sh.style.display = 'block';
    setTimeout(function(){ sh.classList.add('open'); }, 10);
    document.body.style.overflow = 'hidden';
    HBCheckout.renderCart();
    HBAddress.loadSaved();
    HBCheckout.refreshCaptcha();
  },

  close: function() {
    var sh = $('hb-checkout-sheet');
    sh.classList.remove('open');
    setTimeout(function(){
      sh.style.display = 'none';
      $('hb-overlay').style.display = 'none';
    }, 400);
    document.body.style.overflow = '';
  },

  go: function(s) {
    [1,2,3].forEach(function(i){
      var sc = $('hb-sc' + i);
      var sl = $('hb-sl' + i);
      var pn = $('hb-panel' + i);
      var ft = $('hb-foot' + i);
      var ln = $('hb-sline' + i);
      if (i < s) {
        sc.className = 'hb-step-circle done';
        sc.textContent = '✓';
        sl.className = 'hb-step-label';
        if (ln) ln.classList.add('done');
      } else if (i === s) {
        sc.className = 'hb-step-circle active';
        sc.textContent = String(i);
        sl.className = 'hb-step-label active';
        if (ln) ln.classList.remove('done');
      } else {
        sc.className = 'hb-step-circle';
        sc.textContent = String(i);
        sl.className = 'hb-step-label';
        if (ln) ln.classList.remove('done');
      }
      pn.className = 'hb-step-panel' + (i === s ? ' active' : '');
      ft.className = 'hb-sheet-footer' + (i === s ? ' show' : '');
    });
    $('hb-sheet-title').textContent = STEP_TITLES[s];
    $('hb-checkout-sheet').scrollTop = 0;
  },

  renderCart: function() {
    var el = $('hb-co-items');
    var keys = Object.keys(App.cart);
    if (!keys.length) {
      el.innerHTML = '<div style="text-align:center;padding:30px;color:#9ca3af">Cart is empty 🌿</div>';
      return;
    }
    el.innerHTML = keys.map(function(k){
      var info = HBProducts.getInfo(k);
      if (!info) return '';
      var qty = App.cart[k];
      var amt = qty * info.sp;
      return '<div class="hb-co-item">' +
        '<div class="hb-co-img">' + HBUtils.emoji(info.p.name) + '</div>' +
        '<div class="hb-co-info"><div class="hb-co-name">' + HBUtils.esc(info.label) + '</div><div class="hb-co-meta">per ' + HBUtils.esc(info.uom) + ' · ₹' + info.sp + '</div></div>' +
        '<div class="hb-co-ctrl">' +
          '<button class="hb-co-btn hb-co-minus" data-act="co-chg" data-key="' + k + '" data-d="-1">−</button>' +
          '<div class="hb-co-qty">' + qty + '</div>' +
          '<button class="hb-co-btn hb-co-plus" data-act="co-chg" data-key="' + k + '" data-d="1">+</button>' +
        '</div>' +
        '<div class="hb-co-price">₹' + amt + '</div>' +
      '</div>';
    }).join('');

    el.onclick = function(e) {
      var t = e.target.closest('[data-act="co-chg"]');
      if (!t) return;
      HBCart.chg(t.getAttribute('data-key'), parseInt(t.getAttribute('data-d')));
      HBCheckout.renderCart();
    };

    var t = HBCart.totals();
    $('hb-b-mrp').textContent = '₹' + t.mrp;
    $('hb-b-save').textContent = t.savings > 0 ? '−₹' + t.savings : '₹0';
    $('hb-b-del').textContent = t.delivery === 0 ? 'FREE 🎉' : '₹' + t.delivery;
    $('hb-b-grand').textContent = '₹' + t.grand;
    $('hb-mini-count').textContent = keys.length + ' item' + (keys.length !== 1 ? 's' : '');
    $('hb-mini-total').textContent = '₹' + t.grand;

    if (t.delivery === 0 && t.total > 0 && !HBCheckout._confettiFired) {
      HBCheckout._confettiFired = true;
      if (window.HBExtras && HBExtras.confetti) HBExtras.confetti();
    }
    if (t.total === 0) HBCheckout._confettiFired = false;
  },

  pickPay: function(m) {
    App.payMethod = m;
    var isCOD = m === 'cod';
    $('hb-pay-cod').className = 'hb-pay-card' + (isCOD ? ' selected' : '');
    $('hb-pay-upi').className = 'hb-pay-card' + (!isCOD ? ' selected' : '');
    var rcod = $('hb-radio-cod'); var rupi = $('hb-radio-upi');
    rcod.className = 'hb-pay-radio' + (isCOD ? ' on' : '');
    rcod.innerHTML = isCOD ? '<div class="hb-pay-dot"></div>' : '';
    rupi.className = 'hb-pay-radio' + (!isCOD ? ' on' : '');
    rupi.innerHTML = !isCOD ? '<div class="hb-pay-dot"></div>' : '';
    $('hb-upi-expand').classList.toggle('show', !isCOD);
  },

  copyUPI: function() {
    var upi = (window.HB && HB.upi) || 'imanohar07773@ybl';
    var fall = function(){
      var t = document.createElement('textarea'); t.value = upi;
      document.body.appendChild(t); t.select(); document.execCommand('copy'); t.remove();
      HBUtils.toast('UPI ID copied! 📋');
    };
    if (navigator.clipboard) navigator.clipboard.writeText(upi).then(function(){ HBUtils.toast('UPI ID copied! 📋'); }).catch(fall);
    else fall();
  },

  refreshCaptcha: function() {
    // BUG FIX #2: Real server-side CAPTCHA via AJAX (token-based, prevents bot bypass)
    var qEl = $('hb-cap-q');
    var inp = $('hb-cap-v');
    if (qEl) qEl.textContent = '⏳ Loading...';
    if (inp) inp.value = '';
    App.captcha = null; // invalidate any prior token

    var ajaxUrl = (window.HB && HB.ajaxUrl) || '/wp-admin/admin-ajax.php';
    if (ajaxUrl === '#preview-mode') {
      // Preview mode — fall back to client-side display (no real verification)
      var a = Math.floor(Math.random() * 9) + 1;
      var b = Math.floor(Math.random() * 9) + 1;
      App.captcha = { token: '__preview__', expectedSum: a + b };
      if (qEl) qEl.textContent = a + ' + ' + b + ' = ?';
      return;
    }

    fetch(ajaxUrl + '?action=hb_captcha', { credentials: 'same-origin' })
      .then(function(r){ return r.json(); })
      .then(function(res){
        if (res && res.success && res.data && res.data.token) {
          App.captcha = { token: res.data.token };
          if (qEl) qEl.textContent = res.data.question;
        } else {
          if (qEl) qEl.textContent = 'CAPTCHA load failed — refresh';
        }
      })
      .catch(function(){
        if (qEl) qEl.textContent = 'CAPTCHA load failed — refresh';
      });
  },

  place: function() {
    if (App.payMethod === 'upi' && !$('hb-f-txn').value.trim()) {
      HBUtils.toast('UPI Transaction ID enter karo ⚠️');
      return;
    }
    // CAPTCHA — basic client-side sanity check (real check on server)
    var capV = parseInt($('hb-cap-v').value);
    if (!App.captcha || !App.captcha.token || isNaN(capV)) {
      HBUtils.toast('CAPTCHA answer enter karo ⚠️');
      $('hb-cap-v').focus();
      return;
    }
    // Preview mode — verify locally
    if (App.captcha.token === '__preview__' && App.captcha.expectedSum !== capV) {
      HBUtils.toast('CAPTCHA galat hai. Math check karo ⚠️');
      HBCheckout.refreshCaptcha();
      return;
    }

    var btn = $('hb-place-btn');
    var btxt = $('hb-place-btn-txt');
    btn.disabled = true;
    btn.classList.add('hb-btn-loading');
    btxt.textContent = '⏳ Placing Order...';

    // Build order
    var afShow = $('hb-addr-form').style.display !== 'none';
    var name, phone, soc, block, flat;
    if (!afShow && App.savedAddr && App.savedAddr.name) {
      name = App.savedAddr.name; phone = App.savedAddr.phone; soc = App.savedAddr.soc;
      block = App.savedAddr.block || ''; flat = App.savedAddr.flat || '';
    } else {
      name = $('hb-f-name').value.trim();
      phone = $('hb-f-phone').value.trim();
      soc = $('hb-f-soc').value.trim();
      block = $('hb-f-block').value.trim();
      flat = $('hb-f-flat').value.trim();
    }
    var items = Object.keys(App.cart).map(function(k){
      var info = HBProducts.getInfo(k);
      if (!info) return null;
      var q = App.cart[k];
      return {
        key: k, pid: info.p.id,
        name: info.label, qty: q, sp: info.sp,
        amount: q * info.sp, uom: info.uom
      };
    }).filter(Boolean);
    var t = HBCart.totals();

    // Submit to WordPress AJAX (saves to hb_order CPT)
    var formData = new FormData();
    formData.append('action', 'hb_place_order');
    formData.append('nonce', (window.HB && HB.nonce) || '');
    formData.append('name', name);
    formData.append('phone', phone);
    formData.append('society', soc);
    formData.append('block', block);
    formData.append('flat', flat);
    formData.append('payment', App.payMethod);
    formData.append('txn', $('hb-f-txn').value.trim());
    formData.append('items', JSON.stringify(items));
    formData.append('total', t.grand);
    formData.append('captcha_token', App.captcha.token);
    formData.append('captcha_answer', capV);

    var ajaxUrl = (window.HB && HB.ajaxUrl) || '/wp-admin/admin-ajax.php';

    fetch(ajaxUrl, { method: 'POST', body: formData, credentials: 'same-origin' })
      .then(function(r){
        if (!r.ok && r.status !== 200) throw new Error('HTTP ' + r.status);
        return r.json();
      })
      .then(function(res){
        btn.disabled = false;
        btn.classList.remove('hb-btn-loading');
        btxt.textContent = '🎉 Place Order';

        if (res && res.success && res.data && res.data.order_id) {
          // SUCCESS — real server-saved order
          App.orderId = res.data.order_id;
          // Save snapshot for repeat-order feature
          HBUtils.storage.set(App.LASTORDER_KEY, items.map(function(i){
            return { key: i.key, qty: i.qty, name: i.name };
          }));
          HBCheckout._afterPlace(name, phone, soc, block, flat, t, items);
        } else {
          // BUG FIX #6: Server-side validation failed — DO NOT fake an order ID
          var msg = (res && res.data && res.data.msg) ? res.data.msg : 'Order submit nahi hua. Try again.';
          HBUtils.toast(msg, 4500);
          if (res && res.data && res.data.captcha_failed) {
            HBCheckout.refreshCaptcha();
          }
          // Stay on payment step so user can fix and retry
        }
      })
      .catch(function(err){
        // Network/parse failure — show error, allow retry
        btn.disabled = false;
        btn.classList.remove('hb-btn-loading');
        btxt.textContent = '🎉 Place Order';
        HBUtils.toast('Network issue — order submit nahi hua. Internet check karke retry karo.', 5000);
        // No fake order ID — order was NOT placed
      });
  },

  _afterPlace: function(name, phone, soc, block, flat, t, items) {
    // WhatsApp message
    var wa = (window.HB && HB.wa) || '918000344554';
    var itemStr = items.map(function(i){
      return '• ' + i.name + ' — ' + i.qty + ' × ₹' + i.sp + ' = ₹' + i.amount;
    }).join('\n');
    var msg = '🌿 *Naya Order — HariyaliBasket*\n\n' +
      '📋 *' + App.orderId + '*\n👤 ' + name + '\n📞 ' + phone +
      '\n🏘️ ' + soc + (block ? ' | Block ' + block : '') + (flat ? ' | Flat ' + flat : '') +
      '\n💳 ' + (App.payMethod === 'upi' ? 'UPI/Online' + ($('hb-f-txn').value.trim() ? ' | TXN: ' + $('hb-f-txn').value.trim() : '') : 'Cash on Delivery') +
      '\n\n🛒 Items:\n' + itemStr +
      '\n\n💰 *Total: ₹' + t.grand + '*\n📦 Kal 4 PM tak delivery.';
    App.waUrl = 'https://wa.me/' + wa + '?text=' + encodeURIComponent(msg);

    // Optional: send to Google Sheet
    var sheet = (window.HB && HB.sheetUrl);
    if (sheet) {
      try {
        fetch(sheet, {
          method: 'POST',
          mode: 'no-cors',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            order_id: App.orderId, name: name, phone: phone, society: soc, block: block, flat: flat,
            payment: App.payMethod, amount: '₹' + t.grand,
            items: items.map(function(i){ return i.name + ' x' + i.qty + ' = ₹' + i.amount; }).join(' | ')
          })
        }).catch(function(){});
      } catch(e) {}
    }

    HBCheckout.close();
    HBCheckout.showSuccess(name, phone, soc, block, flat, t);

    // Clear cart
    App.cart = {};
    HBUtils.storage.remove(App.CART_KEY);
    HBProducts.render();
    HBCart.updateBar();
    if (window.HBExtras && HBExtras.confetti) HBExtras.confetti();
  },

  showSuccess: function(name, phone, soc, block, flat, t) {
    $('hb-s-orderid').textContent = App.orderId;
    var rows = [
      ['👤', 'Customer', name],
      ['📍', 'Address', soc + (block ? ' · Block ' + block : '') + (flat ? ' · Flat ' + flat : '')],
      ['📞', 'Phone', phone],
      ['💳', 'Payment', App.payMethod === 'upi' ? 'UPI / Online' : 'Cash on Delivery'],
      ['💰', 'Amount', '₹' + t.grand],
      ['🚚', 'Delivery', 'Tomorrow before 4:00 PM']
    ];
    $('hb-s-details').innerHTML = rows.map(function(r){
      return '<div class="hb-detail-row"><div class="hb-d-icon">' + r[0] + '</div><div><div class="hb-d-label">' + HBUtils.esc(r[1]) + '</div><div class="hb-d-val">' + HBUtils.esc(r[2]) + '</div></div></div>';
    }).join('');
    $('hb-success-screen').style.display = 'block';
    document.body.style.overflow = 'hidden';
    var b = $('hb-wa-btn'); b.disabled = false;
    b.textContent = '💬 Also Notify on WhatsApp';
  },

  notifyWA: function() {
    if (App.waUrl) window.open(App.waUrl, '_blank');
    var b = $('hb-wa-btn');
    b.disabled = true;
    b.textContent = '✅ Owner Notified!';
  },

  closeSuccess: function() {
    $('hb-success-screen').style.display = 'none';
    document.body.style.overflow = '';
  },

  copyOrderId: function() {
    var id = App.orderId;
    var fall = function(){ HBUtils.toast('Order ID: ' + id); };
    if (navigator.clipboard) navigator.clipboard.writeText(id).then(function(){ HBUtils.toast('Order ID copied!'); }).catch(fall);
    else fall();
  }
};

})();
