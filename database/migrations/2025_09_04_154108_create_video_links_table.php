<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('video_links', function (Blueprint $table) {
            $table->id();
            $table->string('telegram_user_id')->index();
            $table->string('telegram_chat_id')->index();
            $table->unsignedBigInteger('telegram_message_id')->index();
            $table->text('original_url');
            $table->string('slug')->unique();
            $table->string('new_url')->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_links');
    }
};
