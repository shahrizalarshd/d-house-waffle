# 🚀 Complete DigitalOcean Setup Guide - D'house Waffle

## 📋 What You'll Get

- ✅ $200 FREE credit (60 days)
- ✅ 3+ months free hosting
- ✅ Docker pre-installed
- ✅ Singapore datacenter (fast!)
- ✅ Professional hosting

**Total Time:** ~2 hours from zero to live!

---

## 📝 Prerequisites

Before starting, prepare:
- [ ] Email address
- [ ] Credit/Debit card (for verification, won't be charged)
- [ ] GitHub account (for code repository)
- [ ] Domain name (optional, can buy later)

---

## 🎯 PART 1: Create DigitalOcean Account

### **Step 1.1: Sign Up (5 minutes)**

1. **Go to DigitalOcean:**
   ```
   https://www.digitalocean.com/
   ```

2. **Click "Sign Up"** (top right)

3. **Create Account:**
   ```
   Option 1: Sign up with Google
   - Click "Sign up with Google"
   - Choose your Google account
   - Done!

   Option 2: Sign up with Email
   - Enter your email
   - Create password (strong!)
   - Click "Sign Up"
   - Verify email (check inbox)
   ```

4. **Tell Us About Yourself:**
   ```
   What will you use DigitalOcean for?
   → Select: "To build and host a web application"
   
   What is your experience level?
   → Select: "Intermediate" or "Beginner"
   
   Click: "Continue"
   ```

5. **Add Payment Method:**
   ```
   Why needed: To prevent abuse (won't charge!)
   
   Credit Card:
   - Enter card number
   - Expiry date
   - CVV
   - Billing address
   
   OR
   
   PayPal:
   - Click PayPal option
   - Login & authorize
   
   Click: "Link Payment Method"
   ```

6. **Get $200 Credit!** 🎉
   ```
   Automatically applied for new accounts!
   
   Check top right corner:
   "Balance: $200.00"
   
   Valid for: 60 days
   Enough for: 16+ months of $12 droplet
   Can use: 3 months (then credit expires)
   ```

**✅ Checkpoint:** You should see Dashboard with $200 credit!

---

## 🖥️ PART 2: Create Droplet (Server)

### **Step 2.1: Create Droplet (10 minutes)**

1. **Click "Create"** (top right green button)
   ```
   Select: "Droplets"
   ```

2. **Choose Region:**
   ```
   ⭐ IMPORTANT: Choose closest to Malaysia!
   
   Recommended:
   ✅ Singapore - 1 (SGP1)
   
   Why: 20-50ms latency from Malaysia (very fast!)
   
   Click: Singapore checkbox
   ```

3. **Choose Image:**
   ```
   Tab: Marketplace
   
   Search: "docker"
   
   Select: "Docker on Ubuntu 22.04"
   
   What is this:
   - Ubuntu 22.04 OS
   - Docker pre-installed
   - Docker Compose included
   - Ready to use!
   
   Click: "Docker on Ubuntu 22.04"
   ```

4. **Choose Size:**
   ```
   Tab: Basic
   
   CPU Options: Regular Intel
   
   ⭐ RECOMMENDED PLAN:
   
   $12/month ($0.018/hour)
   - 2 GB RAM
   - 1 vCPU
   - 50 GB SSD
   - 2 TB Transfer
   
   Perfect for D'house Waffle!
   
   Click: "$12/mo" plan
   ```

5. **Choose Authentication:**
   ```
   ⭐ OPTION A: SSH Key (More Secure) - RECOMMENDED
   
   What is SSH Key:
   - Password alternative
   - More secure
   - Used to login to server
   
   Create SSH Key (Mac/Linux):
   
   Open Terminal, run:
   ssh-keygen -t rsa -b 4096 -C "your_email@example.com"
   
   Press Enter (save to default location)
   Press Enter (no passphrase, or create one)
   
   Copy public key:
   cat ~/.ssh/id_rsa.pub
   
   Copy entire output (starts with "ssh-rsa")
   
   In DigitalOcean:
   - Click "New SSH Key"
   - Paste your public key
   - Name: "My Laptop" or "My Mac"
   - Click "Add SSH Key"
   
   ---
   
   ⭐ OPTION B: Password (Easier for Beginners)
   
   - Select "Password"
   - You'll get root password via email
   - Less secure but easier
   ```

6. **Finalize Details:**
   ```
   How many Droplets: 1
   
   Choose a hostname:
   → dhouse-waffle
   (or any name you like)
   
   Tags: (optional)
   → leave empty or add: production, laravel
   
   Select Project:
   → "First Project" (default)
   
   Backups: (optional, +$8/month)
   → Uncheck for now (can enable later)
   
   IPv6: 
   → Leave checked (free)
   
   User Data:
   → Leave empty
   
   Monitoring:
   → Check "Enable Monitoring" (free!)
   ```

7. **Create Droplet:**
   ```
   Review Summary:
   - Singapore
   - Docker on Ubuntu 22.04
   - $12/month plan
   - SSH Key or Password
   
   Click: "Create Droplet" (bottom green button)
   
   Wait: 60 seconds
   
   Status will show: "Active" (green dot)
   ```

8. **Get Droplet IP Address:**
   ```
   Your droplet is ready!
   
   Copy IP address (e.g., 159.65.xx.xx)
   
   Write it down!
   ```

**✅ Checkpoint:** Droplet created, status "Active", IP address copied!

---

## 🔗 PART 3: Connect to Your Droplet

### **Step 3.1: First Connection (5 minutes)**

**OPTION A: Using SSH Key (Mac/Linux/Windows WSL)**

```bash
# Open Terminal

# Connect to droplet
ssh root@your-droplet-ip

# Replace your-droplet-ip with actual IP
# Example: ssh root@159.65.12.34

# First time will ask:
# "Are you sure you want to continue connecting?"
# Type: yes

# You're now connected! 🎉
# Prompt will show: root@dhouse-waffle:~#
```

**OPTION B: Using Password**

```bash
# Open Terminal

# Connect
ssh root@your-droplet-ip

# When prompted for password:
# Check your email for root password
# Paste password (won't show when typing)
# Press Enter

# First login will ask to change password:
# Enter current password
# Enter new password (strong!)
# Confirm new password

# Connected! 🎉
```

**OPTION C: Using DigitalOcean Console (Browser)**

```
In DigitalOcean Dashboard:
1. Click on your droplet name
2. Click "Console" (top right)
3. Browser terminal opens
4. Login as: root
5. Enter password
6. Connected!

Note: Browser console is slower, use SSH if possible
```

**✅ Checkpoint:** You can see command prompt: `root@dhouse-waffle:~#`

---

## 🛠️ PART 4: Setup Server

### **Step 4.1: Update System (5 minutes)**

```bash
# Update package list
apt update

# Upgrade packages (press Y when asked)
apt upgrade -y

# This takes 2-3 minutes
# Output will show packages being upgraded

# Verify Docker installed
docker --version
# Should show: Docker version 24.x.x

docker-compose --version
# Should show: docker-compose version 1.29.x or 2.x.x
```

### **Step 4.2: Install Additional Tools (5 minutes)**

```bash
# Install useful tools
apt install -y git curl wget nano ufw

# Install Nginx (web server)
apt install -y nginx

# Install Certbot (for SSL)
apt install -y certbot python3-certbot-nginx

# Verify installations
git --version
nginx -v
certbot --version

# All should show version numbers ✅
```

### **Step 4.3: Configure Firewall (5 minutes)**

```bash
# Allow SSH (important! don't lock yourself out!)
ufw allow OpenSSH

# Allow HTTP (port 80)
ufw allow 'Nginx HTTP'

# Allow HTTPS (port 443)
ufw allow 'Nginx HTTPS'

# Allow Reverb WebSocket (port 8080)
ufw allow 8080/tcp

# Enable firewall (will show warning)
ufw enable

# Press Y and Enter

# Check status
ufw status

# Should show:
# Status: active
# With ports 22, 80, 443, 8080 allowed
```

**✅ Checkpoint:** Firewall enabled, necessary ports open!

---

## 📦 PART 5: Deploy D'house Waffle

### **Step 5.1: Clone Repository (5 minutes)**

```bash
# Create directory for apps
cd /var/www

# Clone your repository
git clone https://github.com/your-username/dhouse-waffle.git

# If repository is private, will ask for credentials:
# Username: your-github-username
# Password: use Personal Access Token (not password!)
#
# How to create GitHub Token:
# 1. GitHub → Settings → Developer settings
# 2. Personal access tokens → Tokens (classic)
# 3. Generate new token
# 4. Select: repo (all)
# 5. Copy token and paste as password

# Enter directory
cd dhouse-waffle

# Check files
ls -la

# Should see:
# app/ config/ database/ docker-compose.yml etc.
```

### **Step 5.2: Configure Environment (10 minutes)**

```bash
# Copy environment file
cp .env.example .env

# Edit environment
nano .env

# Update these values:
```

**.env Production Settings:**
```env
APP_NAME="D'house Waffle"
APP_ENV=production
APP_KEY=    # Will generate later
APP_DEBUG=false
APP_TIMEZONE=Asia/Kuala_Lumpur
APP_URL=http://your-droplet-ip
# or https://your-domain.com (if you have domain)

# Database
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=dhouse_waffle
DB_USERNAME=sail
DB_PASSWORD=password
# Change password to something secure!

# Broadcasting (Reverb)
BROADCAST_CONNECTION=reverb

REVERB_APP_ID=dhouse-waffle
REVERB_APP_KEY=local-key
REVERB_APP_SECRET=local-secret
REVERB_HOST=your-droplet-ip
REVERB_PORT=8080
REVERB_SCHEME=http
# Change to https when SSL setup

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"

# Cache & Queue
CACHE_STORE=database
QUEUE_CONNECTION=database
SESSION_DRIVER=database
```

**Save file:**
```bash
# Press: Ctrl + O (save)
# Press: Enter (confirm)
# Press: Ctrl + X (exit)
```

### **Step 5.3: Start Docker Containers (10 minutes)**

```bash
# Start all containers
docker-compose up -d

# This will:
# - Download Laravel Sail images (first time ~5 min)
# - Start MySQL
# - Start Laravel app
# - Start other services

# Check containers running
docker-compose ps

# Should show:
# dhouse-waffle-app     running
# dhouse-waffle-mysql   running

# Check logs (optional)
docker-compose logs -f

# Press Ctrl+C to exit logs
```

### **Step 5.4: Setup Application (15 minutes)**

```bash
# Generate app key
docker-compose exec app php artisan key:generate

# Run migrations
docker-compose exec app php artisan migrate --force

# Seed database
docker-compose exec app php artisan db:seed --force

# Create storage link
docker-compose exec app php artisan storage:link

# Cache configuration
docker-compose exec app php artisan config:cache

# Cache routes
docker-compose exec app php artisan route:cache

# Cache views
docker-compose exec app php artisan view:cache

# Optimize
docker-compose exec app php artisan optimize

# Set permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### **Step 5.5: Test Application (5 minutes)**

```bash
# Check app is running
docker-compose exec app php artisan --version

# Should show: Laravel Framework 12.x.x

# Test database connection
docker-compose exec app php artisan tinker --execute="echo \App\Models\User::count();"

# Should show: 4 (number of seeded users)

# Exit (if in tinker)
exit
```

**✅ Checkpoint:** Application deployed and running in Docker!

---

## 🌐 PART 6: Configure Nginx (Web Server)

### **Step 6.1: Create Nginx Configuration (10 minutes)**

```bash
# Create config file
nano /etc/nginx/sites-available/dhouse-waffle

# Paste this configuration:
```

**Nginx Configuration:**
```nginx
server {
    listen 80;
    listen [::]:80;
    
    server_name your-droplet-ip;
    # Change to your-domain.com if you have domain
    
    root /var/www/dhouse-waffle/public;
    index index.php index.html;
    
    charset utf-8;
    
    location / {
        proxy_pass http://localhost:8000;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host $host;
        proxy_cache_bypass $http_upgrade;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
    
    # WebSocket (Reverb)
    location /reverb {
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "Upgrade";
        proxy_set_header Host $host;
        proxy_cache_bypass $http_upgrade;
        proxy_pass http://127.0.0.1:8080;
    }
    
    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    
    # Logging
    access_log /var/log/nginx/dhouse-access.log;
    error_log /var/log/nginx/dhouse-error.log;
}
```

**Save and enable:**
```bash
# Save file (Ctrl+O, Enter, Ctrl+X)

# Enable site
ln -s /etc/nginx/sites-available/dhouse-waffle /etc/nginx/sites-enabled/

# Remove default site
rm /etc/nginx/sites-enabled/default

# Test configuration
nginx -t

# Should show: "syntax is ok" and "test is successful"

# Reload Nginx
systemctl reload nginx

# Check Nginx status
systemctl status nginx

# Should show: "active (running)"
```

**✅ Checkpoint:** Nginx configured and running!

---

## 🚀 PART 7: Start Laravel & Reverb

### **Step 7.1: Ensure Laravel Running (5 minutes)**

```bash
# Laravel should be running via Docker
# Check it's accessible

cd /var/www/dhouse-waffle

# Start services if not running
docker-compose up -d

# Check Laravel accessible locally
curl http://localhost:8000

# Should show HTML output (long text)
```

### **Step 7.2: Setup Reverb (10 minutes)**

```bash
# Install Supervisor (process manager)
apt install -y supervisor

# Create Reverb config
nano /etc/supervisor/conf.d/reverb.conf

# Paste this:
```

**Supervisor Configuration:**
```ini
[program:reverb]
process_name=%(program_name)s
command=docker-compose -f /var/www/dhouse-waffle/docker-compose.yml exec -T app php artisan reverb:start
directory=/var/www/dhouse-waffle
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=root
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/dhouse-waffle/storage/logs/reverb.log
stopwaitsecs=3600
```

**Start Reverb:**
```bash
# Save file (Ctrl+O, Enter, Ctrl+X)

# Reload supervisor
supervisorctl reread
supervisorctl update

# Start Reverb
supervisorctl start reverb

# Check status
supervisorctl status reverb

# Should show: RUNNING

# Or start manually (simpler):
cd /var/www/dhouse-waffle
docker-compose exec -d app php artisan reverb:start

# Check logs
docker-compose logs -f app | grep Reverb
```

**✅ Checkpoint:** Reverb running, real-time ready!

---

## 🌍 PART 8: Access Your Site!

### **Step 8.1: Test Website (5 minutes)**

```bash
# Get your droplet IP
curl ifconfig.me

# Or check DigitalOcean dashboard
```

**Open browser:**
```
1. Go to: http://your-droplet-ip

2. You should see: D'house Waffle homepage! 🎉

3. Test login:
   - Customer: buyer@test.com / password
   - Owner: owner@waffle.com / password
   
4. Test features:
   - Browse products
   - Add to cart
   - Place order
   - Owner sees notification (real-time!)
```

**✅ Checkpoint:** Website live and accessible!

---

## 🔒 PART 9: Setup Domain & SSL (Optional but Recommended)

### **Step 9.1: Point Domain to Droplet (5 minutes)**

**If you have a domain (e.g., dhousewaffle.com):**

1. **Go to your domain registrar** (Namecheap, GoDaddy, etc.)

2. **Add A Record:**
   ```
   Type: A
   Name: @ (or leave blank for root domain)
   Value: your-droplet-ip
   TTL: 300 (or automatic)
   
   Type: A
   Name: www
   Value: your-droplet-ip
   TTL: 300
   ```

3. **Wait for DNS propagation** (5-30 minutes)

4. **Test:**
   ```bash
   # Check if domain points to your IP
   ping your-domain.com
   
   # Should show your droplet IP
   ```

### **Step 9.2: Get Free SSL Certificate (10 minutes)**

```bash
# Update Nginx config with domain
nano /etc/nginx/sites-available/dhouse-waffle

# Change:
server_name your-droplet-ip;
# To:
server_name your-domain.com www.your-domain.com;

# Save (Ctrl+O, Enter, Ctrl+X)

# Reload Nginx
nginx -t
systemctl reload nginx

# Get SSL certificate
certbot --nginx -d your-domain.com -d www.your-domain.com

# Follow prompts:
# Email: your@email.com
# Agree to terms: Y
# Share email: N (or Y)
# Redirect HTTP to HTTPS: 2 (Yes - Recommended)

# Certificate installed! 🎉
# Valid for: 90 days
# Auto-renewal: Enabled

# Test auto-renewal
certbot renew --dry-run

# Should show: Congratulations, all renewals succeeded
```

### **Step 9.3: Update Application URL (5 minutes)**

```bash
# Update .env
cd /var/www/dhouse-waffle
nano .env

# Change:
APP_URL=http://your-droplet-ip
REVERB_HOST=your-droplet-ip
REVERB_SCHEME=http

# To:
APP_URL=https://your-domain.com
REVERB_HOST=your-domain.com
REVERB_SCHEME=https

# Also update:
VITE_REVERB_HOST=your-domain.com
VITE_REVERB_SCHEME=https

# Save (Ctrl+O, Enter, Ctrl+X)

# Clear cache
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:cache

# Restart
docker-compose restart
```

**✅ Checkpoint:** HTTPS working, green padlock in browser!

---

## 📊 PART 10: Final Verification

### **Step 10.1: Test All Features (15 minutes)**

**Test Checklist:**

```
Customer Flow:
[ ] Homepage loads (https://your-domain.com)
[ ] Can register new account
[ ] Can login
[ ] Can browse products
[ ] Can add to cart (badge updates)
[ ] Can checkout with Cash
[ ] Can checkout with QR
[ ] Order appears in My Orders

Owner Flow:
[ ] Login as owner (owner@waffle.com / password)
[ ] Dashboard shows stats
[ ] See pending order badge (red)
[ ] Incoming orders shows customer info:
    - Name
    - Unit & Block
    - Phone (clickable)
[ ] Can update order status
[ ] Can mark payment received
[ ] Sales report loads
[ ] Can download Excel

Real-time:
[ ] Owner dashboard open
[ ] Customer places order (another browser/device)
[ ] Owner hears "DING!" immediately
[ ] Toast notification appears
[ ] Browser notification (if allowed)
[ ] Badges update automatically
[ ] All within 1 second!

Staff Flow:
[ ] Login as staff (staff@waffle.com / password)
[ ] Can view orders
[ ] Can update statuses
[ ] Real-time notifications work

Super Admin:
[ ] Login as super (super@admin.com / password)
[ ] Can view all users
[ ] Settings accessible
```

**✅ Checkpoint:** All features working perfectly!

---

## 🎉 SUCCESS! YOU'RE LIVE!

### **What You've Accomplished:**

✅ Created DigitalOcean account with $200 credit
✅ Created droplet in Singapore
✅ Deployed D'house Waffle with Docker
✅ Configured Nginx web server
✅ Setup Laravel Reverb (real-time)
✅ (Optional) Domain & SSL configured
✅ All features working!

### **Your System:**
```
URL: https://your-domain.com
Cost: FREE for 3 months ($200 credit)
Then: $12/month (RM 57)

Resources:
- 2GB RAM
- 50GB Storage
- 2TB Bandwidth
- Singapore datacenter

Features:
- Real-time notifications ✅
- 3 payment methods ✅
- Sales reports ✅
- Excel export ✅
- Professional hosting ✅
```

---

## 📚 Daily Operations

### **Useful Commands:**

```bash
# SSH into server
ssh root@your-droplet-ip

# Check Docker containers
docker-compose ps

# View logs
docker-compose logs -f

# Restart application
docker-compose restart

# Stop application
docker-compose down

# Start application
docker-compose up -d

# Clear cache
docker-compose exec app php artisan cache:clear

# View Reverb logs
tail -f storage/logs/reverb.log

# Check Nginx logs
tail -f /var/log/nginx/dhouse-error.log

# Supervisor status
supervisorctl status

# Restart Reverb
supervisorctl restart reverb
```

### **Backup Database:**

```bash
# Create backup
docker-compose exec mysql mysqldump -u sail -ppassword dhouse_waffle > backup-$(date +%Y%m%d).sql

# Download backup to local (from your computer)
scp root@your-droplet-ip:/var/www/dhouse-waffle/backup-*.sql ./
```

### **Update Application:**

```bash
# SSH to server
ssh root@your-droplet-ip

# Go to directory
cd /var/www/dhouse-waffle

# Pull latest code
git pull

# Update dependencies (if needed)
docker-compose exec app composer install --no-dev

# Run migrations (if new)
docker-compose exec app php artisan migrate --force

# Clear cache
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:cache

# Restart
docker-compose restart
```

---

## 🆘 Troubleshooting

### **Issue: Can't access website**

```bash
# Check Nginx
systemctl status nginx
systemctl restart nginx

# Check Docker
docker-compose ps
docker-compose up -d

# Check firewall
ufw status
```

### **Issue: Reverb not working**

```bash
# Check Reverb running
supervisorctl status reverb
supervisorctl restart reverb

# Or manual
docker-compose exec app php artisan reverb:start

# Check logs
docker-compose logs app | grep Reverb
```

### **Issue: Database connection error**

```bash
# Check MySQL running
docker-compose ps mysql

# Restart MySQL
docker-compose restart mysql

# Check credentials in .env
cat .env | grep DB_
```

### **Issue: 500 Error**

```bash
# Check logs
docker-compose logs app

# Clear cache
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear

# Check permissions
chmod -R 775 storage bootstrap/cache
```

---

## 💰 Cost Tracking

### **Monitor Usage:**

```
1. DigitalOcean Dashboard
2. Click "Billing" (left menu)
3. See:
   - Current balance: $200.00
   - Month-to-date usage: $X.XX
   - Projected month-end: $Y.YY

Estimated usage:
- $12 droplet = $0.018/hour
- 24/7 running = $12/month
- $200 credit = 16+ months value
- Use 3 months = still have $164 credit!
```

---

## 🎯 Next Steps

### **Now that you're live:**

1. **Share with users:**
   - Send URL to apartment residents
   - Social media announcement
   - QR code posters

2. **Monitor:**
   - Check logs daily
   - Watch for errors
   - Monitor performance

3. **Backup:**
   - Database weekly
   - Keep backups safe

4. **Scale when needed:**
   - If > 100 users: Upgrade to $24 plan
   - If slow: Add more resources
   - Easy to upgrade in DigitalOcean!

---

## 📞 Support

### **Need Help?**

**DigitalOcean:**
- Docs: https://docs.digitalocean.com/
- Community: https://www.digitalocean.com/community
- Support: Ticket system in dashboard

**D'house Waffle:**
- Check: `PRE_PRODUCTION_TESTING.md`
- Check: `PRODUCTION_DEPLOYMENT_GUIDE.md`
- Check logs in server

---

## ✅ Final Checklist

Before announcing launch:

- [ ] Website accessible via domain
- [ ] HTTPS working (green padlock)
- [ ] Can place test order
- [ ] Real-time notifications working
- [ ] Owner can manage orders
- [ ] Sales reports working
- [ ] Excel export working
- [ ] Mobile responsive
- [ ] All payment methods enabled
- [ ] Backup plan in place
- [ ] Monitoring enabled
- [ ] Domain registered
- [ ] SSL certificate valid
- [ ] Firewall configured
- [ ] Performance acceptable

---

## 🎊 CONGRATULATIONS!

**D'house Waffle is now LIVE on the internet!** 🚀

**Your achievement:**
- Professional hosting ✅
- Production-ready system ✅
- Real-time notifications ✅
- Free for 3 months ✅
- Scalable infrastructure ✅

**Total setup time:** ~2 hours
**Cost for first 3 months:** RM 0 (FREE!)
**Monthly cost after:** RM 57 only

---

**Sekarang dah LIVE! Boleh share dengan customer dan start business! 🎉🧇✨**

**URL:** https://your-domain.com (or http://your-droplet-ip)


