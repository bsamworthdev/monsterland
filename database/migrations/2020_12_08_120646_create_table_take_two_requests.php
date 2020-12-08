<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableTakeTwoRequests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('take_two_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('monster_id');
            $table->unsignedBigInteger('requested_by');
            $table->enum('from_segment',['head','body']);
            $table->enum('status',['pending','accepted','rejected']);
            $table->timestamps();

            $table->foreign('monster_id')->references('id')->on('monsters')->onDelete('cascade');
            $table->foreign('requested_by')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('monsters', function (Blueprint $table) {
            $table->tinyInteger('request_take_two')->after('approved_by_admin')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('take_two_requests');

        Schema::table('monsters', function (Blueprint $table) {
            $table->dropColumn('request_take_two');
        });
    }
}
