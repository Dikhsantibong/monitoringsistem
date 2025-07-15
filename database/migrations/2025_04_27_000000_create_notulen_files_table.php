<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('notulen_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('notulen_id')->nullable();
            $table->string('session_id')->nullable();
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_type');
            $table->string('caption')->nullable();
            $table->timestamps();

            $table->foreign('notulen_id')->references('id')->on('notulens')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('notulen_files');
    }
}; 