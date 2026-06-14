<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name', 'slug', 'price', 'category', 'description',
        'size', 'waist_size', 'length', 'open_leg',
        'chest_width', 'body_length', 'sleeve_length',
        'code', 'image', 'images', 'stock', 'is_new_arrival'
    ];

    protected $casts = [
        'images' => 'array',
        'is_new_arrival' => 'boolean',
    ];

    public function getFormattedPriceAttribute()
    {
        return 'IDR ' . number_format($this->price, 0, ',', '.');
    }

    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeNewArrivals($query)
    {
        return $query->where('is_new_arrival', true);
    }
}