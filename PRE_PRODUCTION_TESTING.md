# 🧪 Pre-Production Testing - D'house Waffle

## 📋 Complete System Testing Checklist

**Date:** December 14, 2025  
**System:** D'house Waffle POS  
**Version:** 1.0 (Pre-Production)

---

## 🎯 Testing Scope

### **1. User Roles** (4 roles)
- ✅ Super Admin
- ✅ Owner
- ✅ Staff
- ✅ Customer

### **2. Core Features**
- ✅ Authentication
- ✅ Product Management
- ✅ Order Management
- ✅ Payment Methods (Cash, QR, Online)
- ✅ Real-time Notifications (Reverb)
- ✅ Sales Reports
- ✅ Settings Management

---

## 🧪 TEST PLAN

### **PHASE 1: Authentication & User Management**

#### **Test 1.1: User Login**
**Test Accounts:**
```
Super Admin: super@admin.com / password
Owner: owner@waffle.com / password
Staff: staff@waffle.com / password
Customer: buyer@test.com / password
```

**Steps:**
1. [ ] Login with each account
2. [ ] Verify correct dashboard redirect
3. [ ] Check navigation menu based on role
4. [ ] Test logout functionality
5. [ ] Test invalid credentials
6. [ ] Test "Remember Me" checkbox

**Expected:**
- ✅ Each role sees correct dashboard
- ✅ Role-specific navigation
- ✅ Logout works
- ✅ Error for wrong credentials

---

#### **Test 1.2: Customer Registration**
**Steps:**
1. [ ] Go to /register
2. [ ] Fill all fields
3. [ ] Submit
4. [ ] Verify auto-login
5. [ ] Check profile created

**Test Data:**
```
Name: Test Customer
Email: testcustomer@test.com
Password: password123
Phone: 012-3456789
Apartment: D'house Waffle - Sri Harmonis
Unit No: B-10-5
Block: B
```

**Expected:**
- ✅ Registration success
- ✅ Auto-login after register
- ✅ Profile data saved correctly

---

### **PHASE 2: Customer Journey**

#### **Test 2.1: Browse Products**
**Steps:**
1. [ ] Login as customer
2. [ ] View home page (product list)
3. [ ] Check all products visible
4. [ ] Test category filter
5. [ ] Check product details display
6. [ ] Verify prices shown correctly

**Expected:**
- ✅ All 11 products visible
- ✅ Categories: Classic, Premium, Toppings, Beverages, Combos
- ✅ Images load
- ✅ Prices correct
- ✅ Filter works

---

#### **Test 2.2: Add to Cart**
**Steps:**
1. [ ] Click "Add to Cart" on 3 different products
2. [ ] Check toast notification appears
3. [ ] Verify cart badge updates (shows count)
4. [ ] Check cart badge pulses
5. [ ] Go to cart page
6. [ ] Verify all 3 items in cart

**Products to Test:**
- Classic Belgian Waffle (RM 8.00)
- Nutella Overload (RM 12.00)
- Fresh Strawberry (RM 3.00)

**Expected:**
- ✅ Toast: "Added to cart!"
- ✅ Badge shows: 3
- ✅ Cart page shows all items
- ✅ Quantities correct
- ✅ Total calculated correctly

---

#### **Test 2.3: Cart Management**
**Steps:**
1. [ ] Increase quantity (+)
2. [ ] Decrease quantity (-)
3. [ ] Remove item
4. [ ] Verify total updates
5. [ ] Test empty cart
6. [ ] Add item again

**Expected:**
- ✅ Quantity updates
- ✅ Total recalculates
- ✅ Remove works (with confirmation)
- ✅ Empty cart shows message
- ✅ Badge updates

---

#### **Test 2.4: Checkout - Cash Payment**
**Steps:**
1. [ ] Cart with 2 items
2. [ ] Click "Proceed to Checkout"
3. [ ] Verify payment options visible
4. [ ] Select "Cash on Pickup"
5. [ ] Check order summary correct
6. [ ] Click "Place Order Now"
7. [ ] Verify success message
8. [ ] Check redirect to orders page

**Expected Total:** RM 20.00 (Classic RM 8 + Nutella RM 12)

**Expected:**
- ✅ Checkout page loads
- ✅ All payment methods shown (if enabled)
- ✅ Order summary correct
- ✅ Success toast appears
- ✅ Redirect to /buyer/orders
- ✅ Order appears in list

---

#### **Test 2.5: Checkout - QR Payment**
**Steps:**
1. [ ] Add items to cart
2. [ ] Select "QR Payment"
3. [ ] Place order
4. [ ] Verify redirect to QR page
5. [ ] Check QR code displays
6. [ ] Upload payment proof (screenshot)
7. [ ] Add payment notes
8. [ ] Submit
9. [ ] Verify success message

**Expected:**
- ✅ QR code visible
- ✅ Owner's payment details shown
- ✅ Upload works
- ✅ Notes saved
- ✅ Status: Payment pending verification

---

#### **Test 2.6: Checkout - Online Payment (Demo)**
**Steps:**
1. [ ] Add items
2. [ ] Select "Online Payment"
3. [ ] Place order
4. [ ] Redirect to payment page
5. [ ] Click demo payment button
6. [ ] Verify payment completed

**Expected:**
- ✅ Payment page loads
- ✅ Demo payment works
- ✅ Status updates to paid
- ✅ Order status updates

---

#### **Test 2.7: View Orders**
**Steps:**
1. [ ] Go to /buyer/orders
2. [ ] Check all orders listed
3. [ ] Click on an order
4. [ ] View order details
5. [ ] Check status badges
6. [ ] Verify payment info

**Expected:**
- ✅ All orders visible
- ✅ Status badges colored correctly
- ✅ Order details complete
- ✅ Payment method shown
- ✅ Items listed

---

#### **Test 2.8: Customer Profile**
**Steps:**
1. [ ] Go to /profile
2. [ ] View current info
3. [ ] Edit name
4. [ ] Edit phone
5. [ ] Change password
6. [ ] Save
7. [ ] Verify success

**Expected:**
- ✅ Profile loads
- ✅ Current data shown
- ✅ Edits save
- ✅ Password changes
- ✅ Toast notification

---

### **PHASE 3: Owner Journey**

#### **Test 3.1: Owner Dashboard**
**Steps:**
1. [ ] Login as owner
2. [ ] Check stats cards
3. [ ] Verify pending orders badge
4. [ ] Check recent orders list
5. [ ] Test quick action buttons

**Expected Stats:**
- Total Orders: 2
- Pending: 1
- Total Earnings: RM XX.XX
- Active Products: 11

**Expected:**
- ✅ Stats accurate
- ✅ Badge shows pending count
- ✅ Recent orders displayed
- ✅ Quick actions work

---

#### **Test 3.2: Incoming Orders**
**Steps:**
1. [ ] Go to /owner/orders
2. [ ] View all orders
3. [ ] Check customer info visible:
   - Name
   - Unit & Block
   - Phone number (clickable)
4. [ ] Verify order details
5. [ ] Check payment badges

**Expected:**
- ✅ All orders listed
- ✅ Customer address visible
- ✅ Phone clickable (tel: link)
- ✅ Order items shown
- ✅ Total amounts correct

---

#### **Test 3.3: Update Order Status (Cash)**
**Steps:**
1. [ ] Find cash order (pending)
2. [ ] Update status to "Preparing"
3. [ ] Click Update
4. [ ] Change to "Ready"
5. [ ] Click "Confirm Cash Received"
6. [ ] Verify order completed

**Expected:**
- ✅ Status updates
- ✅ Can progress: Pending → Preparing → Ready
- ✅ Cash button appears when ready
- ✅ Order marked complete
- ✅ Payment status = paid
- ✅ paid_at timestamp set

---

#### **Test 3.4: Update Order Status (QR)**
**Steps:**
1. [ ] Find QR order with payment proof
2. [ ] View payment proof image
3. [ ] Read payment notes
4. [ ] Click "Verify Payment"
5. [ ] Confirm
6. [ ] Update status to Preparing
7. [ ] Then Ready
8. [ ] Then Completed

**Expected:**
- ✅ Payment proof displays
- ✅ Notes readable
- ✅ Verify button works
- ✅ Payment status = paid
- ✅ Status progression works
- ✅ Order completes

---

#### **Test 3.5: QR Payment Rejection**
**Steps:**
1. [ ] Find QR order
2. [ ] Click "Reject Payment"
3. [ ] Confirm
4. [ ] Verify rejection

**Expected:**
- ✅ Confirmation dialog
- ✅ Payment rejected
- ✅ Order status appropriate
- ✅ Customer notified (via order page)

---

#### **Test 3.6: Product Management**
**Steps:**
1. [ ] Go to /owner/products
2. [ ] View all products
3. [ ] Click "Add New Waffle"
4. [ ] Fill form:
   - Name: Test Waffle
   - Category: Classic
   - Price: RM 10.00
   - Description: Test description
   - Image: Upload test.jpg
5. [ ] Save
6. [ ] Verify product added

**Expected:**
- ✅ Product list loads
- ✅ Add form works
- ✅ Image uploads
- ✅ Product created
- ✅ Appears in list

---

#### **Test 3.7: Edit Product**
**Steps:**
1. [ ] Click Edit on "Test Waffle"
2. [ ] Change price to RM 11.00
3. [ ] Change description
4. [ ] Upload new image
5. [ ] Save
6. [ ] Verify changes

**Expected:**
- ✅ Edit form loads with data
- ✅ Changes save
- ✅ New image replaces old
- ✅ Product updated

---

#### **Test 3.8: Hide/Show Product**
**Steps:**
1. [ ] Toggle "Test Waffle" to hidden
2. [ ] Check not visible to customers
3. [ ] Toggle back to visible
4. [ ] Verify visible again

**Expected:**
- ✅ Toggle works
- ✅ Hidden products not in customer view
- ✅ Show works

---

#### **Test 3.9: Delete Product**
**Steps:**
1. [ ] Click Delete on "Test Waffle"
2. [ ] Confirm deletion
3. [ ] Verify removed from list

**Expected:**
- ✅ Confirmation dialog
- ✅ Product deleted
- ✅ Not in list anymore

---

#### **Test 3.10: Sales Report**
**Steps:**
1. [ ] Go to /owner/sales-report
2. [ ] Check default filters (current month)
3. [ ] View statistics cards
4. [ ] Check orders table
5. [ ] Apply date filter
6. [ ] Apply status filter
7. [ ] Apply payment method filter
8. [ ] Click "Download Excel"
9. [ ] Open downloaded file

**Expected:**
- ✅ Report loads
- ✅ Stats accurate
- ✅ Filters work
- ✅ Excel downloads
- ✅ Excel contains filtered data
- ✅ Formatting correct

---

#### **Test 3.11: Business Settings**
**Steps:**
1. [ ] Go to /owner/settings
2. [ ] Check current settings loaded
3. [ ] Change service fee to 5%
4. [ ] Update pickup location
5. [ ] Change pickup times
6. [ ] Toggle payment methods
7. [ ] Save
8. [ ] Verify success

**Test Settings:**
```
Service Fee: 5%
Pickup Location: Lobby Baru
Start Time: 09:00
End Time: 23:00
Payment Methods:
- Online: OFF
- QR: ON
- Cash: ON
```

**Expected:**
- ✅ Settings load
- ✅ All fields editable
- ✅ Saves successfully
- ✅ Toast notification
- ✅ Changes reflected in checkout

---

#### **Test 3.12: Owner Profile (QR Setup)**
**Steps:**
1. [ ] Go to /owner/profile
2. [ ] View current info
3. [ ] Edit name
4. [ ] Edit phone
5. [ ] Edit unit/block
6. [ ] Change apartment
7. [ ] Upload QR code image
8. [ ] Add QR instructions
9. [ ] Save
10. [ ] Verify all saved

**Expected:**
- ✅ Profile loads
- ✅ All fields editable
- ✅ QR image uploads
- ✅ Instructions save
- ✅ Changes persist

---

### **PHASE 4: Staff Journey**

#### **Test 4.1: Staff Dashboard**
**Steps:**
1. [ ] Login as staff
2. [ ] Check dashboard
3. [ ] Verify limited access (no settings)
4. [ ] Check can see orders
5. [ ] Check pending badge

**Expected:**
- ✅ Dashboard loads
- ✅ Stats visible
- ✅ No Settings access
- ✅ Can view/manage orders

---

#### **Test 4.2: Staff Order Management**
**Steps:**
1. [ ] Go to /staff/orders
2. [ ] View orders
3. [ ] Update order status
4. [ ] Mark cash payment
5. [ ] Verify QR payment

**Expected:**
- ✅ Can view all orders
- ✅ Can update statuses
- ✅ Can mark payments
- ✅ Same as owner (minus settings)

---

### **PHASE 5: Super Admin Journey**

#### **Test 5.1: User Management**
**Steps:**
1. [ ] Login as super admin
2. [ ] Go to /super/users
3. [ ] View all users
4. [ ] Create new user
5. [ ] Edit user role
6. [ ] Verify changes

**Expected:**
- ✅ All users listed
- ✅ Can create users
- ✅ Can edit roles
- ✅ Changes save

---

#### **Test 5.2: System Settings**
**Steps:**
1. [ ] Go to /super/settings
2. [ ] View global settings
3. [ ] Modify system configs
4. [ ] Save changes

**Expected:**
- ✅ Settings load
- ✅ Can modify
- ✅ Saves successfully

---

### **PHASE 6: Real-Time Notifications**

#### **Test 6.1: Start Reverb Server**
**Steps:**
1. [ ] Run: `./start-reverb.sh`
2. [ ] Check server starts
3. [ ] Verify port 8080 open
4. [ ] Check for errors

**Expected Output:**
```
INFO  Server running on 0.0.0.0:8080
Press Ctrl+C to stop the server
```

**Expected:**
- ✅ Server starts without errors
- ✅ Port 8080 listening
- ✅ No connection issues

---

#### **Test 6.2: Real-Time Order Notification**
**Steps:**
1. [ ] Reverb running
2. [ ] Browser 1: Owner dashboard
3. [ ] Check console: "Real-time notifications active"
4. [ ] Browser 2: Customer checkout
5. [ ] Place order
6. [ ] Watch owner dashboard

**Expected (< 1 second):**
1. ✅ Sound plays (DING!)
2. ✅ Toast notification appears
3. ✅ Browser notification pops up
4. ✅ Pending badge updates
5. ✅ Page refreshes
6. ✅ New order visible

---

#### **Test 6.3: Browser Notifications**
**Steps:**
1. [ ] Click "Allow" for notifications
2. [ ] Minimize browser
3. [ ] Place order from another device
4. [ ] Check desktop notification

**Expected:**
- ✅ Permission requested
- ✅ Desktop alert appears
- ✅ Shows order details
- ✅ Clickable notification

---

#### **Test 6.4: Sound Notification**
**Steps:**
1. [ ] Ensure speakers on
2. [ ] Owner on different tab
3. [ ] Place order
4. [ ] Listen for sound

**Expected:**
- ✅ "Ding" sound plays
- ✅ Works from different tab
- ✅ Audible alert

---

### **PHASE 7: UI/UX Testing**

#### **Test 7.1: Mobile Responsiveness**
**Steps:**
1. [ ] Open on mobile (or resize browser)
2. [ ] Test all pages
3. [ ] Check navigation works
4. [ ] Verify buttons accessible
5. [ ] Test forms on mobile

**Pages to Test:**
- Home
- Products
- Cart
- Checkout
- Orders
- Profile
- Owner Dashboard
- Orders Management

**Expected:**
- ✅ All pages responsive
- ✅ Bottom nav works
- ✅ Buttons tap-friendly
- ✅ Forms usable
- ✅ No horizontal scroll

---

#### **Test 7.2: Toast Notifications**
**Steps:**
1. [ ] Trigger success (place order)
2. [ ] Trigger error (empty cart checkout)
3. [ ] Check toast styling
4. [ ] Verify auto-dismiss

**Expected:**
- ✅ Success: Green toast
- ✅ Error: Red toast
- ✅ Auto-dismisses after 5s
- ✅ Animated slide-in

---

#### **Test 7.3: Custom Confirmations**
**Steps:**
1. [ ] Delete product
2. [ ] Remove cart item
3. [ ] Reject payment
4. [ ] Mark cash received

**Expected:**
- ✅ Custom modal (not browser confirm)
- ✅ Styled confirmation
- ✅ Cancel/Confirm buttons
- ✅ Works correctly

---

#### **Test 7.4: Cart Badge**
**Steps:**
1. [ ] Empty cart
2. [ ] Badge hidden
3. [ ] Add 1 item
4. [ ] Badge shows "1"
5. [ ] Add 9 more
6. [ ] Badge shows "9+"
7. [ ] Remove items
8. [ ] Badge updates

**Expected:**
- ✅ Badge hides when empty
- ✅ Shows correct count
- ✅ 9+ for 10+ items
- ✅ Pulses when updated
- ✅ Red background

---

#### **Test 7.5: Order Badges (Owner)**
**Steps:**
1. [ ] Check top header bell
2. [ ] Check bottom nav dashboard
3. [ ] Check dashboard card
4. [ ] All show same count
5. [ ] Complete an order
6. [ ] Badges update

**Expected:**
- ✅ All 3 badges visible
- ✅ Same count everywhere
- ✅ Red background
- ✅ Pulse animation
- ✅ Update when status changes

---

### **PHASE 8: Payment Methods**

#### **Test 8.1: Payment Toggle**
**Steps:**
1. [ ] Owner settings
2. [ ] Disable Online payment
3. [ ] Save
4. [ ] Customer checkout
5. [ ] Verify Online hidden
6. [ ] Enable again
7. [ ] Verify visible

**Expected:**
- ✅ Toggle saves
- ✅ Disabled methods hidden
- ✅ Only enabled shown
- ✅ At least 1 required

---

#### **Test 8.2: Cash Payment Full Flow**
**Steps:**
1. [ ] Place cash order
2. [ ] Owner sees pending
3. [ ] Update to preparing
4. [ ] Update to ready
5. [ ] Confirm cash received
6. [ ] Order completed

**Expected:**
- ✅ Order created
- ✅ Status: pending
- ✅ Payment: pending
- ✅ Can update status anytime
- ✅ Cash confirm button appears
- ✅ Marks as completed + paid

---

#### **Test 8.3: QR Payment Full Flow**
**Steps:**
1. [ ] Place QR order
2. [ ] Upload payment proof
3. [ ] Owner verifies
4. [ ] Payment approved
5. [ ] Update to preparing
6. [ ] Then ready
7. [ ] Then completed

**Expected:**
- ✅ QR code displays
- ✅ Upload works
- ✅ Owner sees proof
- ✅ Verify button works
- ✅ Payment marked paid
- ✅ Order progresses

---

#### **Test 8.4: Online Payment (Demo)**
**Steps:**
1. [ ] Place online order
2. [ ] Redirect to payment
3. [ ] Click demo pay
4. [ ] Verify payment success
5. [ ] Check order paid

**Expected:**
- ✅ Payment page loads
- ✅ Demo works
- ✅ Payment recorded
- ✅ Order status updates

---

### **PHASE 9: Data Integrity**

#### **Test 9.1: Order Calculations**
**Test Orders:**
```
Order 1:
- 2x Classic (RM 8) = RM 16
- 1x Topping (RM 3) = RM 3
- Subtotal: RM 19
- Service Fee (0%): RM 0
- Total: RM 19

Order 2:
- 1x Premium (RM 12) = RM 12
- 2x Beverage (RM 5) = RM 10
- Subtotal: RM 22
- Service Fee (5%): RM 1.10
- Total: RM 23.10
```

**Steps:**
1. [ ] Place orders
2. [ ] Check subtotals
3. [ ] Verify service fees
4. [ ] Check totals
5. [ ] Owner sees correct amounts

**Expected:**
- ✅ All calculations correct
- ✅ Service fee applies correctly
- ✅ No rounding errors
- ✅ Owner amount = Subtotal - Fee

---

#### **Test 9.2: Database Relationships**
**Steps:**
1. [ ] Check orders have buyer
2. [ ] Check orders have seller
3. [ ] Check orders have items
4. [ ] Check items have products
5. [ ] Verify all relationships load

**Expected:**
- ✅ No N+1 queries
- ✅ Eager loading works
- ✅ All data accessible
- ✅ No null references

---

### **PHASE 10: Error Handling**

#### **Test 10.1: Empty Cart Checkout**
**Steps:**
1. [ ] Empty cart
2. [ ] Go to checkout
3. [ ] Check error message

**Expected:**
- ✅ Toast error
- ✅ "Cart is empty"
- ✅ No checkout allowed

---

#### **Test 10.2: Invalid Product**
**Steps:**
1. [ ] Add product to cart
2. [ ] Delete product as owner
3. [ ] Try to checkout
4. [ ] Check error handling

**Expected:**
- ✅ Error message
- ✅ Cart validation
- ✅ Graceful failure

---

#### **Test 10.3: Unauthorized Access**
**Steps:**
1. [ ] Login as customer
2. [ ] Try: /owner/dashboard
3. [ ] Try: /staff/orders
4. [ ] Try: /super/users

**Expected:**
- ✅ 403 Forbidden or redirect
- ✅ Cannot access
- ✅ Proper error message

---

#### **Test 10.4: Session Timeout**
**Steps:**
1. [ ] Login
2. [ ] Wait (or clear session)
3. [ ] Try to place order
4. [ ] Check redirect to login

**Expected:**
- ✅ Redirects to login
- ✅ Flash message
- ✅ Can login again

---

### **PHASE 11: Performance Testing**

#### **Test 11.1: Page Load Times**
**Target: < 2 seconds**

**Pages to Test:**
- [ ] Home page
- [ ] Products list
- [ ] Cart page
- [ ] Checkout
- [ ] Owner dashboard
- [ ] Orders list
- [ ] Sales report

**Expected:**
- ✅ All pages < 2s
- ✅ Images optimized
- ✅ No slow queries

---

#### **Test 11.2: Concurrent Users**
**Steps:**
1. [ ] Open 5 browser windows
2. [ ] Login different users
3. [ ] Place orders simultaneously
4. [ ] Check all process correctly

**Expected:**
- ✅ No conflicts
- ✅ All orders save
- ✅ Notifications work
- ✅ No database locks

---

### **PHASE 12: Security Testing**

#### **Test 12.1: SQL Injection**
**Steps:**
1. [ ] Try SQL in login: `' OR '1'='1`
2. [ ] Try in search/filters
3. [ ] Verify no injection

**Expected:**
- ✅ Inputs sanitized
- ✅ No SQL injection
- ✅ Eloquent protects

---

#### **Test 12.2: XSS Protection**
**Steps:**
1. [ ] Try `<script>alert('xss')</script>` in:
   - Product name
   - Order notes
   - User name
2. [ ] Verify escaped

**Expected:**
- ✅ Scripts not executed
- ✅ Output escaped
- ✅ No XSS vulnerability

---

#### **Test 12.3: CSRF Protection**
**Steps:**
1. [ ] Check all forms have @csrf
2. [ ] Try submit without token
3. [ ] Verify rejection

**Expected:**
- ✅ All forms protected
- ✅ 419 error without token
- ✅ CSRF validation works

---

### **PHASE 13: Browser Compatibility**

**Browsers to Test:**
- [ ] Chrome (Desktop)
- [ ] Firefox (Desktop)
- [ ] Safari (Desktop)
- [ ] Edge (Desktop)
- [ ] Chrome (Mobile)
- [ ] Safari (iOS)

**Features to Check Each:**
- [ ] Login/logout
- [ ] Place order
- [ ] View pages
- [ ] Notifications
- [ ] Responsive design

**Expected:**
- ✅ Works on all browsers
- ✅ No console errors
- ✅ UI consistent

---

## 📊 TEST SUMMARY

### **Total Test Cases:** 100+

### **Critical Features:**
- [ ] Authentication ✅
- [ ] Order placement ✅
- [ ] Payment processing ✅
- [ ] Real-time notifications ✅
- [ ] Order management ✅
- [ ] Reports ✅

### **Sign-Off Checklist:**
- [ ] All tests passed
- [ ] No critical bugs
- [ ] Performance acceptable
- [ ] Security verified
- [ ] Mobile responsive
- [ ] Real-time working
- [ ] Documentation complete

---

## 🚀 PRODUCTION READINESS

### **Pre-Deployment:**
1. [ ] All tests passed
2. [ ] Database backed up
3. [ ] .env configured
4. [ ] SSL certificate ready
5. [ ] Domain configured
6. [ ] Reverb supervisor setup
7. [ ] Logs monitoring setup
8. [ ] Error tracking (optional)

### **Go-Live Checklist:**
1. [ ] Deploy code
2. [ ] Run migrations
3. [ ] Seed database
4. [ ] Start Reverb
5. [ ] Test critical paths
6. [ ] Monitor logs
7. [ ] Ready for users

---

**Testing Status:** 🟡 **In Progress**  
**Production Ready:** ⏳ **After Testing Complete**  
**Estimated Testing Time:** 2-3 hours

---

**Mari kita test semua satu persatu!** 🧪✨

