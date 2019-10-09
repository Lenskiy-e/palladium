<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeReorderFieldsInCategoryDescriptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('category_description', function (Blueprint $table) {
            $table->integer('lft')->unsigned()->nullable()->default('0')->change();
            $table->integer('rgt')->unsigned()->nullable()->default('0')->change();
            $table->integer('depth')->unsigned()->nullable()->default('1')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('category_description', function (Blueprint $table) {
            $table->integer('lft')->unsigned()->nullable()->change();
            $table->integer('rgt')->unsigned()->nullable()->change();
            $table->integer('depth')->unsigned()->nullable()->change();
        });
    }
}
