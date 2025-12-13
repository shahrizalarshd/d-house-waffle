# 📊 Sales Report & Excel Export Feature

## Overview
Complete sales reporting system with advanced filtering and Excel export functionality for D'house Waffle business owners.

---

## 🎯 Features Implemented

### 1. **Sales Report Dashboard**
- **Route:** `/owner/sales-report`
- **Access:** Owner only
- **Features:**
  - Real-time sales statistics
  - Advanced filtering system
  - Paginated orders list
  - Excel export functionality

### 2. **Filter Options**

#### **Date Range Filter**
- **Date From:** Start date for report
- **Date To:** End date for report
- **Default:** Current month (1st to today)

#### **Order Status Filter**
- All Status
- Pending
- Preparing
- Ready
- Completed
- Cancelled

#### **Payment Method Filter**
- All Methods
- Cash
- QR Payment
- Online Payment

#### **Payment Status Filter**
- All Payment Status
- Paid
- Pending
- Failed

### 3. **Sales Statistics**

#### **Total Orders**
- Count of all orders matching filters
- Blue gradient card

#### **Total Revenue**
- Sum of all PAID orders (seller_amount)
- Green gradient card
- Formatted as RM X,XXX.XX

#### **Average Order Value**
- Average of all paid orders
- Purple gradient card
- Formatted as RM XXX.XX

#### **Total Items Sold**
- Sum of all item quantities
- Orange gradient card

### 4. **Excel Export**

#### **Export Features:**
- ✅ Applies same filters as current view
- ✅ Exports all matching records (not just current page)
- ✅ Professional formatting with headers
- ✅ Auto-sized columns
- ✅ Timestamped filename

#### **Excel Columns:**
1. Order No
2. Date (d M Y H:i)
3. Customer Name
4. Unit No (with Block)
5. Items (comma-separated)
6. Quantity (total)
7. Subtotal (RM)
8. Service Fee (RM)
9. Total Amount (RM)
10. Payment Method
11. Payment Status
12. Order Status
13. Paid At

#### **Filename Format:**
```
dhouse-waffle-sales-YYYY-MM-DD-HHMMSS.xlsx
Example: dhouse-waffle-sales-2025-12-14-153045.xlsx
```

---

## 🔧 Technical Implementation

### **Package Used**
```bash
composer require maatwebsite/excel
```
- Package: `maatwebsite/excel` v3.1.67
- Based on: PHPSpreadsheet

### **Files Created/Modified**

#### **1. Export Class**
**File:** `app/Exports/OrdersExport.php`
```php
- Implements: FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
- Constructor accepts filtered orders collection
- Maps order data to Excel columns
- Styles header row (bold, size 12)
```

#### **2. Controller Methods**
**File:** `app/Http/Controllers/SellerController.php`

**Method: `salesReport()`**
- Builds filtered query
- Calculates statistics
- Returns paginated orders (20 per page)

**Method: `exportSalesReport()`**
- Uses same filter logic
- Gets ALL matching orders (no pagination)
- Returns Excel download

#### **3. View**
**File:** `resources/views/owner/sales-report.blade.php`
- Filter form with 5 filter options
- Statistics cards (4 metrics)
- Export button
- Orders table with pagination
- Responsive design

#### **4. Routes**
**File:** `routes/web.php`
```php
Route::get('/sales-report', [SellerController::class, 'salesReport'])->name('sales-report');
Route::get('/sales-report/export', [SellerController::class, 'exportSalesReport'])->name('sales-report.export');
```

#### **5. Navigation Updates**
- **Dashboard:** Added "Sales Report" quick action button
- **Bottom Nav:** Replaced "Orders" with "Reports" for owner

---

## 📱 User Interface

### **Color Scheme**
- **Filter Button:** Amber to Orange gradient
- **Export Button:** Green to Emerald gradient
- **Reset Button:** Gray
- **Stats Cards:** Blue, Green, Purple, Orange gradients

### **Icons**
- 📊 Sales Report (main)
- 🔍 Filter
- 📥 Download Excel
- 📈 Statistics

### **Responsive Design**
- Mobile: 1-2 columns
- Desktop: 3-4 columns
- Table: Horizontal scroll on mobile

---

## 🧪 Testing Guide

### **Test Scenario 1: Default View**
1. Login as owner: `owner@waffle.com` / `password`
2. Go to `/owner/sales-report`
3. **Expected:**
   - Shows current month data
   - All filters set to default
   - Statistics displayed
   - Orders listed

### **Test Scenario 2: Date Filter**
1. Set Date From: `2025-12-01`
2. Set Date To: `2025-12-14`
3. Click "Apply Filters"
4. **Expected:**
   - Only orders in date range shown
   - Statistics updated
   - URL contains filter params

### **Test Scenario 3: Status Filter**
1. Select Status: `Completed`
2. Click "Apply Filters"
3. **Expected:**
   - Only completed orders shown
   - Total orders count updated

### **Test Scenario 4: Payment Method Filter**
1. Select Payment Method: `Cash`
2. Click "Apply Filters"
3. **Expected:**
   - Only cash orders shown
   - Revenue calculated from cash orders only

### **Test Scenario 5: Multiple Filters**
1. Date From: `2025-12-01`
2. Status: `Completed`
3. Payment Method: `QR`
4. Payment Status: `Paid`
5. Click "Apply Filters"
6. **Expected:**
   - All filters applied simultaneously
   - Accurate statistics

### **Test Scenario 6: Excel Export**
1. Apply any filters
2. Click "Download Excel"
3. **Expected:**
   - File downloads immediately
   - Filename: `dhouse-waffle-sales-YYYY-MM-DD-HHMMSS.xlsx`
   - Opens in Excel/Sheets
   - Contains filtered data
   - Headers bold and formatted
   - Columns auto-sized

### **Test Scenario 7: Reset Filters**
1. Apply multiple filters
2. Click "Reset" button
3. **Expected:**
   - All filters cleared
   - Returns to default view (current month)
   - Statistics recalculated

### **Test Scenario 8: Empty Results**
1. Set impossible filter combination
2. Click "Apply Filters"
3. **Expected:**
   - "No orders found" message
   - Statistics show zeros
   - Export button still visible

### **Test Scenario 9: Pagination**
1. Ensure > 20 orders exist
2. View report
3. **Expected:**
   - Shows 20 orders per page
   - Pagination links at bottom
   - Filters persist across pages

### **Test Scenario 10: Navigation**
1. Click "Sales Report" from dashboard
2. Check bottom navigation
3. **Expected:**
   - "Reports" tab highlighted
   - Can navigate to other sections
   - Returns to report with filters intact

---

## 📊 Sample Data Queries

### **Get Total Revenue (This Month)**
```sql
SELECT SUM(seller_amount) 
FROM orders 
WHERE seller_id = 2 
  AND payment_status = 'paid'
  AND MONTH(created_at) = MONTH(NOW())
  AND YEAR(created_at) = YEAR(NOW());
```

### **Get Top Selling Products**
```sql
SELECT product_name, SUM(quantity) as total_sold
FROM order_items
JOIN orders ON order_items.order_id = orders.id
WHERE orders.seller_id = 2
  AND orders.payment_status = 'paid'
GROUP BY product_name
ORDER BY total_sold DESC
LIMIT 10;
```

### **Get Daily Sales**
```sql
SELECT DATE(created_at) as date, 
       COUNT(*) as orders, 
       SUM(seller_amount) as revenue
FROM orders
WHERE seller_id = 2
  AND payment_status = 'paid'
GROUP BY DATE(created_at)
ORDER BY date DESC;
```

---

## 🎨 UI Screenshots Reference

### **Filter Section**
```
┌─────────────────────────────────────────┐
│ 🔍 Filter Sales Data                    │
├─────────────────────────────────────────┤
│ Date From    Date To      Status        │
│ [2025-12-01] [2025-12-14] [Completed▼]  │
│                                         │
│ Payment Method  Payment Status          │
│ [Cash▼]         [Paid▼]                 │
│                                         │
│ [Apply Filters] [Reset]                 │
└─────────────────────────────────────────┘
```

### **Statistics Cards**
```
┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐
│ Total    │ │ Total    │ │ Avg Order│ │ Total    │
│ Orders   │ │ Revenue  │ │ Value    │ │ Items    │
│   125    │ │ RM 3,450 │ │ RM 27.60 │ │   342    │
└──────────┘ └──────────┘ └──────────┘ └──────────┘
```

### **Export Section**
```
┌─────────────────────────────────────────┐
│ Export Sales Report                     │
│ Download filtered data as Excel file    │
│                            [📥 Download] │
└─────────────────────────────────────────┘
```

---

## 🚀 Future Enhancements (Optional)

### **Phase 2 Ideas:**
1. **Chart Visualizations**
   - Daily sales line chart
   - Payment method pie chart
   - Product category breakdown

2. **Additional Exports**
   - PDF reports
   - CSV format
   - Print-friendly view

3. **Advanced Analytics**
   - Peak hours analysis
   - Customer retention metrics
   - Product performance ranking

4. **Scheduled Reports**
   - Email daily/weekly reports
   - Auto-generate monthly summaries

5. **Comparison Features**
   - Compare periods (This month vs Last month)
   - Year-over-year comparison

---

## ✅ Feature Status

- ✅ Sales report page with filters
- ✅ Date range filter
- ✅ Status filters (order, payment, method)
- ✅ Real-time statistics calculation
- ✅ Excel export with formatting
- ✅ Pagination support
- ✅ Filter persistence across pages
- ✅ Responsive design
- ✅ Navigation integration
- ✅ Professional UI/UX

---

## 🔐 Security & Access Control

- **Route Protection:** `role:owner` middleware
- **Data Isolation:** Only owner's orders shown
- **Query Optimization:** Eager loading relationships
- **Input Validation:** Date format validation
- **SQL Injection:** Protected by Eloquent ORM

---

## 📝 Notes

1. **Default Date Range:** Current month (1st to today)
2. **Pagination:** 20 orders per page
3. **Export Limit:** No limit (exports all matching records)
4. **Statistics:** Calculated from filtered dataset
5. **Revenue:** Only counts PAID orders
6. **File Format:** XLSX (Excel 2007+)

---

**Feature Completed:** December 14, 2025
**Package Version:** maatwebsite/excel v3.1.67
**Laravel Version:** 11.x
**Status:** ✅ Production Ready

