<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeviceTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('device_types', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('device_connection_type_id');
            $table->string('name');
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('status')->default(1);
            $table->timestamps();
        });

        \Illuminate\Support\Facades\DB::table('device_types')->insert([
            ['name' => 'S90', 'device_connection_type_id' => 2],
            ['name' => 'S900', 'device_connection_type_id' => 2],
            ['name' => 'D180', 'device_connection_type_id' => 3],
            ['name' => 'S80', 'device_connection_type_id' => 4],
            ['name' => 'S800', 'device_connection_type_id' => 4],
            ['name' => 'D210', 'device_connection_type_id' => 2],
            ['name' => 'D210', 'device_connection_type_id' => 2],
            ['name' => 'AF70', 'device_connection_type_id' => 5],
            ['name' => 'AF70', 'device_connection_type_id' => 5],
            ['name' => 'I9000S', 'device_connection_type_id' => 6],
            ['name' => 'P1000', 'device_connection_type_id' => 6],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('device_types');
    }
}
