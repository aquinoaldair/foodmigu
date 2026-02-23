<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeeklyMenuBuild extends Model
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';

    protected $fillable = [
        'title',
        'menu_id',
        'start_date',
        'end_date',
        'status',
        'published_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'published_at' => 'datetime',
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function days()
    {
        return $this->hasMany(WeeklyMenuDay::class, 'weekly_menu_build_id');
    }
}
