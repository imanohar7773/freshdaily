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
 * Score a product against the query
 * Higher = better match
 */
function score(product, query) {
  var name = product.name.toLowerCase();
  var q = query.toLowerCase().trim();
  if (!q) return 0;

  // 1. Exact name match
  if (name === q) return 100;
  // 2. Starts with
  if (name.indexOf(q) === 0) return 80;
  // 3. Contains
  if (name.indexOf(q) !== -1) return 60;

  // 4. Synonym match
  var syns = getSynonyms(q);
  for (var i = 0; i < syns.length; i++) {
    if (name.indexOf(syns[i]) !== -1) return 50;
  }
  // Reverse — if any word in product matches a synonym of query
  var nameWords = name.split(/\s+/);
  for (var j = 0; j < nameWords.length; j++) {
    var ws = getSynonyms(nameWords[j]);
    for (var k = 0; k < ws.length; k++) {
      if (q.indexOf(ws[k]) !== -1) return 45;
    }
  }

  // 5. Typo tolerance — check distance for short queries
  if (q.length >= 3) {
    var minDist = Infinity;
    for (var n = 0; n < nameWords.length; n++) {
      var d = distance(q, nameWords[n]);
      if (d < minDist) minDist = d;
    }
    if (minDist <= 1) return 40;
    if (minDist <= 2 && q.length >= 5) return 30;

    // Also typo on synonyms
    if (syns.length > 0) {
      for (var s = 0; s < syns.length; s++) {
        if (distance(q, syns[s]) <= 1) return 35;
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
   * Suggest products as user types (top 5)
   */
  suggest: function(query) {
    if (!query || query.length < 1) return [];
    return this.filter(window.HB_PRODUCTS || [], query).slice(0, 5);
  },
};

// Wire up search input
function init() {
  var inp = $('hb-search-inp');
  var clearBtn = $('hb-clear-btn');
  var suggestBox = $('hb-search-suggest');
  if (!inp) return;

  inp.addEventListener('input', function(){
    var v = this.value;
    App.searchQuery = v;
    if (clearBtn) clearBtn.classList.toggle('show', !!v);

    // Suggestions
    if (suggestBox) {
      var sug = HBSearch.suggest(v);
      if (sug.length && v.length > 0) {
        suggestBox.innerHTML = sug.map(function(p){
          return '<div class="hb-suggest-item" data-name="' + HBUtils.esc(p.name) + '"><span>' + HBUtils.emoji(p.name) + '</span><span>' + HBUtils.esc(p.name) + '</span></div>';
        }).join('');
        suggestBox.classList.add('show');
      } else {
        suggestBox.classList.remove('show');
      }
    }
    HBProducts.render();
  });

  if (clearBtn) {
    clearBtn.addEventListener('click', function(){
      inp.value = '';
      App.searchQuery = '';
      clearBtn.classList.remove('show');
      if (suggestBox) suggestBox.classList.remove('show');
      HBProducts.render();
    });
  }

  if (suggestBox) {
    suggestBox.addEventListener('click', function(e){
      var item = e.target.closest('.hb-suggest-item');
      if (!item) return;
      inp.value = item.getAttribute('data-name');
      App.searchQuery = inp.value;
      suggestBox.classList.remove('show');
      HBProducts.render();
    });
    document.addEventListener('click', function(e){
      if (!e.target.closest('.hb-search-section')) suggestBox.classList.remove('show');
    });
  }
}

if (document.readyState !== 'loading') init();
else document.addEventListener('DOMContentLoaded', init);

})();
