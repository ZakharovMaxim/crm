<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('shop_id')->unsigned()->nullable();
            $table->string('customer_name')->nullable();
            $table->string('customer_surname')->nullable();
            $table->string('customer_fathername')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('customer_email')->nullable();
            $table->bigInteger('customer_id')->unsigned()->nullable();
            $table->bigInteger('payment_type_id')->unsigned()->nullable();
            $table->bigInteger('bill_id')->unsigned()->nullable();
            $table->bigInteger('delivery_id')->unsigned()->nullable();
            $table->bigInteger('creator_id')->unsigned()->nullable();
            $table->bigInteger('manager_id')->unsigned()->nullable();
            $table->enum('delivery_payer', ['1', '2'])->nullable()->default('1');
            $table->string('delivery_city')->nullable();
            $table->string('delivery_address')->nullable();
            $table->text('check_comment')->nullable();
            $table->string('roistat_visit_id')->nullable();
            $table->bigInteger('payment_source_id')->unsigned()->nullable();
            $table->string('payment_source_link')->nullable();
            $table->string('ttn')->nullable();
            $table->string('np_key')->nullable();
            $table->integer('status')->default(1);
            $table->text('order_comment')->nullable();
            $table->text('user_comment')->nullable();
            $table->foreign('shop_id')
                  ->references('id')->on('shops')
                  ->onDelete('cascade');
            $table->foreign('customer_id')
                  ->references('id')->on('clients')
                  ->onDelete('cascade');
            $table->foreign('creator_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');
            $table->foreign('manager_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');
            $table->foreign('payment_type_id')
                  ->references('id')->on('payment_types')
                  ->onDelete('cascade');
            $table->foreign('delivery_id')
                  ->references('id')->on('deliveries')
                  ->onDelete('cascade');
                  $table->foreign('bill_id')
                  ->references('id')->on('bills')
                  ->onDelete('cascade');
            $table->timestamp('status_updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));
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
        Schema::dropIfExists('orders');
    }
}
