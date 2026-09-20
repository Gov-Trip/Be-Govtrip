<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('travel_policies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('destination_city_id')->constrained('ref_cities');
            $table->string('grade_group'); // Golongan/Eselon[cite: 1]
            $table->decimal('daily_allowance', 12, 2);    // Uang Harian[cite: 1]
            $table->decimal('max_hotel_rate', 12, 2);     // Batas Penginapan[cite: 1]
            $table->decimal('max_transport_rate', 12, 2); // Batas Transportasi[cite: 1]
            $table->integer('effective_year');            // Tahun Kebijakan[cite: 1]
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('travel_policies');
    }
};