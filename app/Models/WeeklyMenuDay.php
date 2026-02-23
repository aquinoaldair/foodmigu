<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeeklyMenuDay extends Model
{
    protected $fillable = [
        'weekly_menu_build_id',
        'date',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function weeklyMenuBuild()
    {
        return $this->belongsTo(WeeklyMenuBuild::class);
    }

    public function items()
    {
        return $this->hasMany(WeeklyMenuDayItem::class, 'weekly_menu_day_id')->orderBy('display_order');
    }
}
