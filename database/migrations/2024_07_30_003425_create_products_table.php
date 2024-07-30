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
        Schema::create('products', function (Blueprint $table) {
            $table->ulid('id')->primary();
            //
            $table->string('name')->nullable();
            $table->string('part_code')->nullable();
            $table->string('type')->nullable();
            $table->decimal('discount', 20, 2)->default(0);
            $table->decimal('cost', 20, 2)->default(0);
            $table->decimal('price', 20, 2)->default(0);
            $table->ulid('brand_id')->nullable();
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
        Schema::dropIfExists('products');
    }
};
