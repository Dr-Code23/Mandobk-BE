<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pharmacy_visits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('visitor_recipe_id');
            $table->foreign('visitor_recipe_id')
                ->on('visitor_recipes')
                ->references('id')
                ->onUpdate('cascade');

            $table->unsignedBigInteger('doctor_id');
            $table->foreign('doctor_id')
                ->on('users')
                ->references('id')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->unsignedBigInteger('pharmacy_id');
            $table->foreign('pharmacy_id')
                ->on('users')
                ->references('id')
                ->cascadeOnUpdate();

            $table->timestamp('created_at')->default(now());
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pharmacy_visits');
    }
};
