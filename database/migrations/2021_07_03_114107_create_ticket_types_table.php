<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        $types = [
            ['name' => 'امور نمایندگان'],
            ['name' => 'امور مالی'],
            ['name' => 'امور پذیرندگان'],
            ['name' => 'واحد فنی'],
        ];
        foreach ($types as $type) {
            \App\Models\Tickets\Type::create($type);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ticket_types');
    }
}
