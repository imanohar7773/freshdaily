/* ═══ PRODUCTS — render, filter, variants ═══ */
(function(){
'use strict';

var $ = HBUtils.$;
var App = HBApp;

window.HBProducts.CATS = [
  { id: 'all', label: '🛒 All' },
  { id: 'Fruits', label: '🍊 Fruits' },
  { id: 'Root Vegetables', label: '🥕 Root Veg' },
  { id: 'Green Vegetables', label: '🥬 Green Veg' },
  { id: 'Exotic & Packed', label: '🍄 Exotic' },
  { id: 'Herbs & Leafy', label: '🌿 Herbs' },
  { id: 'Sabzi', label: '🥬 Sabzi' },
];

HBProducts.buildFilters = function() {
  var bar = $('hb-filter-bar');
  if (!bar) return;
  bar.innerHTML = '';
  var existingCats = {};
  (window.HB_PRODUCTS || []).forEach(function(p){ existingCats[p.cat] = true; });

  HBProducts.CATS.forEach(function(c){
    if (c.id !== 'all' && !existingCats[c.id]) return;
    var btn = document.createElement('button');
    btn.className = 'hb-fchip' + (c.id === 'all' ? ' active' : '');
    btn.textContent = c.label;
    btn.onclick = function(){
      HBUtils.qsa('.hb-fchip').forEach(function(b){ b.classList.remove('active'); });
      btn.classList.add('active');
      App.curCat = c.id;
      HBProducts.render();
    };
    bar.appendChild(btn);
  });
};

HBProducts.render = function() {
  var grid = $('hb-product-grid');
  if (!grid) return;

  var list = window.HB_PRODUCTS || [];
  if (App.curCat !== 'all') list = list.filter(function(p){ return p.cat === App.curCat; });

  // Apply search (with smart Hindi if available)
  var q = App.searchQuery || '';
  if (q && window.HBSearch && HBSearch.filter) {
    list = HBSearch.filter(list, q);
  } else if (q) {
    list = list.filter(function(p){ return p.name.toLowerCase().indexOf(q.toLowerCase()) !== -1; });
  }

  $('hb-prod-count').textContent = list.length + ' items';

  if (!list.length) {
    grid.innerHTML = '<div class="hb-empty"><div class="hb-empty-icon">🌿</div><p>Koi item nahi mila</p></div>';
    return;
  }

  grid.innerHTML = list.map(function(p){
    var hasVar = p.variants && p.variants.length > 0;
    var vi = App.varSel[p.id] !== undefined ? App.varSel[p.id]
           : (hasVar ? (p.variants.length === 3 ? 2 : p.variants.length - 1) : -1);
    var av = (vi >= 0 && hasVar) ? p.variants[vi] : null;
    var sp = av ? av.sp : p.sp;
    var mrp = av ? av.mrp : p.mrp;
    var uom = av ? av.size : p.uom;
    var key = hasVar ? (p.id + '_' + vi) : String(p.id);
    var disc = mrp > sp ? Math.round((1 - sp/mrp) * 100) : 0;
    var qty = App.cart[key] || 0;
    var outOfStock = p.stock === 'out';

    var badges = '';
    if (outOfStock) {
      badges = '<div class="hb-soon-overlay"><span>🕐 COMING SOON</span></div>';
    } else {
      if (disc > 0) badges += '<div class="hb-badge hb-badge-disc">' + disc + '% OFF</div>';
      badges += '<div class="hb-badge hb-badge-fresh">✅ Fresh</div>';
    }

    var imgHtml = p.img
      ? '<div class="hb-p-img"><img src="' + HBUtils.esc(p.img) + '" alt="' + HBUtils.esc(p.name) + '" loading="lazy" onerror="this.parentNode.innerHTML=\'<span class=hb-p-emoji>' + HBUtils.emoji(p.name) + '</span>\'"></div>'
      : '<div class="hb-p-img"><span class="hb-p-emoji">' + HBUtils.emoji(p.name) + '</span></div>';

    var chips = hasVar
      ? '<div class="hb-var-chips">' + p.variants.map(function(v,i){
          return '<span class="hb-vchip' + (i === vi ? ' active' : '') + '" data-act="var" data-pid="' + p.id + '" data-idx="' + i + '">' + HBUtils.esc(v.size) + '</span>';
        }).join('') + '</div>'
      : '';

    var ctrl = outOfStock
      ? ''
      : (qty === 0
          ? '<button class="hb-pcard-add" data-act="add" data-key="' + key + '">+ Add</button>'
          : '<div class="hb-qty-ctrl"><button class="hb-qty-btn hb-qty-minus" data-act="chg" data-key="' + key + '" data-d="-1">−</button><div class="hb-qty-num">' + qty + '</div><button class="hb-qty-btn hb-qty-plus" data-act="chg" data-key="' + key + '" data-d="1">+</button></div>');

    return '<div class="hb-pcard' + (outOfStock ? ' hb-out-stock' : '') + '" data-pid="' + p.id + '">' +
      badges +
      imgHtml +
      '<div class="hb-p-name">' + HBUtils.esc(p.name) + '</div>' +
      chips +
      '<div class="hb-p-uom">per ' + HBUtils.esc(uom) + '</div>' +
      '<div class="hb-p-price-row">' + (disc > 0 ? '<span class="hb-p-mrp">₹' + mrp + '</span>' : '') + '<span class="hb-p-sp">₹' + sp + '</span></div>' +
      ctrl +
    '</div>';
  }).join('');

  // Event delegation: variant pick + add/chg
  grid.onclick = function(e) {
    var t = e.target.closest('[data-act]');
    if (!t) return;
    var act = t.getAttribute('data-act');
    if (act === 'var') {
      var pid = t.getAttribute('data-pid');
      var idx = parseInt(t.getAttribute('data-idx'));
      App.varSel[pid] = idx;
      HBProducts.render();
    } else if (act === 'add') {
      var key = t.getAttribute('data-key');
      HBCart.chg(key, 1);
    } else if (act === 'chg') {
      var k = t.getAttribute('data-key');
      var d = parseInt(t.getAttribute('data-d'));
      HBCart.chg(k, d);
    }
  };
};

// Init
if (document.readyState !== 'loading') {
  HBProducts.buildFilters();
  HBProducts.render();
} else {
  document.addEventListener('DOMContentLoaded', function(){
    HBProducts.buildFilters();
    HBProducts.render();
  });
}

})();
