<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Order extends Model
{
    protected $fillable = [
        'user_id', 'session_id', 'tracking_code', 'email', 'name', 'phone',
        'address', 'province', 'city', 'district', 'subdistrict', 'postal_code',
        'subtotal', 'shipping_cost', 'shipping_zone', 'total',
        'payment_method', 'payment_proof', 'courier', 'shipping_service', 'tracking_number',
        'order_status', 'payment_status',
        'notes', 'confirmed_at', 'shipped_at', 'delivered_at',
        'midtrans_transaction_id', 'midtrans_payment_type', 'va_bank', 'va_number',
        'qr_code_url', 'midtrans_paid_at', 'payment_expires_at',
    ];

    protected $casts = [
        'confirmed_at'  => 'datetime',
        'shipped_at'    => 'datetime',
        'delivered_at'  => 'datetime',
        'midtrans_paid_at' => 'datetime',
        'payment_expires_at' => 'datetime',
    ];

    /**
     * ============================================================
     * RELASI
     * ============================================================
     */

    /**
     * Relasi: Order HAS MANY OrderItem
     * Satu order dapat berisi satu atau lebih order_item.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Batalkan order & kembalikan stok produk yang sudah dikurangi saat
     * checkout. Dipakai baik untuk pembatalan manual oleh pelanggan
     * maupun pembatalan otomatis oleh sistem (order kedaluwarsa).
     */
    public function cancelAndRestoreStock(): void
    {
        DB::transaction(function () {
            foreach ($this->items as $item) {
                if ($item->product_id) {
                    Product::where('id', $item->product_id)->increment('stock', $item->quantity);
                }
            }

            $this->update(['order_status' => 'cancelled']);
        });
    }

    /**
     * ============================================================
     * BOOTED / EVENT HOOKS
     * ============================================================
     */

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

    /**
     * ============================================================
     * HELPERS
     * ============================================================
     */

    public static function generateTrackingCode()
    {
        do {
            $code = 'RJ-' . date('Y') . '-' . strtoupper(Str::random(6));
        } while (self::where('tracking_code', $code)->exists());

        return $code;
    }

    /**
     * ============================================================
     * ACCESSORS (Label & UI Helpers)
     * ============================================================
     */

    public function getPaymentMethodLabelAttribute()
    {
        return match($this->payment_method) {
            'qris' => 'QRIS',
            'bank_transfer' => 'Transfer Bank',
            'cod' => 'Cash on Delivery',
            default => 'Unknown',
        };
    }

    /**
     * Label untuk order_status (alur pesanan)
     */
    public function getOrderStatusLabelAttribute()
    {
        return match($this->order_status) {
            'pending'   => 'Menunggu Konfirmasi',
            'confirmed' => 'Dikonfirmasi',
            'shipped'   => 'Dalam Pengiriman',
            'delivered' => 'Terkirim',
            'cancelled' => 'Dibatalkan',
            default => 'Unknown',
        };
    }

    /**
     * Label untuk payment_status (alur pembayaran)
     */
    public function getPaymentStatusLabelAttribute()
    {
        return match($this->payment_status) {
            'unpaid'                => 'Belum Dibayar',
            'awaiting_verification' => 'Menunggu Verifikasi',
            'paid'                  => 'Sudah Dibayar',
            'rejected'              => 'Pembayaran Ditolak',
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
     * Menggunakan order_status untuk alur pengiriman.
     */
    public function getTrackingStepsAttribute()
    {
        if ($this->order_status === 'cancelled') {
            return [
                [
                    'key' => 'pending',
                    'label' => 'Pesanan Dibuat',
                    'description' => 'Pesanan kamu telah diterima',
                    'icon' => 'package',
                    'date' => $this->created_at,
                    'completed' => true,
                    'cancelled' => false,
                ],
                [
                    'key' => 'cancelled',
                    'label' => 'Pesanan Dibatalkan',
                    'description' => 'Pesanan ini telah dibatalkan dan tidak diproses lebih lanjut',
                    'icon' => 'cancel',
                    'date' => $this->updated_at,
                    'completed' => true,
                    'cancelled' => true,
                ],
            ];
        }

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
                'completed' => in_array($this->order_status, ['confirmed', 'shipped', 'delivered']),
            ],
            [
                'key' => 'shipped',
                'label' => 'Dalam Pengiriman',
                'description' => 'Pesanan sudah dikirim via kurir',
                'icon' => 'truck',
                'date' => $this->shipped_at,
                'completed' => in_array($this->order_status, ['shipped', 'delivered']),
            ],
            [
                'key' => 'delivered',
                'label' => 'Pesanan Sampai',
                'description' => 'Pesanan telah diterima customer',
                'icon' => 'home',
                'date' => $this->delivered_at,
                'completed' => $this->order_status === 'delivered',
            ],
        ];

        return $steps;
    }
}