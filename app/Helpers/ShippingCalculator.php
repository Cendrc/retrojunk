<?php

namespace App\Helpers;

class ShippingCalculator
{
    /**
     * Ongkos kirim per zona (dalam IDR)
     */
    public static function getZones()
    {
        return [
            'samarinda' => [
                'name' => 'Samarinda',
                'cost' => 10000,
                'estimated' => '1-2 hari',
                'cities' => ['KOTA SAMARINDA'],
            ],
            'kaltim' => [
                'name' => 'Kalimantan Timur',
                'cost' => 20000,
                'estimated' => '2-3 hari',
                'provinces' => ['KALIMANTAN TIMUR'],
            ],
            'kalimantan' => [
                'name' => 'Kalimantan',
                'cost' => 35000,
                'estimated' => '3-5 hari',
                'provinces' => ['KALIMANTAN BARAT', 'KALIMANTAN TENGAH', 'KALIMANTAN SELATAN', 'KALIMANTAN UTARA'],
            ],
            'jawa_bali' => [
                'name' => 'Jawa & Bali',
                'cost' => 30000,
                'estimated' => '3-5 hari',
                'provinces' => ['DKI JAKARTA', 'JAWA BARAT', 'JAWA TENGAH', 'JAWA TIMUR', 'DI YOGYAKARTA', 'BANTEN', 'BALI'],
            ],
            'sumatera' => [
                'name' => 'Sumatera',
                'cost' => 40000,
                'estimated' => '4-6 hari',
                'provinces' => ['ACEH', 'SUMATERA UTARA', 'SUMATERA BARAT', 'RIAU', 'KEPULAUAN RIAU', 'JAMBI', 'SUMATERA SELATAN', 'BANGKA BELITUNG', 'BENGKULU', 'LAMPUNG'],
            ],
            'sulawesi_ntb' => [
                'name' => 'Sulawesi & NTB',
                'cost' => 40000,
                'estimated' => '4-6 hari',
                'provinces' => ['SULAWESI UTARA', 'GORONTALO', 'SULAWESI TENGAH', 'SULAWESI BARAT', 'SULAWESI SELATAN', 'SULAWESI TENGGARA', 'NUSA TENGGARA BARAT'],
            ],
            'indonesia_timur' => [
                'name' => 'Indonesia Timur',
                'cost' => 60000,
                'estimated' => '5-10 hari',
                'provinces' => ['MALUKU', 'MALUKU UTARA', 'PAPUA', 'PAPUA BARAT', 'PAPUA BARAT DAYA', 'PAPUA TENGAH', 'PAPUA PEGUNUNGAN', 'PAPUA SELATAN', 'NUSA TENGGARA TIMUR'],
            ],
        ];
    }

    /**
     * Hitung ongkos kirim berdasarkan provinsi & kota
     */
    public static function calculate($province, $city = null, $subtotal = 0)
    {
        $province = strtoupper(trim($province));
        $city = strtoupper(trim($city ?? ''));
        $zones = self::getZones();

        // Cek Samarinda dulu (free shipping kalau > 500rb)
        if (str_contains($city, 'SAMARINDA') && $subtotal >= 500000) {
            return [
                'zone' => 'samarinda',
                'name' => 'Samarinda (GRATIS ONGKIR)',
                'cost' => 0,
                'estimated' => '1-2 hari',
                'free_shipping' => true,
            ];
        }

        if (str_contains($city, 'SAMARINDA')) {
            return [
                'zone' => 'samarinda',
                'name' => 'Samarinda',
                'cost' => 10000,
                'estimated' => '1-2 hari',
            ];
        }

        // Cek per zona berdasarkan provinsi
        foreach ($zones as $key => $zone) {
            if (isset($zone['provinces']) && in_array($province, $zone['provinces'])) {
                return [
                    'zone' => $key,
                    'name' => $zone['name'],
                    'cost' => $zone['cost'],
                    'estimated' => $zone['estimated'],
                ];
            }
        }

        // Default: zona terjauh
        return [
            'zone' => 'unknown',
            'name' => 'Lainnya',
            'cost' => 50000,
            'estimated' => '5-10 hari',
        ];
    }
}