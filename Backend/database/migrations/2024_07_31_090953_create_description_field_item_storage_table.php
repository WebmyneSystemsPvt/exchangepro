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
        Schema::table('item_storages', function (Blueprint $table) {
            $table->text('description')->after('location')->nullable();
            $table->text('default_storage_photo')->after('description')->nullable();
            $table->string('country')->after('default_storage_photo')->nullable();
            $table->string('state')->after('country')->nullable();
            $table->string('city')->after('state')->nullable();
            $table->string('pincode')->after('city')->nullable();
            $table->string('landmark')->after('pincode')->nullable();
            $table->string('latitude')->after('landmark')->nullable();
            $table->string('longitude')->after('latitude')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seller_details');
    }
};
