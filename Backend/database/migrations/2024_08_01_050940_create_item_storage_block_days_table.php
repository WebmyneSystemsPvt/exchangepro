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
        Schema::create('item_storage_block_days', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_storage_id');
            $table->date('block_days_date');
            $table->foreign('item_storage_id')->references('id')->on('item_storages');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_storage_block_days');
    }
};
