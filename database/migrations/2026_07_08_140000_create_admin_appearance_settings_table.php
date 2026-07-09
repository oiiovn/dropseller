<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_appearance_settings', function (Blueprint $table) {
            $table->id();
            $table->string('background_color', 32)->default('#f4f4f5');
            $table->string('sidebar_background_color', 32)->default('#020617');
            $table->string('sidebar_text_color', 32)->default('#f4f4f5');
            $table->string('sidebar_active_color', 32)->default('#2563eb');
            $table->string('header_background_color', 32)->default('#ffffff');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_appearance_settings');
    }
};
