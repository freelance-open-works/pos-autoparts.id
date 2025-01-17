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
        Schema::create('product_stock_histories', function (Blueprint $table) {
            $table->ulid('id')->primary();
            //
            $table->ulid('product_id');
            $table->ulid('product_stock_id');
            $table->decimal('start', 20, 2)->nullable()->default(0);
            $table->decimal('in', 20, 2)->nullable()->default(0);
            $table->decimal('out', 20, 2)->nullable()->default(0);
            $table->decimal('last', 20, 2)->nullable()->default(0);
            $table->text('extras')->nullable();
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
        Schema::dropIfExists('product_stock_histories');
    }
};
