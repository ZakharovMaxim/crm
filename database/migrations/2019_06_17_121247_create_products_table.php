<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('sku')->nullable();
            $table->integer('purchase_price')->nullable();
            $table->integer('selling_price')->nullable();
            $table->boolean('is_variation')->default(false);
            $table->boolean('is_variant')->default(false);
            $table->boolean('is_deleted')->default(false);
            $table->bigInteger('catalog_id')->unsigned()->nullable();
            $table->bigInteger('parent_id')->unsigned()->nullable();
            $table->bigInteger('trademark_id')->unsigned()->nullable();
            $table->bigInteger('root_id')->unsigned();
            $table->foreign('root_id')
                ->references('id')->on('folders')
                ->onDelete('cascade');
            $table->foreign('trademark_id')
                ->references('id')->on('trademarks')
                ->onDelete('cascade');
            $table->mediumText('description')->nullable();
            $table->mediumText('additional_info')->nullable();
            $table->integer('min_count')->default(0);
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
