<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePrivateGroupsTableAddCreatedByUserIdColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('private_groups', function (Blueprint $table) {
            $table->integer('created_by_user_id')->after('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('private_groups', function (Blueprint $table) {
            $table->dropColumn('created_by_user_id');
        });
    }
}
