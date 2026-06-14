<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    protected $fillable = [
        'user_id', 'session_id', 'tracking_code', 'email', 'name', 'phone',
        'address', 'province', 'city', 'district', 'postal_code',
        'items', 'subtotal', 'shipping_cost', 'shipping_zone', 'total',
        'payment_method', 'payment_proof', 'courier', 'tracking_number',
        'status', 'notes', 'confirmed_at', 'shipped_at', 'delivered_at',
    ];

    protected $casts = [
        'items'         => 'array',
        'confirmed_at'  => 'datetime',
        'shipped_at'    => 'datetime',
        'delivered_at'  => 'datetime',
    ];

    /**
     * Auto-generate tracking code saat order dibuat
     */
    protected static function booted()
    {
        static::creating(function ($order) {
            if (empty($order->tracking_code)) {
                $order->tracking_code = self::generateTrackingCode();
            }
        });
    }

    public static function generateTrackingCode()
    {
        do {
            $code = 'RJ-' . date('Y') . '-' . strtoupper(Str::random(6));
        } while (self::where('tracking_code', $code)->exists());

        return $code;
    }

    public function getPaymentMethodLabelAttribute()
    {
        return match($this->payment_method) {
            'qris' => 'QRIS',
            'bank_transfer' => 'Transfer Bank',
            'cod' => 'Cash on Delivery',
            default => 'Unknown',
        };
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pending'   => 'Menunggu Konfirmasi',
            'confirmed' => 'Dikonfirmasi',
            'shipped'   => 'Dalam Pengiriman',
            'delivered' => 'Terkirim',
            'cancelled' => 'Dibatalkan',
            default => 'Unknown',
        };
    }

    public function getCourierLabelAttribute()
    {
        return match($this->courier) {
            'jne'           => 'JNE',
            'jnt'           => 'J&T Express',
            'sicepat'       => 'SiCepat',
            'anteraja'      => 'AnterAja',
            'pos'           => 'Pos Indonesia',
            'tiki'          => 'TIKI',
            'custom'        => 'Kurir Lainnya',
            default => $this->courier,
        };
    }

    public function getCourierTrackingUrlAttribute()
    {
        if (!$this->tracking_number) return null;

        return match($this->courier) {
            'jne'      => 'https://www.jne.co.id/id/tracking/trace/' . $this->tracking_number,
            'jnt'      => 'https://www.jet.co.id/track/' . $this->tracking_number,
            'sicepat'  => 'https://www.sicepat.com/checkAwb/' . $this->tracking_number,
            'anteraja' => 'https://anteraja.id/tracking/' . $this->tracking_number,
            'pos'      => 'https://www.posindonesia.co.id/id/tracking/' . $this->tracking_number,
            'tiki'     => 'https://www.tiki.id/id/tracking/' . $this->tracking_number,
            default    => null,
        };
    }

    /**
     * Generate tracking steps untuk timeline visual
     */
    public function getTrackingStepsAttribute()
    {
        $steps = [
            [
                'key' => 'pending',
                'label' => 'Pesanan Dibuat',
                'description' => 'Pesanan kamu telah diterima',
                'icon' => 'package',
                'date' => $this->created_at,
                'completed' => true,
            ],
            [
                'key' => 'confirmed',
                'label' => 'Dikonfirmasi',
                'description' => 'Pembayaran diverifikasi & pesanan sedang diproses',
                'icon' => 'check',
                'date' => $this->confirmed_at,
                'completed' => in_array($this->status, ['confirmed', 'shipped', 'delivered']),
            ],
            [
                'key' => 'shipped',
                'label' => 'Dalam Pengiriman',
                'description' => 'Pesanan sudah dikirim via kurir',
                'icon' => 'truck',
                'date' => $this->shipped_at,
                'completed' => in_array($this->status, ['shipped', 'delivered']),
            ],
            [
                'key' => 'delivered',
                'label' => 'Pesanan Sampai',
                'description' => 'Pesanan telah diterima customer',
                'icon' => 'home',
                'date' => $this->delivered_at,
                'completed' => $this->status === 'delivered',
            ],
        ];

        return $steps;
    }
}