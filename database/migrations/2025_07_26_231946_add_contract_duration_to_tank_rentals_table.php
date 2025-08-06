<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddContractDurationToTankRentalsTable extends Migration
{
    public function up()
    {
        Schema::table('tank_rentals', function (Blueprint $table) {
            $table->integer('contract_duration')->nullable()->after('product_id'); // In months
        });
    }

    public function down()
    {
        Schema::table('tank_rentals', function (Blueprint $table) {
            $table->dropColumn('contract_duration');
        });
    }
}
