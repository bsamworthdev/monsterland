<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTablePrintfulMockups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('printful_mockups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('monster_id')->default(0);
            $table->enum('status',['pending','created','deleted'])->default('pending');
            $table->string('task_key')->nullable()->default(0);
            $table->string('url');
            $table->string('description')->nullable()->default(null);
            $table->timestamps();

            $table->foreign('monster_id')->references('id')->on('monsters')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('printful_mockups');
    }
}
