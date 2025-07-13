<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('draft_notulen', function (Blueprint $table) {
            // Add basic notulen fields if they don't exist
            if (!Schema::hasColumn('draft_notulen', 'nomor_urut')) {
                $table->integer('nomor_urut')->nullable();
            }
            if (!Schema::hasColumn('draft_notulen', 'unit')) {
                $table->string('unit')->nullable();
            }
            if (!Schema::hasColumn('draft_notulen', 'bidang')) {
                $table->string('bidang')->nullable();
            }
            if (!Schema::hasColumn('draft_notulen', 'sub_bidang')) {
                $table->string('sub_bidang')->nullable();
            }
            if (!Schema::hasColumn('draft_notulen', 'bulan')) {
                $table->integer('bulan')->nullable();
            }
            if (!Schema::hasColumn('draft_notulen', 'tahun')) {
                $table->integer('tahun')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('draft_notulen', function (Blueprint $table) {
            $table->dropColumn([
                'nomor_urut',
                'unit',
                'bidang',
                'sub_bidang',
                'bulan',
                'tahun'
            ]);
        });
    }
};
