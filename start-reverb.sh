#!/bin/bash

echo "🚀 Starting Laravel Reverb Server..."
echo ""
echo "📡 Real-time notifications will be active!"
echo "🔔 Owner will get instant alerts when orders placed"
echo ""
echo "Press Ctrl+C to stop"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

./vendor/bin/sail artisan reverb:start

