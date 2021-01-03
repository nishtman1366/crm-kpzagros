<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('name');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->foreignId('current_team_id')->nullable();
            $table->text('profile_photo_path')->nullable();
            $table->enum('level', ['ADMIN', 'AGENT']);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
        });
        \Illuminate\Support\Facades\DB::table('users')->insert([
            ['name' => 'مدیر سیستم', 'username' => 'admin', 'email' => 'admin@secd.ir', 'password' => \Illuminate\Support\Facades\Hash::make('admin'), 'level' => 'ADMIN'],
            ['parent_id' => 1, 'name' => 'نماینده شماره ۱', 'username' => 'agent1', 'email' => 'agent1@secd.ir', 'password' => \Illuminate\Support\Facades\Hash::make('agent1'), 'level' => 'AGENT'],
            ['parent_id' => 2, 'name' => 'بازاریاب شماره ۱', 'username' => 'marketer1', 'email' => 'marketer1@secd.ir', 'password' => \Illuminate\Support\Facades\Hash::make('marketer1'), 'level' => 'MARKETER'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
