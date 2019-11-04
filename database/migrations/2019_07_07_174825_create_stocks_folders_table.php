<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStocksFoldersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stocks_folders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('stock_id')->unsigned();
            $table->bigInteger('folder_id')->unsigned();
            $table->foreign('stock_id')
                ->references('id')->on('stocks')
                ->onDelete('cascade');
            $table->foreign('folder_id')
                ->references('id')->on('folders')
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
        Schema::dropIfExists('stocks_folders');
    }
}
