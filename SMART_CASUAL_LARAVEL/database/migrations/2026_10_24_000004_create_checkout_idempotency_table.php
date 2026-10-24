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
        if (!Schema::hasTable('CHECKOUT_IDEMPOTENCY')) {
            Schema::create('CHECKOUT_IDEMPOTENCY', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('idempotency_key', 100)->unique();
                $table->string('customer_id', 20)->nullable();
                $table->string('request_hash', 64);
                $table->string('order_id', 30)->nullable();
                $table->string('status', 20)->default('processing'); // 'processing', 'completed', 'failed'
                $table->timestamps();

                $table->index(['customer_id', 'status']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('CHECKOUT_IDEMPOTENCY');
    }
};
