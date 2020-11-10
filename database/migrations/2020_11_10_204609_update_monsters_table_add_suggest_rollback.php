<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateMonstersTableAddSuggestRollback extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('monsters', function (Blueprint $table) {
            $table->tinyInteger('suggest_rollback')->after('vip')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('monsters', function (Blueprint $table) {
            $table->dropColumn('suggest_rollback');
        });
    }
}
