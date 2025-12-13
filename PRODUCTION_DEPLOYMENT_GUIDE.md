# 🚀 Production Deployment Guide

## 📋 Pre-Deployment Checklist

### **1. Code Preparation**
- [ ] All features tested
- [ ] No console errors
- [ ] Linter checks passed
- [ ] Code committed to Git
- [ ] Version tagged (v1.0)

### **2. Environment Setup**
- [ ] Production server ready
- [ ] PHP 8.2+ installed
- [ ] MySQL 8.0+ installed
- [ ] Composer installed
- [ ] Node.js & NPM installed
- [ ] SSL certificate obtained

### **3. Database**
- [ ] Backup current data
- [ ] Database created
- [ ] User privileges set
- [ ] Connection tested

### **4. Domain & DNS**
- [ ] Domain purchased
- [ ] DNS configured
- [ ] A record points to server
- [ ] SSL cert installed

---

## 🔧 Production .env Configuration

```env
APP_NAME="D'house Waffle"
APP_ENV=production
APP_KEY=base64:YOUR_KEY_HERE
APP_DEBUG=false
APP_TIMEZONE=Asia/Kuala_Lumpur
APP_URL=https://your-domain.com
APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

# Database
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=dhouse_waffle_prod
DB_USERNAME=your_db_user
DB_PASSWORD=your_secure_password

# Broadcasting (Reverb)
BROADCAST_CONNECTION=reverb

REVERB_APP_ID=dhouse-waffle-prod
REVERB_APP_KEY=your-production-key
REVERB_APP_SECRET=your-production-secret
REVERB_HOST=your-domain.com
REVERB_PORT=443
REVERB_SCHEME=https

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"

# Cache & Sessions
CACHE_STORE=redis
QUEUE_CONNECTION=database
SESSION_DRIVER=database
SESSION_LIFETIME=120

# Mail (if needed)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@your-domain.com"
MAIL_FROM_NAME="${APP_NAME}"

# Logging
LOG_CHANNEL=daily
LOG_LEVEL=error
```

---

## 📦 Deployment Steps

### **Step 1: Upload Code**
```bash
# Clone repository
cd /var/www
git clone https://github.com/your-repo/dhouse-waffle.git
cd dhouse-waffle

# Or upload via FTP/SFTP
```

### **Step 2: Install Dependencies**
```bash
# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Install Node dependencies
npm install

# Build assets
npm run build
```

### **Step 3: Configure Environment**
```bash
# Copy .env
cp .env.example .env

# Edit .env with production values
nano .env

# Generate app key
php artisan key:generate

# Create storage link
php artisan storage:link
```

### **Step 4: Set Permissions**
```bash
# Set ownership
sudo chown -R www-data:www-data /var/www/dhouse-waffle

# Set permissions
sudo chmod -R 755 /var/www/dhouse-waffle
sudo chmod -R 775 /var/www/dhouse-waffle/storage
sudo chmod -R 775 /var/www/dhouse-waffle/bootstrap/cache
```

### **Step 5: Database Setup**
```bash
# Run migrations
php artisan migrate --force

# Seed database
php artisan db:seed --force

# Or fresh install
php artisan migrate:fresh --seed --force
```

### **Step 6: Cache & Optimize**
```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize
php artisan optimize
```

---

## 🌐 Nginx Configuration

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name your-domain.com www.your-domain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name your-domain.com www.your-domain.com;
    root /var/www/dhouse-waffle/public;

    index index.php;

    charset utf-8;

    # SSL Configuration
    ssl_certificate /path/to/ssl/cert.pem;
    ssl_certificate_key /path/to/ssl/key.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;

    # Logs
    access_log /var/log/nginx/dhouse-access.log;
    error_log /var/log/nginx/dhouse-error.log;

    # Laravel location
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP-FPM
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
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

    # Deny access to hidden files
    location ~ /\. {
        deny all;
    }

    # Disable access to storage
    location ~* ^/storage/(.*)$ {
        try_files /storage/$1 =404;
    }
}
```

---

## 🔄 Supervisor Configuration (Reverb)

```ini
[program:reverb]
process_name=%(program_name)s
command=php /var/www/dhouse-waffle/artisan reverb:start
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/dhouse-waffle/storage/logs/reverb.log
stopwaitsecs=3600
```

**Install & Start:**
```bash
# Copy config
sudo cp reverb.conf /etc/supervisor/conf.d/

# Reload supervisor
sudo supervisorctl reread
sudo supervisorctl update

# Start Reverb
sudo supervisorctl start reverb

# Check status
sudo supervisorctl status reverb
```

---

## 🔐 Security Hardening

### **1. Firewall**
```bash
# Allow HTTP/HTTPS
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp

# Allow SSH (if needed)
sudo ufw allow 22/tcp

# Enable firewall
sudo ufw enable
```

### **2. Disable Debug Mode**
```env
APP_DEBUG=false
```

### **3. Hide Server Info**
```nginx
# In nginx.conf
server_tokens off;
```

### **4. Rate Limiting**
Laravel rate limiting already configured in routes.

### **5. HTTPS Only**
Force HTTPS redirect (see Nginx config above).

---

## 📊 Monitoring Setup

### **1. Laravel Logs**
```bash
# Check logs
tail -f /var/www/dhouse-waffle/storage/logs/laravel.log

# Reverb logs
tail -f /var/www/dhouse-waffle/storage/logs/reverb.log
```

### **2. Server Monitoring**
```bash
# Install monitoring tools
sudo apt install htop iotop

# Monitor resources
htop
```

### **3. Uptime Monitoring**
Use services like:
- UptimeRobot (free)
- Pingdom
- StatusCake

### **4. Error Tracking (Optional)**
- Sentry
- Bugsnag
- Rollbar

---

## 🔄 Backup Strategy

### **1. Database Backup**
```bash
# Daily backup script
#!/bin/bash
BACKUP_DIR="/var/backups/mysql"
DATE=$(date +%Y%m%d_%H%M%S)
DB_NAME="dhouse_waffle_prod"

mkdir -p $BACKUP_DIR

mysqldump -u root -p $DB_NAME | gzip > $BACKUP_DIR/backup_$DATE.sql.gz

# Keep only last 7 days
find $BACKUP_DIR -type f -mtime +7 -delete
```

**Cron Job:**
```bash
# Edit crontab
crontab -e

# Add daily backup at 3 AM
0 3 * * * /path/to/backup-script.sh
```

### **2. File Backup**
```bash
# Backup uploads
rsync -av /var/www/dhouse-waffle/storage/app/public/ /var/backups/uploads/
```

---

## 🧪 Post-Deployment Testing

### **Critical Paths to Test:**
1. [ ] Homepage loads
2. [ ] Login works (all roles)
3. [ ] Customer can place order
4. [ ] Owner can see order
5. [ ] Real-time notification works
6. [ ] Payment methods work
7. [ ] Sales report downloads

### **Quick Test Script:**
```bash
# Test database connection
php artisan tinker --execute="echo \App\Models\User::count();"

# Test cache
php artisan tinker --execute="Cache::put('test', 'value', 60);"

# Check routes
php artisan route:list

# Check jobs/queue (if used)
php artisan queue:work --once
```

---

## 📱 Mobile App Considerations

### **If Building Mobile App:**
1. **API Endpoints:** Already available (JSON responses)
2. **Authentication:** Can use Laravel Sanctum
3. **Real-time:** Reverb supports mobile WebSocket
4. **Push Notifications:** Need FCM/APNs integration

---

## 🎯 Go-Live Checklist

### **Final Checks:**
- [ ] .env configured for production
- [ ] SSL certificate active
- [ ] Database seeded
- [ ] Reverb running (via Supervisor)
- [ ] Nginx configured
- [ ] Logs writable
- [ ] Backups scheduled
- [ ] All tests passed
- [ ] Documentation complete
- [ ] Team trained

### **Go-Live Steps:**
1. [ ] Deploy code
2. [ ] Run migrations
3. [ ] Seed initial data
4. [ ] Start services (Nginx, Reverb)
5. [ ] Test critical paths
6. [ ] Monitor logs for 1 hour
7. [ ] Announce launch

---

## 📞 Support & Maintenance

### **Regular Tasks:**
- **Daily:** Check logs for errors
- **Weekly:** Review backups
- **Monthly:** Update dependencies
- **Quarterly:** Security audit

### **Common Issues:**

#### **Issue: Reverb not connecting**
```bash
# Check Reverb status
sudo supervisorctl status reverb

# Restart
sudo supervisorctl restart reverb

# Check logs
tail -f storage/logs/reverb.log
```

#### **Issue: 500 Error**
```bash
# Check Laravel logs
tail -f storage/logs/laravel.log

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

#### **Issue: Slow performance**
```bash
# Enable OPcache
sudo apt install php8.2-opcache

# Optimize autoloader
composer dump-autoload --optimize

# Cache everything
php artisan optimize
```

---

## 🎉 Launch Announcement

### **Social Media Template:**
```
🎉 Introducing D'house Waffle! 🧇

Order delicious waffles online with:
✅ Real-time notifications
✅ Multiple payment options
✅ Easy pickup system

Visit: https://your-domain.com
Download app: [Coming Soon]

#DhouseWaffle #FoodDelivery #Waffles
```

### **User Onboarding:**
1. Create tutorial videos
2. Add help tooltips
3. Provide customer support contact
4. FAQ page

---

**Status:** ✅ **Ready for Production**  
**Deployment Time:** ~2 hours  
**Go-Live:** After testing complete

---

**Semua dah ready untuk production! Just follow checklist ni step by step!** 🚀✨

