<?php

namespace App\Console\Commands;

use App\Services\RajaOngkirService;
use Illuminate\Console\Command;

class ShippingSearchOrigin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shipping:search-origin {query : Nama kecamatan/kota, mis. "balikpapan tengah"}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cari subdistrict ID RajaOngkir buat diisi ke RAJAONGKIR_ORIGIN_ID di .env';

    public function handle(RajaOngkirService $rajaOngkir): int
    {
        if (!config('rajaongkir.api_key')) {
            $this->error('RAJAONGKIR_API_KEY belum diisi di .env.');
            return self::FAILURE;
        }

        $results = $rajaOngkir->searchDestination($this->argument('query'), limit: 100);

        if (empty($results)) {
            $this->warn('Tidak ada hasil. Coba kata kunci lain.');
            return self::FAILURE;
        }

        $this->table(
            ['ID', 'Label'],
            array_map(fn ($r) => [$r['id'], $r['label']], $results)
        );

        $this->line('');
        $this->info('Isi RAJAONGKIR_ORIGIN_ID di .env dengan ID yang sesuai lokasi toko.');

        return self::SUCCESS;
    }
}
