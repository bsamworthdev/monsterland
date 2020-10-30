<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateBookMonsterTableAddEditedColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('book_monster', function (Blueprint $table) {
            $table->string('legs_creator')->after('monster_id')->nullable()->default(NULL);
            $table->string('body_creator')->after('monster_id')->nullable()->default(NULL);
            $table->string('head_creator')->after('monster_id')->nullable()->default(NULL);
            $table->string('name')->after('monster_id')->nullable()->default(NULL);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('book_monster', function (Blueprint $table) {
            $table->dropColumn('legs_creator');
            $table->dropColumn('body_creator');
            $table->dropColumn('head_creator');
            $table->dropColumn('name');
        });
    }
}
