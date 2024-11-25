<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMachineCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('machine_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Tambahkan foreign key ke tabel machines
        Schema::table('machines', function (Blueprint $table) {
            $table->foreignId('category_id')
                  ->after('code')
                  ->constrained('machine_categories')
                  ->onDelete('restrict');
        });
    }

    public function down()
    {
        Schema::table('machines', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
        
        Schema::dropIfExists('machine_categories');
    }
} 