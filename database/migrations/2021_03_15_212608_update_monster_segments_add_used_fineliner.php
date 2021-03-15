<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateMonsterSegmentsAddUsedFineliner extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('monster_segments', function (Blueprint $table) {
            $table->tinyInteger('fineliner_used')->default(0)->after('colors_used');
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
            $table->dropColumn('fineliner_used');
        });
    }
}
