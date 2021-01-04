<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification_types', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('pattern');
            $table->string('body');
            $table->timestamps();
        });

        \Illuminate\Support\Facades\DB::table('notification_types')->insert([
            ['title' => 'ثبت نهایی پرونده برای مدیر', 'pattern' => '15pkkot4c9', 'body' => 'پرونده ای به نام %customer_name% توسط %marketer_name% در سامانه به ثبت رسیده است.\n\n سامانه جامع امور نمایندگان\n توسعه فن آوری کیان پرداز زاگرس'],
            ['title' => 'ثبت نهایی پرونده برای مشتری', 'pattern' => '15pkkot4c9', 'body' => 'پرونده ای به نام %customer_name% توسط %marketer_name% در سامانه به ثبت رسیده است.\n\n سامانه جامع امور نمایندگان\n توسعه فن آوری کیان پرداز زاگرس'],

            ['title' => 'تایید مدارک برای مشتری', 'pattern' => '15pkkot4c9', 'body' => 'پرونده ای به نام %customer_name% توسط %marketer_name% در سامانه به ثبت رسیده است.\n\n سامانه جامع امور نمایندگان\n توسعه فن آوری کیان پرداز زاگرس'],
            ['title' => 'تایید مدارک برای بازاریاب', 'pattern' => '15pkkot4c9', 'body' => 'پرونده ای به نام %customer_name% توسط %marketer_name% در سامانه به ثبت رسیده است.\n\n سامانه جامع امور نمایندگان\n توسعه فن آوری کیان پرداز زاگرس'],

            ['title' => 'عدم تایید مدارک برای بازاریاب', 'pattern' => '15pkkot4c9', 'body' => 'پرونده ای به نام %customer_name% توسط %marketer_name% در سامانه به ثبت رسیده است.\n\n سامانه جامع امور نمایندگان\n توسعه فن آوری کیان پرداز زاگرس'],

            ['title' => 'تایید شاپرک برای نماینده', 'pattern' => '15pkkot4c9', 'body' => 'پرونده ای به نام %customer_name% توسط %marketer_name% در سامانه به ثبت رسیده است.\n\n سامانه جامع امور نمایندگان\n توسعه فن آوری کیان پرداز زاگرس'],
            ['title' => 'عدم تایید شاپرک برای نماینده', 'pattern' => '15pkkot4c9', 'body' => 'پرونده ای به نام %customer_name% توسط %marketer_name% در سامانه به ثبت رسیده است.\n\n سامانه جامع امور نمایندگان\n توسعه فن آوری کیان پرداز زاگرس'],
            ['title' => 'عدم تایید شاپرک برای بازایاب', 'pattern' => '15pkkot4c9', 'body' => 'پرونده ای به نام %customer_name% توسط %marketer_name% در سامانه به ثبت رسیده است.\n\n سامانه جامع امور نمایندگان\n توسعه فن آوری کیان پرداز زاگرس'],

            ['title' => 'ثبت سریال برای مدیر', 'pattern' => '15pkkot4c9', 'body' => 'پرونده ای به نام %customer_name% توسط %marketer_name% در سامانه به ثبت رسیده است.\n\n سامانه جامع امور نمایندگان\n توسعه فن آوری کیان پرداز زاگرس'],

            ['title' => 'تخصیص سریال برای نماینده', 'pattern' => '15pkkot4c9', 'body' => 'پرونده ای به نام %customer_name% توسط %marketer_name% در سامانه به ثبت رسیده است.\n\n سامانه جامع امور نمایندگان\n توسعه فن آوری کیان پرداز زاگرس'],
            ['title' => 'تخصیص سریال برای بازاریاب', 'pattern' => '15pkkot4c9', 'body' => 'پرونده ای به نام %customer_name% توسط %marketer_name% در سامانه به ثبت رسیده است.\n\n سامانه جامع امور نمایندگان\n توسعه فن آوری کیان پرداز زاگرس'],

            ['title' => 'عدم تایید سریال نماینده', 'pattern' => '15pkkot4c9', 'body' => 'پرونده ای به نام %customer_name% توسط %marketer_name% در سامانه به ثبت رسیده است.\n\n سامانه جامع امور نمایندگان\n توسعه فن آوری کیان پرداز زاگرس'],

            ['title' => 'نصب دستگاه نماینده', 'pattern' => '15pkkot4c9', 'body' => 'پرونده ای به نام %customer_name% توسط %marketer_name% در سامانه به ثبت رسیده است.\n\n سامانه جامع امور نمایندگان\n توسعه فن آوری کیان پرداز زاگرس'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notification_types');
    }
}
