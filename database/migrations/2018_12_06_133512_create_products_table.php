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
            $table->increments('product_id');
            $table->boolean('active')->default(1);;
            $table->integer('available')->default(0);
            $table->integer('quantity');
            $table->integer('minimum_quantity')->nullable();
            $table->integer('maximum_quantity')->nullable();
            $table->float('volume_weight')->nullable();
            $table->boolean('adult')->default(0);
            $table->integer('ean')->nullable();
            $table->string('sku')->nullable();
            $table->string('model')->nullable();
            $table->string('vendor')->nullable();
            $table->text('image')->nullable();
            $table->integer('sort_order');
            $table->integer('purchase_price')->nullable();
            $table->integer('rrc_price')->nullable();
            $table->integer('base_price')->nullable();
            $table->integer('price_mark')->default(0);
            $table->boolean('markdown')->default(0);
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
