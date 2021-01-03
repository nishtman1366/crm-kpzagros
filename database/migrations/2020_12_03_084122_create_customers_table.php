<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->enum('type', ['PERSON', 'ORGANIZATION']);
            $table->string('national_code');
            $table->string('id_code');
            $table->string('first_name');
            $table->string('first_name_english')->nullable();
            $table->string('last_name');
            $table->string('last_name_english')->nullable();
            $table->string('father');
            $table->string('father_english')->nullable();
            $table->enum('gender', ['male', 'female']);
            $table->string('mobile');
            $table->date('birthday');

            $table->string('company_name')->nullable();
            $table->string('company_name_english')->nullable();
            $table->string('business_name')->nullable();
            $table->date('reg_date')->nullable();
            $table->string('reg_code')->nullable();
            $table->string('company_national_code')->nullable();

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customers');
    }
}
