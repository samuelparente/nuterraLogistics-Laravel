<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeAttributteValueTable extends Migration
{
    public function up()
    {
        Schema::table('attribute_value_translations', function (Blueprint $table) {
            $table->text('value')->change();
        });
    }

    public function down()
    {
        Schema::table('attribute_value_translations', function (Blueprint $table) {
            $table->string('value', 255)->change();
        });
    }
}
