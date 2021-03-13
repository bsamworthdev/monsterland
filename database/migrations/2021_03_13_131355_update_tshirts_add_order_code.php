<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTshirtsAddOrderCode extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tshirts', function (Blueprint $table) {
            $table->string('design_code', 6)->after('monster_id')->nullable()->default(NULL);
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
            $table->dropColumn('design_code');
        });
    }
}
