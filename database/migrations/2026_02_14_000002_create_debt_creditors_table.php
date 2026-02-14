<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('debt_creditors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('debtor_user_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('total_debt', 18, 2)->default(0);
            $table->string('phone', 20)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'debtor_user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('debt_creditors');
    }
};
