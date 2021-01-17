<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableTakeTwos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('take_twos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('monster_id');
            $table->string('from_segment');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('monster_id')->references('id')->on('monsters')->onDelete('cascade');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->integer('take_two_count')->after('peek_count')->default(3);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('take_twos');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('take_two_count');
        });
    }
}
