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
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')
                ->on('users')
                ->references('id')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')
                ->on('products')
                ->references('id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->enum('offer_duration', [0, 1, 2])->comment('0 => day , 1 => week , 2=>cheek');
            $table->unsignedBigInteger('pay_method');
            $table->foreign('pay_method')->on('pay_methods')->references('id');
            $table->double('bonus')->default('0');
            $table->enum('type', ['1', '2'])->comment('1=>Company Offer , 2=> Storehouse Offer');
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
        Schema::dropIfExists('offers');
    }
};
