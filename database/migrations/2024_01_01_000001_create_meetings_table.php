<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->datetime('start_time');
            $table->datetime('end_time');
            $table->string('status')->default('scheduled');
            $table->string('meeting_link')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->string('meeting_type')->default('regular');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('meetings');
    }
};