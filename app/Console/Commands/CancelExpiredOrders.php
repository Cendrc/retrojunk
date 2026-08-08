<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

class CancelExpiredOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:cancel-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Batalkan otomatis order QRIS/Transfer Bank yang belum dibayar setelah batas waktu (payment_expires_at) lewat, dan kembalikan stok produknya.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $expiredOrders = Order::with('items')
            ->whereIn('payment_method', ['bank_transfer', 'qris'])
            ->whereIn('payment_status', ['unpaid', 'awaiting_verification'])
            ->where('order_status', '!=', 'cancelled')
            ->whereNotNull('payment_expires_at')
            ->where('payment_expires_at', '<', now())
            ->get();

        if ($expiredOrders->isEmpty()) {
            $this->info('Tidak ada order yang kedaluwarsa.');
            return;
        }

        foreach ($expiredOrders as $order) {
            $order->cancelAndRestoreStock();
            $this->info("Order #{$order->id} ({$order->tracking_code}) dibatalkan otomatis — batas waktu pembayaran sudah lewat.");
        }

        $this->info("Selesai. {$expiredOrders->count()} order dibatalkan otomatis.");
    }
}
