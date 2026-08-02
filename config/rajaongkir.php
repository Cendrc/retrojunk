<?php

return [
    'base_url' => 'https://rajaongkir.komerce.id/api/v1',
    'api_key' => env('RAJAONGKIR_API_KEY'),

    // Subdistrict ID asal pengiriman (lokasi toko). Cari ID-nya lewat:
    // php artisan shipping:search-origin "nama kecamatan/kota"
    'origin_id' => env('RAJAONGKIR_ORIGIN_ID'),

    // Kode kurir yang ditawarkan ke pembeli. Tier gratis Komerce cuma
    // support jne, tiki, pos — tambah kode lain (jnt, sicepat, anteraja,
    // dst) di .env kalau paket Komerce sudah upgrade.
    'couriers' => array_filter(explode(',', env('RAJAONGKIR_COURIERS', 'jne,tiki,pos'))),
];
