<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalvagedSegments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salvaged_segments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('monster_id');
            $table->enum('segment',['head','body','legs']);
            $table->longText('image');
            $table->json('colors_used')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->string('created_by_session_id');
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
        Schema::dropIfExists('salvaged_segments');
    }
}
