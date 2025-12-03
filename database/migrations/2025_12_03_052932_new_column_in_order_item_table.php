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
        Schema::table('order_items', function (Blueprint $table) {
            //
            $table->dropForeign(['variant_id']);
            $table->dropForeign(['product_id']);

            // Change columns to nullable
            $table->unsignedBigInteger('variant_id')->nullable()->change();
            $table->unsignedBigInteger('product_id')->nullable()->change();

            // Re-add FK with SET NULL
            $table->foreign('variant_id')
                ->references('id')
                ->on('variants')
                ->onDelete('set null')
                ->onUpdate('cascade');

            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('set null')
                ->onUpdate('cascade');

            // Add new columns
            $table->string('size')->nullable()->after('variant_id');
            $table->string('color')->nullable()->after('size');
            $table->text('product_name')->nullable()->after('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            // Drop new columns
            $table->dropColumn(['size', 'color', 'product_name']);

            // Drop updated foreign keys
            $table->dropForeign(['variant_id']);
            $table->dropForeign(['product_id']);

            // Revert nullable
            $table->unsignedBigInteger('variant_id')->nullable(false)->change();
            $table->unsignedBigInteger('product_id')->nullable(false)->change();

            // Re-add old foreign keys with CASCADE
            $table->foreign('variant_id')
                ->references('id')
                ->on('variants')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }
};
