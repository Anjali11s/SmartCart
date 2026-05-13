<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // First, modify the column to have all possible values
        DB::statement("ALTER TABLE orders MODIFY order_status ENUM(
            'pending', 
            'confirmed', 
            'processing', 
            'shipped', 
            'out_for_delivery', 
            'delivered', 
            'cancelled', 
            'return_requested',
            'return_approved',
            'return_rejected',
            'returned'
        ) DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE orders MODIFY order_status ENUM(
            'pending', 
            'confirmed', 
            'processing', 
            'shipped', 
            'out_for_delivery', 
            'delivered', 
            'cancelled', 
            'return_requested',
            'returned'
        ) DEFAULT 'pending'");
    }
};