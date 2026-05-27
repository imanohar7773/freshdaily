/* ═══ CART — add/remove/persistence/meter ═══ */
(function(){
'use strict';

var $ = HBUtils.$;
var App = HBApp;

window.HBCart = {

  totals: function() {
    var t = 0, m = 0, s = 0;
    Object.keys(App.cart).forEach(function(k){
      var info = HBProducts.getInfo(k);
      if (!info) return;
      var q = App.cart[k];
      t += q * info.sp;
      m += q * info.mrp;
      s += q * Math.max(0, info.mrp - info.sp);
    });
    var minFree = (window.HB && HB.minFree) || 199;
    var fee = (window.HB && HB.deliveryFee) || 69;
    var del = (t === 0 || t >= minFree) ? 0 : fee;
    return { total: t, mrp: m, savings: s, delivery: del, grand: t + del };
  },

  chg: function(key, d) {
    var prev = App.cart[key] || 0;
    var nxt = Math.max(0, prev + d);
    if (nxt === 0) delete App.cart[key];
    else App.cart[key] = nxt;
    HBUtils.storage.set(App.CART_KEY, App.cart);
    HBProducts.render();
    HBCart.updateBar();
    if (HBCart._drawerOpen) HBCart.renderDrawer();

    // Trigger animation hooks
    if (d > 0 && nxt > prev && window.HBExtras && HBExtras.onAdd) {
      HBExtras.onAdd(key);
    }
  },

  clear: function() {
    App.cart = {};
    HBUtils.storage.remove(App.CART_KEY);
    HBProducts.render();
    HBCart.updateBar();
    HBCart.renderDrawer();
    HBCart.toggleDrawer(false);
    HBUtils.toast('Cart clear ho gaya 🛒');
  },

  updateBar: function() {
    var t = HBCart.totals();
    var cnt = 0;
    Object.keys(App.cart).forEach(function(k){ cnt += App.cart[k]; });
    var bar = $('hb-cart-bar');
    if (cnt > 0) bar.classList.add('show');
    else bar.classList.remove('show');

    $('hb-cart-items-txt').textContent = cnt + ' item' + (cnt !== 1 ? 's' : '');
    $('hb-cart-total-txt').textContent = '₹' + t.grand;

    // Header badge
    var hb = $('hb-hdr-badge');
    if (hb) {
      hb.textContent = cnt;
      if (cnt > 0) hb.classList.add('show');
      else hb.classList.remove('show');
    }
    // Bottom nav badge
    var bn = $('hb-bn-cart-badge');
    if (bn) {
      bn.textContent = cnt;
      if (cnt > 0) bn.classList.add('show');
      else bn.classList.remove('show');
    }

    // Meter
    var minFree = (window.HB && HB.minFree) || 199;
    var rem = Math.max(0, minFree - t.total);
    var pct = Math.min(100, Math.round(t.total / minFree * 100));
    $('hb-meter-txt').textContent = rem > 0
      ? '🚚 ₹' + rem + ' aur → FREE delivery!'
      : '🎉 Free Delivery unlock!';
    $('hb-meter-pct').textContent = pct + '%';
    $('hb-meter-fill').style.width = pct + '%';
  },

  toggleDrawer: function(force) {
    var d = $('hb-cart-drawer');
    var open = (typeof force === 'boolean') ? force : !HBCart._drawerOpen;
    HBCart._drawerOpen = open;
    d.style.display = open ? 'block' : 'none';
    if (open) HBCart.renderDrawer();
  },

  renderDrawer: function() {
    var keys = Object.keys(App.cart);
    var el = $('hb-cart-items');
    if (!keys.length) {
      el.innerHTML = '<div style="text-align:center;padding:30px;color:#8ed46a"><div style="font-size:42px">🧺</div><p style="margin-top:10px">Cart khaali hai!</p><small style="color:rgba(255,255,255,.5)">Upar se fresh sabzi add karo 🌿</small></div>';
      return;
    }
    el.innerHTML = keys.map(function(k){
      var info = HBProducts.getInfo(k);
      if (!info) return '';
      var qty = App.cart[k];
      var amt = qty * info.sp;
      return '<div class="hb-cart-row-item">' +
        '<div class="hb-cri-img">' + HBUtils.emoji(info.p.name) + '</div>' +
        '<div class="hb-cri-info">' +
          '<div class="hb-cri-name">' + HBUtils.esc(info.label) + '</div>' +
          '<div class="hb-cri-meta">₹' + info.sp + ' / ' + HBUtils.esc(info.uom) + '</div>' +
        '</div>' +
        '<div class="hb-cri-ctrl">' +
          '<button class="hb-cri-btn hb-cri-minus" data-act="cri-chg" data-key="' + k + '" data-d="-1">−</button>' +
          '<span class="hb-cri-qty">' + qty + '</span>' +
          '<button class="hb-cri-btn hb-cri-plus" data-act="cri-chg" data-key="' + k + '" data-d="1">+</button>' +
        '</div>' +
        '<div class="hb-cri-amt">₹' + amt + '</div>' +
      '</div>';
    }).join('');

    // Delegate
    el.onclick = function(e) {
      var t = e.target.closest('[data-act="cri-chg"]');
      if (!t) return;
      HBCart.chg(t.getAttribute('data-key'), parseInt(t.getAttribute('data-d')));
    };
  },

  load: function() {
    var saved = HBUtils.storage.get(App.CART_KEY);
    if (saved && typeof saved === 'object') Object.assign(App.cart, saved);
    HBProducts.render();
    HBCart.updateBar();
  }
};

// Init on load
if (document.readyState !== 'loading') HBCart.load();
else document.addEventListener('DOMContentLoaded', HBCart.load);

})();
