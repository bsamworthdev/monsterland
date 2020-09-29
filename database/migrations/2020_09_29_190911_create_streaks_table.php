<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStreaksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('streaks', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->integer('current_streak')->default(0);
            $table->integer('top_streak')->default(0);
            $table->dateTime('top_streak_at')->nullable()->default(null);
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
        Schema::dropIfExists('streaks');
    }
}
