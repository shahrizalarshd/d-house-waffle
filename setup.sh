#!/bin/bash

echo "==================================="
echo "D'house Waffle - Setup Script"
echo "==================================="
echo ""

# Check if .env exists
if [ ! -f .env ]; then
    echo "Creating .env file..."
    cp .env.example .env
    echo "✓ .env file created"
else
    echo "✓ .env file already exists"
fi

echo ""
echo "Installing Composer dependencies..."
composer install

echo ""
echo "Generating application key..."
php artisan key:generate

echo ""
echo "Running database migrations..."
read -p "This will create/reset the database. Continue? (y/n) " -n 1 -r
echo ""
if [[ $REPLY =~ ^[Yy]$ ]]
then
    php artisan migrate:fresh --seed
    echo "✓ Database setup complete"
fi

echo ""
echo "Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear

echo ""
echo "==================================="
echo "Setup Complete!"
echo "==================================="
echo ""
echo "Default Credentials:"
echo "-----------------------------------"
echo "Super Admin:"
echo "  Email: super@admin.com"
echo "  Password: password"
echo ""
echo "Apartment Admin:"
echo "  Email: admin@apartment.com"
echo "  Password: password"
echo ""
echo "Seller:"
echo "  Email: seller@test.com"
echo "  Password: password"
echo ""
echo "Buyer:"
echo "  Email: buyer@test.com"
echo "  Password: password"
echo "==================================="
echo ""
echo "Start development server:"
echo "  php artisan serve"
echo ""
echo "Then visit: http://localhost:8000"
echo ""

