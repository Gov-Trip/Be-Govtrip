<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('travel_request_id')->constrained('travel_requests')->cascadeOnDelete();
            $table->foreignId('approver_employee_id')->constrained('employees');
            $table->foreignId('delegated_from_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->string('status', 50); // APPROVED, REJECTED, PERLU_REVISI[cite: 1, 3]
            $table->text('notes')->nullable();
            $table->timestamp('action_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approvals');
    }
};
