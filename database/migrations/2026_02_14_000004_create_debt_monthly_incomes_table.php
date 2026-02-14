<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('debt_monthly_incomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('debtor_user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('month');
            $table->unsignedSmallInteger('year');
            $table->decimal('amount', 18, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['debtor_user_id', 'month', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('debt_monthly_incomes');
    }
};
