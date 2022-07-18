<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessSubCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_sub_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Variables\BusinessCategory::class,'category_id')->constrained('business_categories')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('name');
            $table->string('code')->unique();
            $table->timestamps();
        });

        Schema::table('businesses', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Variables\BusinessCategory::class,'business_category_code')->nullable()->after('description')->constrained('business_categories')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignIdFor(\App\Models\Variables\BusinessSubCategory::class,'business_subCategory_code')->nullable()->after('business_category_code')->constrained('business_sub_categories')->cascadeOnUpdate()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('business_sub_categories');
    }
}
