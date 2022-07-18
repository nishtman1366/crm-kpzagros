<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBirthCertificateItemsToCustomers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('birth_crtfct_serial')->nullable()->after('id_code');
            $table->string('birth_crtfct_series_letter')->nullable()->after('birth_crtfct_serial');
            $table->string('birth_crtfct_series_number')->nullable()->after('birth_crtfct_series_letter');
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
