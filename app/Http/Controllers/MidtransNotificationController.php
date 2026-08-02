<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Notification;

class MidtransNotificationController extends Controller
{
    public function handle(Request $request)
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');

        try {
            $notif = new Notification();
        } catch (\Exception $e) {
            Log::error('Midtrans notification parse error: ' . $e->getMessage());
            return response()->json(['status' => 'error'], 400);
        }

        // Verifikasi signature secara manual untuk keamanan tambahan
        $expectedSignature = hash('sha512',
            $notif->order_id . $notif->status_code . $notif->gross_amount . config('midtrans.server_key')
        );
        if ($expectedSignature !== $notif->signature_key) {
            Log::warning('Midtrans notification: signature tidak valid');
            return response()->json(['status' => 'invalid signature'], 403);
        }

        $order = Order::where('midtrans_transaction_id', $notif->transaction_id)->first();

        if (!$order) {
            Log::warning('Midtrans notification: order tidak ditemukan untuk transaction_id ' . $notif->transaction_id);
            return response()->json(['status' => 'order not found'], 404);
        }

        $status = $notif->transaction_status;
        $fraud = $notif->fraud_status ?? null;

        if ($status === 'capture' && $fraud === 'accept') {
            $order->update(['payment_status' => 'paid', 'midtrans_paid_at' => now()]);
        } elseif ($status === 'settlement') {
            $order->update(['payment_status' => 'paid', 'midtrans_paid_at' => now()]);
        } elseif (in_array($status, ['cancel', 'deny', 'expire'])) {
            $order->update(['payment_status' => 'rejected']);
        } elseif ($status === 'pending') {
            $order->update(['payment_status' => 'awaiting_verification']);
        }

        return response()->json(['status' => 'ok']);
    }
}