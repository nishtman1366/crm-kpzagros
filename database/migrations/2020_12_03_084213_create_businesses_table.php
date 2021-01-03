<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('profile_id');
            $table->integer('ostan_id');
            $table->integer('shahrestan_id');
            $table->integer('bakhsh_id');
            $table->integer('shahr_id')->nullable();
            $table->integer('dehestan_id')->nullable();
            $table->integer('abadi_id')->nullable();
            $table->string('phone_code');
            $table->string('senf');
            $table->string('name');
            $table->string('name_english');
            $table->string('address');
            $table->string('postal_code');
            $table->string('phone');
            $table->enum('has_license', ['YES', 'NO']);
            $table->string('license_code')->nullable();
            $table->date('license_date')->nullable();
            $table->timestamps();

            $table->foreign('profile_id')->references('id')->on('profiles')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('ostan_id')->references('id')->on('ostan')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('shahrestan_id')->references('id')->on('shahrestan')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('bakhsh_id')->references('id')->on('bakhsh')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('shahr_id')->references('id')->on('shahr')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('dehestan_id')->references('id')->on('dehestan')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('abadi_id')->references('id')->on('abadi')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('businesses');
    }
}
