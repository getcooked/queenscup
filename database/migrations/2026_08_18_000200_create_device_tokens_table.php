<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('device_tokens', function (Blueprint $table) {
            $table->id();

            // FCM registration tokens are long and have no documented maximum,
            // so the raw value is text and uniqueness is enforced on its hash.
            $table->text('token');
            $table->string('token_hash', 64)->unique();
            $table->string('platform', 20)->default('android');

            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // Lets a guest receive push for a reservation without an account.
            $table->string('reservation_reference', 20)->nullable()->index();

            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('device_tokens');
    }
};
