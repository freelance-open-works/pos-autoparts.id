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
        Schema::create('sale_items', function (Blueprint $table) {
            $table->ulid('id')->primary();
            //
            $table->ulid('sale_id')->nullable();
            $table->ulid('product_id')->nullable();
            $table->decimal('qty', 20, 2)->nullable()->default(0);
            $table->decimal('qty_return', 20, 2)->nullable()->default(0);
            $table->decimal('price', 20, 2)->nullable()->default(0);
            $table->decimal('subtotal', 20, 2)->nullable()->default(0);
            $table->decimal('discount_percent_1', 20, 2)->nullable()->default(0);
            $table->decimal('discount_percent_2', 20, 2)->nullable()->default(0);
            $table->decimal('discount_total', 20, 2)->nullable()->default(0);
            $table->decimal('subtotal_discount', 20, 2)->nullable()->default(0);
            $table->decimal('subtotal_net', 20, 2)->nullable()->default(0);
            $table->decimal('subtotal_ppn', 20, 2)->nullable()->default(0);
            //
            $table->timestamps();
            $table->softDeletes();
            $table->ulid('created_by')->nullable();
            $table->ulid('updated_by')->nullable();
            $table->ulid('deleted_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_items');
    }
};
