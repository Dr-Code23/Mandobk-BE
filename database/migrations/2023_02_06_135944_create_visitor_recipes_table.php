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
    public function up()
    {
        Schema::create('visitor_recipes', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('visitor_id');
            $table->foreign('visitor_id')
                ->on('users')
                ->references('id')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->unsignedBigInteger('random_number')->unique();
            $table->string('alias');
            $table->json('details');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('visitor_recipes');
    }
};

/*

    id visitor_id random_number details
    id doctor_id visitor_recipe_id created_at updated_at


*/
