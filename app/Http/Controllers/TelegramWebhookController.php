<?php

namespace App\Http\Controllers;

use App\Services\LinkProcessorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TelegramWebhookController extends Controller
{
    

    public function __invoke(Request $request)
{
    $expected = config('services.telegram.webhook_secret');
    if ($expected && $request->header('X-Telegram-Bot-Api-Secret-Token') !== $expected) {
        abort(403, 'Invalid webhook secret');
    }

    $update = $request->all();
    $message = $update['message']['text'] ?? null;
    $chatId  = $update['message']['chat']['id'] ?? null;

    if (!$message || !$chatId) {
        return response()->json(['status' => 'ignored']);
    }

    preg_match_all('/https?:\/\/www\.terabox\.com\/s\/[A-Za-z0-9]+/', $message, $matches);
    $links = $matches[0];

    if (empty($links)) {
        $this->sendMessage($chatId, "Only Terabox video links are allowed.");
        return response()->json(['status' => 'invalid']);
    }

    $reply = $message;

    foreach ($links as $link) {
        $video = \App\Models\VideoLink::create([
            'original_url' => $link,
            'slug' => \Str::random(10),
        ]);

        $newUrl = config('app.url') . "/video/" . $video->slug;
        $reply = str_replace($link, $newUrl, $reply);
    }

    $this->sendMessage($chatId, $reply);

    return response()->json(['status' => 'ok']);
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
