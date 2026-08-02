<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $description = 'Produk preloved berkualitas dari koleksi Retro Junk. Kondisi barang terawat dengan baik.';

        $products = [
            // ===== CARGO / PANTS =====
            [
                'name' => 'Cargo Carfarmall',
                'slug' => 'cargo-carfarmall',
                'price' => 230000,
                'category' => 'pants',
                'description' => $description,
                'size' => '29/M',
                'waist_size' => '74 cm',
                'length' => '98 cm',
                'open_leg' => '22 cm (adjustable)',
                'code' => 'CG-14',
                'image' => '/images/cargo-carfarmall.png',
                'stock' => 1,
                'is_new_arrival' => true,
            ],
            [
                'name' => 'Cargo Short Change2118',
                'slug' => 'cargo-short-change2118',
                'price' => 130000,
                'category' => 'pants',
                'description' => $description,
                'size' => '30/M',
                'waist_size' => '78 cm',
                'length' => '55 cm',
                'open_leg' => '28 cm',
                'code' => 'CG-15',
                'image' => '/images/cargo-short-change2118.png',
                'stock' => 1,
                'is_new_arrival' => true,
            ],
            [
                'name' => 'Cargo Lafudge',
                'slug' => 'cargo-lafudge',
                'price' => 210000,
                'category' => 'pants',
                'description' => $description,
                'size' => '31/L',
                'waist_size' => '80 cm',
                'length' => '100 cm',
                'open_leg' => '24 cm',
                'code' => 'CG-16',
                'image' => '/images/cargo-lafudge.png',
                'stock' => 1,
                'is_new_arrival' => true,
            ],
            [
                'name' => 'Cargo Unbranded',
                'slug' => 'cargo-unbranded',
                'price' => 175000,
                'category' => 'pants',
                'description' => $description,
                'size' => '32/L',
                'waist_size' => '82 cm',
                'length' => '95 cm',
                'open_leg' => '20 cm',
                'code' => 'CG-17',
                'image' => '/images/cargo-unbranded.png',
                'stock' => 1,
                'is_new_arrival' => true,
            ],
            [
                'name' => 'Cargo Neo-Sixx',
                'slug' => 'cargo-neo-sixx',
                'price' => 185000,
                'category' => 'pants',
                'description' => $description,
                'size' => '30/M',
                'waist_size' => '76 cm',
                'length' => '99 cm',
                'open_leg' => '22 cm',
                'code' => 'CG-18',
                'image' => '/images/cargo-neo-sixx.png',
                'stock' => 1,
                'is_new_arrival' => true,
            ],
            [
                'name' => 'Cargo Denim Jungle Storm',
                'slug' => 'cargo-denim-jungle-storm-1784994386',
                'price' => 210000,
                'category' => 'pants',
                'description' => $description,
                'size' => '30',
                'waist_size' => '76cm',
                'length' => '106cm',
                'open_leg' => '22cm',
                'code' => 'CG-13',
                'image' => 'images/products/snapinstato-623851109-17976561092973039-5872548738493641411-n-1784994386.jpg',
                'stock' => 1,
                'is_new_arrival' => true,
            ],

            // ===== OUTERWEAR =====
            [
                'name' => 'Harrington Jacket Dino Passy',
                'slug' => 'harrington-jacket-dino-passy',
                'price' => 180000,
                'category' => 'outerwear',
                'description' => $description,
                'size' => 'M-L',
                'chest_width' => '58 cm',
                'body_length' => '60 cm',
                'sleeve_length' => '55 cm',
                'code' => 'OW-01',
                'image' => '/images/harrington-dino-passy.png',
                'stock' => 1,
                'is_new_arrival' => true,
            ],
            [
                'name' => 'Harrington Jacket Dunlop',
                'slug' => 'harrington-jacket-dunlop',
                'price' => 150000,
                'category' => 'outerwear',
                'description' => $description,
                'size' => 'M-L',
                'chest_width' => '60 cm',
                'body_length' => '64 cm',
                'sleeve_length' => '58 cm',
                'code' => 'OW-02',
                'image' => '/images/harrington-dunlop.png',
                'stock' => 1,
                'is_new_arrival' => true,
            ],
        ];

        foreach ($products as $p) {
            Product::updateOrCreate(['slug' => $p['slug']], $p);
        }
    }
}
