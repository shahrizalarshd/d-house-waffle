# 🚀 Quick Start: Real-Time Notifications

## ✅ Setup Complete!

Laravel Reverb dah installed dan configured! Sekarang owner akan dapat notification **INSTANT** bila customer order! ⚡

---

## 🎯 Start Reverb Server

### **Method 1: Using Script (Easiest)**
```bash
cd /Users/shah/Laravel/dhouse-waffle
./start-reverb.sh
```

### **Method 2: Manual Command**
```bash
cd /Users/shah/Laravel/dhouse-waffle
./vendor/bin/sail artisan reverb:start
```

### **Expected Output:**
```
  INFO  Server running on 0.0.0.0:8080.

  Press Ctrl+C to stop the server
```

**✅ Bila nampak message ni, Reverb dah running!**

---

## 🧪 Test Real-Time (Step by Step)

### **Step 1: Start Reverb** ⚡
```bash
# Terminal 1
./start-reverb.sh
```

### **Step 2: Open Owner Dashboard** 👨‍💼
```
Browser 1: http://localhost/owner/dashboard
Login: owner@waffle.com / password
```

**Check browser console (F12):**
```
✅ Real-time notifications active for seller 2
```

### **Step 3: Open Customer Page** 👤
```
Browser 2: http://localhost/buyer/home
Login: buyer@test.com / password
```

### **Step 4: Place Order** 🛒
```
1. Add "Classic Waffle" to cart
2. Click cart icon
3. Click "Proceed to Checkout"
4. Select "Cash on Pickup"
5. Click "Place Order Now"
```

### **Step 5: Watch Owner Dashboard** 👀

**Instantly (< 1 second), owner akan dapat:**

1. 🔔 **DING!** - Sound notification
2. 🟢 **Toast** - "New Order: ORD-XXX - RM XX.XX"
3. 📢 **Browser Alert** - Desktop notification
4. 🔄 **Auto Refresh** - Badges update

---

## 🎉 What You'll See

### **Owner Dashboard:**
```
[Before Order]
Pending Orders: 0

[After Order - INSTANT!]
🔔 DING!
┌─────────────────────────────────────┐
│ 🔔 New Order: ORD-ABC123           │
│ RM 28.50                            │
└─────────────────────────────────────┘

Pending Orders: 1 (with red badge!)
```

### **Browser Notification:**
```
[Desktop Alert Pops Up]
┌─────────────────────────────────────┐
│ 🧇 D'house Waffle - New Order!     │
│ ORD-ABC123 - RM 28.50               │
│ From: Siti Abdullah                │
│ Just now                            │
└─────────────────────────────────────┘
```

---

## 🔧 Common Issues

### **Issue 1: Port 8080 already in use**
```bash
# Find process using port
lsof -i :8080

# Kill it
kill -9 <PID>

# Start Reverb again
./start-reverb.sh
```

### **Issue 2: No notification sound**
```
Solution: Click anywhere on page first
(Browser requires user interaction before playing sound)
```

### **Issue 3: No browser notification**
```javascript
// Check permission
console.log(Notification.permission);

// If "default", click "Allow" when prompted
// If "denied", go to browser settings and enable
```

### **Issue 4: Connection refused**
```bash
# Make sure Reverb is running
ps aux | grep reverb

# Clear config
./vendor/bin/sail artisan config:clear

# Restart Reverb
./start-reverb.sh
```

---

## 📊 Before vs After

### **Before (No Real-Time):**
```
Customer orders → Owner must refresh → 5 min delay ❌
```

### **After (With Reverb):**
```
Customer orders → Owner notified instantly → < 1 sec ✅
```

---

## 🎯 Features

### **1. Sound Notification** 🔔
- Plays "ding" when order arrives
- Works even if tab not focused
- Can hear from another tab

### **2. Toast Notification** 🟢
- Shows order number and amount
- Green success toast
- Auto-dismisses after 5 seconds

### **3. Browser Notification** 📢
- Desktop alert pops up
- Shows order details
- Stays until clicked
- Works even if browser minimized

### **4. Auto Badge Update** 🔴
- Pending count increases
- Red badge appears
- All 3 badges update
- Page auto-refreshes

---

## 💡 Tips

### **Keep Reverb Running:**
```bash
# Run in background
./vendor/bin/sail artisan reverb:start &

# Or use screen/tmux
screen -S reverb
./vendor/bin/sail artisan reverb:start
# Press Ctrl+A then D to detach
```

### **Check if Running:**
```bash
ps aux | grep reverb
# Should show: php artisan reverb:start
```

### **Stop Reverb:**
```bash
# If running in foreground
Press Ctrl+C

# If running in background
pkill -f "reverb:start"
```

---

## 📝 Technical Details

### **How It Works:**
```
1. Customer places order
   ↓
2. OrderController broadcasts OrderPlaced event
   ↓
3. Reverb WebSocket server receives event
   ↓
4. Reverb pushes to channel: seller.{seller_id}
   ↓
5. Owner's browser (Laravel Echo) listening
   ↓
6. JavaScript receives event
   ↓
7. Play sound + Show notifications + Update badges
```

### **Channel:**
```
seller.2  (for owner with ID 2)
```

### **Event:**
```
order.placed
```

### **Data:**
```json
{
  "order_id": 123,
  "order_no": "ORD-ABC123",
  "total_amount": 28.50,
  "buyer_name": "Siti Abdullah",
  "status": "pending",
  "payment_method": "cash"
}
```

---

## ✅ Checklist

- [x] ✅ Reverb installed
- [x] ✅ .env configured
- [x] ✅ Event created
- [x] ✅ Broadcasting setup
- [x] ✅ Frontend listener
- [x] ✅ Notifications ready
- [ ] ⏳ Start Reverb server
- [ ] ⏳ Test with real order

---

## 🚀 Ready to Go!

**Just run:**
```bash
./start-reverb.sh
```

**Then test with real order!**

---

## 📞 Support

### **Check Logs:**
```bash
# Reverb logs
./vendor/bin/sail artisan pail

# Laravel logs
tail -f storage/logs/laravel.log
```

### **Debug Mode:**
```bash
# Check browser console (F12)
# Should see: "✅ Real-time notifications active"

# Check Reverb output
# Should see connections when page loads
```

---

**Status:** ✅ **Ready to Test!**  
**Command:** `./start-reverb.sh`  
**Test:** Place order and watch magic! ✨

---

**Sekarang system ada real-time! Owner tak perlu refresh lagi!** 🔔⚡🎉

