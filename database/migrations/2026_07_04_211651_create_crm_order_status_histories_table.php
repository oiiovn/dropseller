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
        Schema::create('crm_order_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('crm_orders')->cascadeOnDelete();
            $table->string('from_status', 30)->nullable();
            $table->string('to_status', 30)->index();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_order_status_histories');
    }
};
