# 📱 SELLER QR CODE UPLOAD GUIDE

**Quick Guide:** Macam mana seller upload QR code untuk terima payment! 🎯

---

## 🔗 **DI MANA NAK UPLOAD?**

### **3 Ways to Access:**

#### **Method 1: Dashboard Quick Action** ⭐ (EASIEST!)
```
1. Login sebagai seller
2. Go to: Seller Dashboard
3. Click button: "QR Payment Setup" (purple/pink button)
4. ✅ Done! Dah sampai QR setup page!
```

#### **Method 2: Direct URL**
```
URL: http://localhost/seller/profile
atau
URL: http://your-domain.com/seller/profile

Just type dekat browser!
```

#### **Method 3: Bottom Navigation** (Mobile)
```
1. Look at bottom of screen
2. Click "QR Setup" icon (QR code icon)
3. ✅ Sampai!
```

---

## 📸 **CARA UPLOAD QR CODE**

### **Step-by-Step:**

```
╔══════════════════════════════════════╗
║  1. DAPATKAN QR CODE ANDA            ║
╚══════════════════════════════════════╝

Option A: DuitNow QR (RECOMMENDED!)
   ✅ Works with ALL Malaysian banks
   ✅ Universal payment
   
   How to get:
   1. Open banking app (Maybank/CIMB/Public/etc)
   2. Go to "DuitNow QR" or "Receive Money"
   3. Find your personal QR code
   4. Screenshot!

Option B: E-Wallet QR
   - Touch 'n Go eWallet
   - Boost
   - GrabPay
   - MAE
   
   How to get:
   1. Open e-wallet app
   2. Go to "Receive Money" or "My QR"
   3. Screenshot your QR code

╔══════════════════════════════════════╗
║  2. UPLOAD KE SYSTEM                 ║
╚══════════════════════════════════════╝

1. Go to /seller/profile
2. Scroll to "QR Payment Setup"
3. Click "Choose File"
4. Select your QR screenshot
5. (Optional) Select QR Type:
   - DuitNow QR
   - Touch 'n Go
   - Boost
   - etc.
6. (Optional) Add instructions:
   Example: "Scan dengan apa-apa banking app"
7. Click "Save QR Settings"
8. ✅ Done!

╔══════════════════════════════════════╗
║  3. CONFIRM SETUP                    ║
╚══════════════════════════════════════╝

You should see:
✅ "QR Payment: ENABLED"
✅ Your QR code displayed
✅ Green success message

Now buyers boleh bayar guna QR!
```

---

## 🎯 **FORM FIELDS EXPLAINED**

### **1. QR Code Image** (REQUIRED)
```
File type: Image (JPG, PNG, etc.)
Max size: 2MB
What: Screenshot of your DuitNow/e-wallet QR
```

### **2. QR Type** (OPTIONAL)
```
Options:
- DuitNow QR ⭐ (Most universal)
- Touch 'n Go eWallet
- Boost
- GrabPay
- Maybank MAE
- Other

Purpose: Help buyers tahu app mana nak guna
```

### **3. Payment Instructions** (OPTIONAL)
```
Max: 500 characters
Example:
"Sila scan dengan apa-apa banking app. 
WhatsApp saya lepas bayar: 012-3456789"

Purpose: Extra info untuk buyers
```

---

## 💡 **BEST PRACTICES**

### **✅ DO:**
```
✅ Use DuitNow QR (universal!)
✅ Screenshot clearly (QR code jelas)
✅ Update if change bank account
✅ Add WhatsApp number in instructions
✅ Test scan your own QR first
```

### **❌ DON'T:**
```
❌ Upload blurry image
❌ Upload expired QR
❌ Use temporary QR code
❌ Forget to save after upload
❌ Share QR password/PIN
```

---

## 🎨 **WHAT YOU'LL SEE**

### **Before Upload:**
```
┌─────────────────────────────┐
│ QR Payment Setup            │
├─────────────────────────────┤
│                             │
│ ⚠️  QR Payment: NOT SETUP   │
│                             │
│ Upload your QR code to      │
│ accept QR payments          │
│                             │
│ [Choose File]               │
│                             │
│ [QR Type dropdown]          │
│                             │
│ [Instructions textarea]     │
│                             │
│ [Save QR Settings]          │
│                             │
└─────────────────────────────┘
```

### **After Upload:**
```
┌─────────────────────────────┐
│ QR Payment Setup            │
├─────────────────────────────┤
│                             │
│ Current QR Code:            │
│  ┌─────────┐               │
│  │ [QR IMG]│               │
│  └─────────┘               │
│                             │
│ [Choose File] to replace    │
│                             │
│ [QR Type: DuitNow ▼]       │
│                             │
│ [Instructions...]           │
│                             │
│ [Update QR Settings]        │
│                             │
│ ✅ QR Payment: ENABLED      │
│ Buyers can now pay via QR!  │
│                             │
└─────────────────────────────┘
```

---

## 🧪 **TEST YOUR QR**

### **After upload, test:**
```
1. Create test order as buyer
2. Select "QR Payment"
3. Check if your QR displays
4. Try scan with your phone
5. ✅ Should work!
```

---

## 🔄 **UPDATE QR CODE**

### **To change QR:**
```
1. Go to /seller/profile
2. Upload new QR image
3. Old QR automatically replaced
4. Click "Update QR Settings"
5. ✅ Done!
```

---

## 📊 **FLOW DIAGRAM**

```
SELLER SETUP FLOW
─────────────────

Login as Seller
      ↓
Go to Dashboard
      ↓
Click "QR Payment Setup"
      ↓
Upload QR Screenshot
      ↓
(Optional) Select QR Type
      ↓
(Optional) Add Instructions
      ↓
Click "Save QR Settings"
      ↓
✅ QR ENABLED!
      ↓
Start Receiving QR Orders!


BUYER WILL SEE
──────────────

Checkout
      ↓
Select "QR Payment"
      ↓
Place Order
      ↓
See YOUR QR CODE!
      ↓
Scan & Pay
      ↓
Upload Proof
      ↓
You Verify
      ↓
Order Complete!
```

---

## ❓ **TROUBLESHOOTING**

### **Problem: Can't find QR Setup page**
```
Solution:
1. Clear browser cache
2. Logout & login again
3. Go directly: /seller/profile
4. Check you're logged in as SELLER
```

### **Problem: Upload fails**
```
Possible causes:
❌ File too large (max 2MB)
❌ Wrong file type (need image)
❌ No internet connection

Solution:
✅ Compress image first
✅ Use JPG or PNG
✅ Check connection
```

### **Problem: QR not displaying**
```
Solution:
1. Check if file uploaded successfully
2. Check storage permissions
3. Run: php artisan storage:link
4. Refresh page
```

---

## 🎯 **QUICK REFERENCE**

```
URL:           /seller/profile
Route Name:    seller.profile
Method:        GET (view), PUT (update)
Controller:    SellerController@profile
View:          resources/views/seller/profile.blade.php

Navigation:
- Dashboard button: "QR Payment Setup"
- Bottom nav: "QR Setup" icon
- Direct URL: /seller/profile

File Storage:
- Location: storage/app/public/qr-codes/
- Public URL: /storage/qr-codes/filename.jpg
- Max Size: 2MB
```

---

## 📱 **RECOMMENDED QR TYPES**

### **1. DuitNow QR** ⭐⭐⭐⭐⭐ (BEST!)
```
✅ Works with ALL banks
✅ No app needed
✅ Universal
✅ Most convenient for buyers

Get from: Any Malaysian banking app
```

### **2. Touch 'n Go** ⭐⭐⭐⭐
```
✅ Very popular
✅ Fast payment
⚠️  Need TNG app

Get from: TNG eWallet app
```

### **3. Boost** ⭐⭐⭐
```
✅ Common
⚠️  Need Boost app

Get from: Boost app
```

### **4. Bank-specific QR** ⭐⭐
```
✅ Direct to bank
⚠️  Limited to specific bank users

Get from: Your bank's app
```

---

## ✨ **TIPS FOR SUCCESS**

```
💡 Use DuitNow for maximum compatibility
💡 Add WhatsApp number for buyer questions
💡 Test QR before going live
💡 Update QR if change bank
💡 Reply buyers quickly after payment
💡 Verify payments promptly
💡 Keep QR image clear and readable
```

---

## 🎊 **AFTER SETUP**

### **What happens next:**
```
1. ✅ Buyers can select "QR Payment" at checkout
2. ✅ They will see your QR code
3. ✅ They scan & pay
4. ✅ They upload payment proof
5. ✅ You verify in seller orders
6. ✅ Order completed!
7. ✅ Money in your account instantly!
```

---

## 📞 **NEED HELP?**

```
Steps sudah clear kan? 😊

Summary:
1. Login as seller
2. Go to dashboard
3. Click "QR Payment Setup" button
4. Upload QR screenshot
5. Save
6. Done!

URL: /seller/profile
```

---

**SENANG JE! JUST 5 MINUTES TO SETUP! ⚡**

*Generated: December 13, 2025*  
*Updated navigation for easy access!* ✅

