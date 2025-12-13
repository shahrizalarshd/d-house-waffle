#!/bin/bash

# Add/Update Reverb settings in .env
echo "Updating .env for Reverb..."

# Backup .env
cp .env .env.backup

# Update BROADCAST_CONNECTION
sed -i '' 's/BROADCAST_CONNECTION=.*/BROADCAST_CONNECTION=reverb/' .env || echo "BROADCAST_CONNECTION=reverb" >> .env

# Add Reverb settings if not exist
if ! grep -q "REVERB_APP_ID" .env; then
    echo "" >> .env
    echo "# Reverb Settings" >> .env
    echo "REVERB_APP_ID=dhouse-waffle" >> .env
    echo "REVERB_APP_KEY=local-key" >> .env
    echo "REVERB_APP_SECRET=local-secret" >> .env
    echo "REVERB_HOST=0.0.0.0" >> .env
    echo "REVERB_PORT=8080" >> .env
    echo "REVERB_SCHEME=http" >> .env
    echo "" >> .env
    echo "VITE_REVERB_APP_KEY=\"\${REVERB_APP_KEY}\"" >> .env
    echo "VITE_REVERB_HOST=\"\${REVERB_HOST}\"" >> .env
    echo "VITE_REVERB_PORT=\"\${REVERB_PORT}\"" >> .env
    echo "VITE_REVERB_SCHEME=\"\${REVERB_SCHEME}\"" >> .env
fi

echo "✅ .env updated!"
echo "Backup saved as .env.backup"
