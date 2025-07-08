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
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->enum('transport_type', ['vessel', 'truck']);
            $table->foreignId('vessel_id')->nullable()->constrained()->onDelete('set null');
            $table->string('truck_number', 50)->nullable();
            $table->string('trailer_number', 50)->nullable();
            $table->string('driver_name', 255)->nullable();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->decimal('total_quantity', 10, 2);
            $table->string('port_of_discharge', 255)->nullable();
            $table->date('arrival_date');
            $table->enum('status', ['Pending', 'Completed']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
