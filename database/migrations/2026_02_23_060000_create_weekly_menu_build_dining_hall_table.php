<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWeeklyMenuBuildDiningHallTable extends Migration
{
    public function up()
    {
        Schema::create('weekly_menu_build_dining_hall', function (Blueprint $table) {
            $table->id();
            $table->foreignId('weekly_menu_build_id')->constrained('weekly_menu_builds')->cascadeOnDelete();
            $table->foreignId('dining_hall_id')->constrained('dining_halls')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('weekly_menu_build_dining_hall');
    }
}
