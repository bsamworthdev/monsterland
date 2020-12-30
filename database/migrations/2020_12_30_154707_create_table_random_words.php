<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableRandomWords extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('random_words', function (Blueprint $table) {
            $table->id();
            $table->enum('type',['adjective','noun','prefix','suffix']);
            $table->string('word',255);
            $table->tinyInteger('nsfw')->default(0);
            $table->bigInteger('added_by_user')->default(1);
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
        Schema::dropIfExists('random_words');
    }
}
