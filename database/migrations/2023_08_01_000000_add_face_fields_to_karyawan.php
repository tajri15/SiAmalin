<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFaceFieldsToKaryawan extends Migration
{
    public function up()
    {
        Schema::connection('mongodb')->table('karyawan', function ($collection) {
            $collection->array('face_embedding')->nullable();
            $collection->boolean('is_trained')->default(false);
            $collection->array('face_samples')->nullable();
        });
    }

    public function down()
    {
        Schema::connection('mongodb')->table('karyawan', function ($collection) {
            $collection->dropColumn('face_embedding');
            $collection->dropColumn('is_trained');
            $collection->dropColumn('face_samples');
        });
    }
}