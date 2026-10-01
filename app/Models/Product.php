<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'store_id',
        'category_id',
        'name',
        'slug',
        'description',
        'designer',
        'fabric',
        'fit',
        'colors',
        'available_sizes',
        'show_price',
        'price',
        'stock',
        'stock_threshold',
        'sku',
        'care_instructions',
        'admin_note',
        'tags',
        'status',
    ];

    protected $hidden = [
        'admin_note',
    ];

    protected function casts(): array
    {
        return [
            'colors' => 'array',
            'available_sizes' => 'array',
            'tags' => 'array',
            'show_price' => 'boolean',
            'price' => 'decimal:2',
            'stock' => 'integer',
            'stock_threshold' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Product $product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name).'-'.Str::random(4);
            }
        });
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function primaryImage(): ?ProductImage
    {
        return $this->images->firstWhere('is_primary', true) ?? $this->images->first();
    }

    public function getPrimaryImageUrlAttribute(): ?string
    {
        $image = $this->relationLoaded('images')
            ? ($this->images->firstWhere('is_primary', true) ?? $this->images->first())
            : $this->images()->where('is_primary', true)->first() ?? $this->images()->first();

        return $image ? Storage::disk('public')->url($image->path) : null;
    }
}
