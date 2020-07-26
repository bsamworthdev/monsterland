<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateMonsterSegmentsTableAddCreatedBySessionIdColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('monster_segments', function (Blueprint $table) {
            $table->string('created_by_session_id',255)->after('created_by')->nullable()->default(NULL);
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
            $table->dropColumn('created_by_session_id');
        });
    }
}
