<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTypeOfPriceFieldsInProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->float('purchase_price')->nullable()->change();
            $table->float('rrc_price')->nullable()->change();
            $table->float('base_price')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('purchase_price')->nullable()->change();
            $table->integer('rrc_price')->nullable()->change();
            $table->integer('base_price')->nullable()->change();
        });
    }
}
