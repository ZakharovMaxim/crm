<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_stats', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('product_id')->unsigned();
            $table->bigInteger('stock_id')->unsigned();
            $table->unsignedInteger('in_stock')->default(0);
            $table->unsignedInteger('in_prepare')->default(0);
            $table->unsignedInteger('in_reserve')->default(0);
            $table->unsignedInteger('in_delivery')->default(0);
            $table->unsignedInteger('in_package')->default(0);
            $table->unsignedInteger('in_sold')->default(0);
            $table->boolean('is_deleted')->default(false);
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('stock_id')->references('id')->on('stocks')->onDelete('cascade');
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
        Schema::dropIfExists('product_stats');
    }
}
