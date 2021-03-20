<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTshirtsAddUserIdSessionIdColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tshirts', function (Blueprint $table) {
            $table->string('session_id')->nullable()->default(NULL)->after('id');
            $table->unsignedBigInteger('user_id')->default(0)->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tshirts', function (Blueprint $table) {
            $table->dropColumn('session_id');
            $table->dropColumn('user_id');
        });
    }
}
