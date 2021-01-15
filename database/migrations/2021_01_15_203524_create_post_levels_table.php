<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostLevelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_levels', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Posts\Post::class)->references('id')->on('posts')->cascadeOnDelete()->cascadeOnUpdate();
            $table->enum('level', ['AGENT','MARKETER','SUPERUSER','TECHNICAL','OFFICE']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('levels');
    }
}
