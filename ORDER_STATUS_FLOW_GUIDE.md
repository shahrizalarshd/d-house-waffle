# 📋 Order Status Flow Guide
**D'house Waffle - Owner & Staff Manual**

**Date:** December 14, 2025  
**For:** Owner & Staff

---

## 🎯 Order Status Overview

D'house Waffle menggunakan **4 order status** untuk tracking waffle orders dari pending hingga siap.

---

## 📊 Order Status Types

### 1. ⏳ **PENDING** (Baru Masuk)
**Meaning:** Order baru diterima, belum start prepare  
**Color:** Yellow badge  
**Customer sees:** "Order placed, waiting to be prepared"

**What to do:**
- Review order details
- Check ingredients available
- Start preparing kalau ready

---

### 2. 👨‍🍳 **PREPARING** (Sedang Buat)
**Meaning:** Sedang prepare waffle  
**Color:** Blue badge  
**Customer sees:** "Your waffles are being prepared"

**What to do:**
- Make the waffles
- Prepare toppings
- Pack nicely

---

### 3. 🎉 **READY** (Siap Pickup)
**Meaning:** Waffle dah siap, tunggu customer pickup  
**Color:** Orange/Amber badge  
**Customer sees:** "Ready for pickup! Come collect your order"

**What to do:**
- Place order at pickup counter
- Wait for customer to collect
- Keep warm if possible

---

### 4. ✅ **COMPLETED** (Selesai)
**Meaning:** Customer dah collect, order complete  
**Color:** Green badge  
**Customer sees:** "Order completed. Enjoy your waffles!"

**What to do:**
- Archive
- Profit counted
- Customer can review (future feature)

---

### 5. ❌ **CANCELLED** (Dibatal)
**Meaning:** Order cancelled  
**Color:** Red badge  
**Customer sees:** "Order cancelled"

**Reasons:**
- Out of stock
- Customer request
- Payment issue

---

## 🔄 Complete Order Flow

### Standard Flow (Happy Path)

```
Customer Place Order
        ↓
📱 1. PENDING (New Order Alert)
        ↓
   [Owner/Staff: Click "Preparing"]
        ↓
👨‍🍳 2. PREPARING (Making Waffles)
        ↓
   [Owner/Staff: Click "Ready"]
        ↓
🎉 3. READY FOR PICKUP (Notify Customer)
        ↓
   [Customer Arrives & Collects]
   [Owner/Staff: Click "Completed"]
        ↓
✅ 4. COMPLETED (Done!)
```

**Timeline:** Usually 10-15 minutes from order to ready

---

## 💰 Payment Methods & Flow

### 1. 💵 **Cash Payment**

```
Order Placed → Status: PENDING
Payment Status: Pending

[Customer pays at pickup]
↓
[Owner/Staff: "Confirm Cash Received"]
↓
Payment Status: PAID
Order Status: COMPLETED (auto)
```

**Button:** "Confirm Cash Received & Complete Order" (Orange)

---

### 2. 📱 **QR Payment**

```
Order Placed → Status: PENDING
Payment Status: Pending

[Customer upload payment proof]
↓
[Owner/Staff review proof]
↓
[Click "Verify Payment"]
↓
Payment Status: PAID
Can update order status now
```

**Steps:**
1. Check payment proof image
2. Verify amount matches
3. Click "Verify Payment & Continue"
4. Update order status to Preparing → Ready → Completed

---

### 3. 💳 **Online Payment**

```
Order Placed
↓
[Customer pays via Billplz]
↓
Payment Status: PAID (automatic)
↓
Can update order status immediately
```

**Note:** Payment auto-verified by system

---

## 🎮 How to Update Status (Owner/Staff)

### Access Orders Page:
```
Owner: /owner/orders
Staff: /staff/orders
```

### For Each Order:

#### **Step 1: Check Payment Status**
```
🟢 PAID → Can update order status
🟡 PENDING → Wait for payment first
```

#### **Step 2: Update Order Status**

**If Cash Payment Pending:**
```
1. Wait for customer to arrive
2. Collect cash
3. Click "Confirm Cash Received & Complete Order"
4. Done! Status → COMPLETED
```

**If Payment Already Confirmed:**
```
1. Find the dropdown menu below order
2. Select new status:
   • Preparing (start making)
   • Ready (done, wait pickup)
   • Completed (customer collected)
3. Click "Update Status" button
4. Page refresh, status updated!
```

---

## 📱 Owner vs Staff Access

### 👨‍🍳 **Staff Can:**
- ✅ View all orders
- ✅ Update order status
- ✅ Confirm cash payments
- ✅ Verify QR payments
- ❌ Cannot see revenue details
- ❌ Cannot access settings

### 🧇 **Owner Can:**
- ✅ Everything staff can do
- ✅ View full revenue
- ✅ Access business settings
- ✅ Manage products/menu
- ✅ View all statistics

---

## 🎯 Best Practices

### 1. **Quick Response**
```
New Order → Update to "Preparing" dalam 2-3 minit
Shows customer: "We're working on it!"
```

### 2. **Accurate Status**
```
Actually preparing → Update to "Preparing"
Actually ready → Update to "Ready"
Customer collected → Update to "Completed"
```

### 3. **Payment Verification**
```
Cash: Verify amount before marking paid
QR: Check payment proof properly
Online: Auto-verified, proceed directly
```

### 4. **Communication**
```
If delay: Consider adding notes (future)
If out of stock: Cancel immediately
If ready: Update status so customer knows
```

---

## ⚠️ Important Rules

### 1. **Cannot Update Status Without Payment**
```
If payment_status = "pending"
→ Dropdown menu tidak muncul
→ Must wait for payment first
```

**Exception:** Cash orders can skip this (pay at pickup)

### 2. **Status Order Matters**
```
✅ GOOD: Pending → Preparing → Ready → Completed
❌ BAD: Pending → Completed (skip steps)
```

**Why:** Customer tracking updates

### 3. **Cash Orders Special**
```
Payment Status: Pending
↓
[Confirm Cash Received Button]
↓
• Payment Status → PAID
• Order Status → COMPLETED
Both updated at once!
```

---

## 🖥️ UI Elements

### Order Card Layout:
```
┌─────────────────────────────────────────┐
│ 🧇 ORD-XXXXX          [PENDING Badge]  │
│ 👤 Customer Name      [CASH Badge]     │
│ 🕐 13 Dec 2025, 4:56 PM               │
│                                         │
│ 2x Original Belgian Waffle             │
│                                         │
│ 💰 RM 16.00    Total: RM 16.00        │
│                                         │
│ ┌───────────────────────────────────┐ │
│ │ ⚠️ Cash Payment Pending          │ │
│ │ Collect RM 16.00 at pickup       │ │
│ │                                  │ │
│ │ [Confirm Cash Received] Button   │ │
│ └───────────────────────────────────┘ │
│                                         │
│ OR (if paid):                           │
│                                         │
│ Status: [Dropdown ▼] [Update] Button   │
└─────────────────────────────────────────┘
```

### Status Dropdown Options:
```
┌────────────────────────┐
│ Pending                │
│ Preparing              │
│ Ready for Pickup       │
│ Completed              │
│ Cancelled              │
└────────────────────────┘
```

---

## 📊 Status Color Guide

| Status | Badge Color | Icon | Customer Message |
|--------|-------------|------|------------------|
| Pending | 🟡 Yellow | ⏳ | "Order received" |
| Preparing | 🔵 Blue | 👨‍🍳 | "Being prepared" |
| Ready | 🟠 Orange | 🎉 | "Ready for pickup!" |
| Completed | 🟢 Green | ✅ | "Completed" |
| Cancelled | 🔴 Red | ❌ | "Cancelled" |

---

## 🎓 Training Scenarios

### Scenario 1: Cash Order (Most Common)
```
1. Order masuk → PENDING, Cash Payment Pending
2. Update to "Preparing" (optional, for tracking)
3. Make waffles
4. Update to "Ready"
5. Customer arrives
6. Collect RM 16.00 cash
7. Click "Confirm Cash Received & Complete Order"
8. Done! ✅
```

### Scenario 2: QR Payment
```
1. Order masuk → PENDING, Payment Pending
2. Customer uploads QR proof
3. Click view proof, verify payment
4. Click "Verify Payment"
5. Now can update status
6. Set to "Preparing"
7. Make waffles
8. Set to "Ready"
9. Customer collects
10. Set to "Completed"
```

### Scenario 3: Online Payment (Easiest)
```
1. Order masuk → PENDING, PAID ✅
2. Start immediately
3. Set to "Preparing"
4. Make waffles
5. Set to "Ready"
6. Customer collects
7. Set to "Completed"
```

### Scenario 4: Cancellation
```
1. Order masuk
2. Check stock
3. Oh no, out of ingredients!
4. Update status dropdown → "Cancelled"
5. Customer notified
6. Refund handled (if paid)
```

---

## 💡 Pro Tips

### 1. **Batch Processing**
```
Multiple orders → Set all to "Preparing"
Make all together → Efficiency!
Set ready one by one as done
```

### 2. **Peak Hours**
```
Ramai orders → Prioritize by time
First come first serve
Update status actively so customers track
```

### 3. **Quality Check**
```
Before set to "Ready":
- Double check order items
- Ensure proper packaging
- Include serviettes/utensils
```

### 4. **Cash Handling**
```
- Have change ready
- Count cash properly
- Only mark paid after receive
```

---

## ❓ FAQ

### Q: Boleh skip dari Pending terus ke Completed?
**A:** Technically yes, tapi not recommended. Better follow flow untuk customer tracking yang proper.

### Q: Kalau customer cancel after prepare?
**A:** Set status to "Cancelled". Handle refund manually if already paid.

### Q: Staff boleh access revenue?
**A:** No. Staff only see order processing. Revenue details owner sahaja.

### Q: Customer tak datang pickup?
**A:** Wait reasonable time (30 min?), then contact. If no response, can set to "Cancelled".

### Q: Multiple staff update same order?
**A:** Last update wins. Coordinate with team to avoid conflicts.

### Q: Nak revert status?
**A:** Yes! Just select previous status from dropdown and update. Very flexible.

---

## 🔗 Quick Links

**Owner Access:**
- Orders: `/owner/orders`
- Dashboard: `/owner/dashboard`
- Products: `/owner/products`
- Settings: `/owner/settings`

**Staff Access:**
- Orders: `/staff/orders`
- Dashboard: `/staff/dashboard`

---

## ✅ Checklist

**Before Starting Shift:**
- [ ] Check pending orders
- [ ] Verify ingredient stock
- [ ] Ensure QR code working (if used)
- [ ] Have change ready (for cash)

**During Orders:**
- [ ] Update status promptly
- [ ] Verify payments properly
- [ ] Quality check before marking ready
- [ ] Keep workspace organized

**End of Shift:**
- [ ] Complete all pending orders
- [ ] Update all statuses
- [ ] Report any issues to owner
- [ ] Clean workspace

---

**Guide Version:** 1.0  
**Last Updated:** December 14, 2025  
**For:** D'house Waffle Operations

🧇 **Happy Waffle Making!** ✨

