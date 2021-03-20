<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableTshirts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tshirts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('monster_id')->default(0);
            $table->enum('color',['white','black','grey','blue','green','red','navy','sportgrey','darkheather'])->default('white');
            $table->enum('size',['XXL','XL','L','M','S','XS','unset']);
            $table->enum('gender',['mens','womens']);
            $table->integer('show_border')->default(0);
            $table->integer('show_name')->default(0);
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
        Schema::dropIfExists('tshirts');
    }
}
