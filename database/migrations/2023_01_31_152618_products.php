<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('role_id');
            $table->foreign('role_id')
                ->on('roles')
                ->references('id')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')
                ->on('users')
                ->references('id')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->string('com_name'); // Commercial Name
            $table->string('sc_name'); // Scientefic Name
            $table->unsignedBigInteger('qty'); // Quantity
            $table->unsignedDouble('pur_price'); // Purchasing price
            $table->unsignedDouble('sel_price'); // Selling Price
            $table->unsignedDouble('bonus'); // Bonus
            $table->unsignedDouble('con'); // Concentrate
            $table->string('patch_number'); // Patch Number
            $table->string('bar_code'); // QR Code
            $table->string('provider'); // Provider Name
            $table->boolean('limited')->default(false); // Limited Exchange
            $table->date('entry_date')->default(now()); // Entry Date In
            $table->date('expire_date')->default(now()); // Expire Date In
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
        Schema::dropIfExists('products');
    }
};
