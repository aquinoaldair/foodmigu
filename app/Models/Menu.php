<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = [
        'name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function categories()
    {
        return $this->belongsToMany(MenuCategory::class, 'menu_menu_category')
            ->withPivot('display_order')
            ->withTimestamps()
            ->orderByPivot('display_order');
    }
}
