<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTemperatureToTanksTable extends Migration
{
    public function up()
    {
        Schema::table('tanks', function (Blueprint $table) {
            $table->float('temperature')->nullable()->after('current_level');
        });
    }

    public function down()
    {
        Schema::table('tanks', function (Blueprint $table) {
            $table->dropColumn('temperature');
        });
    }
}
