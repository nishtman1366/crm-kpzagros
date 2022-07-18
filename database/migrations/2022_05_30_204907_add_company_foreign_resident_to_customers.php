<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCompanyForeignResidentToCustomers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('country_code')->default('IR')->after('residency');
            $table->string('foreign_pervasive_code')->nullable()->after('country_code');
            $table->string('passport_number')->nullable()->after('foreign_pervasive_code');
            $table->date('passport_expireDate')->nullable()->after('passport_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            //
        });
    }
}
