<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 20)->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_name');
            $table->string('customer_email')->nullable();
            $table->string('customer_contact', 40)->nullable();
            $table->string('branch', 40)->default('kotapark');

            // dine_in | take_out - take_out carries a per-cup surcharge.
            $table->string('service_type', 20)->default('dine_in');

            // pending -> preparing -> ready -> completed, or cancelled.
            $table->string('status', 20)->default('pending');

            $table->unsignedInteger('cup_count')->default(0);
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('takeout_fee', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);

            // cash | gcash | paymaya - recorded at the counter, not a gateway.
            $table->string('payment_method', 20)->nullable();
            $table->string('payment_status', 20)->default('unpaid');
            $table->foreignId('paid_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('paid_at')->nullable();

            $table->timestamp('ready_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            // web | android | pos
            $table->string('source', 20)->default('web');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['branch', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('reservations');
    }
};
