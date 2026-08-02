<?php

namespace App\Services;

use App\Models\Order;
use Midtrans\Config;
use Midtrans\CoreApi;
use Illuminate\Support\Facades\Log;

class MidtransService
{
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
        ];

        // DEBUG SEMENTARA — hapus setelah masalah selesai
        Log::info('Midtrans DEBUG', [
            'server_key_masked' => substr(config('midtrans.server_key'), 0, 14) . '...' . substr(config('midtrans.server_key'), -4),
            'server_key_length' => strlen(config('midtrans.server_key')),
            'is_production' => config('midtrans.is_production'),
            'config_server_key' => Config::$serverKey,
            'config_is_production' => Config::$isProduction,
        ]);

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
        ];

        // DEBUG SEMENTARA — hapus setelah masalah selesai
        Log::info('Midtrans DEBUG', [
            'server_key_masked' => substr(config('midtrans.server_key'), 0, 14) . '...' . substr(config('midtrans.server_key'), -4),
            'server_key_length' => strlen(config('midtrans.server_key')),
            'is_production' => config('midtrans.is_production'),
            'config_server_key' => Config::$serverKey,
            'config_is_production' => Config::$isProduction,
        ]);

        $response = CoreApi::charge($params);
        $vaNumber = $response->va_numbers[0]->va_number ?? null;

        $order->update([
            'midtrans_transaction_id' => $response->transaction_id,
            'midtrans_payment_type' => 'bank_transfer',
            'va_bank' => $bank,
            'va_number' => $vaNumber,
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