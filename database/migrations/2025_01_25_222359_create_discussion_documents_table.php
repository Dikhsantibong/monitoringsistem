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
        Schema::create('discussion_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('other_discussion_id')->constrained('other_discussions')->onDelete('cascade');
            $table->string('file_path');
            $table->string('original_name');
            $table->string('file_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discussion_documents');
    }
};
