<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('shop_id')->unsigned();
            $table->bigInteger('payment_category_id')->unsigned();
            $table->bigInteger('bill_id')->unsigned();
            $table->bigInteger('order_id')->unsigned()->nullable();
            $table->integer('sum')->unsigned();
            $table->enum('type', ['1', '2'])->default('1');
            $table->timestamp('date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->boolean('is_deleted')->default(false);
            $table->text('comment')->nullable();
            $table->foreign('shop_id')
                  ->references('id')->on('shops')
                  ->onDelete('cascade');
            $table->foreign('payment_category_id')
                  ->references('id')->on('payment_states')
                  ->onDelete('cascade');
            $table->foreign('bill_id')
                  ->references('id')->on('bills')
                  ->onDelete('cascade');
            $table->foreign('order_id')
                  ->references('id')->on('orders')
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
        Schema::dropIfExists('payments');
    }
}
