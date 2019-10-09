<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->integer('id');
            $table->string('name',255)->nullable();
            $table->string('meta_title', 255)->nullable();
            $table->text('meta_description')->nullable();
            $table->string('email',255)->nullable();
            $table->json('phones')->nullable();
            $table->string('logo',255)->nullable();
            $table->string('placeholder',255)->nullable();
            $table->string('favicon',255)->nullable();
            $table->text('additional')->nullable();
            $table->json('addresses')->nullable();
            $table->json('work_times')->nullable();
            $table->boolean('tech_works')->default(0);
            $table->string('footer_template')->default('footer');
            $table->primary(['id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
}
