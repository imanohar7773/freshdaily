# 🖼️ HB Bulk Image Setter — Existing Products Ki Images Change Karo

Aapke website pe **already maujood products** ki emoji images ko ek click mein **real photos** se replace karne ka WordPress plugin.

---

## 🎯 Kya Karega Ye Plugin?

✅ **Auto-fetch images** — har product ke liye Wikipedia se sahi photo dhoondhta hai
✅ **Featured image set** — har product ke saath WordPress mein featured image attach kar deta hai
✅ **Frontend pe automatic dikhne lagta** — emoji ki jagah real photo dikhne lagega
✅ **No template editing** — aapki existing template ko touch nahi karta
✅ **One-click bulk** — saare 71 products ek button mein

---

## 📦 File: `hb-bulk-image-setter.php`

👉 https://github.com/imanohar7773/freshdaily/blob/add-invoice-generator/hb-bulk-image-setter.php

---

## 🚀 INSTALLATION (3 Step)

### **STEP 1 — Plugin File Download Karo**

1. Upar wala link kholo
2. Top right par **"Download raw file"** button dabao
3. File save ho jayegi: `hb-bulk-image-setter.php`

### **STEP 2 — WordPress Mein Upload Karo**

#### Option A: Admin Se Upload (Easy)

1. File ko **ZIP** banao:
   - Right-click → "Compressed (zipped) folder"
   - Naam: `hb-bulk-image-setter.zip`
2. WordPress Admin → **Plugins → Add New**
3. Top par **"Upload Plugin"** click karo
4. ZIP file choose karo → **Install Now**
5. **Activate Plugin** dabao

#### Option B: FTP/cPanel Se

1. cPanel/FTP se: `/wp-content/plugins/` folder kholo
2. Naya folder banao: `hb-bulk-image-setter`
3. Folder ke andar `hb-bulk-image-setter.php` upload karo
4. WordPress Admin → Plugins → "HariyaliBasket Bulk Image Setter" → **Activate**

### **STEP 3 — Images Auto-Fetch Karo**

1. WordPress Admin → left sidebar mein **🖼️ HB Images** dikhega
2. Click karo
3. Aapke saare products grid mein dikhenge
4. Top par 2 button hain:

   | Button | Kya Karega |
   |---|---|
   | **▶️ Auto-Fetch All Images (jin mein nahi hai)** | Sirf un products ki image fetch karega jin mein abhi koi featured image nahi hai |
   | **🔄 Re-Fetch ALL (existing replace karo)** | SAARE products ki images dobara fetch karega (existing replace ho jayengi) |

5. Pehli baar use kar rahe ho toh **"Auto-Fetch All Images"** dabao
6. ⏳ 30-60 second wait karo — har product ke liye image automatic load hogi
7. Live progress dikhegi: ✅ Success / ⏳ Loading / ❌ Failed
8. Done!

### **STEP 4 — Website Check Karo**

1. Apni website kholo (refresh karo: `Ctrl + Shift + R`)
2. Products section mein dekho
3. **Emoji ki jagah ab real photos dikh rahi hongi!** 🎉

---

## ✨ FEATURES

### 🤖 Smart Mapping
Plugin ko 71 products ke liye automatic Wikipedia article mappings pata hain:

| Aapka Product | Wikipedia Article |
|---|---|
| Apple Green Imp. | Granny Smith |
| Mango Alphonso | Alphonso (mango) |
| Sweet Lime (Mosambi) | Mosambi |
| Lady Finger (Bhindi) | Okra |
| Bitter Gourd (Karela) | Bitter melon |
| Drumsticks (Sahjan) | Moringa oleifera |
| Onion Small | Shallot |
| Spinach (Palak) | Spinach |
| ... aur 60+ more | |

### 🎨 Frontend Auto-Replace
Plugin install karte hi:
- Aapki template ko **touch nahi karta**
- Apne aap emoji ki jagah real image inject karta hai
- Hover pe smooth zoom effect
- Wishlist aur cart mein bhi images dikhti hain

### 📊 Real-time Stats
Admin page par live counter:
- Total products
- ✅ Image set hue
- ⏳ Image baaki

### 🔄 Failed Products
Agar koi product ki image automatic nahi mili:
- Card par ❌ Failed dikhega
- Hover karne par error reason dikhega
- Aap manually featured image set kar sakte ho usually:
  - Products → Edit product → Featured image → Set

---

## ⚙️ KAISE KAAM KARTA HAI

### Backend Process
```
For each hb_product:
  1. Title se Wikipedia article title nikalo
     (e.g., "Mango Alphonso" → "Alphonso_(mango)")
  2. Wikipedia API se image URL fetch karo
  3. Image download karo aur Media Library mein save karo
  4. Featured image set karo
```

### Frontend Process
```
On every page load:
  1. Plugin checks all hb_products with featured images
  2. Outputs JS in footer with image URL map
  3. Overrides getEmoji() function:
     - If product has featured image → return <img>
     - Else → return original emoji
  4. Re-renders products with real images
```

---

## ❓ TROUBLESHOOTING

### "Plugin activate nahi ho raha"
- PHP version 7.0+ chahiye (mostly already hai)
- File correctly upload hui hai? `wp-content/plugins/` mein hona chahiye

### "Some images failed"
- Wikipedia rate limit ho sakta hai — 5 minute baad **"Auto-Fetch All Images"** dobara dabao
- Specific product ki image manually set kar sakte ho:
  - Products → Edit → Featured image → Use Pexels/Unsplash plugin

### "Frontend pe abhi bhi emoji dikh rahi"
- Browser cache clear karo: `Ctrl + Shift + R`
- Caching plugin (WP Rocket / W3 Total Cache) ho toh cache purge karo
- Verify karo: WordPress Admin → Posts → hb_product → product edit → Featured image set hai?

### "Wikipedia se accha image nahi mila"
- Manual replacement: Products → Edit → Featured image → Replace
- WordPress mein "Instant Images" plugin install karo Pexels search ke liye

### "Memory ya timeout error"
- `wp-config.php` mein add karo:
  ```php
  @ini_set( 'max_execution_time', 300 );
  @ini_set( 'memory_limit', '512M' );
  ```

---

## 🆚 PEHLE WALE TOOLS SE DIFFERENCE

| Tool | Use Case |
|---|---|
| `auto-image-fetcher.html` | **NEW SITE** — CSV import karne ke liye |
| `image-generator.html` | Emoji-style images banane ke liye |
| `real-image-fetcher.html` | Manual Pexels picking ke liye |
| 🆕 **`hb-bulk-image-setter.php`** | **EXISTING SITE — 1 click mein update** ⭐ |

**Aapke case mein**: Aapke products already site pe hain, isliye **`hb-bulk-image-setter.php`** SABSE BEST option hai!

---

## 🎬 EXPECTED RESULT

### Before:
```
┌──────────────┐  ┌──────────────┐  ┌──────────────┐
│      🥔       │  │      🧅       │  │      🍅       │
│              │  │              │  │              │
│  Aloo        │  │  Pyaaz       │  │  Tamatar     │
│  ₹25/Kg      │  │  ₹35/Kg      │  │  ₹30/Kg      │
└──────────────┘  └──────────────┘  └──────────────┘
```

### After:
```
┌──────────────┐  ┌──────────────┐  ┌──────────────┐
│ [Real photo] │  │ [Real photo] │  │ [Real photo] │
│ of potatoes  │  │ of onions    │  │ of tomatoes  │
│  Aloo        │  │  Pyaaz       │  │  Tamatar     │
│  ₹25/Kg      │  │  ₹35/Kg      │  │  ₹30/Kg      │
└──────────────┘  └──────────────┘  └──────────────┘
```

---

## 📊 EXPECTED SUCCESS RATE

| Category | Auto-Success Rate |
|---|---|
| Common fruits (Apple, Banana, Mango, Pineapple) | **100%** ✅ |
| Common vegetables (Tomato, Onion, Potato, Carrot) | **100%** ✅ |
| Indian-specific (Aloo, Bhindi, Karela, Lauki) | **95%** ✅ |
| Regional (Chola Fali, Kachri, Tinda) | **70%** ✅ |

**Realistic:** ~60-65 out of 71 will get images automatically. Failed wale 5-10 manually 5 min mein set ho jayenge.

---

## 💡 BONUS TIP

Agar aapke paas **WooCommerce products** bhi hain (`hb_product` ke alawa), aur unke liye bhi automatic images chahiye:

Plugin file mein line dhundo:
```php
'post_type' => 'hb_product',
```

Aur change karo:
```php
'post_type' => [ 'hb_product', 'product' ],
```

Save karo — ab WooCommerce products ke liye bhi kaam karega.

---

🌿 **HariyaliBasket** — Real photos, zero manual work, 1-click update!
