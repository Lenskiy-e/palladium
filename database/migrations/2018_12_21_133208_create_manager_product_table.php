<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateManagerProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manager_product', function (Blueprint $table) {
            $table->integer('manager_id');
            $table->integer('product_id');
            $table->date('check_date')->nullable();
            $table->date('publish_date')->nullable();
            $table->primary(['manager_id','product_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('manager_product');
    }
}
