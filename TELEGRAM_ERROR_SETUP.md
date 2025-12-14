# Telegram Error Notifications Setup Guide

## ✅ Configuration Complete

Your Telegram error notifications are configured with:
- **Bot Token**: `8503654264:AAGXZ9RtpeLtFxaqnS08C5yfHZDyioZV9NI`
- **Chat ID**: `-1003599844107`
- **Channel**: D'House Waffle Log Error

---

## 🚀 Installation Steps (Run on Production Server)

```bash
# SSH to server
ssh root@152.42.208.154

# Navigate to project
cd /var/www/d-house-waffle

# Step 1: Copy TelegramLogger.php
# Create directory
mkdir -p app/Logging

# Create the logger file
nano app/Logging/TelegramLogger.php
```

**Copy the contents from your local file:**  
`/Users/shah/Laravel/dhouse-waffle/app/Logging/TelegramLogger.php`

Or use this command to create it directly:

```bash
cat > app/Logging/TelegramLogger.php << 'EOF'
<?php

namespace App\Logging;

use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\LogRecord;
use Illuminate\Support\Facades\Http;

class TelegramLogger
{
    public function __invoke(array $config): Logger
    {
        $logger = new Logger('telegram');
        $logger->pushHandler(new TelegramHandler($config['level'] ?? Logger::ERROR));
        return $logger;
    }
}

class TelegramHandler extends AbstractProcessingHandler
{
    protected function write(LogRecord $record): void
    {
        $token = env('TELEGRAM_BOT_TOKEN');
        $chatId = env('TELEGRAM_CHAT_ID');
        
        if (!$token || !$chatId || !env('LOG_TELEGRAM_ERRORS', false)) {
            return;
        }

        $message = "🚨 *D'house Waffle Production Error*\n\n";
        $message .= "*Environment:* " . config('app.env') . "\n";
        $message .= "*Level:* " . $record->level->getName() . "\n";
        $message .= "*Time:* " . $record->datetime->format('Y-m-d H:i:s') . "\n";
        $message .= "*Message:* `" . $this->escapeMarkdown($record->message) . "`\n\n";
        
        if (isset($record->context['exception'])) {
            $e = $record->context['exception'];
            if ($e instanceof \Throwable) {
                $message .= "*Exception:* " . get_class($e) . "\n";
                $message .= "*File:* `" . basename($e->getFile()) . ":" . $e->getLine() . "`\n";
                
                $trace = explode("\n", $e->getTraceAsString());
                $shortTrace = array_slice($trace, 0, 3);
                $message .= "*Stack Trace:*\n```\n" . implode("\n", $shortTrace) . "\n```";
            }
        }

        if (isset($record->context['url'])) {
            $message .= "\n*URL:* `" . $record->context['url'] . "`";
        }

        if (strlen($message) > 4000) {
            $message = substr($message, 0, 3900) . "\n\n... (truncated)";
        }

        try {
            Http::timeout(5)->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'Markdown',
            ]);
        } catch (\Exception $e) {
            error_log('Failed to send Telegram notification: ' . $e->getMessage());
        }
    }

    private function escapeMarkdown(string $text): string
    {
        $text = str_replace(['_', '*', '[', ']', '(', ')', '~', '`', '>', '#', '+', '-', '=', '|', '{', '}', '.', '!'], 
                          ['\_', '\*', '\[', '\]', '\(', '\)', '\~', '\`', '\>', '\#', '\+', '\-', '\=', '\|', '\{', '\}', '\.', '\!'], 
                          $text);
        return substr($text, 0, 200);
    }
}
EOF
```

### Step 2: Update .env

```bash
# Edit .env
nano .env

# Add these lines at the bottom:
```

```env
# Telegram Error Notifications
TELEGRAM_BOT_TOKEN=8503654264:AAGXZ9RtpeLtFxaqnS08C5yfHZDyioZV9NI
TELEGRAM_CHAT_ID=-1003599844107
LOG_TELEGRAM_ERRORS=true
```

Save: `Ctrl+O`, `Enter`, `Ctrl+X`

### Step 3: Update logging config

```bash
# Backup current config
cp config/logging.php config/logging.php.backup

# Edit config
nano config/logging.php
```

**Find line 57** (in 'stack' channel):
```php
'channels' => explode(',', (string) env('LOG_STACK', 'single')),
```

**Change to**:
```php
'channels' => explode(',', (string) env('LOG_STACK', 'single,telegram')),
```

**Find line 128** (before the closing `],`):
```php
        'emergency' => [
            'path' => storage_path('logs/laravel.log'),
        ],

    ],
```

**Add telegram channel before the closing `],`**:
```php
        'emergency' => [
            'path' => storage_path('logs/laravel.log'),
        ],

        'telegram' => [
            'driver' => 'custom',
            'via' => App\Logging\TelegramLogger::class,
            'level' => env('LOG_LEVEL', 'error'),
        ],

    ],
```

Save: `Ctrl+O`, `Enter`, `Ctrl+X`

### Step 4: Clear cache and restart

```bash
# Clear config cache
docker compose exec laravel.test php artisan config:clear
docker compose exec laravel.test php artisan config:cache

# Restart Laravel container
docker compose restart laravel.test

# Wait for restart
sleep 10
```

---

## 🧪 Test the Setup

### Test 1: Send test error via tinker

```bash
docker compose exec laravel.test php artisan tinker
```

```php
# In tinker:
Log::error('Test error from production server', [
    'test' => true,
    'url' => 'http://152.42.208.154/test'
]);

# You should receive Telegram notification!
exit
```

### Test 2: Trigger actual error

```bash
# Create test route that throws error
nano routes/web.php
```

Add at the bottom:
```php
Route::get('/test-error', function () {
    throw new \Exception('This is a test error for Telegram notifications!');
});
```

```bash
# Clear route cache
docker compose exec laravel.test php artisan route:clear
docker compose exec laravel.test php artisan route:cache

# Visit in browser:
# http://152.42.208.154/test-error

# Check Telegram - should receive notification!
```

### Test 3: Remove test route

```bash
nano routes/web.php
# Remove the test-error route
# Save and cache again
docker compose exec laravel.test php artisan route:cache
```

---

## 📱 What You'll Receive

Every error will send a Telegram message with:
- 🚨 Alert indicator
- Environment (production)
- Error level (ERROR, CRITICAL, etc.)
- Timestamp
- Error message
- Exception type
- File and line number
- First 3 lines of stack trace
- URL where error occurred

Example:
```
🚨 D'house Waffle Production Error

Environment: production
Level: ERROR
Time: 2025-12-14 04:30:00
Message: `SQLSTATE[HY000]: General error`

Exception: Illuminate\Database\QueryException
File: `AuthController.php:59`
Stack Trace:
```
#0 /var/www/html/vendor/laravel/framework...
#1 /var/www/html/vendor/laravel/framework...
#2 /var/www/html/app/Http/Controllers...
```

URL: `/register`
```

---

## ⚙️ Configuration Options

### Change notification level

In `.env`:
```env
# Only send ERROR and above (default)
LOG_LEVEL=error

# Send WARNING and above
LOG_LEVEL=warning

# Send INFO and above (verbose)
LOG_LEVEL=info
```

### Disable temporarily

```env
# Disable Telegram notifications
LOG_TELEGRAM_ERRORS=false
```

### Multiple channels

```env
# Send to multiple Telegram channels
TELEGRAM_CHAT_ID=-1003599844107,-1002222333444
```

---

## 🔧 Troubleshooting

### Not receiving notifications?

1. **Check bot token and chat ID**:
```bash
grep TELEGRAM .env
```

2. **Test bot connection**:
```bash
curl "https://api.telegram.org/bot8503654264:AAGXZ9RtpeLtFxaqnS08C5yfHZDyioZV9NI/getMe"
```

3. **Check Laravel logs**:
```bash
docker compose exec laravel.test tail -50 storage/logs/laravel.log
```

4. **Verify config cached**:
```bash
docker compose exec laravel.test php artisan config:show logging.channels.telegram
```

### Bot not in channel?

Make sure your bot is added to the channel "D'House Waffle Log Error" and has permission to send messages.

---

## 🎯 Best Practices

1. **Don't spam**: Use `LOG_LEVEL=error` to avoid too many notifications
2. **Monitor regularly**: Check Telegram daily for any issues
3. **Act quickly**: Respond to errors as soon as they appear
4. **Keep secure**: Don't share bot token publicly

---

## 📊 Advanced: Error Context

To add more context to errors, use:

```php
Log::error('Payment failed', [
    'order_id' => $order->id,
    'user_id' => $user->id,
    'amount' => $order->total,
    'payment_method' => $request->payment_method,
    'url' => request()->fullUrl(),
]);
```

---

## 🚀 Next Steps

After setup:
1. ✅ Test with tinker command
2. ✅ Test with /test-error route
3. ✅ Monitor real production errors
4. 📝 Document common errors and solutions
5. 🔔 Setup alerts for critical errors only

**Ready to catch all production errors in real-time!** 🎉


