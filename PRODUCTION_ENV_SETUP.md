# Production Environment Setup Guide

## 📋 Configuration Changes for Production

### Critical Changes Required:

1. **APP_ENV**: Changed to `production`
2. **APP_DEBUG**: Changed to `false` (NEVER use true in production)
3. **APP_KEY**: Must generate new key
4. **DB_PASSWORD**: Must change to secure password
5. **REVERB Keys**: Must generate secure keys
6. **APP_URL**: Update to your actual domain/IP
7. **LOG_LEVEL**: Changed to `info` (less verbose than debug)

---

## 🔧 Step-by-Step Setup

### 1. Copy Configuration File

```bash
# On your DigitalOcean droplet
cd /root/dhouse-waffle

# Copy the production env file
cp .env.production .env
```

### 2. Generate Application Key

```bash
# Generate Laravel application key
docker-compose exec laravel.test php artisan key:generate

# This will automatically update APP_KEY in .env
```

### 3. Update Database Password

```bash
# Edit .env file
nano .env

# Find this line:
DB_PASSWORD=CHANGE_TO_SECURE_PASSWORD

# Change to a strong password (example):
DB_PASSWORD=Wf@2024$ecur3P@ss!

# Save: Ctrl+O, Enter, Ctrl+X
```

### 4. Update APP_URL

```bash
# Edit .env file
nano .env

# Option A - Using IP address:
APP_URL=http://159.223.xxx.xxx

# Option B - Using domain (if you have one):
APP_URL=https://dhousewaffle.com

# Also update REVERB_HOST to match:
REVERB_HOST=159.223.xxx.xxx
# or
REVERB_HOST=dhousewaffle.com
```

### 5. Generate Reverb Keys

```bash
# Generate secure random strings for Reverb
# You can use online tools or this command:

# Generate APP_KEY (use output for REVERB_APP_KEY)
openssl rand -base64 32

# Generate SECRET (use output for REVERB_APP_SECRET)
openssl rand -base64 32

# Update in .env:
nano .env

# Replace:
REVERB_APP_KEY=your_generated_key_here
REVERB_APP_SECRET=your_generated_secret_here
```

### 6. Update MySQL Root Password (Docker)

Since you're using Docker Compose with Sail, you also need to update the MySQL container:

```bash
# Edit docker-compose.yml
nano docker-compose.yml

# Find the mysql service and update:
MYSQL_ROOT_PASSWORD: 'your_secure_password'
MYSQL_PASSWORD: 'your_secure_password'

# Save the file
```

### 7. Restart Services

```bash
# Stop all containers
docker-compose down

# Rebuild and start (to apply new passwords)
docker-compose up -d --force-recreate

# Wait for containers to start (30 seconds)
sleep 30

# Check status
docker-compose ps
```

### 8. Run Migrations and Setup

```bash
# Run database migrations
docker-compose exec laravel.test php artisan migrate --force

# Clear all caches
docker-compose exec laravel.test php artisan config:clear
docker-compose exec laravel.test php artisan cache:clear
docker-compose exec laravel.test php artisan view:clear
docker-compose exec laravel.test php artisan route:clear

# Cache configuration for production
docker-compose exec laravel.test php artisan config:cache
docker-compose exec laravel.test php artisan route:cache
docker-compose exec laravel.test php artisan view:cache
```

### 9. Start Queue Worker

```bash
# Start queue worker for background jobs
docker-compose exec -d laravel.test php artisan queue:work --daemon
```

### 10. Start Reverb WebSocket Server

```bash
# Start Reverb for real-time features
docker-compose exec -d laravel.test php artisan reverb:start
```

---

## ✅ Verification Checklist

After setup, verify these:

```bash
# 1. Check containers are running
docker-compose ps

# 2. Test database connection
docker-compose exec laravel.test php artisan migrate:status

# 3. Check application logs
docker-compose logs laravel.test --tail=50

# 4. Test website
curl http://your-droplet-ip

# 5. Check Reverb is running
docker-compose exec laravel.test ps aux | grep reverb
```

---

## 🔐 Security Checklist

- [ ] `APP_ENV=production` ✓
- [ ] `APP_DEBUG=false` ✓
- [ ] `APP_KEY` generated ✓
- [ ] `DB_PASSWORD` changed from default ✓
- [ ] `REVERB_APP_KEY` set to secure value ✓
- [ ] `REVERB_APP_SECRET` set to secure value ✓
- [ ] `APP_URL` set to actual domain/IP ✓
- [ ] `LOG_LEVEL=info` or `error` ✓
- [ ] MySQL password updated in docker-compose.yml ✓

---

## 📝 Important Production Settings Explained

### APP_DEBUG=false
- **Why**: Debug mode shows sensitive error details
- **Production**: Must be false to hide errors from users
- **Errors**: Will be logged to `storage/logs/laravel.log`

### LOG_LEVEL=info
- **Debug**: Very verbose, logs everything (development only)
- **Info**: Normal operations + important events (production)
- **Warning**: Warnings + errors only
- **Error**: Errors only

### DB_HOST=mysql
- **Why**: Using Docker Compose, containers communicate via service names
- **Value**: `mysql` is the service name in docker-compose.yml
- **Don't change** unless you modify docker-compose.yml

### Session & Cache = database
- **Why**: File-based storage doesn't work well with Docker
- **Database**: More reliable for production
- **Redis**: Would be even better (future upgrade)

---

## 🚨 Common Issues & Solutions

### Issue: "Application key not set"
```bash
# Solution: Generate key
docker-compose exec laravel.test php artisan key:generate
```

### Issue: "Could not connect to database"
```bash
# Solution 1: Check MySQL is running
docker-compose ps

# Solution 2: Restart MySQL
docker-compose restart mysql

# Solution 3: Wait 30 seconds and try again
sleep 30
```

### Issue: "Reverb connection failed"
```bash
# Solution 1: Check Reverb is running
docker-compose exec laravel.test ps aux | grep reverb

# Solution 2: Start Reverb
docker-compose exec -d laravel.test php artisan reverb:start

# Solution 3: Check firewall allows port 8080
sudo ufw allow 8080/tcp
```

### Issue: Changes not reflecting
```bash
# Solution: Clear all caches
docker-compose exec laravel.test php artisan config:clear
docker-compose exec laravel.test php artisan cache:clear
docker-compose exec laravel.test php artisan config:cache
```

---

## 🔄 Update Production Environment

When you need to update .env in the future:

```bash
# 1. Edit .env
nano .env

# 2. Save changes

# 3. Clear config cache
docker-compose exec laravel.test php artisan config:clear

# 4. Rebuild config cache
docker-compose exec laravel.test php artisan config:cache

# 5. Restart if needed
docker-compose restart laravel.test
```

---

## 📞 Next Steps

1. ✅ Setup production .env (this guide)
2. 📦 Deploy to DigitalOcean (see DIGITALOCEAN_SETUP_GUIDE.md)
3. 🔒 Setup SSL/HTTPS (see DIGITALOCEAN_SETUP_GUIDE.md - Step 7)
4. 🧪 Test all features (see PRE_PRODUCTION_TESTING.md)
5. 🚀 Go live!

---

## 📚 Related Documentation

- `DIGITALOCEAN_SETUP_GUIDE.md` - Full deployment guide
- `PRODUCTION_DEPLOYMENT_GUIDE.md` - Deployment process
- `PRE_PRODUCTION_TESTING.md` - Testing checklist
- `QUICK_START_GUIDE.md` - Quick reference

---

**Last Updated**: December 2025  
**Version**: 1.0


