# 🌿 Auto Invoice on Order Placement — Setup Guide

Customer jab website pe order place karega, **automatically beautiful invoice ban jaayega** same HariyaliBasket format mein. Customer print/PDF/WhatsApp kar sakta hai.

---

## 🎯 Kya Hoga?

**Pehle (abhi):**
1. Customer cart mein items add karta hai
2. "Order on WhatsApp" dabaata hai
3. Form bharta hai (naam, society, phone, payment)
4. WhatsApp khulta hai with order text
5. Order khatam ✅

**Ab (naya flow):**
1. Customer cart mein items add karta hai
2. "Order on WhatsApp" dabaata hai
3. Form bharta hai
4. WhatsApp khulta hai with order text
5. **🎉 SAATH HI Invoice automatic ban ke pop-up hota hai!**
6. Customer ke paas options:
   - 🖨️ **Print / PDF** — bill print ya PDF save karo
   - 📱 **WhatsApp Bhejo** — bill ka formatted text WhatsApp pe share
   - ✅ **OK · Naya Order** — close kar do, naya order ke liye taiyaar

---

## 📦 File: `invoice-auto-snippet.php`

Ye ek single snippet hai jo aapke existing template mein paste karna hai. Kuch delete nahi karna — sirf neeche add karna hai.

---

## 🚀 INSTALL — Sirf 3 Step

### **STEP 1 — Apna Existing Template File Open Karo**

Aapke WordPress theme mein wo file kholo jisme aapne ye saara cart + order code likha hua hai. Ye file usually:

- `/wp-content/themes/[your-theme]/page-template.php`
- Ya `header.php`
- Ya jis bhi file mein aapka `<?php wp_footer(); ?>` aur `</body>` hai

### **STEP 2 — Snippet Paste Karo**

Apni file mein neeche scroll karke ye line dhundo:

```php
<?php wp_footer(); ?>
```

**Iss line ke OOPAR (just before)** ye poora content paste karo:

```html
<!-- Yahan se invoice-auto-snippet.php ka content paste karo -->
<!-- (PHP starting tag <?php /* ... */ ?> wala comment optional hai, sirf HTML+JS+CSS bhi paste kar sakte ho) -->
```

📁 Snippet file: `invoice-auto-snippet.php`

GitHub se download karne ka link:
👉 https://github.com/imanohar7773/freshdaily/blob/add-invoice-generator/invoice-auto-snippet.php

(Page khol ke "Raw" button dabao → poora text copy karo)

### **STEP 3 — File Save Karo aur Refresh**

- Apni template file save kar do
- Browser mein website refresh karo (Ctrl + F5)
- Test karo: koi bhi item cart mein add karo → order place karo → invoice automatic dikhega! ✅

---

## 🧪 KAISE TEST KARE

1. Apni website kholo
2. Koi bhi 2-3 items cart mein add karo (example: 1 Kg Aloo, 500g Pyaaz)
3. "📱 Order on WhatsApp" button dabao
4. Form bharo:
   - Naam: **Test User**
   - Society: **Test Colony**
   - Phone: **9876543210** (koi bhi 10-digit number)
5. "📱 WhatsApp pe Order Bhejein" dabao
6. **Aapko ye dikhega:**
   - WhatsApp tab khulega (existing)
   - Saath hi screen pe sundar invoice modal dikhega 🎉

---

## 🎨 INVOICE MEIN KYA-KYA HOTA HAI?

```
┌─────────────────────────────────────┐
│  🌿 HariyaliBasket                   │  ← Logo + Brand
│  FARM TO DOORSTEP                    │
│  📱 +91 80003 44554 · Jaipur        │
├─────────────────────────────────────┤
│  🧾 INVOICE          HB-260525-378   │  ← Auto bill number
├─────────────────────────────────────┤
│  ✅ Aapka order successfully place    │
│  ho gaya!                            │
├─────────────────────────────────────┤
│  Date: 25 May 2026  Time: 02:30 PM   │
├─────────────────────────────────────┤
│  🚚 DELIVER TO                       │
│  Test User                           │
│  Test Colony · Block A · Flat 101    │
│  📞 9876543210                       │
├─────────────────────────────────────┤
│  # │ Item  │ Qty  │ Rate │ Amount   │
│  1 │ Aloo  │ 1 Kg │ ₹30  │ ₹30      │
│  2 │ Pyaaz │ 500g │ ₹20  │ ₹20      │
├─────────────────────────────────────┤
│  Subtotal:                  ₹50      │
│  🚚 Delivery:               ₹69      │
│  ─────────────────────────────       │
│  GRAND TOTAL:              ₹119      │
├─────────────────────────────────────┤
│  💳 Payment: Cash on Delivery        │
├─────────────────────────────────────┤
│  🙏 Dhanyawaad! Aapka Order Mil Gaya!│
│  📦 Kal 4 PM tak delivery hogi       │
│  🌿 100% Farm Fresh Guarantee        │
└─────────────────────────────────────┘

[🖨️ Print/PDF]  [📱 WhatsApp Bhejo]
[✅ OK · Naya Order]
```

---

## ⚙️ AUTO FEATURES

| Feature | Detail |
|---------|--------|
| 🧾 **Bill Number** | Automatic: `HB-YYMMDD-RAND` (e.g. HB-260525-378) |
| 📅 **Date / Time** | Current date & time auto-fill |
| 🛒 **Items** | Cart se directly aate hain — naam, qty, rate, amount |
| 💰 **Total Calculation** | Subtotal + delivery automatic |
| 🎁 **Savings Display** | Agar discount hai ya FREE delivery hai toh dikhata hai |
| 📲 **UPI Txn ID** | Agar UPI payment hai toh transaction ID bhi bill mein |
| 🖨️ **Print Format** | Print karne pe sirf invoice print hoti hai, baaki page hide ho jaata hai |

---

## ❓ TROUBLESHOOTING

### "Invoice modal nahi dikh raha"
- Browser console (F12) kholo, error check karo
- Ye sunishchit karo ki snippet `<?php wp_footer(); ?>` ke OOPAR hai
- Caching plugin ho toh cache clear karo
- Hard refresh: `Ctrl + Shift + R`

### "Print mein design break ho raha hai"
- Print preview mein "More settings" → "Background graphics" enable karo
- Ya "Save as PDF" use karo (better quality)

### "WhatsApp button kaam nahi kar raha"
- WhatsApp mobile pe install hona chahiye, ya WhatsApp Web khula hona chahiye
- Computer pe agar nahi khul raha toh QR scan karke WA Web open kar lo

### "Bill number har customer ke liye unique chahiye"
- Abhi bill number `date + random` se ban raha hai (e.g. HB-260525-378)
- 99% case mein unique hoga
- Agar bilkul guarantee chahiye toh server-side number generate karna padega (Google Sheet se ya WordPress se)

---

## 💡 BONUS: WordPress Plugin Bhi Hai!

Agar aapko **WordPress admin se manually bill banana** ho (jaise koi customer phone pe order kare aur aap admin se bill banao), toh aapke paas already WordPress plugin hai:

📁 File: `hb-invoice-generator.php`
📖 Guide: `INSTALL-INVOICE-PLUGIN.md`

Dono cheezein parallel work karti hain:
- **Website pe order** → auto invoice (ye snippet se)
- **Manual bill banao** → admin plugin se

---

🌿 **HariyaliBasket** — Jaipur ki Taazi Sabzi
