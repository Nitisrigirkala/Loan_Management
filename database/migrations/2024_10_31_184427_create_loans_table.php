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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 15, 2);
            $table->decimal('interest_rate', 5, 2);
            $table->integer('duration_years');
            $table->unsignedBigInteger('lender_id');
            $table->unsignedBigInteger('borrower_id');
            $table->foreign('lender_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('borrower_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
