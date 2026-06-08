<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->unique(); // Relación 1:1 con users
            $table->string('business_name'); // Razón social
            $table->string('rfc');
            $table->string('trade_name')->nullable(); // Nombre comercial
            $table->string('tax_regime'); // Régimen fiscal
            $table->string('contact')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('street')->nullable();
            $table->string('ext_number')->nullable();
            $table->string('int_number')->nullable();
            $table->string('between_streets')->nullable();
            $table->string('and_street')->nullable();
            $table->string('country')->default('MEX. México');
            $table->string('state')->nullable();
            $table->string('municipality')->nullable();
            $table->string('population')->nullable();
            $table->string('colony')->nullable();
            $table->string('postal_code');
            $table->timestamps();

            // Relación con users
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customers');
    }
}
