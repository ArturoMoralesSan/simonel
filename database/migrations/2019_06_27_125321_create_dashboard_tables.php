<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDashboardTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Sections
        Schema::create('dashboard_sections', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50)->unique();
            $table->string('tile', 50);
            $table->unsignedTinyInteger('order')->default(1);
            $table->timestamps();
        });

        // Submenus
        Schema::create('dashboard_submenus', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50);
            $table->string('icon', 50);
            $table->unsignedTinyInteger('order')->default(1);
            $table->unsignedInteger('section_id');
            $table->timestamps();

            $table->unique(['name', 'section_id']);
            $table->foreign('section_id')->references('id')->on('dashboard_sections');
        });

        // Links
        Schema::create('dashboard_links', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name', 60);
            $table->string('route');
            $table->unsignedTinyInteger('order')->default(1);
            $table->unsignedInteger('submenu_id');
            $table->unsignedInteger('permission_id')->nullable();
            $table->timestamps();

            $table->unique(['name', 'submenu_id']);
            $table->foreign('submenu_id')->references('id')->on('dashboard_submenus');
            $table->foreign('permission_id')->references('id')->on('permissions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dashboard_links');
        Schema::dropIfExists('dashboard_submenus');
        Schema::dropIfExists('dashboard_sections');
    }
}