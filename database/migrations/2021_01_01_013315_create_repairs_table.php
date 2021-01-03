<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRepairsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('repairs', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Variables\DeviceType::class)->nullable()->references('id')->on('device_types')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(\App\Models\Variables\Psp::class)->nullable()->references('id')->on('psps')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(\App\Models\User::class)->nullable()->references('id')->on('users')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(\App\Models\Repairs\Location::class)->nullable()->references('id')->on('repair_locations')->nullOnDelete()->cascadeOnUpdate();

            $table->string('serial');
            $table->string('name');
            $table->string('mobile');
            $table->string('national_code');
            $table->date('guarantee_end')->nullable();
            $table->text('description');
            $table->unsignedInteger('price')->default(0);
            $table->string('new_serial')->nullable();
            $table->string('loan_serial')->nullable();
            $table->unsignedInteger('deposit')->default(0);

            $table->string('tracking_code');
            $table->unsignedTinyInteger('status')->default(0);
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
        Schema::dropIfExists('repairs');
    }
}
