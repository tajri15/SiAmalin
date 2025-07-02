<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOfficeLocationToKaryawan extends Migration
{
    public function up()
    {
        Schema::connection('mongodb')->table('karyawan', function ($collection) {
            $collection->point('office_location')->nullable();
            $collection->float('office_radius')->default(55); // dalam meter
        });
    }

    public function down()
    {
        Schema::connection('mongodb')->table('karyawan', function ($collection) {
            $collection->dropColumn('office_location');
            $collection->dropColumn('office_radius');
        });
    }
}