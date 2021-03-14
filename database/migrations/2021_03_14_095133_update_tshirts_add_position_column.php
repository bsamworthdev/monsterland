<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTshirtsAddPositionColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tshirts', function (Blueprint $table) {
            $table->enum('position',['high','middle','low'])->default('middle')->after('design_code');
            $table->string('entered_name')->nullable()->default(NULL);
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
            $table->dropColumn('position');
            $table->dropColumn('entered_name');
        });
    }
}
