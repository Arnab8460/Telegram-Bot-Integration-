<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TelegramSetWebhook extends Command
{
    protected $signature = 'telegram:set-webhook {--drop : Drop pending updates}';
    protected $description = 'Set Telegram webhook to APP_URL/api/telegram/webhook';

    public function handle(): int
    {
        $token = config('services.telegram.bot_token');
        $secret = config('services.telegram.webhook_secret');
        $url = rtrim(config('app.url'), '/') . '/api/telegram/webhook';

        $resp = Http::asForm()->post("https://api.telegram.org/bot{$token}/setWebhook", [
            'url' => $url,
            'secret_token' => $secret,
            'allow_updates' => json_encode(['message']),
            'drop_pending_updates' => $this->option('drop') ? 'true' : 'false',
        ]);

        $this->info($resp->body());
        return $resp->ok() ? self::SUCCESS : self::FAILURE;
    }
}
