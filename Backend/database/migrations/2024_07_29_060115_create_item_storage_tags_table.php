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
        Schema::create('item_storage_tags', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_storage_id');
            $table->string('tag_name')->nullable();
            $table->timestamps();
            $table->foreign('item_storage_id')->references('id')->on('item_storages');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_storage_tags');
    }
};
