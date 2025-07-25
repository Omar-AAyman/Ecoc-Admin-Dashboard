<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trailers', function (Blueprint $table) {
            $table->id();
            $table->string('trailer_number', 255);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trailers');
    }
};
