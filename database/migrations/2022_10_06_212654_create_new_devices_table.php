<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('new_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Variables\DeviceType::class)->nullable()->constrained()->nullOnDelete()->cascadeOnUpdate();
            $table->string('serial');
            $table->string('imei');
            $table->string('sim_number');
            $table->string('national_code');
            $table->string('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('new_devices');
    }
}
