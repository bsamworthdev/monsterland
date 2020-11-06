<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateInfoMessagesAddMemberStatusColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('info_messages', function (Blueprint $table) {
            $table->enum('member_status',['any','members','non-members'])->default('any')->after('user');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('info_messages', function (Blueprint $table) {
            $table->dropColumn('member_status');
        });
    }
}
