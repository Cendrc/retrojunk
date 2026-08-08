@extends('layouts.app')
@section('content')

<section class="about-page">
    <div class="about-page__inner">
        <div class="about-card">
            <h1>Tentang Kami</h1>
            <p>
                Retro Junk merupakan usaha yang bergerak di bidang penjualan pakaian preloved (bekas pakai)
                dengan gaya retro, yang berlokasi di Balikpapan, Kalimantan Timur. Usaha ini mulai beroperasi
                sejak bulan Juli 2025. Ide awal berdirinya Retro Junk bermula dari banyaknya pakaian pribadi
                milik pemilik usaha yang sudah tidak terpakai, namun masih dalam kondisi layak digunakan.
                Berangkat dari keinginan untuk mendaur ulang (recycle) barang-barang tersebut agar tidak
                terbuang sia-sia, pemilik usaha kemudian memutuskan untuk menjualnya kembali kepada
                masyarakat yang tertarik dengan gaya fashion retro.
            </p>
        </div>
    </div>
</section>

<style>
.about-page {
    background: var(--olive);
    min-height: calc(100vh - var(--nav-h));
    padding: 3rem 2rem;
}

.about-page__inner {
    max-width: 800px;
    margin: 0 auto;
}

.about-card {
    background: var(--cream);
    padding: 2.5rem;
    border-radius: var(--radius);
    box-shadow: var(--shadow);
}

.about-card h1 {
    font-family: var(--font-display);
    color: var(--text-dark);
    margin-bottom: 1.5rem;
}

.about-card p {
    color: var(--text-mid);
    line-height: 1.8;
    font-size: 1.02rem;
}
</style>

@endsection
