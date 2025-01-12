<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopsTable extends Migration
{
    public function up()
    {
        Schema::create('shop_name', function (Blueprint $table) {
            $table->id(); 
            $table->string('shop_id')->unique(); 
            $table->string('shop_name'); 
            $table->timestamps(); 
        });
    }

    public function down()
    {
        Schema::dropIfExists('shop_name'); 
    }
}
