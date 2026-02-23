<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuCategory extends Model
{
    public const TYPE_NONE = 'none';
    public const TYPE_SINGLE = 'single';
    public const TYPE_MULTIPLE = 'multiple';

    protected $fillable = [
        'name',
        'selection_type',
        'is_required',
        'is_active',
        'display_order',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'menu_menu_category')
            ->withPivot('display_order')
            ->withTimestamps();
    }

    public static function selectionTypes(): array
    {
        return [
            'none' => 'Solo informativo',
            'single' => 'Selección única',
            'multiple' => 'Selección múltiple',
        ];
    }
}
