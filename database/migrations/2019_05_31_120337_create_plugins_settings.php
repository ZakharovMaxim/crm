<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePluginsSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plugins_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('plugin_id')->unsigned();
            $table->string('name');
            $table->string('value');
            $table->foreign('plugin_id')
                  ->references('id')->on('plugins')
                  ->onDelete('cascade');
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
        Schema::dropIfExists('plugins_settings');
    }
}
