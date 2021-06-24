<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeviceFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->enum('device_sell_type', ['cash', 'installment', 'dept'])->nullable();
            $table->string('device_amount')->nullable();
            $table->string('device_dept_profile_id')->nullable();

            $table->enum('device_physical_status', ['new', 'stock']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
