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



---

# 🔍 SEO Setup Guide — Get Your Site on Google

Theme already includes complete technical SEO. **But you must do 3 manual steps:**

## ⭐ Step 1: Google Search Console (10 min)

This tells Google your site exists.

1. Go to **https://search.google.com/search-console**
2. Add property → enter `hariyalibasket.com`
3. Verify ownership: pick **HTML tag method** → Google gives you a `<meta>` code
4. WordPress Admin → **Customize → Theme File Editor** OR add the meta to `header.php` after `<meta name="theme-color">`
5. Click "Verify"
6. Submit sitemap: **Sitemaps** → enter `sitemap.xml` → Submit
   - Your sitemap is auto-generated at: `https://hariyalibasket.com/sitemap.xml`

## ⭐ Step 2: Google My Business (15 min — CRITICAL for local search)

This is **the #1 thing** for local Jaipur ranking. Without it you won't appear in "vegetables near me" searches.

1. Go to **https://business.google.com**
2. Add business → search "HariyaliBasket" → Add new
3. Fill in:
   - **Name:** HariyaliBasket
   - **Category:** Vegetable Wholesale, Online Grocery, Delivery Service
   - **Service area:** Jaipur (add specific colonies)
   - **Phone:** +91 80003 44554
   - **Website:** https://hariyalibasket.com
   - **Hours:** 9 AM – 9 PM (Mon-Sun)
4. Verify via postcard or phone (takes 3-7 days)
5. Once verified: add 5+ photos (logo, vegetables, delivery), description, products

After verification you'll appear in Google Maps + local search results.

## ⭐ Step 3: Get Reviews (ongoing)

Google ranks businesses with more 5-star reviews higher.

1. Send WhatsApp link to happy customers: `https://g.page/r/YOUR-BUSINESS-ID/review`
2. Ask 5 satisfied customers per week
3. Aim for 50+ reviews in 3 months

## 📊 Optional — Submit to Other Indexes

- **Bing Webmaster:** https://www.bing.com/webmasters
- **Justdial:** Free listing → 1 lakh+ Jaipur searches/month
- **Sulekha:** Free listing
- **Yellow Pages India:** Free listing

## 🎯 What's Already Built In (Code-Level SEO)

The theme automatically handles:

✅ **Title tags** — optimized with brand + tagline
✅ **Meta description** — auto-generated per page (160 chars)
✅ **Keywords meta** — Jaipur + vegetable + delivery focused
✅ **Canonical URLs** — prevents duplicate content
✅ **Open Graph** — Facebook/WhatsApp share previews
✅ **Twitter Cards** — Twitter share previews
✅ **Schema.org JSON-LD:**
  - **Organization** — your business identity
  - **LocalBusiness + GroceryStore** — Jaipur location, hours, phone, area served
  - **WebSite + SearchAction** — sitelinks search box in Google
  - **FAQPage** — FAQ rich results in Google
  - **ItemList** — top 20 products on homepage
  - **Product** — each product page (price, availability, brand)
✅ **XML Sitemap** — `/sitemap.xml` auto-updated
✅ **robots.txt** — sitemap reference + crawl directives
✅ **Geo tags** — Jaipur coordinates for local search
✅ **Mobile-first** — Google mobile-friendly tested
✅ **Fast loading** — modular CSS/JS

## 🧪 Test Your SEO

After deployment:

1. **Google Mobile-Friendly Test:** https://search.google.com/test/mobile-friendly
   - Enter your URL → should pass

2. **Rich Results Test:** https://search.google.com/test/rich-results
   - Enter your URL → should show: LocalBusiness, FAQ, Product, Organization

3. **PageSpeed Insights:** https://pagespeed.web.dev
   - Enter URL → aim for 80+ on mobile, 90+ on desktop

4. **Schema.org Validator:** https://validator.schema.org
   - Verify all structured data is valid

## ⚠️ Important Hostinger Steps

After uploading the theme:

1. **WordPress Admin → Settings → Permalinks:**
   - Select **"Post name"** (not Plain)
   - Click **Save Changes**
   - This activates `/sitemap.xml` and clean URLs

2. **Hostinger SSL:**
   - cPanel → SSL → Force HTTPS (Google ranks HTTPS sites higher)

3. **Cache plugin (recommended):**
   - Install **LiteSpeed Cache** (Hostinger has LiteSpeed servers)
   - Enable: combine CSS, combine JS, lazy load images
   - Page speed will jump 30-50%

## 📈 Expected Timeline

| Week | What Happens |
|------|--------------|
| Week 1 | Google starts crawling (after Search Console submission) |
| Week 2-3 | First pages indexed, brand searches start working |
| Week 4-6 | "hariyalibasket Jaipur" → you appear |
| Week 8-12 | Long-tail keywords ("vegetable delivery Vaishali Nagar") |
| Month 4-6 | Organic traffic from Google |

## 💡 Pro Tips

- **Add real product images** — Google Image Search drives traffic
- **Write blog posts** — recipes, vegetable benefits (1 per week)
- **Get backlinks** — list on Justdial, Sulekha, local Jaipur food blogs
- **Social signals** — Instagram daily posts, Facebook page active
- **Internal linking** — link from blog → products
