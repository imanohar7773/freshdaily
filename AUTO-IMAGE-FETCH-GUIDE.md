# 🤖 Auto Image Fetcher — 30 Second Mein 71 Images!

Tool jo **automatic** Wikipedia/Pexels se aapke saare 71 products ki real photos fetch karega aur CSV bana ke dega. Aap sirf 2 button dabana — bas!

---

## 📦 File: `auto-image-fetcher.html`

👉 https://github.com/imanohar7773/freshdaily/blob/add-invoice-generator/auto-image-fetcher.html

---

## 🚀 USE KARNE KE SIRF 3 STEP

### **STEP 1 — Tool Open Karo**

1. Upar wala link kholo
2. Top right par **"Download raw file"** button dabao
3. File ko apne computer mein save karo
4. Browser mein open karo (Chrome/Safari)

### **STEP 2 — Auto-Fetch Karo**

1. **"▶️ Auto-Fetch All Images"** dabao
2. ⏳ 30-60 second wait karo — har product ki image automatic load hogi
3. Saari 71 products ke saamne ✅ Success ya ❌ Failed status dikhega
4. Failed wale (jo nahi mile) — wahan **"🔄 Replace"** button hai — manually URL paste kar sakte ho

### **STEP 3 — CSV Download Karo**

1. **"📥 Download CSV"** button dabao
2. File download hogi: `hariyalibasket-products-with-images.csv`
3. CSV mein har product + uski image URL hai

---

## ✨ WORDPRESS MEIN IMPORT

1. WordPress Admin → **Products → All Products → Import** (top button)
2. CSV file choose karo
3. **Continue** → **Run the importer**
4. **WordPress automatic saari images download karke** products mein lagayega! 🎉

⏱️ **Total time: 5 minute mein 71 products + images live!**

---

## 🎯 KAISE KAAM KARTA HAI?

Tool 2 sources se images fetch karta hai:

### Primary: 🌐 Wikipedia API
- Free, no API key needed
- Har product ke liye Wikipedia article ki main image fetch karta hai
- Example: "Mango" → Wikipedia ke "Mango" article ki photo
- High quality, scientifically accurate images

### Fallback: 🔍 Wikipedia Search
- Agar exact article nahi mila, search results ki top image use karta hai
- Smart matching — example: "Mango Alphonso" → "Alphonso (mango)" page

### Manual Backup: 📸 Pexels/Unsplash/Pixabay/Google
- Har product ke saamne button hai jo new tab mein search kholta hai
- "🔄 Replace" button — image URL paste karke replace kar sakte ho
- 4 sources: Pexels, Unsplash, Pixabay, Google

---

## 💡 SMART FEATURES

| Feature | Detail |
|---------|--------|
| ✅ **Auto-fetch** | 71 images one click mein |
| 💾 **Auto-save** | Browser mein progress save — band karke continue kar sakte ho |
| 🔄 **Re-fetch** | Failed wale dobara try kar sakte ho |
| ✏️ **Manual override** | Koi bhi image manually replace kar sakte ho |
| 📥 **CSV export** | WooCommerce import-ready format |
| 🚦 **Status badges** | ✓ Success / ⏳ Loading / ✗ Failed clearly visible |

---

## 🆚 DOOSRE TOOLS SE DIFFERENCE

| Feature | `auto-image-fetcher.html` | `real-image-fetcher.html` | `image-generator.html` |
|---------|:---:|:---:|:---:|
| Automatic fetch | ✅ Yes (1-click) | ❌ Manual | N/A |
| Real photos | ✅ Wikipedia | ✅ Pexels (manual) | ❌ Emoji only |
| Time required | 1 minute | 30+ minutes | 2 minutes |
| Quality | Good (Wikimedia) | Best (you pick) | Stylish (emoji) |

**Mera Suggestion:** Pehle `auto-image-fetcher.html` use karo — instant results. Agar koi specific image change karni ho toh "🔄 Replace" button se Pexels se manually dal lo.

---

## ❓ TROUBLESHOOTING

### "Sab failed dikha raha hai"
- Internet check karo
- Wait karke "🔄 Re-Fetch All" dabao (Wikipedia rate limit ho sakta hai)
- VPN off kar do agar use kar rahe ho

### "Image dikha raha hai but galat product ki hai"
- Click "🔄 Replace" → manually correct image URL paste karo
- Pexels/Unsplash se accha image dhundo

### "WordPress mein import ke baad image nahi dikh rahi"
- WordPress hosting ko external URLs se download karne ki permission chahiye
- `wp-config.php` mein add karo:
  ```php
  @ini_set('max_execution_time', 300);
  @ini_set('memory_limit', '512M');
  ```

### "CSV mein khali rows hain"
- Failed products manual replace karo, ya delete kar do CSV se before import

---

## 📊 EXPECTED SUCCESS RATE

Aapke 71 products ke liye Wikipedia se expected results:

| Category | Expected Success |
|----------|-----------------|
| Common fruits (Apple, Banana, Mango) | 100% ✅ |
| Common vegetables (Tomato, Onion, Potato) | 100% ✅ |
| Indian-specific (Aloo, Bhindi, Karela) | 95% ✅ |
| Regional names (Chola Fali, Kachri) | 70% ✅ |
| Brand variants (Mango Alphonso, Apple Imp.) | 90% ✅ |

**Realistic Expectation:** ~60-65 out of 71 will succeed automatically. Baki ke 5-10 manually replace kar sakte ho 2-3 minute mein.

---

## 🔗 Aapke Paas Total Tools Ab

| File | Use |
|------|-----|
| 🆕 **`auto-image-fetcher.html`** | **Auto fetch + CSV — fastest!** |
| `real-image-fetcher.html` | Manual Pexels/Unsplash search per product |
| `image-generator.html` | Emoji-based product cards (backup) |
| `products-master-list.csv` | Text-only product list |

---

🌿 **HariyaliBasket** — Auto images, asli photos, 2-click setup!
