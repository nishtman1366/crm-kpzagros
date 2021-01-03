<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key');
            $table->string('name');
            $table->string('value')->nullable();
            $table->timestamps();
        });

        \Illuminate\Support\Facades\DB::table('settings')->insert([
            ['key' => 'PAGE_TITLE', 'name' => 'تیتر صفحات', 'value' => 'مدیریت ارتباط با بازاریابان'],
            ['key' => 'COMPANY_NAME', 'name' => 'نام شرکت', 'value' => 'تجارت الکترونیک سیران'],
            ['key' => 'COMPANY_LOGO', 'name' => 'لوگوی شرکت', 'value' => null],
            ['key' => 'STATUS', 'name' => 'وضعیت', 'value' => 1]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
}
