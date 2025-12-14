<?php

namespace App\Logging;

use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\LogRecord;
use Illuminate\Support\Facades\Http;

class TelegramLogger
{
    /**
     * Create a custom Monolog instance.
     */
    public function __invoke(array $config): Logger
    {
        $logger = new Logger('telegram');
        $logger->pushHandler(new TelegramHandler($config['level'] ?? Logger::ERROR));
        return $logger;
    }
}

class TelegramHandler extends AbstractProcessingHandler
{
    /**
     * Write the log record to Telegram.
     */
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
                
                // Add first 3 lines of stack trace
                $trace = explode("\n", $e->getTraceAsString());
                $shortTrace = array_slice($trace, 0, 3);
                $message .= "*Stack Trace:*\n```\n" . implode("\n", $shortTrace) . "\n```";
            }
        }

        // Add URL if available
        if (isset($record->context['url'])) {
            $message .= "\n*URL:* `" . $record->context['url'] . "`";
        }

        // Limit message length
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
            // Silently fail to avoid infinite loop
            error_log('Failed to send Telegram notification: ' . $e->getMessage());
        }
    }

    /**
     * Escape markdown special characters.
     */
    private function escapeMarkdown(string $text): string
    {
        $text = str_replace(['_', '*', '[', ']', '(', ')', '~', '`', '>', '#', '+', '-', '=', '|', '{', '}', '.', '!'], 
                          ['\_', '\*', '\[', '\]', '\(', '\)', '\~', '\`', '\>', '\#', '\+', '\-', '\=', '\|', '\{', '\}', '\.', '\!'], 
                          $text);
        return substr($text, 0, 200); // Limit length
    }
}


