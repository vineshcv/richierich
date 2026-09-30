<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'show_share',
        'show_whatsapp',
        'show_cart',
        'show_view',
        'whatsapp_number',
        'store_name',
    ];

    protected function casts(): array
    {
        return [
            'show_share' => 'boolean',
            'show_whatsapp' => 'boolean',
            'show_cart' => 'boolean',
            'show_view' => 'boolean',
        ];
    }
}
