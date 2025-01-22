<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopNameTable extends Migration
{
    public function up()
    {
        Schema::create('shops_name', function (Blueprint $table) {
            $table->id(); 
            $table->string('shop_id')->unique(); 
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('shop_name'); 
            $table->timestamps(); 
        });
    }

    public function down()
    {
        Schema::dropIfExists('shops_name'); 
    }
}
