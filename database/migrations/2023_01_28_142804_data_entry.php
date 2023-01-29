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
        Schema::create('data_entry', function (Blueprint $table) {
            $table->id();
            $table->string('com_name')->unique(); // Commercial Name
            $table->string('sc_name')->unique(); // Scientefic Name
            $table->integer('qty'); // Quantity
            $table->double('pur_price'); // Purchasing price
            $table->double('sel_price'); // Selling Price
            $table->double('bonus'); // Bonus
            $table->double('con'); // Concentrate
            $table->string('patch_number'); // Patch Number
            $table->string('bar_code'); // QR Code
            $table->string('provider'); // Provider Name
            $table->boolean('limited')->default(false); // Limited Exchange
            $table->timestamp('entry_date')->default(now()); // Entry Date In
            $table->timestamp('expire_date')->default(now()); // Expire Date In
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
        Schema::dropIfExists('data_entry');
    }
};
