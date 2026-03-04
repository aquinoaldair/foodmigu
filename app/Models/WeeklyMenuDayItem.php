<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeeklyMenuDayItem extends Model
{
    protected $fillable = [
        'weekly_menu_day_id',
        'menu_category_id',
        'name',
        'description',
        'price',
        'display_order',
        'image_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function weeklyMenuDay()
    {
        return $this->belongsTo(WeeklyMenuDay::class);
    }

    public function menuCategory()
    {
        return $this->belongsTo(MenuCategory::class);
    }

    public function image()
    {
        return $this->belongsTo(Image::class);
    }

    public function selections()
    {
        return $this->hasMany(WeeklyMenuSelection::class);
    }
}
