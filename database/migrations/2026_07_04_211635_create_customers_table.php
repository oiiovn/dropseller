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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_id')->nullable()->constrained('affiliates')->nullOnDelete();
            $table->string('full_name');
            $table->string('phone', 30)->index();
            $table->string('email')->nullable()->index();
            $table->string('address')->nullable();
            $table->string('prefecture')->nullable()->index();
            $table->string('city')->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->string('preferred_bicycle_line')->nullable();
            $table->string('consultation_status', 30)->default('new')->index();
            $table->date('last_consulted_at')->nullable();
            $table->date('last_purchased_at')->nullable();
            $table->text('purchase_interest')->nullable();
            $table->text('consultation_history')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
