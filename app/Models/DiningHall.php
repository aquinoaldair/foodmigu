<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiningHall extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function diners()
    {
        return $this->hasMany(Diner::class);
    }

    public function weeklyMenuBuilds()
    {
        return $this->belongsToMany(WeeklyMenuBuild::class, 'weekly_menu_build_dining_hall')
            ->withTimestamps();
    }
}
