# 🌿 HariyaliBasket v2 — WordPress Theme

**Modular farm-fresh delivery theme** — products, cart, 3-step checkout, real order saving, admin dashboard, smart Hindi search, and more.

---

## 📦 Quick Install (Hostinger / cPanel)

### Method 1: Upload as ZIP (RECOMMENDED)
1. Download the entire `hariyalibasket-v2/` folder as a ZIP
2. WordPress Admin → **Appearance → Themes → Add New → Upload Theme** → upload the ZIP
3. Click **Activate**
4. Open `https://YOURSITE.com/wp-content/themes/hariyalibasket-v2/hb-import-products.php?run=1&clean=1` to import 71 products
5. **DELETE** `hb-import-products.php` from theme folder after import (security!)

### Method 2: cPanel File Manager
1. cPanel → File Manager → `public_html/wp-content/themes/`
2. Upload the ZIP, extract there
3. WordPress Admin → Appearance → Themes → Activate **HariyaliBasket v2**
4. Run importer URL above
5. Delete importer file

---

## 🆕 9 New Additions (vs v1)

| # | Feature | Where to find |
|---|---------|---------------|
| 1 | **Real Order Saving (CPT `hb_order`)** | WP Admin → Orders sidebar |
| 2 | **Admin Order Dashboard** | WP Admin → 🌿 HB Dashboard |
| 3 | **Sticky Bottom Navigation** | Mobile bottom — Home/Shop/Cart/Orders/Account |
| 4 | **Smart Search** | Top of products — typo tolerance + Hindi (e.g. "pyaj" finds Onion) |
| 5 | **CAPTCHA** | Step 3 of checkout — math problem |
| 6 | **Rate Limiting** | Backend — 5 orders/hr/IP, 3/hr/phone |
| 7 | **Repeat Order Button** | Above products if a previous order exists |
| 8 | **CDN/Minify Docs** | This file → see "Production Tips" below |
| 9 | **Pincode Check Widget** | Below products section |

---

## 📂 Folder Structure

```
hariyalibasket-v2/
├── style.css                    Theme metadata
├── functions.php                Module loader
├── index.php                    Main template
├── header.php
├── footer.php
├── hb-import-products.php       ⚠️ DELETE after first run
├── README.md
│
├── inc/                         All PHP logic (modular)
│   ├── helpers.php              hb_get(), emoji map, IP, order ID
│   ├── theme-setup.php          theme support, menus
│   ├── enqueue.php              CSS/JS loading
│   ├── post-types.php           hb_product CPT
│   ├── taxonomies.php           hb_category
│   ├── meta-fields.php          MRP, SP, UOM, variants meta box
│   ├── customizer.php           WhatsApp, UPI, validity, etc.
│   ├── products.php             get_all_products() with variant logic
│   ├── ajax.php                 hb_place_order, hb_get_products
│   ├── woo-sync.php             WooCommerce price sync
│   ├── bulk-editor.php          /wp-admin/?page=hb_bulk_price_editor
│   ├── security.php             security headers
│   ├── cache.php                cache clearing on save
│   ├── orders.php               🆕 hb_order CPT (REAL order saving)
│   ├── admin-dashboard.php      🆕 /wp-admin/?page=hb_dashboard
│   ├── rate-limit.php           🆕 spam prevention
│   └── captcha.php              🆕 math captcha
│
├── template-parts/              Reusable UI components
│   ├── hero.php
│   ├── info-cards.php
│   ├── countdown.php
│   ├── how-it-works.php
│   ├── trust-bar.php            🆕
│   ├── repeat-order.php         🆕
│   ├── products.php
│   ├── reviews.php
│   ├── features.php
│   ├── faq.php
│   ├── contact.php
│   ├── pincode-check.php        🆕
│   ├── about.php
│   ├── blog.php
│   ├── wishlist.php
│   ├── privacy.php
│   ├── cart-bar.php
│   ├── cart-drawer.php
│   ├── checkout.php             3-step wizard
│   ├── success.php
│   ├── bottom-nav.php           🆕 Mobile-app feel
│   └── nav-drawer.php           Hamburger menu + section modal
│
└── assets/
    ├── css/                     8 modular stylesheets
    │   ├── base.css             Variables, fonts, reset
    │   ├── layout.css           Header, marquee, hero, info cards, footer, bottom nav
    │   ├── products.css         Cards, badges, variants
    │   ├── cart.css             Cart bar + drawer + 3-step checkout
    │   ├── sections.css         How-it-works, reviews, features, FAQ, contact, etc.
    │   ├── modals.css           Nav drawer + section modal
    │   ├── animations.css       Confetti, leaves, fly-to-cart
    │   └── responsive.css       Mobile + tablet + print
    │
    ├── js/                      6 modular scripts
    │   ├── main.js              Globals, helpers, namespace
    │   ├── products.js          Render, filter, variants
    │   ├── search.js            🆕 Hindi synonyms + typo tolerance
    │   ├── cart.js              Add/remove, persistence, meter
    │   ├── checkout.js          3-step wizard + AJAX order placement
    │   └── extras.js            Animations, countdown, repeat, pincode, wishlist, nav
    │
    └── img/                     Future product images
```

---

## ⚙️ Customizer Settings

WP Admin → **Appearance → Customize → 🌿 HariyaliBasket Settings**

| Setting | Default |
|---------|---------|
| WhatsApp Number | `918000344554` |
| UPI ID | `imanohar07773@ybl` |
| Price Validity | auto (current month) |
| Min Order Free Delivery | `₹199` |
| Delivery Fee | `₹69` |
| Delivery Areas (comma) | Hanging Garden, Vaishali Nagar, ... |
| Google Sheet URL (logger) | _empty_ |
| Contact Email | hariyalibasket@gmail.com |

---

## 🛠 Admin URLs

| Tool | URL |
|------|-----|
| 🌿 Order Dashboard (NEW) | `/wp-admin/admin.php?page=hb_dashboard` |
| 📦 All Orders (NEW) | `/wp-admin/edit.php?post_type=hb_order` |
| 🥕 All Products | `/wp-admin/edit.php?post_type=hb_product` |
| 💰 Bulk Price Editor | `/wp-admin/admin.php?page=hb_bulk_price_editor` |
| 🔄 WooCommerce Bulk Sync | `/wp-admin/?hb_bulk_sync=1` |

---

## 🚀 Production Tips (Performance)

### Minify CSS/JS
Install one of these plugins:
- **Autoptimize** (free, easiest) — auto combines + minifies
- **LiteSpeed Cache** (Hostinger has LiteSpeed servers — best fit)
- **WP Rocket** (paid, best results)

After install, enable:
- ✅ Combine + Minify CSS
- ✅ Combine + Minify JS
- ✅ Defer non-critical JS
- ✅ Lazy load images

### CDN (Cloudflare — free)
1. Sign up at https://cloudflare.com (free tier is enough)
2. Add `hariyalibasket.com` site
3. Update nameservers at Hostinger (instructions Cloudflare gives you)
4. Wait 24 hours for propagation
5. Cloudflare will cache + serve from edge → 2-3× faster globally

### Browser Cache
Hostinger cPanel → LiteSpeed Cache settings → enable browser caching for CSS/JS/images (30 days)

### Image Optimization
- Use `webp` format for product images (smaller than jpg)
- Plugin: **Smush** (free) → auto compresses uploaded images

---

## 🔒 Security

- ✅ Security headers (X-Frame-Options, XSS, etc.) — `inc/security.php`
- ✅ XML-RPC disabled (common attack vector)
- ✅ WordPress version hidden from `<head>`
- ✅ CAPTCHA on checkout
- ✅ Rate limiting (5 orders/hr/IP, 3/hr/phone)
- ✅ Capability checks on admin pages
- ✅ Nonces on AJAX calls

⚠️ **Don't forget**: After running `hb-import-products.php`, **DELETE** that file!

---

## 🐛 Troubleshooting

### "No products showing"
1. Run the importer: `/wp-content/themes/hariyalibasket-v2/hb-import-products.php?run=1&clean=1`
2. Check WP Admin → Products → confirm 71 items exist
3. Check Customizer → WhatsApp + UPI fields are filled

### "Cart not saving"
- Browser localStorage is disabled (private mode?) — try regular browser
- Clear browser cache and reload

### "Order not appearing in WP Admin"
- Check WP Admin → Orders sidebar (auto-created CPT)
- AJAX nonce expired — refresh the page

### "Smart search not finding 'pyaj'"
- Edit `assets/js/search.js` → `SYNONYMS` object → add custom terms
- E.g. `'kanda': ['onion','pyaaz']` for Marathi users

---

## 📝 Changelog

### v2.0 (Major Release)
- ✨ Complete visual redesign (Nunito + Sora fonts, deeper green palette)
- ✨ 3-step checkout wizard (Cart → Address → Payment)
- ✨ Modular file structure (`/inc`, `/template-parts`, `/assets/{css,js}`)
- 🆕 Real Order Saving System (`hb_order` CPT)
- 🆕 Admin Order Dashboard
- 🆕 Sticky Bottom Navigation
- 🆕 Smart Search (Hindi synonyms + typo tolerance)
- 🆕 Math CAPTCHA
- 🆕 Rate Limiting
- 🆕 Repeat Order button
- 🆕 Pincode Check
- ✅ All v1 features preserved (WooCommerce sync, bulk editor, customizer, etc.)

---

## 📞 Support

If anything breaks, check:
1. PHP error log: cPanel → File Manager → `error_log`
2. Browser console (F12) for JS errors
3. WP Admin → Tools → Site Health

— Built with 🌿 for HariyaliBasket
