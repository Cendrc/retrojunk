<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name', 'slug', 'price', 'category', 'description',
        'size', 'waist_size', 'length', 'open_leg',
        'chest_width', 'body_length', 'sleeve_length',
        'code', 'image', 'images', 'stock', 'weight', 'is_new_arrival'
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

    /**
     * Total berat (gram) untuk sekumpulan product ID di keranjang.
     * Tiap entri keranjang dianggap qty 1 (barang preloved satuan).
     */
    public static function totalWeightFor(array $productIds): int
    {
        return (int) static::whereIn('id', $productIds)->sum('weight');
    }
}