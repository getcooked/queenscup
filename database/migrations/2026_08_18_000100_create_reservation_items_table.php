<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reservation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained()->cascadeOnDelete();

            // Kept nullable so a reservation survives a product being removed.
            $table->foreignId('inventory_id')->nullable()->constrained()->nullOnDelete();

            // Name and price are copied in, so history is not rewritten when
            // the catalogue changes later.
            $table->string('name');
            $table->string('size', 20)->default('regular');
            $table->decimal('unit_price', 10, 2);
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('line_total', 10, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reservation_items');
    }
};
