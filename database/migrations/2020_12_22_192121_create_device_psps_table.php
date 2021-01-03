<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDevicePspsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('device_psps', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Variables\DeviceType::class)->references('id')->on('device_types')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(\App\Models\Variables\Psp::class)->references('id')->on('psps')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('device_psps');
    }
}
