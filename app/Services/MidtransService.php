<?php

namespace App\Services;

use App\Models\Order;
use Midtrans\Config;
use Midtrans\CoreApi;

class MidtransService
{
    /**
     * Batas waktu pembayaran untuk QRIS & Transfer Bank. Tanpa custom_expiry,
     * default Midtrans beda-beda per metode (QRIS 15 menit, Bank Transfer/VA
     * 24 jam) — disamakan jadi 24 jam untuk keduanya di sini.
     */
    private const EXPIRY_HOURS = 24;

    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    /**
     * Buat transaksi QRIS di Midtrans untuk sebuah order,
     * simpan URL gambar QR ke tabel orders.
     */
    public function chargeQris(Order $order): Order
    {
        $params = [
            'payment_type' => 'qris',
            'transaction_details' => [
                'order_id' => $this->midtransOrderId($order),
                'gross_amount' => (int) $order->total,
            ],
            'customer_details' => [
                'first_name' => $order->name,
                'email' => $order->email,
                'phone' => $order->phone,
            ],
            'custom_expiry' => [
                'expiry_duration' => self::EXPIRY_HOURS,
                'unit' => 'hour',
            ],
        ];

        $response = CoreApi::charge($params);
        $qrUrl = null;
        foreach ($response->actions as $action) {
            if ($action->name === 'generate-qr-code') {
                $qrUrl = $action->url;
                break;
            }
        }

        $order->update([
            'midtrans_transaction_id' => $response->transaction_id,
            'midtrans_payment_type' => 'qris',
            'qr_code_url' => $qrUrl,
            'payment_expires_at' => now()->addHours(self::EXPIRY_HOURS),
        ]);

        return $order;
    }

    /**
     * Buat transaksi Bank Transfer (Virtual Account) BCA di Midtrans.
     */
    public function chargeBankTransfer(Order $order, string $bank = 'bca'): Order
    {
        $params = [
            'payment_type' => 'bank_transfer',
            'transaction_details' => [
                'order_id' => $this->midtransOrderId($order),
                'gross_amount' => (int) $order->total,
            ],
            'bank_transfer' => [
                'bank' => $bank,
            ],
            'customer_details' => [
                'first_name' => $order->name,
                'email' => $order->email,
                'phone' => $order->phone,
            ],
            'custom_expiry' => [
                'expiry_duration' => self::EXPIRY_HOURS,
                'unit' => 'hour',
            ],
        ];

        $response = CoreApi::charge($params);
        $vaNumber = $response->va_numbers[0]->va_number ?? null;

        $order->update([
            'midtrans_transaction_id' => $response->transaction_id,
            'midtrans_payment_type' => 'bank_transfer',
            'va_bank' => $bank,
            'va_number' => $vaNumber,
            'payment_expires_at' => now()->addHours(self::EXPIRY_HOURS),
        ]);

        return $order;
    }

    /**
     * order_id yang dikirim ke Midtrans wajib unik secara global.
     * Kombinasi ID order lokal + timestamp mencegah bentrok kalau
     * transaksi lama pernah dibuat lalu expired.
     */
    private function midtransOrderId(Order $order): string
    {
        return 'RJ-' . $order->id . '-' . now()->timestamp;
    }
}