<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRepairTypesListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('repair_types_lists', function (Blueprint $table) {
            $table->foreignIdFor(App\Models\Repairs\Repair::class)->references('id')->on('repairs')->cascadeOnDelete()->cascadeOnDelete();
            $table->foreignIdFor(App\Models\Repairs\Type::class)->references('id')->on('repair_types')->cascadeOnDelete()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('repair_types_lists');
    }
}
