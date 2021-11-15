<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('domain')->default('kpzagors-crm.com');
        });

        $data = [
            ['key' => 'COMPANY_NAME', 'name' => 'نام شرکت', 'value' => 'فرا سیستم پرتو', 'domain' => 'crm.linkeee.ir'],
            ['key' => 'COMPANY_LOGO', 'name' => 'لوگوی شرکت', 'value' => '/images/company/logo_001.png', 'domain' => 'crm.linkeee.ir'],
            ['key' => 'PAGE_TITLE', 'name' => 'عنوان صفحات', 'value' => 'سامانه جامع امور نمایندگان', 'domain' => 'crm.linkeee.ir'],
        ];
        \App\Models\Setting::insert($data);
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
