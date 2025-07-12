<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Rename the port_of_discharge column to berth_number
        Schema::table('shipments', function (Blueprint $table) {
            $table->renameColumn('port_of_discharge', 'berth_number');
        });

        // Set all values in the berth_number column to null
        DB::table('shipments')->update(['berth_number' => null]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rename the berth_number column back to port_of_discharge
        Schema::table('shipments', function (Blueprint $table) {
            $table->renameColumn('berth_number', 'port_of_discharge');
        });

        // Optionally set all values in the port_of_discharge column to null
        // Remove this if you want to preserve data during rollback
        DB::table('shipments')->update(['port_of_discharge' => null]);
    }
};
