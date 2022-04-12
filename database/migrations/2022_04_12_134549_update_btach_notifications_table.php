<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateBtachNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('batch_notifications', function (Blueprint $table) {
            $table->string('valid_recipients')->after('id')->nullable();
            $table->string('cost')->after('valid_recipients')->nullable();
            $table->string('payback_cost')->after('cost')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('batch_notifications', function (Blueprint $table) {
            //
        });
    }
}
