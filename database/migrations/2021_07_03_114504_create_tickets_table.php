<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\User::class)->nullable()->constrained()->nullOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(\App\Models\Tickets\Agent::class)->nullable()->constrained('ticket_agents')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(\App\Models\Tickets\Type::class, 'ticket_type_id')->nullable()->constrained()->nullOnDelete()->cascadeOnUpdate();
            $table->string('code');
            $table->string('title')->nullable();
            $table->text('body');
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
        Schema::dropIfExists('tickets');
    }
}
