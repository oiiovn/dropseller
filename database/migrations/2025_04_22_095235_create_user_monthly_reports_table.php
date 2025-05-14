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
        Schema::create('user_monthly_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('month'); // YYYY-MM
            $table->string('id_QT');
            $table->bigInteger('total_topup')->default(0);
            $table->bigInteger('total_paid')->default(0);
            $table->bigInteger('total_paid_ads')->default(0);
            $table->bigInteger('total_canceled')->default(0);
            $table->bigInteger('total_chi')->default(0);
            $table->bigInteger('khau_trang')->default(0); 
            $table->bigInteger('tien_thuc_te')->default(0); 
            $table->bigInteger('tien_phai_thanh_toan')->default(0); 
            $table->bigInteger('total_return')->default(0);  
            $table->bigInteger('ending_balance')->default(0);
            $table->json('shop_details')->nullable(); // chứa chi tiết shops & hoàn
            $table->timestamps();
        
            $table->unique(['user_id', 'month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_monthly_reports');
    }
};
