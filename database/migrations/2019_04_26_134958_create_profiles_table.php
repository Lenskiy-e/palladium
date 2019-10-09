<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('last_name',255)->collation('utf8_general_ci')->nullable();
            $table->string('patronymic',255)->collation('utf8_general_ci')->nullable();
            $table->date('birthday')->nullable();
            $table->string('photo')->nullable();
            $table->tinyInteger('gender')->default(0);
            $table->tinyInteger('role')->default(0);
            $table->timestamps();
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('profiles');
    }
}
