<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWeeklyMenuSelectionsTable extends Migration
{
    public function up()
    {
        Schema::create('weekly_menu_selections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('weekly_menu_day_id')->constrained()->cascadeOnDelete();
            $table->foreignId('diner_id')->constrained()->cascadeOnDelete();
            $table->foreignId('weekly_menu_day_item_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->index(['diner_id', 'weekly_menu_day_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('weekly_menu_selections');
    }
}
