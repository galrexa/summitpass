<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->enum('gateway', ['midtrans', 'xendit', 'manual'])->default('midtrans');
            $table->string('transaction_id')->nullable()
                ->comment('ID transaksi dari payment gateway');
            $table->enum('status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->decimal('amount', 12, 2);
            $table->timestamp('paid_at')->nullable();
            $table->json('gateway_response')->nullable()
                ->comment('Raw response dari payment gateway untuk audit');
            $table->timestamps();

            $table->index('booking_id');
            $table->index('status');
            $table->index('transaction_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
