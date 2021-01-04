<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification_events', function (Blueprint $table) {
            $table->id();
            $table->enum('event_type', ['PROFILES', 'REPAIRS', 'DEVICES']);
            $table->foreignIdFor(\App\Models\Notifications\Type::class)->references('id')->on('notification_types')->cascadeOnDelete()->cascadeOnDelete();
            $table->unsignedTinyInteger('event');
            $table->enum('level', ['SUPERUSER', 'ADMIN', 'AGENT', 'MARKETER', 'CUSTOMER', 'TECHNICAL']);
            $table->boolean('status')->default(1);
            $table->timestamps();
        });

        \Illuminate\Support\Facades\DB::table('notification_events')->insert([
            ['event_type' => 'PROFILES', 'notification_type_id' => 1, 'event' => 1, 'level' => 'ADMIN'],
            ['event_type' => 'PROFILES', 'notification_type_id' => 2, 'event' => 1, 'level' => 'CUSTOMER'],
            ['event_type' => 'PROFILES', 'notification_type_id' => 3, 'event' => 3, 'level' => 'CUSTOMER'],
            ['event_type' => 'PROFILES', 'notification_type_id' => 4, 'event' => 3, 'level' => 'MARKETER'],
            ['event_type' => 'PROFILES', 'notification_type_id' => 5, 'event' => 10, 'level' => 'MARKETER'],
            ['event_type' => 'PROFILES', 'notification_type_id' => 6, 'event' => 5, 'level' => 'AGENT'],
            ['event_type' => 'PROFILES', 'notification_type_id' => 7, 'event' => 11, 'level' => 'AGENT'],
            ['event_type' => 'PROFILES', 'notification_type_id' => 8, 'event' => 11, 'level' => 'MARKETER'],
            ['event_type' => 'PROFILES', 'notification_type_id' => 9, 'event' => 6, 'level' => 'ADMIN'],
            ['event_type' => 'PROFILES', 'notification_type_id' => 10, 'event' => 7, 'level' => 'AGENT'],
            ['event_type' => 'PROFILES', 'notification_type_id' => 11, 'event' => 7, 'level' => 'MARKETER'],
            ['event_type' => 'PROFILES', 'notification_type_id' => 12, 'event' => 13, 'level' => 'AGENT'],
            ['event_type' => 'PROFILES', 'notification_type_id' => 13, 'event' => 8, 'level' => 'AGENT'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events');
    }
}
