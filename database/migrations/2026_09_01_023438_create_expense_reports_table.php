<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expense_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('travel_request_id')->constrained('travel_requests')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees');
            $table->decimal('total_advance', 12, 2)->default(0);      // Uang Muka Diterima[cite: 1, 3]
            $table->decimal('total_real_expense', 12, 2)->default(0); // Pengeluaran Riil[cite: 1, 3]
            $table->decimal('balance_amount', 12, 2)->default(0);     // Selisih[cite: 1, 3]
            $table->text('activity_summary');                         // Ringkasan Kegiatan[cite: 1, 3]
            $table->string('status', 50)->default('SUBMITTED');       // SUBMITTED, VERIFIED, REJECTED[cite: 1, 3]
            $table->text('finance_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expense_reports');
    }
};
