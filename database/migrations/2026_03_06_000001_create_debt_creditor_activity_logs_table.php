<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('debt_creditor_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('action', 32); // login, login_failed, view_page
            $table->string('route_name', 128)->nullable();
            $table->string('path', 512)->nullable();
            $table->string('ip_address', 45);
            $table->string('user_agent', 1024)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::table('debt_creditor_activity_logs', function (Blueprint $table) {
            $table->index(['user_id', 'created_at']);
            $table->index(['action', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('debt_creditor_activity_logs');
    }
};
