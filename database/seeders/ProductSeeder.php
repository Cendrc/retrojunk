<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // ===== CARGO / PANTS =====
            [
                'name' => 'Cargo Carfarmall',
                'price' => 230000,
                'category' => 'pants',
                'size' => '29/M',
                'waist_size' => '74 cm',
                'length' => '98 cm',
                'open_leg' => '22 cm (adjustable)',
                'code' => 'CG-14',
                'image' => '/images/cargo-carfarmall.png',
                'is_new_arrival' => true,
            ],
            [
                'name' => 'Cargo Short Change2118',
                'price' => 130000,
                'category' => 'pants',
                'size' => '30/M',
                'waist_size' => '78 cm',
                'length' => '55 cm',
                'open_leg' => '28 cm',
                'code' => 'CG-15',
                'image' => '/images/cargo-short-change2118.png',
                'is_new_arrival' => true,
            ],
            [
                'name' => 'Cargo Lafudge',
                'price' => 210000,
                'category' => 'pants',
                'size' => '31/L',
                'waist_size' => '80 cm',
                'length' => '100 cm',
                'open_leg' => '24 cm',
                'code' => 'CG-16',
                'image' => '/images/cargo-lafudge.png',
                'is_new_arrival' => true,
            ],
            [
                'name' => 'Cargo Unbranded',
                'price' => 175000,
                'category' => 'pants',
                'size' => '32/L',
                'waist_size' => '82 cm',
                'length' => '95 cm',
                'open_leg' => '20 cm',
                'code' => 'CG-17',
                'image' => '/images/cargo-unbranded.png',
                'is_new_arrival' => true,
            ],
            [
                'name' => 'Cargo Neo-Sixx',
                'price' => 185000,
                'category' => 'pants',
                'size' => '30/M',
                'waist_size' => '76 cm',
                'length' => '99 cm',
                'open_leg' => '22 cm',
                'code' => 'CG-18',
                'image' => '/images/cargo-neo-sixx.png',
                'is_new_arrival' => true,
            ],

            // ===== OUTERWEAR =====
            [
                'name' => 'Harrington Jacket Dino Passy',
                'price' => 180000,
                'category' => 'outerwear',
                'size' => 'M-L',
                'chest_width' => '58 cm',
                'body_length' => '60 cm',
                'sleeve_length' => '55 cm',
                'code' => 'OW-01',
                'image' => '/images/harrington-dino-passy.png',
                'is_new_arrival' => true,
            ],
            [
                'name' => 'Harrington Jacket Dunlop',
                'price' => 150000,
                'category' => 'outerwear',
                'size' => 'M-L',
                'chest_width' => '60 cm',
                'body_length' => '64 cm',
                'sleeve_length' => '58 cm',
                'code' => 'OW-02',
                'image' => '/images/harrington-dunlop.png',
                'is_new_arrival' => true,
            ],
        ];

        foreach ($products as $p) {
            Product::create([
                ...$p,
                'slug' => Str::slug($p['name']),
                'description' => 'Produk preloved berkualitas dari koleksi Retro Junk. Kondisi barang terawat dengan baik.',
                'stock' => 1,
            ]);
        }
    }
}