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
        Schema::create('store_orders', function (Blueprint $table) {
            $table->ulid('id')->primary();
            //
            $table->ulid('customer_id')->nullable();
            $table->string('so_code')->nullable(); // generated
            $table->timestamp('so_date')->nullable(); // generated
            $table->string('type')->nullable();
            $table->string('status')->nullable();
            $table->decimal('amount_cost', 20, 2)->nullable()->default(0);
            $table->text('address')->nullable();
            $table->text('note')->nullable();
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
        Schema::dropIfExists('store_orders');
    }
};
