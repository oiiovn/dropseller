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
Schema::create('checkso', function (Blueprint $table) {
    $table->id();
    $table->string('phone')->nullable();
    $table->string('username')->nullable();
    $table->boolean('exists')->default(false);
    $table->string('status')->default('pending'); // pending, success, fail
    $table->timestamps();
});


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
Schema::create('checkso', function (Blueprint $table) {
    $table->id();
    $table->string('phone')->nullable();
    $table->string('username')->nullable();
    $table->boolean('exists')->default(false);
    $table->string('status')->default('pending'); // pending, success, fail
    $table->timestamps();
});

    }
};
