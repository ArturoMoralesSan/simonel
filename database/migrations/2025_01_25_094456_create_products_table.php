<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('vinil_cost');
            $table->string('impresion_cost');
            $table->string('indirect_cost');
            $table->string('costo_total');
            $table->string('costo_venta');
            $table->text('description')->nullable();
            $table->foreignId('type_id')->constrained('types')->onDelete('cascade');
            $table->foreignId('measures_id')->constrained('measures')->onDelete('cascade');
            $table->integer('quantity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
