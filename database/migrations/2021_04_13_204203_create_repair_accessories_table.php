<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRepairAccessoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('repair_accessories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedTinyInteger('status')->default(1);
            $table->timestamps();
        });
        $accessories = collect([
            ['id' => 1, 'name' => 'شارژر'],
            ['id' => 2, 'name' => 'باطری'],
            ['id' => 3, 'name' => 'درب باطری'],
            ['id' => 4, 'name' => 'سیم کارت'],
            ['id' => 5, 'name' => 'کارتن'],
        ]);
        $accessories->each(function($accessory){
            \App\Models\Repairs\Accessory::create($accessory);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accessories');
    }
}
