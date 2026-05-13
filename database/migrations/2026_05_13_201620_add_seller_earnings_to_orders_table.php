<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('seller_earnings', 10, 2)->default(0)->after('total_amount');
            $table->decimal('refund_amount', 10, 2)->nullable()->after('refund_status');
            $table->timestamp('refund_processed_at')->nullable()->after('refund_amount');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['seller_earnings', 'refund_amount', 'refund_processed_at']);
        });
    }
};