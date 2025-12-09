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
        $table->string('name')->nullable()->change();
        $table->string('phone')->nullable()->change();
        $table->string('address')->nullable()->change();
        $table->string('email')->nullable()->change();
        $table->date('date')->nullable()->change();

        // Drop foreign key and column safely
        if (Schema::hasColumn('whatapp_orders', 'product_id')) {
            $table->dropForeign(['product_id']);
            $table->dropColumn('product_id');
        }

        // Add new column
        $table->string('product_code')->nullable();
    });
}

public function down(): void
{
    Schema::table('whatapp_orders', function (Blueprint $table) {
        $table->string('name')->nullable(false)->change();
        $table->string('phone')->nullable(false)->change();
        $table->string('address')->nullable(false)->change();
        $table->string('email')->nullable(false)->change();
        $table->date('date')->nullable(false)->change();

        // Restore product_id foreign key
        $table->foreignId('product_id')->constrained('products')->onDelete('cascade');

        // Drop new column
        $table->dropColumn('product_code');
    });
}

};
