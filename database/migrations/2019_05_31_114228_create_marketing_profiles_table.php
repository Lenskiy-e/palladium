<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMarketingProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('marketing_profiles', function (Blueprint $table) {
            $table->integer('user_id');
            $table->tinyInteger('auto')->default(0)->nullable();
            $table->boolean('children')->default(false);
            $table->bigInteger('discount')->nullable();
            $table->double('discount_balance')->default(0);
            $table->double('discount_spent')->default(0);
            $table->boolean('mailing')->default(false)->nullable();
            $table->timestamps();
            $table->primary('user_id');
            $table->index(['auto', 'children', 'discount', 'mailing']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('marketing_profiles');
    }
}
