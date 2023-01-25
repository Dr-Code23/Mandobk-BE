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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('com_name'); // Commercial Name
            $table->string('sc_name'); // Scientefic Name
            $table->integer('qty'); // Quantity
            $table->double('pur_price'); // Purchasing price
            $table->double('sel_price'); // Selling Price
            $table->double('bonus'); // Bonus
            $table->double('con'); // Concentrate
            $table->string('patch_number'); // Patch Number
            $table->string('qr_code'); // QR Code
            $table->string('provider'); // Provider Name
            $table->timestamp('created_at')->default(now()); // Created At
            $table->timestamp('expire_in')->default(now()); // Expire In
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categories');
    }
};
