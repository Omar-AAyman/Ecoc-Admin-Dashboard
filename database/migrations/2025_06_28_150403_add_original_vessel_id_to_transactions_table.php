<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOriginalVesselIdToTransactionsTable extends Migration
{
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('original_vessel_id')->nullable();
            $table->foreign('original_vessel_id')->references('id')->on('vessels')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['original_vessel_id']);
            $table->dropColumn('original_vessel_id');
        });
    }
}