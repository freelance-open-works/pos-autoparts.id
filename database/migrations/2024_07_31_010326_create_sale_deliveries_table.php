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
        Schema::create('sale_deliveries', function (Blueprint $table) {
            $table->ulid('id')->primary();
            //
            $table->ulid('sale_id')->nullable();
            $table->ulid('expedition_id')->nullable();
            $table->string('sd_code')->nullable();
            $table->timestamp('sd_date')->nullable();
            $table->string('qty')->nullable();
            $table->string('qty_unit')->nullable();
            $table->string('volume')->nullable();
            $table->string('volume_unit')->nullable();
            $table->text('note')->nullable();
            $table->string('service')->nullable();
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
        Schema::dropIfExists('sale_deliveries');
    }
};
