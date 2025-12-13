# Billplz Setup Guide
**How to Configure Billplz Payment Gateway in POS Apartment**

---

## 🎯 WHERE TO SETUP BILLPLZ

### ✅ **SUPER ADMIN Dashboard** (Recommended)

```
URL: /super/dashboard
Access: super_admin role only
Why: Payment gateway is platform-wide setting
```

**Navigation:**
1. Login as `super_admin`
2. Go to Dashboard
3. Click "Platform Settings"
4. Configure Billplz settings
5. Save & Test connection

---

## 🔐 SECURITY & ACCESS LEVELS

### **Who Can Access?**

| Role | Access | Reason |
|------|--------|--------|
| **super_admin** | ✅ Full Access | Platform-wide settings |
| **apartment_admin** | ❌ No Access | Security sensitive |
| **seller** | ❌ No Access | Security sensitive |
| **buyer** | ❌ No Access | Security sensitive |

### **Why Super Admin Only?**

1. ✅ **Security**: API keys are sensitive
2. ✅ **Platform-wide**: One Billplz account for all apartments
3. ✅ **Centralized**: Easier management
4. ✅ **SaaS-ready**: Prepare for multi-tenant future

---

## 📋 SETUP STEPS

### **Step 1: Get Billplz Account**

1. **Sign up**: https://www.billplz.com/join
2. **Choose account type:**
   - Individual (for personal/small business)
   - Business (for company)
3. **Complete verification:**
   - Upload MyKad/Business Registration
   - Wait for approval (1-3 days)

### **Step 2: Add Bank Account**

1. Login to Billplz Dashboard
2. Go to **Settings → Bank Account**
3. Add your bank account details
4. Verify account (micro-deposit method)

### **Step 3: Create Collection**

1. Go to **Collections**
2. Click **Create Collection**
3. Fill in details:
   ```
   Collection Name: POS Apartment Orders
   Description: Order payments
   Reference 1 Label: Order ID
   ```
4. Note down **Collection ID** (e.g., `abc_xyz123`)

### **Step 4: Get API Credentials**

1. Go to **Settings → API Keys**
2. Copy **API Secret Key**
   - Format: `abc123-def456-ghi789-xxx`
   - **Keep it SECRET!**
3. Copy **X Signature Key**
   - Format: `S-xxxxxxxxxxxxxxxx`
   - Used for webhook verification

### **Step 5: Configure in System**

1. Login to your POS Apartment as **super_admin**
2. Go to `/super/settings`
3. Fill in Billplz settings:

```
┌─────────────────────────────────────────┐
│ ☑ Enable Billplz Payment Gateway       │
│ ☑ Sandbox/Testing Mode                 │
│                                         │
│ API Secret Key:                         │
│ [abc123-def456-ghi789-xxx]             │
│                                         │
│ Collection ID:                          │
│ [abc_xyz123]                           │
│                                         │
│ X Signature Key:                        │
│ [S-xxxxxxxxxxxxxxxx]                   │
└─────────────────────────────────────────┘

[Save Settings] [Test Connection]
```

4. Click **"Test Connection"** to verify
5. If successful, you'll see:
   ```
   ✅ Billplz connection successful!
   Collection: POS Apartment Orders
   ```

### **Step 6: Set Webhook URL**

1. Go back to Billplz Dashboard
2. **Settings → Webhook URL**
3. Set webhook URL to:
   ```
   https://yourdomain.com/webhook/billplz
   ```
   **Important:** Must be HTTPS (secure)!

4. **Test webhook** (Billplz will send test notification)

---

## 🔧 CONFIGURATION OPTIONS

### **Sandbox vs Production Mode**

#### **Sandbox Mode (Testing):**
```
☑ Sandbox/Testing Mode

- Use Billplz sandbox credentials
- No real money involved
- For development & testing
- URL: billplz-sandbox.com
```

#### **Production Mode (Live):**
```
☐ Sandbox/Testing Mode

- Use real Billplz credentials
- Real money transactions
- For live operations
- URL: billplz.com
```

**Recommendation:** Start with Sandbox, test thoroughly, then switch to Production.

---

## 💻 DATABASE STRUCTURE

### **New Table: platform_settings**

```sql
CREATE TABLE platform_settings (
    id BIGINT PRIMARY KEY,
    key VARCHAR(255) UNIQUE,
    value TEXT,
    type VARCHAR(50),
    description TEXT,
    is_sensitive BOOLEAN,
    timestamps
);
```

### **Billplz Settings Stored:**

```
billplz_enabled           → true/false
billplz_api_key          → API Secret Key (encrypted)
billplz_collection_id    → Collection ID
billplz_x_signature      → X Signature Key (encrypted)
billplz_sandbox_mode     → true/false
```

### **Security Features:**

1. ✅ Sensitive fields marked (`is_sensitive = true`)
2. ✅ Passwords hidden in UI (type="password")
3. ✅ Only super_admin can access
4. ✅ Cached for performance
5. ✅ Logged for audit trail

---

## 🎯 USAGE IN CODE

### **Check if Billplz is Ready:**

```php
use App\Models\PlatformSetting;

if (PlatformSetting::isBillplzReady()) {
    // Billplz is configured and enabled
    // Proceed with payment creation
} else {
    // Show manual payment option
    // Or disable checkout
}
```

### **Get Billplz Settings:**

```php
$settings = PlatformSetting::getBillplzSettings();

// Returns:
// [
//     'enabled' => true,
//     'api_key' => 'abc123...',
//     'collection_id' => 'abc_xyz123',
//     'x_signature' => 'S-xxxx...',
//     'sandbox_mode' => true,
// ]
```

### **Set Individual Setting:**

```php
PlatformSetting::set('billplz_enabled', true);
PlatformSetting::set('billplz_sandbox_mode', false);
```

### **Get Individual Setting:**

```php
$apiKey = PlatformSetting::get('billplz_api_key');
$isSandbox = PlatformSetting::get('billplz_sandbox_mode', true);
```

---

## 📱 ADMIN UI SCREENSHOTS (Conceptual)

### **Super Admin Dashboard:**

```
┌────────────────────────────────────────────┐
│ Super Admin Dashboard                      │
├────────────────────────────────────────────┤
│                                            │
│  [Stats Cards]                             │
│  Total Apartments: 1                       │
│  Total Users: 25                           │
│  Total Orders: 150                         │
│                                            │
│  Payment Gateway Status:                   │
│  🔴 Not Configured                         │
│  [Configure Now] ← Click here!            │
│                                            │
└────────────────────────────────────────────┘
```

### **Platform Settings Page:**

```
┌────────────────────────────────────────────┐
│ Platform Settings                          │
├────────────────────────────────────────────┤
│                                            │
│ 💳 Billplz Payment Gateway                 │
│ ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━  │
│                                            │
│ ☑ Enable Billplz Payment Gateway          │
│ ☑ Sandbox/Testing Mode                    │
│                                            │
│ 🔑 API Secret Key:                         │
│ [••••••••••••••••]                        │
│                                            │
│ 📁 Collection ID:                          │
│ [abc_xyz123]                              │
│                                            │
│ 🛡 X Signature Key:                        │
│ [••••••••••••••••]                        │
│                                            │
│ 🔗 Webhook URL:                            │
│ [https://...com/webhook/billplz] [Copy]   │
│                                            │
│ [Save Settings] [Test Connection]         │
│                                            │
└────────────────────────────────────────────┘
```

---

## 🧪 TESTING

### **Test Connection:**

1. Fill in Billplz credentials
2. Click **"Test Connection"**
3. System will call Billplz API
4. If successful:
   ```
   ✅ Billplz connection successful!
   Collection: POS Apartment Orders
   ```
5. If failed:
   ```
   ❌ Billplz connection failed: Invalid API key
   ```

### **Test Payment Flow (Sandbox):**

1. Enable Sandbox Mode
2. Create a test order
3. Proceed to checkout
4. Use Billplz test credentials:
   ```
   Test Bank: Maybank2u
   Username: Test123
   Password: Test123
   TAC: 123456
   ```
5. Complete payment
6. Check webhook received
7. Verify order status updated

---

## ⚠️ IMPORTANT NOTES

### **Security Warnings:**

1. ❌ **NEVER** commit API keys to Git
2. ❌ **NEVER** share API keys publicly
3. ❌ **NEVER** hardcode keys in code
4. ✅ **ALWAYS** use environment variables or database
5. ✅ **ALWAYS** use HTTPS for webhook
6. ✅ **ALWAYS** verify webhook signatures

### **Production Checklist:**

Before going live:

- [ ] Billplz account fully verified
- [ ] Bank account added & verified
- [ ] Collection created
- [ ] API credentials obtained
- [ ] Credentials saved in system
- [ ] Connection test passed
- [ ] Webhook URL set (HTTPS)
- [ ] Webhook test successful
- [ ] Sandbox mode DISABLED
- [ ] Test order in production
- [ ] Monitor webhook logs

---

## 🔄 MIGRATION FROM .ENV TO DATABASE

**Old Way (Not Recommended):**
```env
# .env file
BILLPLZ_API_KEY=abc123
BILLPLZ_COLLECTION_ID=xyz789
```

**New Way (Recommended):**
```
Database: platform_settings table
UI: Super Admin → Settings
Benefits:
  ✅ No need to redeploy to change
  ✅ UI for easy management
  ✅ Audit trail
  ✅ Multi-environment ready
```

**If you have existing .env config:**

1. Copy values from .env
2. Login as super_admin
3. Go to `/super/settings`
4. Paste values into form
5. Save
6. Test connection
7. Remove from .env (optional)

---

## 📞 SUPPORT

### **If Billplz Connection Fails:**

**Check:**
1. API key is correct (no extra spaces)
2. Collection ID is correct
3. Internet connection working
4. Billplz service is up (check status.billplz.com)
5. Account is verified
6. Account not suspended

**Error Messages:**

| Error | Cause | Solution |
|-------|-------|----------|
| `Invalid API key` | Wrong key or typo | Double-check API key |
| `Collection not found` | Wrong ID | Verify Collection ID |
| `Unauthorized` | Account issue | Check Billplz account status |
| `Connection timeout` | Network issue | Check internet connection |

### **Contact Billplz Support:**

- Email: support@billplz.com
- Website: https://www.billplz.com/contact
- Documentation: https://www.billplz.com/api

---

## 🎓 SUMMARY

### **Quick Setup (TL;DR):**

1. ✅ Sign up at Billplz.com
2. ✅ Get API credentials
3. ✅ Login as super_admin
4. ✅ Go to `/super/settings`
5. ✅ Fill in Billplz config
6. ✅ Test connection
7. ✅ Set webhook URL
8. ✅ Start accepting payments!

### **Key Points:**

- 🔐 Setup location: **Super Admin → Settings**
- 🚫 Apartment admins **CANNOT** access
- ✅ Platform-wide configuration
- ✅ Secure & encrypted storage
- ✅ Easy to manage via UI
- ✅ Test mode available

**You're now ready to accept payments! 🎉**

---

**Document Created:** 2025-12-13  
**Version:** 1.0  
**Status:** Production Ready ✅

