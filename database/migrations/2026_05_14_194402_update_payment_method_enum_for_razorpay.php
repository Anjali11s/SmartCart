<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::statement("ALTER TABLE orders MODIFY payment_method ENUM('COD', 'QR', 'razorpay') DEFAULT NULL");
    }

    public function down()
    {
        DB::statement("ALTER TABLE orders MODIFY payment_method ENUM('COD', 'QR') DEFAULT NULL");
    }
};