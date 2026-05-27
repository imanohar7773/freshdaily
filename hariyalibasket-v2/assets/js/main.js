/* ═══ HARIYALIBASKET v2 — MAIN ═══
 * Global init, helpers, namespace
 */
(function(){
'use strict';

// Global namespace
window.HBApp = {
  cart: {},          // { 'pid' or 'pid_varIdx': qty }
  varSel: {},        // { pid: variantIndex }
  curCat: 'all',
  payMethod: 'cod',
  savedAddr: null,
  waUrl: '',
  orderId: '',
  searchQuery: '',
  captcha: null,
  CART_KEY: 'hb_cart_v2',
  ADDR_KEY: 'hb_addr_v2',
  LASTORDER_KEY: 'hb_last_order_v2',
  CSOC_KEY: 'hb_csocs_v2',
  WISHLIST_KEY: 'hb_wishlist_v2',
};

// Helpers
window.HBUtils = {
  $: function(id) { return document.getElementById(id); },
  qs: function(sel, root) { return (root || document).querySelector(sel); },
  qsa: function(sel, root) { return Array.prototype.slice.call((root || document).querySelectorAll(sel)); },
  toast: function(msg, dur) {
    var t = HBUtils.$('hb-toast');
    if (!t) return;
    t.textContent = msg;
    t.classList.add('show');
    clearTimeout(t._tmr);
    t._tmr = setTimeout(function(){ t.classList.remove('show'); }, dur || 2400);
  },
  inr: function(n){ return '₹' + Math.round(n); },
  emoji: function(name) {
    var map = {
      'aloo':'🥔','potato':'🥔','pyaaz':'🧅','onion':'🧅','tamatar':'🍅','tomato':'🍅',
      'gajar':'🥕','carrot':'🥕','palak':'🥬','spinach':'🥬','gobhi':'🥦','cauliflower':'🥦',
      'cabbage':'🥬','bhindi':'🫛','okra':'🫛','lady finger':'🫛','shimla':'🫑','capsicum':'🫑',
      'mango':'🥭','aam':'🥭','kela':'🍌','banana':'🍌','seb':'🍎','apple':'🍎',
      'dhaniya':'🌿','coriander':'🌿','pudina':'🌿','mint':'🌿','methi':'🌱','mushroom':'🍄',
      'broccoli':'🥦','adrak':'🫚','ginger':'🫚','garlic':'🧄','lahsun':'🧄','beetroot':'🫀',
      'beet':'🫀','corn':'🌽','bhutta':'🌽','lauki':'🥒','gourd':'🥒','cucumber':'🥒',
      'kheera':'🥒','kakri':'🥒','baingan':'🍆','brinjal':'🍆','chilli':'🌶️','mirch':'🌶️',
      'lemon':'🍋','nimbu':'🍋','mosambi':'🍋','lime':'🍋','watermelon':'🍉','papaya':'🫒',
      'pineapple':'🍍','coconut':'🥥','kiwi':'🥝','avocado':'🥑','dragon':'🐲','guava':'🍈',
      'pomegranate':'🍑','anar':'🍑','jamun':'🫐','beans':'🫘','pumpkin':'🎃',
      'jackfruit':'🍈','kathal':'🍈','arbi':'🥔','tinda':'🥒','parwal':'🥒','drumstick':'🌿',
      'banana leaf':'🍃','spring':'🧅','lettuce':'🥬','zucchini':'🥒','kachri':'🥒',
      'guar':'🫘','chola':'🫘','chikoo':'🟤'
    };
    var n = (name || '').toLowerCase();
    for (var k in map) {
      if (n.indexOf(k) !== -1) return map[k];
    }
    return '🌿';
  },
  esc: function(s) {
    var div = document.createElement('div');
    div.textContent = s;
    return div.innerHTML;
  },
  storage: {
    get: function(key) {
      try { return JSON.parse(localStorage.getItem(key) || 'null'); } catch(e) { return null; }
    },
    set: function(key, val) {
      try { localStorage.setItem(key, JSON.stringify(val)); } catch(e) {}
    },
    remove: function(key) {
      try { localStorage.removeItem(key); } catch(e) {}
    }
  }
};

// Get product info from cart key
window.HBProducts = window.HBProducts || {};
window.HBProducts.getInfo = function(key) {
  var parts = String(key).split('_');
  var pid = parts[0];
  var vi = parts.length > 1 ? parseInt(parts[parts.length-1]) : -1;
  var p = (window.HB_PRODUCTS || []).find(function(x){ return String(x.id) === String(pid); });
  if (!p) return null;
  var av = (vi >= 0 && p.variants && p.variants[vi]) ? p.variants[vi] : null;
  return {
    p: p,
    av: av,
    sp: av ? av.sp : p.sp,
    mrp: av ? av.mrp : p.mrp,
    uom: av ? av.size : p.uom,
    label: av ? p.name + ' · ' + av.size : p.name
  };
};

// On DOMReady
function ready(fn) {
  if (document.readyState !== 'loading') fn();
  else document.addEventListener('DOMContentLoaded', fn);
}

ready(function(){
  // Inject scroll progress bar
  var sb = document.createElement('div');
  sb.id = 'hb-scroll-bar';
  document.body.insertBefore(sb, document.body.firstChild);
  window.addEventListener('scroll', function(){
    var sc = document.documentElement.scrollTop;
    var ht = document.documentElement.scrollHeight - document.documentElement.clientHeight;
    sb.style.width = (ht > 0 ? (sc/ht*100) : 0) + '%';
  }, { passive: true });
});

})();
