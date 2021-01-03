<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRepairTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('repair_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('status')->default(1);
            $table->timestamps();

            $table->index('status');
        });
        $data = [
            ['name' => 'اتصال شارژ متفرقه به دستگاه'],
            ['name' => 'باطری ضعیف شده'],
            ['name' => 'برخی از دکمه ها عمل نمی کنند'],
            ['name' => 'پرش آنتن'],
            ['name' => 'پرینتر چاپ غیر منظم دارد'],
            ['name' => 'چاپگر رسید را چاپ نمیکند'],
            ['name' => 'خاموش شدن در طول تراکنش'],
            ['name' => 'خشاب سیمکارت شکسته'],
            ['name' => 'خطای POS Be Attack'],
            ['name' => 'خطای Tamper'],
            ['name' => 'خطای نبود کاغذ'],
            ['name' => 'درب باطری شکسته و یا مفقود شده است'],
            ['name' => 'درب پرینتر شکسته'],
            ['name' => 'درخواست تعویض صفحه کلید'],
            ['name' => 'درخواست تعویض نمایشگر'],
            ['name' => 'درخواست تغییر سامانه دستگاه'],
            ['name' => 'دستگاه برنامه نمیگیرد'],
            ['name' => 'دستگاه روشن نمی شود'],
            ['name' => 'دستگاه شارژ نمیکند'],
            ['name' => 'دستگاه ضربه خورده'],
            ['name' => 'سرعت چاپ رسید پایین است'],
            ['name' => 'سیاه و یا سفید شدن صفحه نمایش'],
            ['name' => 'صفحه کلید بصورت چشمک زن روشن و خاموش می شود'],
            ['name' => 'صفحه کلید عمل نمیکند'],
            ['name' => 'صفحه نمایش شکسته شده'],
            ['name' => 'غلطک پرینتر مفقود شده است'],
            ['name' => 'کارت های بانکی را نمی خواند'],
            ['name' => 'نفوذ مایعات به داخل دستگاه'],
            ['name' => 'نور پس زمینه نمایشگر قطع شده'],
            ['name' => 'حافظه دستگاه پر است'],
            ['name' => 'بَک لایت نمایشگر سوخته'],
            ['name' => 'پذیرنده نامعتبر'],
            ['name' => 'عدم ارتباط'],
            ['name' => 'خطای گرمای بیش از حد چاپگر'],
            ['name' => 'خرابی برد اصلی'],
            ['name' => 'GSM سیم کارت'],
            ['name' => 'بارگذاری XML ناموفق'],
        ];

        \Illuminate\Support\Facades\DB::table('repair_types')->insert($data);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('repair_types');
    }
}
