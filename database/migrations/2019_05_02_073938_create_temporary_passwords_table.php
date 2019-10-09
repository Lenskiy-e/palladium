<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTemporaryPasswordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temporary_passwords', function (Blueprint $table) {
            $table->integer('user_id')->primary();
            $table->string('phone',15)->nullable()->unique();
            $table->string('email')->nullable()->unique();
            $table->biginteger('token')->unique();
            $table->integer('attempts');
            $table->timestamp('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('temporary_passwords');
    }
}
