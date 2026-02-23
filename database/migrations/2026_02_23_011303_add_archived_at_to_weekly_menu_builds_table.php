<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddArchivedAtToWeeklyMenuBuildsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('weekly_menu_builds', function (Blueprint $table) {
            $table->dateTime('archived_at')->nullable()->after('published_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('weekly_menu_builds', function (Blueprint $table) {
            $table->dropColumn('archived_at');
        });
    }
}
