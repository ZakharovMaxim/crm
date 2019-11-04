<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStockActionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_actions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('to_stock')->unsigned();
            $table->bigInteger('from_stock')->unsigned()->nullable();
            $table->foreign('to_stock')->references('id')->on('stocks')->onDelete('cascade');
            $table->foreign('from_stock')->references('id')->on('stocks')->onDelete('cascade');
            $table->text('notice')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->boolean('is_submited')->default(false);
            $table->enum('type', ['1', '2', '3'])->default('1');
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
        Schema::dropIfExists('stock_actions');
    }
}
