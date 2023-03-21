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
    Schema::create('transactions', function (Blueprint $table) {
        $table->id();
        $table->string('title', 100);
        $table->string('description')->nullable();
        $table->decimal('amount', 10, 4);
        $table->char('currency',4);
        $table->enum('type', ['fixed', 'recurring']);
        $table->enum('frequency', ['weekly', 'monthly', 'yearly'])->nullable();
        $table->dateTime('date')->nullable();
        $table->dateTime('start_date')->nullable();
        $table->dateTime('end_date')->nullable();
        $table->string('email', 255)->nullable();
        $table->boolean('Paid')->default(0);
        $table->timestamps();
        $table->unsignedBigInteger('category_id')->nullable();
        $table->foreign('category_id')->references('id')->on('categories');
        $table->softDeletes();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
