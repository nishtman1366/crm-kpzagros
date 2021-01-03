<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLicenseTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('license_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('key');
            $table->string('file_name')->nullable();
            $table->boolean('required')->default(1);
            $table->boolean('status')->default(1);
            $table->timestamps();
        });

        \Illuminate\Support\Facades\DB::table('license_types')->insert([
            ['name' => 'تصویر روی کارت ملی', 'key' => 'national_card_file_1'],
            ['name' => 'تصویر پشت کارت ملی', 'key' => 'national_card_file_2'],
            ['name' => 'تصویر شناسنامه', 'key' => 'id_file'],
            ['name' => 'تصویر اساسنامه', 'key' => 'asasname_file'],
            ['name' => 'تصویرر آگهی تاسیس', 'key' => 'agahi_file_1'],
            ['name' => 'تصویر آگهی تغییرات', 'key' => 'agahi_file_2'],
            ['name' => 'تصویر جواز کسب', 'key' => 'license_file'],
            ['name' => 'تصویر استشهادنامه', 'key' => 'esteshhad_file'],
            ['name' => 'تصویر تاییدیه شماره شبا', 'key' => 'sheba_file'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('license_types');
    }
}
