<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tank_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['loading', 'discharging', 'transfer']);
            $table->foreignId('destination_tank_id')->nullable()->constrained('tanks')->onDelete('set null');
            $table->decimal('quantity', 10, 2);
            $table->date('date');

            $table->string('work_order_number', 100)->nullable();
            $table->string('charge_permit_number', 100)->nullable();
            $table->string('discharge_permit_number', 100)->nullable();
            $table->string('bill_of_lading_number', 100)->nullable();

            $table->foreignId('engineer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('technician_id')->nullable()->constrained('users')->onDelete('set null');

            $table->foreignId('shipment_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('delivery_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
