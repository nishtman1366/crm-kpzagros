<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExtraInfoToBusinesses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->date('license_start_date')->nullable()->after('license_date');
            $table->string('description')->nullable()->after('license_start_date');
            $table->enum('ownership_type', ['owner', 'tenant'])->default('owner')->after('description');
            $table->string('rental_contract_number')->nullable()->after('ownership_type');
            $table->string('rental_expiry_date')->nullable()->after('rental_contract_number');
            $table->string('country_code')->default('IR')->after('rental_expiry_date');
            $table->enum('business_type', ['physical', 'physicalVirtual', 'virtual'])->default('physical')->after('country_code');
            $table->string('etrust_certificate_type')->nullable()->after('business_type');
            $table->date('etrust_certificate_issue_date')->nullable()->after('etrust_certificate_type');
            $table->date('etrust_certificate_expiry_date')->nullable()->after('etrust_certificate_issue_date');
            $table->string('email')->nullable()->after('etrust_certificate_expiry_date');
            $table->string('website')->nullable()->after('email');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('businesses', function (Blueprint $table) {
            //
        });
    }
}
