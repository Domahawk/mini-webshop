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
        Schema::create('price_list_product', function (Blueprint $table) {
            $table->char('sku', 12);
            $table->foreignUuid('price_list_id')->references('id')->on('price_lists');
            $table->decimal('price');
            $table->timestamps();

            $table->primary(['sku', 'price_list_id']);
            $table->foreign('sku')->references('sku')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_list_product');
    }
};
