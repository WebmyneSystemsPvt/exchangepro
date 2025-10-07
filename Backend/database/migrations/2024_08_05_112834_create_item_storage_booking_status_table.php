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
        Schema::create('item_storage_booking_status', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_storage_id');
            $table->unsignedBigInteger('borrower_id');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('booking_status', ['pending', 'booked', 'rejected'])->default('pending');
            $table->foreign('item_storage_id')->references('id')->on('item_storages');
            $table->foreign('borrower_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_storage_booking_status');
    }
};
