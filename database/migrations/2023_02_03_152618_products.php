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
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')
                ->on('users')
                ->references('id')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->string('com_name'); // Commercial Name
            $table->string('sc_name'); // Scientefic Name
            $table->unsignedDouble('pur_price'); // Purchasing price
            $table->unsignedDouble('sel_price'); // Selling Price
            $table->unsignedDouble('bonus'); // Bonus
            $table->unsignedDouble('con'); // Concentrate
            $table->string('barcode'); // Bar Code

            /* 
                Used To Find Total Purchases For That Product if 
                the total quantity changed in sale operation for example 
            */
            $table->string('original_total')->nullable();
            $table->boolean('limited')->default(false); // Limited Exchange
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
};
