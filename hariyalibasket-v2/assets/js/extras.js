/* ═══ EXTRAS — animations, leaves, confetti, fly-to-cart, countdown,
       trust bar, repeat order, pincode, wishlist, FAQ, nav drawer ═══ */
(function(){
'use strict';

var $ = HBUtils.$;
var App = HBApp;

// ─── COUNTDOWN ───────────────────────────────
function tickCountdown() {
  var hEl = $('hb-cd-h');
  if (!hEl) return;
  var now = new Date();
  var cutoff = new Date();
  cutoff.setHours(10, 0, 0, 0);
  if (now >= cutoff) cutoff.setDate(cutoff.getDate() + 1);
  var diff = cutoff - now;
  var h = Math.floor(diff / 3600000);
  var m = Math.floor((diff % 3600000) / 60000);
  var s = Math.floor((diff % 60000) / 1000);
  hEl.textContent = String(h).padStart(2, '0');
  $('hb-cd-m').textContent = String(m).padStart(2, '0');
  $('hb-cd-s').textContent = String(s).padStart(2, '0');
  var note = $('hb-cd-note');
  if (note) {
    var isPast10 = now >= new Date(now.getFullYear(), now.getMonth(), now.getDate(), 10, 0, 0);
    note.textContent = isPast10 ? '🚚 Order karo — kal 4 PM tak delivery!' : '⚡ Abhi order karo — aaj 4 PM tak delivery!';
  }
}
function startCountdown() {
  if (!$('hb-cd-h')) return;
  tickCountdown();
  setInterval(tickCountdown, 1000);
}

// ─── TRUST BAR ───────────────────────────────
function startTrust() {
  var el = $('hb-trust-num');
  if (!el) return;
  var n = 38 + Math.floor(Math.random() * 18);
  el.textContent = n;
  setInterval(function(){
    n += Math.random() < 0.6 ? 1 : (Math.random() < 0.3 ? -1 : 0);
    n = Math.max(30, Math.min(80, n));
    el.textContent = n;
  }, 5500);
}

// ─── FLOATING LEAVES ─────────────────────────
function spawnLeaves() {
  var mount = $('hb-leaves-mount');
  if (!mount) return;
  var leaves = ['🌿', '🍃', '🌱', '🍀', '🌾'];
  function spawn() {
    var leaf = document.createElement('span');
    leaf.className = 'hb-leaf';
    leaf.textContent = leaves[Math.floor(Math.random() * leaves.length)];
    leaf.style.left = (Math.random() * 100) + '%';
    leaf.style.fontSize = (12 + Math.random() * 10) + 'px';
    var dur = 8 + Math.random() * 8;
    leaf.style.animationDuration = dur + 's';
    mount.appendChild(leaf);
    setTimeout(function(){ if (leaf.parentNode) leaf.parentNode.removeChild(leaf); }, dur * 1000);
  }
  for (var i = 0; i < 4; i++) {
    (function(d){ setTimeout(spawn, d); })(i * 1400);
  }
  setInterval(spawn, 2200);
}

// ─── CONFETTI ────────────────────────────────
var COLORS = ['#4dda85','#f59e0b','#fff','#25d366','#38b26a','#ef4444','#fcd34d'];
function confetti() {
  for (var i = 0; i < 36; i++) {
    (function(i){
      setTimeout(function(){
        var el = document.createElement('div');
        el.className = 'hb-confetti';
        el.style.left = (15 + Math.random() * 70) + '%';
        el.style.top = (10 + Math.random() * 40) + '%';
        el.style.background = COLORS[Math.floor(Math.random() * COLORS.length)];
        el.style.transform = 'rotate(' + Math.random() * 360 + 'deg)';
        el.style.animationDuration = (0.8 + Math.random() * 0.7) + 's';
        var shapes = ['2px', '50%', '0'];
        el.style.borderRadius = shapes[Math.floor(Math.random() * shapes.length)];
        document.body.appendChild(el);
        setTimeout(function(){ el.remove(); }, 1500);
      }, i * 22);
    })(i);
  }
}

// ─── FLY-TO-CART + GLOW ──────────────────────
function flyToCart(key) {
  var btn = document.querySelector('[data-key="' + key + '"]');
  if (!btn) return;
  var card = btn.closest('.hb-pcard');
  var cart = $('hb-cart-bar');
  if (!cart) return;
  var rect = btn.getBoundingClientRect();
  var cartRect = cart.getBoundingClientRect();
  var emoji = card ? (card.querySelector('.hb-p-emoji') || {}).textContent : '🛒';
  var fly = document.createElement('span');
  fly.className = 'hb-fly-emoji';
  fly.textContent = emoji || '🛒';
  fly.style.left = (rect.left + rect.width/2 - 11) + 'px';
  fly.style.top = (rect.top + rect.height/2 - 11) + 'px';
  fly.style.setProperty('--fx', (cartRect.left + cartRect.width/2 - rect.left - rect.width/2) + 'px');
  fly.style.setProperty('--fy', (cartRect.top - rect.top) + 'px');
  document.body.appendChild(fly);
  setTimeout(function(){ if (fly.parentNode) fly.parentNode.removeChild(fly); }, 750);

  // Cart bounce
  cart.classList.remove('hb-bounce');
  void cart.offsetWidth;
  cart.classList.add('hb-bounce');

  // Card glow
  if (card) {
    card.classList.remove('hb-glow');
    void card.offsetWidth;
    card.classList.add('hb-glow');
  }
}

// ─── REPEAT ORDER ────────────────────────────
function showRepeatBar() {
  var snap = HBUtils.storage.get(App.LASTORDER_KEY);
  if (!snap || !snap.length) return;
  var bar = $('hb-repeat-bar');
  var items = $('hb-rb-items');
  if (!bar || !items) return;
  var labels = snap.slice(0, 3).map(function(s){ return s.qty + '× ' + s.name.split('·')[0].trim().split(' ')[0]; }).join(', ');
  if (snap.length > 3) labels += ' +' + (snap.length - 3) + ' more';
  items.textContent = labels;
  bar.style.display = 'flex';
}

// ─── PINCODE CHECK ───────────────────────────
function setupPincode() {
  var input = $('hb-pin-input');
  if (!input) return;
  input.addEventListener('keydown', function(e){
    if (e.key === 'Enter') checkPin();
  });
  input.addEventListener('input', function(){
    var r = $('hb-pin-result');
    if (r) r.classList.remove('show');
  });
}

function checkPin() {
  var input = $('hb-pin-input');
  var resultEl = $('hb-pin-result');
  if (!input || !resultEl) return;
  var val = input.value.trim().toLowerCase();
  if (!val) { HBUtils.toast('Pincode ya colony name daalein!'); return; }

  // Check against delivery areas (HB_SOCIETIES) + common Jaipur pincodes
  var areas = (window.HB_SOCIETIES || []).map(function(s){ return s.toLowerCase(); });
  var pincodes = ['302017','302026','302020','302013','302018','302019','302015','302016','302021'];

  var ok = false;
  for (var i = 0; i < areas.length; i++) {
    if (val.indexOf(areas[i]) !== -1 || areas[i].indexOf(val) !== -1) { ok = true; break; }
  }
  if (!ok) {
    for (var j = 0; j < pincodes.length; j++) {
      if (val === pincodes[j]) { ok = true; break; }
    }
  }

  resultEl.classList.add('show');
  if (ok) {
    var minFree = (window.HB && HB.minFree) || 199;
    resultEl.innerHTML =
      '<div class="hb-pin-ok">' +
        '<div>🎉</div>' +
        '<div>' +
          '<div>✅ Hum aapke area mein deliver karte hain!</div>' +
          '<div>Next day 4 PM tak delivery. ₹' + minFree + '+ pe FREE!</div>' +
        '</div>' +
      '</div>';
  } else {
    var wa = (window.HB && HB.wa) || '918000344554';
    resultEl.innerHTML =
      '<div class="hb-pin-no">' +
        '<div style="font-size:28px">😕</div>' +
        '<div>' +
          '<div style="color:#fff;font-size:13px;font-weight:800">Abhi is area mein delivery nahi hai</div>' +
          '<div style="margin-top:4px"><a href="https://wa.me/' + wa + '?text=Kya+aap+mere+area+mein+deliver+karte+hain%3F+Area%3A+' + encodeURIComponent(input.value.trim()) + '" target="_blank">📱 WhatsApp pe check karo →</a></div>' +
        '</div>' +
      '</div>';
  }
}

// ─── FAQ ACCORDION ───────────────────────────
function setupFaq() {
  document.addEventListener('click', function(e){
    var item = e.target.closest('.hb-faq-item');
    if (!item) return;
    if (item.classList.contains('open')) {
      item.classList.remove('open');
    } else {
      var siblings = item.parentNode.querySelectorAll('.hb-faq-item');
      siblings.forEach(function(s){ s.classList.remove('open'); });
      item.classList.add('open');
    }
  });
}

// ─── NAV DRAWER ──────────────────────────────
window.HBNav = {
  open: function() {
    $('hb-nav-overlay').classList.add('open');
    $('hb-nav-drawer').classList.add('open');
    document.body.style.overflow = 'hidden';
  },
  close: function() {
    var ov = $('hb-nav-overlay');
    var dr = $('hb-nav-drawer');
    if (ov) ov.classList.remove('open');
    if (dr) dr.classList.remove('open');
    document.body.style.overflow = '';
  },
  scrollTo: function(target) {
    HBNav.close();
    var map = {
      home: 0,
      products: $('hb-products') ? $('hb-products').offsetTop - 60 : 0,
      categories: $('hb-products') ? $('hb-products').offsetTop - 60 : 0
    };
    window.scrollTo({ top: map[target] || 0, behavior: 'smooth' });
  },
  openSection: function(id) {
    HBNav.close();
    var src = $('hb-section-' + id);
    if (!src) return;
    var titles = {
      about: '🌿 About Us',
      blog: '📝 Blog & Recipes',
      wishlist: '❤️ Meri Wishlist',
      faq: '❓ FAQ',
      contact: '📞 Humse Baat Karo',
      privacy: '📋 Privacy & Terms'
    };
    $('hb-modal-title').textContent = titles[id] || id;
    $('hb-modal-body').innerHTML = src.outerHTML;
    $('hb-section-modal').classList.add('open');
    document.body.style.overflow = 'hidden';
    if (id === 'wishlist') HBExtras.renderWishlist();
  },
  closeSection: function() {
    $('hb-section-modal').classList.remove('open');
    document.body.style.overflow = '';
  },
  checkModalClose: function(e) {
    if (e.target.id === 'hb-section-modal') HBNav.closeSection();
  }
};

// ─── WISHLIST ────────────────────────────────
function getWishlist() { return HBUtils.storage.get(App.WISHLIST_KEY) || []; }
function saveWishlist(w) { HBUtils.storage.set(App.WISHLIST_KEY, w); }

window.HBExtras = {
  confetti: confetti,
  onAdd: function(key) { flyToCart(key); },
  repeatOrder: function() {
    var snap = HBUtils.storage.get(App.LASTORDER_KEY);
    if (!snap || !snap.length) { HBUtils.toast('Pichla order nahi mila'); return; }
    snap.forEach(function(s){ App.cart[s.key] = s.qty; });
    HBUtils.storage.set(App.CART_KEY, App.cart);
    HBProducts.render();
    HBCart.updateBar();
    var prods = $('hb-products');
    if (prods) prods.scrollIntoView({ behavior: 'smooth' });
    HBUtils.toast('🔁 Pichla order cart mein add ho gaya!');
  },
  checkPincode: checkPin,
  renderWishlist: function() {
    var grid = document.querySelector('#hb-modal-body #hb-wishlist-grid') || $('hb-wishlist-grid');
    var empty = document.querySelector('#hb-modal-body #hb-wishlist-empty') || $('hb-wishlist-empty');
    if (!grid) return;
    var ids = getWishlist();
    var items = ids.map(function(id){
      return (window.HB_PRODUCTS || []).find(function(p){ return String(p.id) === String(id); });
    }).filter(Boolean);
    if (!items.length) {
      if (empty) empty.style.display = 'block';
      grid.innerHTML = '';
      return;
    }
    if (empty) empty.style.display = 'none';
    grid.innerHTML = items.map(function(p){
      return '<div class="hb-pcard">' +
        '<div class="hb-p-img"><span class="hb-p-emoji">' + HBUtils.emoji(p.name) + '</span></div>' +
        '<div class="hb-p-name">' + HBUtils.esc(p.name) + '</div>' +
        '<div class="hb-p-uom">per ' + HBUtils.esc(p.uom) + '</div>' +
        '<div class="hb-p-price-row"><span class="hb-p-sp">₹' + p.sp + '</span></div>' +
        '<button class="hb-pcard-add" data-act="wl-add" data-pid="' + p.id + '">+ Add</button>' +
      '</div>';
    }).join('');
  },
  toggleWish: function(pid) {
    var w = getWishlist();
    var idx = w.indexOf(String(pid));
    if (idx === -1) { w.push(String(pid)); HBUtils.toast('Wishlist mein add ho gaya ❤️'); }
    else { w.splice(idx, 1); HBUtils.toast('Wishlist se hata diya'); }
    saveWishlist(w);
  }
};

// Init all
function init() {
  startCountdown();
  startTrust();
  spawnLeaves();
  setupPincode();
  setupFaq();
  showRepeatBar();
  // ESC to close modals
  document.addEventListener('keydown', function(e){
    if (e.key === 'Escape') {
      HBNav.close();
      HBNav.closeSection();
      if (HBCart._drawerOpen) HBCart.toggleDrawer(false);
    }
  });
  // Wishlist add from modal
  document.addEventListener('click', function(e){
    var t = e.target.closest('[data-act="wl-add"]');
    if (!t) return;
    var pid = t.getAttribute('data-pid');
    HBCart.chg(String(pid), 1);
  });
}

if (document.readyState !== 'loading') init();
else document.addEventListener('DOMContentLoaded', init);

})();
