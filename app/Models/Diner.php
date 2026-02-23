<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Diner extends Model
{
    protected $fillable = [
        'dining_hall_id',
        'id_code',
        'name',
    ];

    public function diningHall()
    {
        return $this->belongsTo(DiningHall::class);
    }

    public function selections()
    {
        return $this->hasMany(WeeklyMenuSelection::class);
    }
}
