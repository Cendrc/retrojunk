<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\RajaOngkirService;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    public function __construct(private RajaOngkirService $rajaOngkir)
    {
    }

    /**
     * Autocomplete pencarian kecamatan/kelurahan tujuan.
     * Dipanggil dari form checkout — API key RajaOngkir tetap di server,
     * tidak pernah dikirim ke browser.
     */
    public function search(Request $request)
    {
        $query = trim((string) $request->query('q', ''));

        if (strlen($query) < 3) {
            return response()->json(['data' => []]);
        }

        return response()->json(['data' => $this->rajaOngkir->searchDestination($query)]);
    }

    /**
     * Hitung ongkir real ke tujuan yang dipilih, berdasarkan berat
     * produk yang ada di keranjang session saat ini.
     */
    public function cost(Request $request)
    {
        $request->validate([
            'destination' => 'required|integer',
        ]);

        $origin = config('rajaongkir.origin_id');
        if (!$origin) {
            return response()->json([
                'message' => 'Origin pengiriman belum dikonfigurasi. Hubungi admin toko.',
            ], 500);
        }

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return response()->json(['message' => 'Keranjang kosong.'], 422);
        }

        $weight = Product::totalWeightFor(array_column($cart, 'id'));

        $options = $this->rajaOngkir->calculateCost(
            (int) $origin,
            (int) $request->destination,
            $weight
        );

        if (empty($options)) {
            return response()->json([
                'message' => 'Tidak ada layanan kurir yang tersedia untuk tujuan ini. Coba pilih ulang alamat.',
            ], 422);
        }

        return response()->json(['data' => $options, 'weight' => $weight]);
    }
}
