<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\User::class)->nullable()->references('id')->on('users')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(\App\Models\Posts\Category::class, 'post_category_id')->references('id')->on('post_categories')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('title');
            $table->text('body');
            $table->boolean('status')->default(1);
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
        Schema::dropIfExists('posts');
    }
}
