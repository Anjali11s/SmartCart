<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('shipping_address_id')->nullable()->constrained('shipping_addresses')->onDelete('set null');
            $table->string('tracking_number')->nullable();
            $table->string('courier_name')->nullable();
            $table->date('expected_delivery_date')->nullable();
            $table->date('delivered_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('return_reason')->nullable();
            $table->timestamp('return_requested_at')->nullable();
            $table->timestamp('return_approved_at')->nullable();
            $table->enum('refund_status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->string('shipping_phone', 20)->nullable();
            $table->text('shipping_address_text')->nullable(); // Full address as text for snapshot
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'shipping_address_id', 'tracking_number', 'courier_name',
                'expected_delivery_date', 'delivered_at', 'cancellation_reason',
                'cancelled_at', 'return_reason', 'return_requested_at',
                'return_approved_at', 'refund_status', 'shipping_phone', 'shipping_address_text'
            ]);
        });
    }
};