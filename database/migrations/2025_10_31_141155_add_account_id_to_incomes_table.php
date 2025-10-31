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
        Schema::table('incomes', function (Blueprint $table) {
            $table->foreignId('account_id')->constrained()->onDelete('cascade');
            $table->unique(['account_id', 'income_id', 'nm_id', 'supplier_article', 'tech_size'], 'incomes_account_unique');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incomes', function (Blueprint $table) {
            //
        });
    }
};
