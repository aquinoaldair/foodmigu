<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $fillable = [
        'name',
        'path',
    ];

    protected $appends = ['url'];

    public function weeklyMenuDayItems()
    {
        return $this->hasMany(WeeklyMenuDayItem::class, 'image_id');
    }

    /**
     * Get the full URL for the image.
     */
    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->path);
    }
}
