# 📸 Real Product Images — 3 Tarike

Aapke 71 products ke liye **asli photos** lagane ke 3 tarike hain. Apni convenience se choose karo.

---

## 🥇 OPTION 1: WordPress Plugin (SABSE EASY!)

**Time: 10-15 minute mein 71 products done!**

### Steps:

1. **Plugin Install Karo**
   - WordPress Admin → **Plugins → Add New**
   - Search karo: **"Instant Images"** (by ConnektMedia)
   - Install + Activate

   YA

   - Search: **"Pexels Free Stock Photos"**
   - Install + Activate

2. **Media Library Mein Search Karo**
   - **Media → Library**
   - Top par **"Instant Images"** ya **"Pexels"** tab dikhega
   - Search box mein product naam likho — example: "tomato"
   - 100+ free images dikhenge
   - Click karo → automatic Media Library mein save

3. **Product Mein Set Karo**
   - **Products → All Products**
   - Product edit karo
   - **Featured Image** section mein → **Set featured image**
   - Search box mein product naam type karo
   - Image select karo → Done!

### ✅ Pros:
- Zero coding, zero downloading
- Lakhe images available
- WordPress mein hi sab kuch
- Free aur unlimited

---

## 🥈 OPTION 2: HTML Image Fetcher Tool (Mid-level)

**Time: 30-40 minute mein 71 products done!**

Maine ek tool banaya hai jo aapke kaam ko aasan karta hai:

### File: `real-image-fetcher.html`

👉 https://github.com/imanohar7773/freshdaily/blob/add-invoice-generator/real-image-fetcher.html

### Steps:

1. **Tool Open Karo**
   - File download karo, browser mein open karo
   - Aapke saare 71 products dikhenge

2. **Har Product Ke Liye:**
   - **"📸 Pexels"** ya **"🌅 Unsplash"** button dabao → naya tab khulega
   - Wahaan se accha image dhundo
   - Image par **right-click → "Copy image address"**
   - Wapas tool mein aake URL paste karo
   - **"Save"** dabao → preview dikhega ✓

3. **Progress Save Hota Hai**
   - Localstorage mein save hota hai
   - Browser band karke bhi continue kar sakte ho

4. **CSV Download Karo**
   - Sab products done hone par **"📥 Download CSV"** dabao
   - File download hogi: `hariyalibasket-products-with-images.csv`

5. **WordPress Mein Import Karo**
   - WordPress Admin → **Products → All Products → Import**
   - CSV upload → **Run the importer**
   - **WordPress automatically saari images download kar lega!** 🎉

### ✅ Pros:
- Full control over which image you want
- Bulk import — sab ek baar mein
- Progress save hota hai
- Free images sirf

---

## 🥉 OPTION 3: Manual Search & Download

Sabse slow but no setup:

1. Pexels.com / Unsplash.com kholo
2. Har product search karo
3. Image download karo
4. WordPress Media → Add New → upload
5. Product edit → Featured Image set

---

## 🎯 Mera Suggestion

**Aap Option 1 (WordPress plugin) try karo pehle** — 99% chances hai ki 15 minute mein sab ho jayega.

Agar aap chahte ho ki ek hi CSV mein sab images aaye (bulk import), toh **Option 2 (HTML Tool)** use karo.

---

## 🔗 Free Image Sources (No Copyright Issue)

| Site | Best For | Direct URL |
|------|----------|------------|
| **Pexels** | High quality, free, commercial use OK | https://www.pexels.com |
| **Unsplash** | Premium quality, artistic | https://unsplash.com |
| **Pixabay** | Massive library, includes vectors | https://pixabay.com |
| **Freepik** | Vector + photos | https://www.freepik.com (some need attribution) |

**Sab free hain commercial use ke liye!** Aap apni website pe use kar sakte ho.

---

## ❓ FAQ

### Q: Tool mein image preview nahi dikh raha?
A: URL galat copy hua hai. Image par right-click → **"Copy image address"** (NOT "Copy image"). URL `.jpg` ya `.png` ya `.webp` se end hona chahiye.

### Q: WordPress import ke time error aaya?
A: CSV file `UTF-8` format mein hi save karo. Excel mein "Save As" → Format: "CSV UTF-8 (Comma delimited)"

### Q: Images download nahi ho rahin import ke time?
A: Apne hosting ke `wp-config.php` mein add karo:
```php
@ini_set( 'max_execution_time', 300 );
```

### Q: Kuch images blurry aa rahin hain?
A: Pexels/Unsplash mein image kholo → high resolution version copy karo

---

## 📦 Aapke Paas Total Resources

| File | Use |
|------|-----|
| `image-generator.html` | Emoji-based product cards (backup option) |
| `real-image-fetcher.html` | **Real photos ke URLs collect karo** |
| `products-master-list.csv` | 71 products bulk import (text only) |
| `products-with-images.csv` | (Tool se generate hota hai with image URLs) |

---

🌿 **HariyaliBasket** — Asli Photos Se Asli Products!
