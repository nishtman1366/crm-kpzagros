<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeviceConnectionTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('device_connection_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        \Illuminate\Support\Facades\DB::table('device_connection_types')->insert(
            [
                ['name' => 'DIALUP'],
                ['name' => 'GPRS'],
                ['name' => 'MPOS'],
                ['name' => 'LAN'],
                ['name' => 'WIFI'],
                ['name' => 'ANDROID'],
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('device_connection_types');
    }
}
