<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\VideoController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/video/{slug}', [VideoController::class, 'show'])->name('video.show');

// Telegram getUpdates (inbox check)
Route::get('/telegram/test', function () {
    $token = config('services.telegram.bot_token');
    $updates = Http::get("https://api.telegram.org/bot{$token}/getUpdates")->json();

    return $updates;
});

// Telegram send message
Route::get('/telegram/send/{text}', function ($text) {
    $token = config('services.telegram.bot_token');

    $chatId = "855230345";

    $resp = Http::asForm()->post("https://api.telegram.org/bot{$token}/sendMessage", [
        'chat_id' => $chatId,
        'text'    => $text,
    ]);

    return $resp->json();
});
