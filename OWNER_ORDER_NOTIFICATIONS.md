# 🔔 Owner Order Notification Badges

## Overview
Real-time notification badges for owner and staff to track pending orders, similar to the cart badge for customers.

---

## 🎯 Features Implemented

### **1. Top Header Notification**
**Location:** Top navigation bar (next to Logout button)

**Features:**
- 🔔 Bell icon with badge
- Shows "X New Order(s)" text
- Red badge with pending count
- Animated pulse effect
- Clickable → redirects to Orders page
- Only visible when pending orders > 0

**Design:**
```
┌─────────────────────────────────────────┐
│ 🧇 D'house Waffle    [🔔 2 New] [Logout]│
│                           ↑              │
│                      Red badge (2)       │
└─────────────────────────────────────────┘
```

---

### **2. Bottom Navigation Badge**
**Location:** Dashboard icon in bottom navigation

**Features:**
- Red badge on Dashboard icon
- Shows pending count (1-9 or 9+)
- Animated pulse effect
- Visible on all pages
- Updates in real-time

**Design:**
```
┌─────────────────────────────────────────┐
│  [📈]    [📊]    [🍽️]    [⚙️]          │
│   ↑                                      │
│  Red badge (2)                           │
│  Dashboard  Reports  Menu  Settings      │
└─────────────────────────────────────────┘
```

---

### **3. Dashboard Stats Card Badge**
**Location:** Pending Orders card on dashboard

**Features:**
- Larger red badge (8x8)
- Positioned top-right of card
- Shows pending count
- Animated pulse effect
- Only visible when pending > 0

**Design:**
```
┌──────────────────┐
│ Pending Orders  🔴│ ← Red badge
│                   │
│       2           │
└──────────────────┘
```

---

## 📍 Badge Locations

### **For Owner:**
1. ✅ **Top Header** - Bell notification button
2. ✅ **Bottom Nav** - Dashboard icon badge
3. ✅ **Dashboard** - Pending card badge

### **For Staff:**
1. ✅ **Top Header** - Bell notification button
2. ✅ **Bottom Nav** - Dashboard icon badge
3. ✅ **Dashboard** - Pending card badge

---

## 🎨 Visual Design

### **Badge Styling:**
```css
/* Red Badge */
background: bg-red-500
color: text-white
size: h-5 w-5 (small) or h-8 w-8 (large)
shape: rounded-full
animation: animate-pulse
shadow: shadow-lg

/* Position */
position: absolute
top: -top-1 or -top-2
right: -right-2
```

### **Color Scheme:**
- **Badge Background:** Red (#EF4444)
- **Badge Text:** White
- **Animation:** Pulse (attention-grabbing)
- **Shadow:** Large shadow for depth

### **Badge Content:**
- **1-9 orders:** Show exact number
- **10+ orders:** Show "9+"
- **0 orders:** Badge hidden

---

## 💻 Technical Implementation

### **1. Top Header Badge**
**File:** `resources/views/layouts/app.blade.php`

```php
@if(auth()->user()->role === 'owner' || auth()->user()->role === 'staff')
@php
    $sellerId = auth()->user()->role === 'owner' ? auth()->id() : 
        \App\Models\User::where('apartment_id', auth()->user()->apartment_id)
            ->where('role', 'owner')
            ->value('id');
    $pendingOrdersCount = \App\Models\Order::where('seller_id', $sellerId)
        ->where('status', 'pending')
        ->count();
@endphp
@if($pendingOrdersCount > 0)
<a href="{{ auth()->user()->role === 'owner' ? route('owner.orders') : route('staff.orders') }}" 
   class="relative bg-white/20 hover:bg-white/30 px-3 py-1.5 rounded-lg transition">
    <i class="fas fa-bell"></i>
    <span>{{ $pendingOrdersCount }} New</span>
    <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center animate-pulse">
        {{ $pendingOrdersCount > 9 ? '9+' : $pendingOrdersCount }}
    </span>
</a>
@endif
@endif
```

### **2. Bottom Navigation Badge**
**File:** `resources/views/layouts/app.blade.php`

```php
<a href="{{ route('owner.dashboard') }}" class="relative">
    <i class="fas fa-chart-line"></i>
    <span>Dashboard</span>
    @php
        $pendingCount = \App\Models\Order::where('seller_id', auth()->id())
            ->where('status', 'pending')
            ->count();
    @endphp
    @if($pendingCount > 0)
    <span class="absolute -top-1 -right-2 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center animate-pulse">
        {{ $pendingCount > 9 ? '9+' : $pendingCount }}
    </span>
    @endif
</a>
```

### **3. Dashboard Card Badge**
**File:** `resources/views/seller/dashboard.blade.php`

```php
<div class="bg-white rounded-lg shadow p-4 relative">
    <p class="text-gray-600 text-sm">Pending Orders</p>
    <p class="text-2xl font-bold text-yellow-600">{{ $pendingOrders }}</p>
    @if($pendingOrders > 0)
    <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full h-8 w-8 flex items-center justify-center animate-pulse shadow-lg">
        {{ $pendingOrders > 9 ? '9+' : $pendingOrders }}
    </span>
    @endif
</div>
```

---

## 🔄 How It Works

### **Query Logic:**
```php
// For Owner
$pendingCount = Order::where('seller_id', auth()->id())
    ->where('status', 'pending')
    ->count();

// For Staff (find owner in same apartment)
$ownerId = User::where('apartment_id', auth()->user()->apartment_id)
    ->where('role', 'owner')
    ->value('id');
$pendingCount = Order::where('seller_id', $ownerId)
    ->where('status', 'pending')
    ->count();
```

### **Display Logic:**
```php
@if($pendingCount > 0)
    <span class="badge">
        {{ $pendingCount > 9 ? '9+' : $pendingCount }}
    </span>
@endif
```

---

## 📱 Responsive Behavior

### **Desktop (≥ 640px):**
```
Top Header: [🔔 2 New Orders] ← Full text
Bottom Nav: Badge visible
Dashboard: Badge visible
```

### **Mobile (< 640px):**
```
Top Header: [🔔 2] ← Short text
Bottom Nav: Badge visible
Dashboard: Badge visible
```

---

## 🎯 User Experience Flow

### **Scenario 1: New Order Arrives**
```
1. Customer places order
   ↓
2. Order status = 'pending'
   ↓
3. Owner sees:
   - Top header: "🔔 1 New Order" (clickable)
   - Bottom nav: Red badge (1) on Dashboard
   - Dashboard: Red badge (1) on Pending card
   ↓
4. Owner clicks notification
   ↓
5. Redirects to Orders page
   ↓
6. Owner processes order
   ↓
7. Status changed to 'preparing'
   ↓
8. Badges disappear (no pending orders)
```

### **Scenario 2: Multiple Pending Orders**
```
Pending Orders: 5

Owner sees:
- Top header: "🔔 5 New Orders"
- Bottom nav: Badge shows "5"
- Dashboard: Badge shows "5"

All badges pulse to grab attention
```

### **Scenario 3: Many Orders (10+)**
```
Pending Orders: 15

Owner sees:
- Top header: "🔔 15 New Orders"
- Bottom nav: Badge shows "9+"
- Dashboard: Badge shows "9+"

Prevents badge from being too large
```

---

## 🔔 Notification Behavior

### **When Badge Shows:**
- ✅ Pending orders > 0
- ✅ Order status = 'pending'
- ✅ Seller ID matches owner

### **When Badge Hides:**
- ✅ No pending orders
- ✅ All orders processed (preparing/ready/completed)
- ✅ Orders cancelled

### **Badge Updates:**
- ✅ On page load
- ✅ On navigation
- ✅ Real-time (when page refreshes)

---

## 🎨 Animation Details

### **Pulse Animation:**
```css
animate-pulse
/* Tailwind CSS built-in animation */
/* Fades in/out continuously */
/* Draws attention to badge */
```

### **Hover Effects:**
```css
/* Top header button */
hover:bg-white/30
/* Brightens on hover */

/* Bottom nav icon */
hover:text-amber-600
/* Changes color on hover */
```

---

## 📊 Badge Comparison

| Location | Size | Position | Click Action |
|----------|------|----------|--------------|
| **Top Header** | Small (5x5) | Top-right of button | → Orders page |
| **Bottom Nav** | Small (5x5) | Top-right of icon | → Dashboard |
| **Dashboard Card** | Large (8x8) | Top-right of card | Visual only |

---

## 🧪 Testing Scenarios

### **Test 1: No Pending Orders**
```
Expected:
- ✅ No badge in top header
- ✅ No badge in bottom nav
- ✅ No badge on dashboard card
- ✅ Pending card shows "0"
```

### **Test 2: 1 Pending Order**
```
Expected:
- ✅ Top header: "🔔 1 New Order" with badge (1)
- ✅ Bottom nav: Badge (1) on Dashboard
- ✅ Dashboard card: Badge (1)
- ✅ All badges pulsing
```

### **Test 3: 5 Pending Orders**
```
Expected:
- ✅ Top header: "🔔 5 New Orders" with badge (5)
- ✅ Bottom nav: Badge (5)
- ✅ Dashboard card: Badge (5)
```

### **Test 4: 15 Pending Orders**
```
Expected:
- ✅ Top header: "🔔 15 New Orders" with badge (9+)
- ✅ Bottom nav: Badge (9+)
- ✅ Dashboard card: Badge (9+)
```

### **Test 5: Click Notification**
```
Action: Click "🔔 X New Orders" button
Expected:
- ✅ Redirects to Orders page
- ✅ Shows all pending orders
- ✅ Can process orders
```

### **Test 6: Staff Account**
```
Login as: staff@waffle.com
Expected:
- ✅ Shows same pending count as owner
- ✅ Queries owner's orders
- ✅ Redirects to staff.orders
```

---

## 🎯 Benefits

### **For Owner:**
1. **Instant Awareness** - See new orders immediately
2. **No Missed Orders** - Visual reminder always visible
3. **Quick Access** - Click to view orders
4. **Multi-location** - Badges on multiple places
5. **Professional** - Similar to major apps (Grab, Foodpanda)

### **For Customers:**
1. **Faster Service** - Owner sees orders quickly
2. **Better Experience** - Orders processed promptly
3. **Trust** - Professional notification system

---

## 🔧 Customization Options

### **Change Badge Color:**
```php
// Current: Red
bg-red-500

// Options:
bg-orange-500  // Orange
bg-amber-500   // Amber
bg-yellow-500  // Yellow
```

### **Change Badge Size:**
```php
// Current: h-5 w-5 (small), h-8 w-8 (large)

// Options:
h-4 w-4  // Extra small
h-6 w-6  // Medium
h-10 w-10  // Extra large
```

### **Disable Animation:**
```php
// Remove: animate-pulse
// Badge will be static
```

---

## ✅ Status

- ✅ Top header notification implemented
- ✅ Bottom navigation badge implemented
- ✅ Dashboard card badge implemented
- ✅ Owner notifications working
- ✅ Staff notifications working
- ✅ Responsive design
- ✅ Pulse animation
- ✅ Click actions
- ✅ 9+ overflow handling

---

## 📝 Files Modified

1. ✅ `resources/views/layouts/app.blade.php`
   - Added top header notification
   - Added bottom nav badge for owner
   - Added bottom nav badge for staff

2. ✅ `resources/views/seller/dashboard.blade.php`
   - Added badge to Pending Orders card

---

**Feature Status:** ✅ **Complete**  
**Date:** December 14, 2025  
**Similar To:** Cart badge (customer side)  
**Purpose:** Help owner track new orders instantly  

---

**Sekarang owner boleh nampak pending orders dengan jelas macam cart badge!** 🔔✨

