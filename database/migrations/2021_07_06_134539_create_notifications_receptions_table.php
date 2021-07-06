<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsReceptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications_receptions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Notifications\BatchNotification::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('reception');
            $table->unsignedTinyInteger('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifications_receptions');
    }
}
