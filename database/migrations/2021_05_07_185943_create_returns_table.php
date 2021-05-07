<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReturnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('returns', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\User::class)->nullable()->constrained()->nullOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(\App\Models\Variables\DeviceType::class)->nullable()->constrained()->nullOnDelete()->cascadeOnUpdate();
            $table->string('serial');
            $table->string('amount');
            $table->string('name');
            $table->string('national_code');
            $table->string('mobile');
            $table->string('accessories')->nullable();
            $table->string('file')->nullable();
            $table->string('tracking_code')->nullable();
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('status');
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
        Schema::dropIfExists('returns');
    }
}
