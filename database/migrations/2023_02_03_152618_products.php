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
            $table->string('sc_name')->nullable(); // Scientific Name
            $table->unsignedDouble('pur_price'); // Purchasing price
            $table->unsignedDouble('sel_price'); // Selling Price
            $table->unsignedDouble('bonus'); // Bonus
            $table->unsignedDouble('con'); // Concentrate
            $table->string('barcode'); // Bar Code
            $table->unsignedTinyInteger('new_limited_value')->nullable();
            /*
                Used To Find Total Purchases For That Product if
                the total quantity changed in sale operation for example
            */
            $table->string('original_total')->nullable();
            $table->boolean('limited')->default(false); // Limited Exchange

            $table->timestamp('created_at')->default(now());
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
