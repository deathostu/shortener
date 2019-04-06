<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStatisticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statistics', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('counter')->default(0);
        });

        Schema::table('shortlinks', function($table) {
            $table->dropColumn('counter');
            $table->dropColumn('hash');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('statistics');
        Schema::table('shortlinks', function($table) {
            $table->unsignedBigInteger('counter')->default(0)->after('id');
            $table->string('hash', '128')->unique();
        });
    }
}
