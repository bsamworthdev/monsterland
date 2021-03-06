<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateMonsterSegmentsTableAddEmailOnCompleteColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('monster_segments', function (Blueprint $table) {
            $table->tinyInteger('email_on_complete')->after('image')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('monster_segments', function (Blueprint $table) {
            $table->dropColumn('email_on_complete');
        });
    }
}
