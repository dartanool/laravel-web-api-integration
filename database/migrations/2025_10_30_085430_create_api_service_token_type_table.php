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
        Schema::create('api_service_token_type', function (Blueprint $table) {
            $table->id();
            $table->foreignId('api_service_id')->constrained('api_services')->cascadeOnDelete();
            $table->foreignId('token_type_id')->constrained('token_types')->cascadeOnDelete();
            $table->unique(['api_service_id', 'token_type_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_service_token_type');
    }
};
