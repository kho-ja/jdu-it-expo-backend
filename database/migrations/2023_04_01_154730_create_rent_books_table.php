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
        Schema::create('rent_books', function (Blueprint $table) {
            $table->id();
            $table->string('book_id');
            $table->string('book_code');
            $table->string('user_id');
            $table->string('give_date');
            $table->string('return_date');
            $table->string('given_by')->nullable();
            $table->string('taken_by')->nullable();
            $table->string('returned_date')->nullable();
            $table->text('lost_comment')->nullable();
            $table->text('summary')->nullable();
            $table->string('return_code')->nullable();
            $table->enum('status', ['Ijarada', "Yo`qolgan", 'Qabul qilindi'])->default('Ijarada');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rent_books');
    }
};
