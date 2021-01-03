<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePspsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('psps', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedTinyInteger('status')->default(1);
            $table->timestamps();
        });

        DB::table('psps')->insert([
            ['name' => 'الکترونیک کارت دماوند'],
            ['name' => 'آسان پرداخت'],
            ['name' => 'به پرداخت ملت'],
            ['name' => 'پرداخت الکترونیک پاسارگاد'],
            ['name' => 'پرداخت الکترونیک سامان'],
            ['name' => 'پرداخت نوین آرین'],
            ['name' => 'تجارت الکترونیک پارسیان'],
            ['name' => 'پرداخت الکترونیک سداد'],
            ['name' => 'سایان کارت'],
            ['name' => 'فن آوا کارت'],
            ['name' => 'ایران کیش'],
            ['name' => 'پرداخت الکترونیک سپهر'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('psps');
    }
}
