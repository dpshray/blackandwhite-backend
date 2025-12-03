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
        Schema::table('orders', function (Blueprint $table) {
            //
            $table->json('address_details')->nullable()->after('billing_information_id');
            $table->enum('payment_status', ['paid','unpaid'])->default('unpaid')->nullable()->after('address_details');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            //
            $table->dropColumn('address_details');
            $table->dropColumn('payment_status');
        });
    }
};
