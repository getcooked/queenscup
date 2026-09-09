<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Chat history, kept per account.
     *
     * Only signed-in customers get a stored conversation, so it follows them
     * from a phone to a laptop. A visitor on the landing page still gets the
     * assistant, but nothing about that chat is written down.
     */
    public function up(): void
    {
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // 'customer' for what the person typed, 'bot' for the reply.
            $table->string('author', 16);
            $table->text('body');

            // Quick replies offered alongside a bot message, so reopening the
            // window restores the buttons and not just the text.
            $table->json('quick_replies')->nullable();

            $table->timestamps();

            // Every read is "this person's conversation, oldest first".
            $table->index(['user_id', 'id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};
