<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('item_storages', function (Blueprint $table) {
            $table->id();
            $table->string('listing_type')->nullable();
            $table->unsignedBigInteger('user_id')->comment('user_id/seller_id');
            $table->unsignedBigInteger('categories_id');
            $table->unsignedBigInteger('sub_categories_id');
            $table->string('location')->nullable();
            $table->string('map_pin')->nullable();
            $table->text('exception_details')->nullable();
            $table->string('rate')->nullable();
            $table->integer('rented_max_allow_days')->nullable();
            $table->integer('blocked_days')->nullable();
            $table->integer('status')->default(0)->nullable()->comment('1 = approved, 0 = disapproved');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('categories_id')->references('id')->on('categories');
            $table->foreign('sub_categories_id')->references('id')->on('sub_categories');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_storage');
    }
};
