<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCodeToMachinesTable extends Migration
{
    public function up()
    {
        Schema::table('machines', function (Blueprint $table) {
            $table->string('code')->unique()->after('name');
        });
    }

    public function down()
    {
        Schema::table('machines', function (Blueprint $table) {
            $table->dropColumn('code');
        });
    }
} 