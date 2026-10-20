<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('travel_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('travel_request_id')->constrained('travel_requests')->cascadeOnDelete();
            $table->foreignId('expense_report_id')->nullable()->constrained('expense_reports')->nullOnDelete();
            $table->string('file_type', 50); // RECEIPT, PHOTO, SPPD_PDF
            $table->string('file_path', 255);
            $table->decimal('amount', 12, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('travel_attachments');
    }
};
