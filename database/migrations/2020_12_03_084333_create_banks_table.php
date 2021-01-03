<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateBanksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        DB::table('banks')->insert([
            ['name' => 'ملی'],
            ['name' => 'ملت'],
            ['name' => 'سپه'],
            ['name' => 'تجارت'],
            ['name' => 'پارسیان'],
            ['name' => 'سامان'],
            ['name' => 'اقتصاد نوین'],
            ['name' => 'پاسارگاد'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('banks');
    }
}
