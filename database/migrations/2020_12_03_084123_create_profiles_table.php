<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('psp_id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('device_id')->nullable();
            $table->unsignedBigInteger('device_type_id')->nullable();
            $table->boolean('multi_account')->default(0);
            $table->tinyInteger('status')->default(0);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('customer_id')->references('id')->on('customers')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('device_type_id')->references('id')->on('device_types')->nullOnDelete()->cascadeOnUpdate();
            $table->foreign('device_id')->references('id')->on('devices')->nullOnDelete()->cascadeOnUpdate();
            $table->foreign('psp_id')->references('id')->on('psps')->nullOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('profiles');
    }
}
