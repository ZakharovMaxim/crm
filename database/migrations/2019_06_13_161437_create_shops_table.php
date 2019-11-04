<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->bigInteger('roistat_id')->unsigned()->nullable();
            $table->bigInteger('novaposhta_id')->unsigned()->nullable();
            $table->bigInteger('turbosms_id')->unsigned()->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->bigInteger('stock_id')->unsigned();
            $table->foreign('stock_id')
                  ->references('id')->on('stocks')
                  ->onDelete('cascade');
            $table->foreign('roistat_id')
                  ->references('id')->on('plugins')
                  ->onDelete('cascade');
            $table->foreign('novaposhta_id')
                  ->references('id')->on('plugins')
                  ->onDelete('cascade');
            $table->foreign('turbosms_id')
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
        Schema::dropIfExists('shops');
    }
}
