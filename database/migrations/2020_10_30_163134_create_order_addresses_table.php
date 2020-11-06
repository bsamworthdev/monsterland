<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_addresses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->enum('title',['', 'Mr', 'Mrs', 'Ms', 'Dr'])->default('');
            $table->string('firstname', 255);
            $table->string('surname', 255);
            $table->string('address1', 255);
            $table->string('address2', 255)->nullable()->default(NULL);
            $table->string('town', 255);
            $table->string('postcode', 15);
            $table->string('email', 255);
            $table->string('phone', 255)->nullable()->default(NULL);
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_addresses');
    }
}
