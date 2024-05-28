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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('loginID')->unique()->nullable();
            $table->string('name')->nullable();
            $table->string('japan_group_id')->nullable();
            $table->string('it_group_id')->nullable();
            $table->string('password')->nullable();
            $table->text('info')->nullable();
            $table->string('status')->default('active');
            $table->string('image')->default('/storage/users/default.png');
            $table->integer('role_id')->default(1);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
