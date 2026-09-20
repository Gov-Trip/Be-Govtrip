<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('travel_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('sppd_number', 100)->unique()->nullable();
            $table->foreignId('requester_employee_id')->constrained('employees');
            $table->foreignId('origin_city_id')->constrained('ref_cities');
            $table->foreignId('destination_city_id')->constrained('ref_cities');
            $table->date('start_date');
            $table->date('end_date');
            $table->text('purpose');
            $table->decimal('estimated_total_cost', 12, 2)->default(0);
            $table->decimal('advance_payment', 12, 2)->default(0);
            $table->string('status', 50)->default('Menunggu Persetujuan');
            $table->string('cancel_reason_type', 100)->nullable(); // Anggaran dibatalkan, Agenda dibatalkan, Force Majeure
            $table->text('cancel_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['requester_employee_id', 'status']);
            $table->index(['start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('travel_requests');
    }
};