<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Store extends Model
{
    protected $fillable = [
        'name',
        'location',
        'address',
        'phone',
        'whatsapp_number',
        'pin',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public static function siteWhatsapp(): string
    {
        $number = static::query()->where('is_default', true)->value('whatsapp_number')
            ?: static::query()->whereNotNull('whatsapp_number')->orderBy('id')->value('whatsapp_number');

        return preg_replace('/\D+/', '', (string) $number) ?? '';
    }
}
