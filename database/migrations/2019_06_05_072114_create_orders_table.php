<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->integer('user_id')->nullable();
            $table->bigInteger('address_id')->nullable();
            $table->string('phone', 13);
            $table->string('coupon', 24)->nullable();
            $table->smallInteger('getter')->default('0');
            $table->string('getter_first_name')->nullable();
            $table->string('getter_last_name')->nullable();
            $table->text('message')->nullable();
            $table->integer('payment_method')->nullable();
            $table->boolean('complete')->default(0);
            $table->float('total_amount')->nullable();
            $table->integer('total_count')->default('0');
            $table->timestamps();
            $table->index(['user_id', 'coupon']);
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
