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
        Schema::create('sales', function (Blueprint $table) {
            $table->ulid('id')->primary();
            //
            $table->ulid('purchase_id')->nullable();
            $table->ulid('customer_id')->nullable();
            $table->string('s_code')->nullable();
            $table->timestamp('s_date')->nullable();
            $table->string('status')->nullable();
            $table->decimal('amount_cost', 20, 2)->nullable()->default(0);
            $table->decimal('amount_discount', 20, 2)->nullable()->default(0);
            $table->decimal('amount_net', 20, 2)->nullable()->default(0);
            $table->decimal('amount_ppn', 20, 2)->nullable()->default(0);
            $table->decimal('ppn_percent_applied', 20, 2)->nullable()->default(11);
            $table->text('address')->nullable();
            $table->text('note')->nullable();
            $table->string('type')->nullable();
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
        Schema::dropIfExists('sales');
    }
};
