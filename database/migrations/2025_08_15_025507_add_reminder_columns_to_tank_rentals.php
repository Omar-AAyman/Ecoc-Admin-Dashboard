<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tank_rentals', function (Blueprint $table) {
            $table->boolean('reminder_30_days_sent')->default(false);
            $table->boolean('reminder_60_days_sent')->default(false);
        });
    }

    public function down()
    {
        Schema::table('tank_rentals', function (Blueprint $table) {
            $table->dropColumn(['reminder_30_days_sent', 'reminder_60_days_sent']);
        });
    }
};
