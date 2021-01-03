<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRepairEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('repair_events', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\User::class)->nullable()->references('id')->on('users')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(\App\Models\Repairs\Repair::class)->references('id')->on('repairs')->cascadeOnDelete()->cascadeOnUpdate();
            $table->unsignedTinyInteger('status');
            $table->string('title');
            $table->string('description')->nullable();
            $table->timestamps();

            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('repair_events');
    }
}
