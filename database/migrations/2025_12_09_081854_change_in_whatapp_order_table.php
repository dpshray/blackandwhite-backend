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
        Schema::table('whatapp_orders', function (Blueprint $table) {
            //
            $table->string('name')->nullable()->change();
            $table->string('phone')->nullable()->change();
            $table->string('address')->nullable()->change();
            $table->string('email')->nullable()->change();
            $table->dropForeign(['product_id']);
            $table->dropColumn('product_id');
            $table->string('date')->nullable()->change();
            $table->string('product_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('whatapp_orders', function (Blueprint $table) {
            //
            $table->string('name')->change();
            $table->string('phone')->change();
            $table->string('address')->change();
            $table->string('email')->nullable(false)->change();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('date')->change();
            $table->dropColumn('product_code');
        });
    }
};
