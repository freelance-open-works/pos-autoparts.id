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
        Schema::create('purchase_items', function (Blueprint $table) {
            $table->ulid('id')->primary();
            //
            $table->ulid('purchase_id')->nullable();
            $table->ulid('product_id')->nullable();
            $table->decimal('qty', 20, 2)->default(0);
            $table->decimal('cost', 20, 2)->default(0);
            $table->decimal('subtotal', 20, 2)->default(0);
            $table->decimal('discount_percent', 20, 2)->default(0);
            $table->decimal('discount_amount', 20, 2)->default(0);
            $table->decimal('discount_total', 20, 2)->default(0);
            $table->decimal('subtotal_discount', 20, 2)->default(0);
            $table->decimal('subtotal_net', 20, 2)->default(0);
            $table->decimal('subtotal_ppn', 20, 2)->default(0);
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
        Schema::dropIfExists('purchase_items');
    }
};
