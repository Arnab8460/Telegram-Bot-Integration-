<?php

namespace App\Services;

use App\Models\VideoLink;
use Illuminate\Support\Str;

class LinkProcessorService
{
    private string $teraboxPattern = '~https?://(?:www\.)?terabox\.com/s/[^\s]+~i';
    private string $anyUrlPattern  = '~https?://[^\s]+~i';

    public function processMessage(string $text, string $chatId, string $userId, int $messageId, string $appUrl): array
    {
        preg_match_all($this->anyUrlPattern, $text, $allUrls);
        preg_match_all($this->teraboxPattern, $text, $tbUrls);

        $all = array_unique($allUrls[0] ?? []);
        $tb  = array_unique($tbUrls[0] ?? []);

        if (empty($all) && empty($tb)) {
            return [ 'ok' => false, 'error' => 'Only Terabox video links are allowed.' ];
        }

        $nonTb = array_diff($all, $tb);
        if (!empty($nonTb)) {
            return [ 'ok' => false, 'error' => 'Only Terabox video links are allowed.' ];
        }

        if (empty($tb)) {
            return [ 'ok' => false, 'error' => 'Only Terabox video links are allowed.' ];
        }

        $replacements = [];
        foreach ($tb as $original) {
            $slug = $this->uniqueSlug();
            $newUrl = rtrim($appUrl, '/') . '/video/' . $slug;

            VideoLink::create([
                'telegram_user_id'    => $userId,
                'telegram_chat_id'    => $chatId,
                'telegram_message_id' => $messageId,
                'original_url'        => $original,
                'slug'                => $slug,
                'new_url'             => $newUrl,
            ]);

            $replacements[$original] = $newUrl;
        }

        $replyText = $text;
        foreach ($replacements as $from => $to) {
            $replyText = str_replace($from, $to, $replyText);
        }

        return [ 'ok' => true, 'text' => $replyText ];
    }

    private function uniqueSlug(): string
    {
        do {
            $slug = Str::lower(Str::random(10));
        } while (VideoLink::where('slug', $slug)->exists());

        return $slug;
    }
}
