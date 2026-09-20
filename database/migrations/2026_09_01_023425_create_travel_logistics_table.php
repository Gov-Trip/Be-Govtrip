<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('travel_logistics', function (Blueprint $table) {
            $table->id();
            
            $table->foreignUuid('travel_request_id')->constrained('travel_requests')->cascadeOnDelete();
            
            // TAMBAHAN: Kolom untuk mencatat siapa admin travel yang mengurus tiket ini
            $table->foreignId('admin_travel_id')->constrained('users')->cascadeOnDelete();
            
            $table->string('transport_type', 50);
            $table->string('booking_code_or_ticket', 100);
            $table->string('hotel_name', 150)->nullable();
            $table->dateTime('departure_datetime');
            $table->dateTime('return_datetime');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('travel_logistics');
    }
};