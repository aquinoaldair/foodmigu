<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeeklyMenuSelection extends Model
{
    protected $fillable = [
        'weekly_menu_day_id',
        'diner_id',
        'weekly_menu_day_item_id',
    ];

    public function diner()
    {
        return $this->belongsTo(Diner::class);
    }

    public function weeklyMenuDay()
    {
        return $this->belongsTo(WeeklyMenuDay::class);
    }

    public function weeklyMenuDayItem()
    {
        return $this->belongsTo(WeeklyMenuDayItem::class);
    }
}
