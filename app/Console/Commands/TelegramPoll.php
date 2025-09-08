<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\VideoLink;
use Illuminate\Support\Str;

class TelegramPoll extends Command
{
    protected $signature = 'telegram:poll';
    protected $description = 'Poll Telegram API for new updates and process messages';

    public function handle()
    {
        $token = config('services.telegram.bot_token');
        $url = "https://api.telegram.org/bot{$token}/getUpdates";

        $updates = Http::get($url)->json();

        if (!isset($updates['result'])) {
            $this->error('No updates found');
            return self::FAILURE;
        }

        foreach ($updates['result'] as $update) {
            $message = $update['message']['text'] ?? null;
            $chatId  = $update['message']['chat']['id'] ?? null;
            $messageId = $update['message']['message_id'] ?? null;

            if (!$message || !$chatId) {
                continue;
            }

            // preg_match_all('/https?:\/\/www\.terabox\.com\/s\/[A-Za-z0-9]+/', $message, $matches);
            // $links = $matches[0];
            preg_match_all(
                '/https?:\/\/(?:www\.)?(?:terabox|1024tera)\.com\/[^\s]+/i',
                $message,
                $matches
            );

            $links = $matches[0];  

            if (empty($links)) {
                $this->sendMessage($chatId, "Only Terabox video links are allowed.", $messageId);
                continue;
            }

            $reply = $message;

            foreach ($links as $link) {
                $slug = Str::random(10);
                $video = VideoLink::create([
                    'telegram_user_id'   => $update['message']['from']['id'] ?? null,
                    'telegram_chat_id'   => $chatId,
                    'telegram_message_id'=> $messageId,
                    'original_url'       => $link,
                    'slug'               => $slug,
                    'new_url'            => config('app.url') . "/video/" . $slug,
                ]);

                $newUrl = $video->new_url;
                $reply = str_replace($link, $newUrl, $reply);
            }

            $this->sendMessage($chatId, $reply, $messageId);
        }

        Http::get("https://api.telegram.org/bot{$token}/getUpdates", [
            'offset' => end($updates['result'])['update_id'] + 1
        ]);

        $this->info("Processed " . count($updates['result']) . " updates.");
        return self::SUCCESS;
    }

    private function sendMessage($chatId, $text, $replyTo = null)
    {
        $token = config('services.telegram.bot_token');
        $endpoint = "https://api.telegram.org/bot{$token}/sendMessage";

        Http::asForm()->post($endpoint, [
            'chat_id' => $chatId,
            'text'    => $text,
            'reply_to_message_id' => $replyTo,
        ]);
    }
}
