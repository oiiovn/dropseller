<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('debt_old_debt_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('debt_creditor_id')->constrained('debt_creditors')->cascadeOnDelete();
            $table->string('code', 100)->nullable();
            $table->decimal('principal_amount', 18, 2)->default(0);
            $table->text('notes')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('debt_old_debt_items');
    }
};
