<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePromocodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promocodes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code',255);
            $table->text('description');
            $table->boolean('reusable')->default(0);
            $table->boolean('unique')->default(0);
            $table->tinyInteger('type')->default(1);
            $table->double('discount');
            $table->double('minimum_amount')->default(0.00);
            $table->date('start_at');
            $table->date('expired_at');
            $table->boolean('active')->default(0);
            $table->boolean('used')->default(0);
            $table->timestamps();
            $table->index(['active','reusable']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('promocodes');
    }
}
