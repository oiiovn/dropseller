<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProgramsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('name_program'); // Tên chương trình
            $table->json('products'); // Danh sách sản phẩm dạng JSON
            $table->text('description')->nullable(); // Mô tả
            $table->json('shops');
            $table->unsignedBigInteger('created_by'); // Người tạo
            $table->unsignedBigInteger('updated_by')->nullable(); // Người cập nhật cuối
            $table->timestamps();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('programs');
    }
}
