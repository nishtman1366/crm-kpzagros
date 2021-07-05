<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_events', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Tickets\Ticket::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('title');
            $table->text('body')->nullable();
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
        Schema::dropIfExists('ticket_events');
    }
}
