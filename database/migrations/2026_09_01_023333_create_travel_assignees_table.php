<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('travel_assignees', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('travel_request_id')->constrained('travel_requests')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees');
            $table->boolean('is_leader')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('travel_assignees');
    }
};