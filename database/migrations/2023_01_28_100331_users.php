<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('password');
            $table->unsignedBigInteger('role_id');
            $table->foreign('role_id')
                ->on('roles')
                ->references('id')
                ->onUpdate('cascade');

            $table->string('avatar')->nullable();
            $table->enum('status', ['0', '1', '2'])
                ->comment('0 => Deleted , 1=> Active , 2=> Frozen')
                ->default('2'); // Default Frozen Account
            $table->string('full_name');
            $table->string('phone')->nullable()->unique();
            $table->string('email')->nullable()->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
