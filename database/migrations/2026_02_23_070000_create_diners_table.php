<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDinersTable extends Migration
{
    public function up()
    {
        Schema::create('diners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dining_hall_id')->constrained('dining_halls')->cascadeOnDelete();
            $table->string('id_code');
            $table->string('name');
            $table->timestamps();

            $table->unique(['dining_hall_id', 'id_code']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('diners');
    }
}
