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
            $table->enum('status',['', 'Mr', 'Mrs', 'Ms', 'Dr'])->default('');
            $table->string('Firstname', 255);
            $table->string('Surname', 255);
            $table->string('Address1', 255);
            $table->string('Address2', 255)->nullable()->default(NULL);
            $table->string('Town', 255);
            $table->string('Postcode', 15);
            $table->string('Email', 255);
            $table->string('Phone', 255)->nullable()->default(NULL);
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
