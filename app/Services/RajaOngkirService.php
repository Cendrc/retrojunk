<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RajaOngkirService
{
    private function headers(): array
    {
        return [
            'key' => config('rajaongkir.api_key'),
            'Accept' => 'application/json',
        ];
    }

    /**
     * Cari kecamatan/kelurahan tujuan berdasarkan kata kunci.
     * Dipakai buat autocomplete alamat di checkout & saat cari origin toko.
     */
    public function searchDestination(string $query, int $limit = 10): array
    {
        $response = Http::withHeaders($this->headers())
            ->get(config('rajaongkir.base_url') . '/destination/domestic-destination', [
                'search' => $query,
                'limit' => $limit,
                'offset' => 0,
            ]);

        if (!$response->successful()) {
            Log::warning('RajaOngkir searchDestination gagal', [
                'query' => $query,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return [];
        }

        return $response->json('data') ?? [];
    }

    /**
     * Hitung ongkir dari origin (subdistrict ID toko) ke destination
     * (subdistrict ID tujuan) untuk berat tertentu, digabung dari semua
     * kurir yang dikonfigurasi di config('rajaongkir.couriers').
     *
     * Return: array of ['courier_code', 'courier_name', 'service', 'description', 'cost', 'etd']
     */
    public function calculateCost(int $origin, int $destination, int $weightGrams): array
    {
        $weightGrams = max($weightGrams, 1);
        $options = [];

        foreach (config('rajaongkir.couriers') as $courier) {
            $response = Http::asForm()->withHeaders($this->headers())
                ->post(config('rajaongkir.base_url') . '/calculate/domestic-cost', [
                    'origin' => $origin,
                    'destination' => $destination,
                    'weight' => $weightGrams,
                    'courier' => $courier,
                ]);

            if (!$response->successful()) {
                Log::warning('RajaOngkir calculateCost gagal untuk kurir ' . $courier, [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                continue;
            }

            foreach ($response->json('data') ?? [] as $service) {
                $description = $service['description'] ?? '';
                $serviceName = $service['service'] ?? '';

                // Skip layanan kargo/trucking/motor — tidak relevan untuk
                // paket baju/celana preloved yang ringan, harganya juga
                // bisa ratusan ribu sehingga membingungkan kalau ditampilkan.
                if (preg_match('/trucking|kargo|cargo|motor/i', $description . ' ' . $serviceName)) {
                    continue;
                }

                $options[] = [
                    'courier_code' => $service['code'] ?? $courier,
                    'courier_name' => $service['name'] ?? strtoupper($courier),
                    'service' => $serviceName,
                    'description' => $description,
                    'cost' => (int) ($service['cost'] ?? 0),
                    'etd' => $service['etd'] ?? '-',
                ];
            }
        }

        usort($options, fn ($a, $b) => $a['cost'] <=> $b['cost']);

        return $options;
    }
}
