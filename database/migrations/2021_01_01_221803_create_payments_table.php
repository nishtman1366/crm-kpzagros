<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Payments\Type::class)->nullable()->references('id')->on('payment_types')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(\App\Models\User::class)->nullable()->references('id')->on('users')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(\App\Models\Repairs\Repair::class)->nullable()->references('id')->on('repairs')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(\App\Models\Profiles\Profile::class)->nullable()->references('id')->on('profiles')->nullOnDelete()->cascadeOnUpdate();

            $table->string('amount');
            $table->string('req_code')->nullable();
            $table->string('ref_code')->nullable();
            $table->timestamp('date')->nullable();
            $table->string('tracking_code')->nullable();

            $table->unsignedTinyInteger('status')->default(0);

            $table->timestamps();

            $table->index('req_code');
            $table->index('ref_code');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
