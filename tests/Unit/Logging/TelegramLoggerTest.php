<?php

namespace Tests\Unit\Logging;

use App\Logging\TelegramLogger;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Monolog\Level;
use Monolog\LogRecord;
use Tests\TestCase;

class TelegramLoggerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Fake HTTP requests
        Http::fake([
            'api.telegram.org/*' => Http::response(['ok' => true], 200),
        ]);
    }

    public function test_telegram_logger_creates_logger_instance(): void
    {
        $telegramLogger = new TelegramLogger();
        $logger = $telegramLogger(['level' => 'error']);

        $this->assertInstanceOf(\Monolog\Logger::class, $logger);
        $this->assertEquals('telegram', $logger->getName());
    }

    public function test_telegram_handler_does_not_send_when_disabled(): void
    {
        // Ensure telegram logging is disabled
        putenv('LOG_TELEGRAM_ERRORS=false');
        putenv('TELEGRAM_BOT_TOKEN=test-token');
        putenv('TELEGRAM_CHAT_ID=123456');

        $telegramLogger = new TelegramLogger();
        $logger = $telegramLogger(['level' => 'error']);

        // Log an error
        $logger->error('Test error message');

        // No HTTP requests should be made
        Http::assertNothingSent();

        // Clean up
        putenv('LOG_TELEGRAM_ERRORS');
        putenv('TELEGRAM_BOT_TOKEN');
        putenv('TELEGRAM_CHAT_ID');
    }

    public function test_telegram_handler_does_not_send_when_token_missing(): void
    {
        putenv('LOG_TELEGRAM_ERRORS=true');
        putenv('TELEGRAM_BOT_TOKEN=');
        putenv('TELEGRAM_CHAT_ID=123456');

        $telegramLogger = new TelegramLogger();
        $logger = $telegramLogger(['level' => 'error']);

        $logger->error('Test error message');

        Http::assertNothingSent();

        // Clean up
        putenv('LOG_TELEGRAM_ERRORS');
        putenv('TELEGRAM_BOT_TOKEN');
        putenv('TELEGRAM_CHAT_ID');
    }

    public function test_telegram_handler_does_not_send_when_chat_id_missing(): void
    {
        putenv('LOG_TELEGRAM_ERRORS=true');
        putenv('TELEGRAM_BOT_TOKEN=test-token');
        putenv('TELEGRAM_CHAT_ID=');

        $telegramLogger = new TelegramLogger();
        $logger = $telegramLogger(['level' => 'error']);

        $logger->error('Test error message');

        Http::assertNothingSent();

        // Clean up
        putenv('LOG_TELEGRAM_ERRORS');
        putenv('TELEGRAM_BOT_TOKEN');
        putenv('TELEGRAM_CHAT_ID');
    }

    public function test_telegram_handler_sends_message_when_enabled(): void
    {
        putenv('LOG_TELEGRAM_ERRORS=true');
        putenv('TELEGRAM_BOT_TOKEN=test-bot-token');
        putenv('TELEGRAM_CHAT_ID=123456789');

        $telegramLogger = new TelegramLogger();
        $logger = $telegramLogger(['level' => 'error']);

        $logger->error('Test production error');

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'api.telegram.org/bottest-bot-token/sendMessage')
                && $request['chat_id'] === '123456789'
                && str_contains($request['text'], 'Test production error')
                && $request['parse_mode'] === 'Markdown';
        });

        // Clean up
        putenv('LOG_TELEGRAM_ERRORS');
        putenv('TELEGRAM_BOT_TOKEN');
        putenv('TELEGRAM_CHAT_ID');
    }

    public function test_telegram_handler_includes_exception_details(): void
    {
        putenv('LOG_TELEGRAM_ERRORS=true');
        putenv('TELEGRAM_BOT_TOKEN=test-bot-token');
        putenv('TELEGRAM_CHAT_ID=123456789');

        $telegramLogger = new TelegramLogger();
        $logger = $telegramLogger(['level' => 'error']);

        try {
            throw new \RuntimeException('Test exception message');
        } catch (\Exception $e) {
            $logger->error('An error occurred', ['exception' => $e]);
        }

        Http::assertSent(function ($request) {
            $text = $request['text'];
            // Check that exception info is included
            return str_contains($text, 'RuntimeException')
                && str_contains($text, 'Exception');
        });

        // Clean up
        putenv('LOG_TELEGRAM_ERRORS');
        putenv('TELEGRAM_BOT_TOKEN');
        putenv('TELEGRAM_CHAT_ID');
    }

    public function test_telegram_handler_includes_url_context(): void
    {
        putenv('LOG_TELEGRAM_ERRORS=true');
        putenv('TELEGRAM_BOT_TOKEN=test-bot-token');
        putenv('TELEGRAM_CHAT_ID=123456789');

        $telegramLogger = new TelegramLogger();
        $logger = $telegramLogger(['level' => 'error']);

        $logger->error('Page not found', ['url' => '/api/test/endpoint']);

        Http::assertSent(function ($request) {
            return str_contains($request['text'], '/api/test/endpoint');
        });

        // Clean up
        putenv('LOG_TELEGRAM_ERRORS');
        putenv('TELEGRAM_BOT_TOKEN');
        putenv('TELEGRAM_CHAT_ID');
    }

    public function test_telegram_handler_truncates_long_messages(): void
    {
        putenv('LOG_TELEGRAM_ERRORS=true');
        putenv('TELEGRAM_BOT_TOKEN=test-bot-token');
        putenv('TELEGRAM_CHAT_ID=123456789');

        $telegramLogger = new TelegramLogger();
        $logger = $telegramLogger(['level' => 'error']);

        // Create a very long message (the escapeMarkdown limits to 200 chars)
        $longMessage = str_repeat('A', 5000);
        $logger->error($longMessage);

        Http::assertSent(function ($request) {
            // Message content is limited by escapeMarkdown (200 chars) so total should be reasonable
            return strlen($request['text']) < 4100;
        });

        // Clean up
        putenv('LOG_TELEGRAM_ERRORS');
        putenv('TELEGRAM_BOT_TOKEN');
        putenv('TELEGRAM_CHAT_ID');
    }

    public function test_telegram_handler_handles_api_failure_gracefully(): void
    {
        Http::fake([
            'api.telegram.org/*' => Http::response(null, 500),
        ]);

        putenv('LOG_TELEGRAM_ERRORS=true');
        putenv('TELEGRAM_BOT_TOKEN=test-bot-token');
        putenv('TELEGRAM_CHAT_ID=123456789');

        $telegramLogger = new TelegramLogger();
        $logger = $telegramLogger(['level' => 'error']);

        // Should not throw exception even if API fails
        $logger->error('Test error');

        // Request was attempted
        Http::assertSentCount(1);

        // Clean up
        putenv('LOG_TELEGRAM_ERRORS');
        putenv('TELEGRAM_BOT_TOKEN');
        putenv('TELEGRAM_CHAT_ID');
    }

    public function test_telegram_message_includes_environment(): void
    {
        putenv('LOG_TELEGRAM_ERRORS=true');
        putenv('TELEGRAM_BOT_TOKEN=test-bot-token');
        putenv('TELEGRAM_CHAT_ID=123456789');

        $telegramLogger = new TelegramLogger();
        $logger = $telegramLogger(['level' => 'error']);

        $logger->error('Test error');

        Http::assertSent(function ($request) {
            return str_contains($request['text'], 'Environment:')
                && str_contains($request['text'], "D'house Waffle");
        });

        // Clean up
        putenv('LOG_TELEGRAM_ERRORS');
        putenv('TELEGRAM_BOT_TOKEN');
        putenv('TELEGRAM_CHAT_ID');
    }

    public function test_telegram_message_includes_log_level(): void
    {
        putenv('LOG_TELEGRAM_ERRORS=true');
        putenv('TELEGRAM_BOT_TOKEN=test-bot-token');
        putenv('TELEGRAM_CHAT_ID=123456789');

        $telegramLogger = new TelegramLogger();
        $logger = $telegramLogger(['level' => 'error']);

        $logger->critical('Critical error occurred');

        Http::assertSent(function ($request) {
            return str_contains($request['text'], 'Level:')
                && str_contains($request['text'], 'CRITICAL');
        });

        // Clean up
        putenv('LOG_TELEGRAM_ERRORS');
        putenv('TELEGRAM_BOT_TOKEN');
        putenv('TELEGRAM_CHAT_ID');
    }
}
