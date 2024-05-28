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
        Schema::create('book_codes', function (Blueprint $table) {
            $table->id();
            $table->string('book_id');
            $table->string('code');
            $table->enum('status', ["Mavjud", "Yo`qolgan", "Ijarada"])->default("Mavjud");
            $table->string('given_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_codes');
    }
};
