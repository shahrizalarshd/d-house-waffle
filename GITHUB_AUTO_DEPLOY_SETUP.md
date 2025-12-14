# 🚀 GitHub Auto-Deploy Setup Guide

## 📋 Apa Yang Akan Berlaku

Selepas setup ini:
1. Kau `git push` ke GitHub
2. GitHub Actions auto-trigger
3. SSH ke server DigitalOcean
4. Pull code terbaru
5. Clear cache & run migrations
6. Restart services

**Hasilnya:** Code auto-deploy dalam ~1-2 minit selepas push!

---

## 🔧 PART 1: Setup SSH Key untuk GitHub Actions

### Step 1.1: Generate SSH Key Khas untuk Deploy

Di komputer kau (local), run:

```bash
# Generate SSH key baru untuk deploy
ssh-keygen -t ed25519 -C "github-actions-deploy" -f ~/.ssh/github_deploy_key

# Jangan letak passphrase (tekan Enter je)
```

Sekarang ada 2 files:
- `~/.ssh/github_deploy_key` - Private key (RAHSIA!)
- `~/.ssh/github_deploy_key.pub` - Public key

### Step 1.2: Add Public Key ke Server DigitalOcean

```bash
# Copy public key
cat ~/.ssh/github_deploy_key.pub
```

Copy output tu, kemudian:

```bash
# SSH ke server
ssh root@152.42.208.154

# Add public key ke authorized_keys
echo "PASTE_PUBLIC_KEY_SINI" >> ~/.ssh/authorized_keys

# Exit
exit
```

### Step 1.3: Test Connection

```bash
# Test SSH dengan key baru
ssh -i ~/.ssh/github_deploy_key root@152.42.208.154

# Kalau berjaya, akan masuk server tanpa password
```

---

## 🔐 PART 2: Add Secrets ke GitHub Repository

### Step 2.1: Buka GitHub Repo Settings

1. Pergi ke repo kau di GitHub
2. Click **Settings** (tab paling kanan)
3. Left sidebar: **Secrets and variables** → **Actions**
4. Click **New repository secret**

### Step 2.2: Add SECRET #1 - SSH Private Key

```
Name: SSH_PRIVATE_KEY
Value: [Paste SELURUH isi file private key]
```

Untuk dapatkan private key:
```bash
cat ~/.ssh/github_deploy_key
```

Copy **SEMUA** termasuk:
```
-----BEGIN OPENSSH PRIVATE KEY-----
...semua content...
-----END OPENSSH PRIVATE KEY-----
```

### Step 2.3: Add SECRET #2 - Server IP

```
Name: SERVER_IP
Value: 152.42.208.154
```

---

## ✅ PART 3: Test Auto-Deploy

### Step 3.1: Push ke GitHub

```bash
cd /Users/shah/Laravel/dhouse-waffle

# Add changes
git add .

# Commit
git commit -m "Setup GitHub Actions auto-deploy"

# Push
git push origin main
# atau
git push origin master
```

### Step 3.2: Check GitHub Actions

1. Pergi ke repo kau di GitHub
2. Click **Actions** tab
3. Nampak workflow "Deploy to Production" running
4. Click untuk tengok progress

### Step 3.3: Check Server

```bash
# SSH ke server
ssh root@152.42.208.154

# Check deployment berjaya
cd /var/www/d-house-waffle
git log -1

# Patut nampak commit terbaru kau
```

---

## 🔄 Workflow: Macam Mana Ia Berfungsi

```
┌─────────────────┐
│  Local Machine  │
│                 │
│  git push       │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│     GitHub      │
│                 │
│ Trigger Action  │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ GitHub Actions  │
│    Runner       │
│                 │
│ SSH ke server   │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  DigitalOcean   │
│     Server      │
│                 │
│ git pull        │
│ clear cache     │
│ migrate         │
│ restart         │
└─────────────────┘
```

---

## 📝 Nota Penting

### Branch yang Trigger Deploy:

Default setup untuk `main` atau `master`. Kalau nak tukar:

```yaml
# .github/workflows/deploy.yml
on:
  push:
    branches:
      - main      # ← tukar branch name
      - master
```

### Kalau Nak Deploy Manual:

```bash
# Dari local machine, run deploy.sh
./deploy.sh
```

### Check Deploy Log:

1. GitHub → Actions tab
2. Click workflow run
3. Click job "Deploy to DigitalOcean"
4. Expand steps untuk tengok output

---

## 🛠️ Troubleshooting

### Error: Permission denied (publickey)

**Punca:** SSH key tak setup betul

**Penyelesaian:**
1. Check public key ada dalam server:
   ```bash
   ssh root@152.42.208.154
   cat ~/.ssh/authorized_keys
   ```
2. Pastikan private key dalam GitHub secrets betul
3. Check private key include header & footer:
   ```
   -----BEGIN OPENSSH PRIVATE KEY-----
   ...
   -----END OPENSSH PRIVATE KEY-----
   ```

### Error: Host key verification failed

**Punca:** Server IP berubah atau tak kenal

**Penyelesaian:**
Workflow dah handle ni dengan `ssh-keyscan`. Kalau masih error, add step ini:
```yaml
- name: Setup SSH
  run: |
    mkdir -p ~/.ssh
    ssh-keyscan -H ${{ secrets.SERVER_IP }} >> ~/.ssh/known_hosts
```

### Error: Docker command not found

**Punca:** PATH tak set dalam SSH session

**Penyelesaian:**
Tambah dalam SSH command:
```bash
export PATH=$PATH:/usr/local/bin
```

### Deploy Berjaya tapi Website Tak Update

**Punca:** Cache atau browser cache

**Penyelesaian:**
1. Clear browser cache (Ctrl+Shift+R)
2. Atau run di server:
   ```bash
   docker compose exec laravel.test php artisan cache:clear
   docker compose exec laravel.test php artisan view:clear
   ```

---

## 🎯 Tips Tambahan

### 1. Add Slack/Telegram Notification

Kalau nak dapat notification bila deploy:

```yaml
# Tambah step di hujung deploy.yml
- name: Send notification
  run: |
    curl -X POST "https://api.telegram.org/bot${{ secrets.TELEGRAM_BOT_TOKEN }}/sendMessage" \
      -d "chat_id=${{ secrets.TELEGRAM_CHAT_ID }}" \
      -d "text=✅ D'house Waffle deployed successfully!"
```

### 2. Add Tests Sebelum Deploy

```yaml
jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - name: Run tests
        run: |
          # Setup dan run tests
          
  deploy:
    needs: test  # ← Deploy hanya kalau test pass
    runs-on: ubuntu-latest
    # ... rest of deploy steps
```

### 3. Deploy Hanya Bila Merge ke Main

```yaml
on:
  push:
    branches:
      - main
  # Remove master kalau tak guna
```

---

## ✅ Checklist Sebelum Push

- [ ] SSH key generated
- [ ] Public key added to server
- [ ] `SSH_PRIVATE_KEY` secret added to GitHub
- [ ] `SERVER_IP` secret added to GitHub
- [ ] Test SSH connection dari local
- [ ] Workflow file committed

---

## 🎉 Selesai!

Sekarang setiap kali kau:

```bash
git add .
git commit -m "Update feature XYZ"
git push
```

Website production akan auto-update dalam 1-2 minit! 🚀

**Tak perlu:**
- SSH manual ke server
- Run deploy.sh
- Ingat arahan deploy

**GitHub Actions handle semua!** 🎊
