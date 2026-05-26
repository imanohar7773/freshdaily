# 🌿 HariyaliBasket Invoice Plugin — Install Guide

WordPress admin se directly bill banane ka simple plugin.

## 📦 File: `hb-invoice-generator.php`

---

## 🚀 INSTALL KARNE KE 2 TARIKE

### ✅ TARIKA 1 — WordPress Admin Se (SABSE EASY)

1. **Plugin file ko ZIP banao**
   - `hb-invoice-generator.php` ko apne computer pe download karo
   - Right-click → "Send to" → "Compressed (zipped) folder"
   - Ya online ZIP banao: file ko ek folder mein daalo, fir folder ko ZIP karo
   - File ka naam rakho: `hb-invoice-generator.zip`

2. **WordPress mein upload karo**
   - WordPress Admin login karo
   - Left sidebar mein: **Plugins → Add New**
   - Upar **"Upload Plugin"** button par click karo
   - **Choose File** → apni `hb-invoice-generator.zip` select karo
   - **Install Now** click karo

3. **Activate karo**
   - Install hone ke baad **"Activate Plugin"** par click karo
   - Bas! Ho gaya.

---

### ✅ TARIKA 2 — FTP / cPanel Se (Agar admin se na ho)

1. **cPanel ya FTP** se apni website ke files kholo
2. Iss path par jao: `wp-content/plugins/`
3. Wahaan ek **naya folder** banao naam: `hb-invoice-generator`
4. Folder ke andar `hb-invoice-generator.php` file upload karo
5. WordPress Admin → **Plugins** → "HariyaliBasket Invoice Generator" → **Activate**

---

## 🧾 PLUGIN KAISE USE KARE

### 1️⃣ Bill Banao

- WordPress Admin login → left sidebar mein **🧾 HB Invoice** par click karo
- Form bharo:
  - 👤 Customer ka naam
  - 📞 Phone
  - 🏘️ Society / Block / Flat
  - 🛒 Items add karo (item naam likhne par auto-suggest aayega — already added products dikhte hain)
  - 💳 Payment method choose karo (COD / UPI / Paid)
  - 🚚 Delivery charge
  - 🎁 Discount
- **"🧾 Bill Generate Karo"** button dabao

### 2️⃣ Bill Ban Gaya — Ab 5 Options:

| Button | Kya Hota Hai |
|--------|--------------|
| 🖨️ **Print / PDF** | Browser print menu khulega → "Save as PDF" select karke download kar lo |
| 📱 **WhatsApp** | Customer ke phone par directly bill ka text WhatsApp pe khulega |
| 💾 **Save Bill** | Bill database mein save ho jaayega — baad mein dekh sakte ho |
| ✏️ **Edit** | Form wapas khulega — kuch change karna ho toh |
| 🆕 **Naya Bill** | Sab clear, naya customer ka bill banao (bill number auto-increment) |

### 3️⃣ Saved Bills Dekhna

- Left sidebar: **🧾 HB Invoice → 📋 Saved Bills**
- Yahaan saare past bills dikhenge — naam, phone, date, total, payment ke saath

---

## ⚡ AUTO-FEATURES (Aapka Kaam Aasan!)

- ✅ **Bill number auto-increment** — HB-0001, HB-0002, HB-0003... khud badhega
- ✅ **Today's date** auto-fill hoti hai
- ✅ **Item suggest** — naam type karte hi pehle se save kiye products dikhte hain (rate auto-fill)
- ✅ **WooCommerce + hb_product** dono se items aate hain dropdown mein
- ✅ **Total calculation** automatic — qty × rate, subtotal, discount, delivery, grand total
- ✅ **Bills history** — sab kuch database mein save

---

## 🎨 BILL FORMAT (Hamesha Same)

Har bill mein automatic ye sab aata hai:

```
🌿 HariyaliBasket          ← Logo + brand
FARM TO DOORSTEP           ← Tagline
📱 +91 80003 44554

🧾 INVOICE         HB-0001 ← Bill number (auto)
Date: 25 May 2026  Time: 02:30 PM

🚚 DELIVER TO
[Customer Name]
[Society · Block · Flat]
📞 [Phone]

┌──────────────────────────────┐
│ # │ Item │ Qty │ Rate │ Amt  │
├──────────────────────────────┤
│ 1 │ Aloo │ 2Kg │ ₹30  │ ₹60  │
│ 2 │ ...                      │
└──────────────────────────────┘

Subtotal:        ₹500
🎁 Discount:    - ₹50
🚚 Delivery:    FREE
─────────────────────
GRAND TOTAL:    ₹450  ← Big & Orange

💰 ₹50 bachaye · 🎉 FREE Delivery

💳 Payment: Cash on Delivery
📝 Note: Kal subah deliver karna

🙏 Dhanyawaad! Aapka order mila!
🌿 100% Farm Fresh Guarantee · Free Replacement
WhatsApp: +91 80003 44554 · UPI: imanohar07773@ybl
```

---

## 🆘 Problem Aaye Toh?

- **Plugin activate nahi ho raha?** → PHP version 7.0+ chahiye (mostly already hota hai)
- **Menu nahi dikh raha?** → Page refresh karo (Ctrl + F5)
- **Items suggest nahi ho rahe?** → Pehle WooCommerce ya `hb_product` mein kuch products add karne hote hain (jo aapne already kar rakha hai based on aapke index.html se)
- **Print mein design break ho raha hai?** → Print preview mein "More settings" → "Background graphics" enable karo

---

## 📌 BONUS TIPS

1. **Phone se bhi use kar sakte ho** — WordPress admin mobile-friendly hai
2. **Multiple users** — agar staff ko bhi bill banane dena hai, unhe Editor role do
3. **Backup** — `Saved Bills` page se purane bills hamesha milte rahenge

---

🌿 **HariyaliBasket** — Jaipur ki Taazi Sabzi
