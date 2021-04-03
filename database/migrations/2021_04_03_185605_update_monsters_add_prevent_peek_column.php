<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateMonstersAddPreventPeekColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('monsters', function (Blueprint $table) {
            $table->tinyInteger('prevent_peek')->default(0)->after('vip');
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
            $table->dropColumn('prevent_peek');
        });
    }
}
