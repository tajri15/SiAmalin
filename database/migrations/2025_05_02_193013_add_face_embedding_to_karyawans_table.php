<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFaceEmbeddingToKaryawansTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::connection('mongodb')->table('karyawan', function ($collection) {
            $collection->index('face_embedding');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::connection('mongodb')->table('karyawan', function ($collection) {
            $collection->dropIndex('face_embedding');
        });
    }
}