<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TelegramDeleteWebhook extends Command
{
    protected $signature = 'telegram:delete-webhook';
    protected $description = 'Delete Telegram webhook';

    public function handle(): int
    {
        $token = config('services.telegram.bot_token');
        $resp = Http::asForm()->post("https://api.telegram.org/bot{$token}/deleteWebhook");

        $this->info($resp->body());
        return $resp->ok() ? self::SUCCESS : self::FAILURE;
    }
}
