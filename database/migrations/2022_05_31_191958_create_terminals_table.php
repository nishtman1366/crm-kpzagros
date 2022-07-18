<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTerminalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('terminals', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Profiles\Profile::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->enum('type',['POS','IPG','WPOS'])->default('POS');
            $table->string('terminal_number')->nullable();
            $table->foreignIdFor(\App\Models\Variables\DeviceConnectionType::class)->nullable()->constrained()->nullOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(\App\Models\Variables\DeviceType::class)->nullable()->constrained()->nullOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(\App\Models\Variables\Device::class)->nullable()->constrained()->nullOnDelete()->cascadeOnUpdate();
            $table->text('reject_reason')->nullable();
            $table->text('cancel_reason')->nullable();
            $table->unsignedBigInteger('new_device_type_id')->nullable();
            $table->text('change_reason')->nullable();
            $table->unsignedBigInteger('new_device_id')->nullable();
            $table->unsignedBigInteger('status')->default(0);
            $table->date('setup_date')->nullable();
            $table->string('access_address')->nullable();
            $table->string('access_port')->nullable();
            $table->string('callback_address')->nullable();
            $table->string('callback_port')->nullable();
            $table->string('description')->nullable();

            $table->enum('device_sell_type', ['cash', 'installment', 'dept'])->nullable();
            $table->string('device_amount')->nullable();
            $table->string('device_dept_profile_id')->nullable();
            $table->enum('device_physical_status', ['new', 'stock']);

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
        Schema::dropIfExists('terminals');
    }
}
