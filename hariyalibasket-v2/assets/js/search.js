/* ═══ SMART SEARCH — Hindi synonyms + typo tolerance + ranking ═══ */
(function(){
'use strict';

var $ = HBUtils.$;
var App = HBApp;

// Hindi <-> English synonym map (search "pyaj" finds "Onion")
var SYNONYMS = {
  'aloo': ['potato'], 'potato': ['aloo'],
  'pyaaz': ['onion','pyaj','piyaaz'], 'pyaj': ['onion','pyaaz'], 'onion': ['pyaaz','pyaj'],
  'tamatar': ['tomato','tamater'], 'tomato': ['tamatar'],
  'gajar': ['carrot'], 'carrot': ['gajar'],
  'palak': ['spinach'], 'spinach': ['palak'],
  'gobhi': ['cauliflower','gobi'], 'cauliflower': ['gobhi'],
  'bhindi': ['okra','lady finger'], 'okra': ['bhindi'], 'lady finger': ['bhindi'],
  'shimla': ['capsicum'], 'capsicum': ['shimla'],
  'aam': ['mango'], 'mango': ['aam'],
  'kela': ['banana'], 'banana': ['kela'],
  'seb': ['apple'], 'apple': ['seb'],
  'dhaniya': ['coriander','dhania'], 'coriander': ['dhaniya'],
  'pudina': ['mint'], 'mint': ['pudina'],
  'methi': ['fenugreek'],
  'adrak': ['ginger'], 'ginger': ['adrak'],
  'lahsun': ['garlic'], 'garlic': ['lahsun'],
  'baingan': ['brinjal','eggplant'], 'brinjal': ['baingan'],
  'mirch': ['chilli','chili'], 'chilli': ['mirch'],
  'nimbu': ['lemon','lime'], 'lemon': ['nimbu'],
  'mosambi': ['sweet lime','musambi'], 'sweet lime': ['mosambi'],
  'lauki': ['bottle gourd','dudhi'], 'bottle gourd': ['lauki'],
  'kheera': ['cucumber','kakri'], 'cucumber': ['kheera','kakri'],
  'kakri': ['cucumber','kheera'],
  'karela': ['bitter gourd'], 'bitter gourd': ['karela'],
  'tinda': ['apple gourd'],
  'arbi': ['colocasia','taro'], 'colocasia': ['arbi'],
  'parwal': ['pointed gourd'],
  'kathal': ['jackfruit'], 'jackfruit': ['kathal'],
  'mushroom': ['khumbi'],
  'beetroot': ['chukandar'], 'chukandar': ['beetroot'],
  'broccoli': ['gobhi'],
  'avocado': ['butter fruit'],
  'anar': ['pomegranate'], 'pomegranate': ['anar'],
  'jamun': ['black plum'],
  'chikoo': ['sapodilla'],
  'guava': ['amrood'], 'amrood': ['guava'],
  'papaya': ['papita'], 'papita': ['papaya'],
  'matar': ['peas'], 'peas': ['matar'],
  'kaddu': ['pumpkin'], 'pumpkin': ['kaddu'],
  'bhutta': ['corn'], 'corn': ['bhutta'],
  'sahjan': ['drumstick'], 'drumstick': ['sahjan'],
};

/**
 * Levenshtein distance (typo tolerance)
 */
function distance(a, b) {
  if (!a.length) return b.length;
  if (!b.length) return a.length;
  var m = [];
  for (var i = 0; i <= b.length; i++) m[i] = [i];
  for (var j = 0; j <= a.length; j++) m[0][j] = j;
  for (i = 1; i <= b.length; i++) {
    for (j = 1; j <= a.length; j++) {
      m[i][j] = b.charAt(i-1) === a.charAt(j-1)
        ? m[i-1][j-1]
        : Math.min(m[i-1][j-1] + 1, m[i][j-1] + 1, m[i-1][j] + 1);
    }
  }
  return m[b.length][a.length];
}

function getSynonyms(word) {
  var w = word.toLowerCase().trim();
  return SYNONYMS[w] || [];
}

/**
 * Score a product against the query.
 * Higher = better match.
 *
 * BUG FIX #3: Short queries (< 3 chars) only match prefix patterns —
 *   no "contains" anywhere, no typo tolerance. This prevents "al" from
 *   matching "Brinjal" / "Spinach (Palak)" purely because they happen
 *   to contain those 2 letters.
 *
 * BUG FIX #2: Partial synonym match — if the query is a prefix of any
 *   synonym key (e.g. "al" → "aloo"), the matched products surface even
 *   though no product is literally named "Aloo".
 */
function score(product, query) {
  var name = product.name.toLowerCase();
  var q = query.toLowerCase().trim();
  if (!q) return 0;

  var isShort = q.length < 3;

  // 1. Exact name match
  if (name === q) return 100;

  // 2. Name starts with query (high signal)
  if (name.indexOf(q) === 0) return 90;

  // 3. Any word in name starts with query (e.g. "Apple Himachal" matches "him")
  var nameWords = name.split(/\s+/);
  for (var i = 0; i < nameWords.length; i++) {
    if (nameWords[i].indexOf(q) === 0 && nameWords[i] !== '') return 75;
  }

  // 4. Synonym key starts with query — "al" → "aloo" → Potato (BUG FIX #2)
  for (var key in SYNONYMS) {
    if (key.length <= q.length) continue; // need actual prefix relationship
    if (key.indexOf(q) !== 0) continue;
    // Match products that contain any of this key's targets
    var targets = SYNONYMS[key];
    for (var t = 0; t < targets.length; t++) {
      if (name.indexOf(targets[t]) !== -1) return 60;
    }
    // Also, if a product name starts with the key itself (e.g. "Aloo Bhindi")
    if (name.indexOf(key) === 0) return 60;
  }

  // 5. Reverse — any word in product has a synonym that starts with q
  for (var w = 0; w < nameWords.length; w++) {
    var ws = getSynonyms(nameWords[w]);
    for (var s = 0; s < ws.length; s++) {
      if (ws[s].indexOf(q) === 0) return 55;
    }
  }

  // ── Stop here for short queries (BUG FIX #3) ──
  // No "contains" matches, no typo tolerance. They cause noise.
  if (isShort) return 0;

  // 6. Contains anywhere in name (3+ char queries only)
  if (name.indexOf(q) !== -1) return 50;

  // 7. Synonym match (full query maps to known synonym)
  var fullSyns = getSynonyms(q);
  for (var i2 = 0; i2 < fullSyns.length; i2++) {
    if (name.indexOf(fullSyns[i2]) !== -1) return 45;
  }

  // 8. Typo tolerance — only for 4+ char queries (was 3+, too permissive)
  if (q.length >= 4) {
    var minDist = Infinity;
    for (var n = 0; n < nameWords.length; n++) {
      var d = distance(q, nameWords[n]);
      if (d < minDist) minDist = d;
    }
    if (minDist <= 1) return 35;
    if (minDist <= 2 && q.length >= 6) return 25;

    if (fullSyns.length > 0) {
      for (var sy = 0; sy < fullSyns.length; sy++) {
        if (distance(q, fullSyns[sy]) <= 1) return 30;
      }
    }
  }

  return 0;
}

window.HBSearch = {
  filter: function(list, query) {
    if (!query) return list;
    var scored = list.map(function(p){
      return { p: p, s: score(p, query) };
    }).filter(function(x){ return x.s > 0; });
    scored.sort(function(a, b){ return b.s - a.s; });
    return scored.map(function(x){ return x.p; });
  },

  /**
   * Suggest products as user types (top 7)
   */
  suggest: function(query) {
    if (!query || query.length < 1) return [];
    return this.filter(window.HB_PRODUCTS || [], query).slice(0, 7);
  },
};

// Wire up search input
function init() {
  var inp = $('hb-search-inp');
  var clearBtn = $('hb-clear-btn');
  var suggestBox = $('hb-search-suggest');
  if (!inp) return;

  // BUG FIX: kill any browser autofill datalist that might attach
  inp.setAttribute('autocomplete', 'off');
  inp.setAttribute('autocorrect', 'off');
  inp.setAttribute('autocapitalize', 'off');
  inp.setAttribute('spellcheck', 'false');
  inp.removeAttribute('list');

  // ── PORTAL FIX ──
  // Move the suggest dropdown out of .hb-search-section and into <body>.
  // This bypasses any parent stacking context (filter-bar, products-section, etc.)
  // that was clipping the dropdown.
  if (suggestBox && suggestBox.parentNode !== document.body) {
    document.body.appendChild(suggestBox);
  }

  /**
   * Position the dropdown immediately below the input, in viewport coords.
   * Uses position: fixed (set in CSS), so coords are relative to viewport.
   */
  function positionSuggest() {
    if (!suggestBox || !inp) return;
    var rect = inp.getBoundingClientRect();
    suggestBox.style.top   = (rect.bottom + 6) + 'px';
    suggestBox.style.left  = rect.left + 'px';
    suggestBox.style.width = rect.width + 'px';
  }

  function showSuggest() {
    positionSuggest();
    suggestBox.classList.add('show');
  }
  function hideSuggest() {
    suggestBox.classList.remove('show');
  }

  inp.addEventListener('input', function(){
    var v = this.value;
    App.searchQuery = v;
    if (clearBtn) clearBtn.classList.toggle('show', !!v);

    if (suggestBox) {
      var sug = HBSearch.suggest(v);
      if (sug.length && v.length > 0) {
        suggestBox.innerHTML = sug.map(function(p){
          return '<div class="hb-suggest-item" data-name="' + HBUtils.esc(p.name) + '"><span>' + HBUtils.emoji(p.name) + '</span><span>' + HBUtils.esc(p.name) + '</span></div>';
        }).join('');
        showSuggest();
      } else {
        hideSuggest();
      }
    }
    HBProducts.render();
  });

  // Reposition on scroll / resize / orientation-change
  // (mobile keyboard appearance also fires resize — important on iOS/Android)
  window.addEventListener('scroll', function(){
    if (suggestBox.classList.contains('show')) positionSuggest();
  }, { passive: true });
  window.addEventListener('resize', function(){
    if (suggestBox.classList.contains('show')) positionSuggest();
  });
  inp.addEventListener('focus', function(){
    if (inp.value && HBSearch.suggest(inp.value).length) showSuggest();
  });

  if (clearBtn) {
    clearBtn.addEventListener('click', function(){
      inp.value = '';
      App.searchQuery = '';
      clearBtn.classList.remove('show');
      hideSuggest();
      HBProducts.render();
    });
  }

  if (suggestBox) {
    suggestBox.addEventListener('click', function(e){
      var item = e.target.closest('.hb-suggest-item');
      if (!item) return;
      inp.value = item.getAttribute('data-name');
      App.searchQuery = inp.value;
      hideSuggest();
      HBProducts.render();
    });
    // Clicking outside the input AND outside the dropdown closes it
    document.addEventListener('click', function(e){
      if (e.target === inp) return;
      if (suggestBox.contains(e.target)) return;
      hideSuggest();
    });
  }
}

if (document.readyState !== 'loading') init();
else document.addEventListener('DOMContentLoaded', init);

})();
