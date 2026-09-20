<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ref_cities', function (Blueprint $table) {
            $table->id();
            $table->string('province_name');
            $table->string('city_name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ref_cities');
    }
};