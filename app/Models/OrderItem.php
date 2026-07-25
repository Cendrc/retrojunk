<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    /**
     * Kolom yang bisa diisi mass-assignment.
     * Semua kolom kecuali id, created_at, updated_at.
     */
    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'product_price',
        'product_image',
        'quantity',
        'subtotal',
    ];

    /**
     * Tipe data kolom untuk auto-casting.
     * Decimal disimpan sebagai string di PHP, jadi kita cast ke float untuk kemudahan.
     */
    protected $casts = [
        'product_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'quantity' => 'integer',
    ];

    /**
     * Relasi: OrderItem BELONGS TO Order.
     * Setiap order_item termasuk dalam satu order.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relasi: OrderItem BELONGS TO Product.
     * Setiap order_item mereferensikan satu product.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}