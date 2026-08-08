@extends('layouts.app')
@section('content')

<section class="checkout-page">
    <a href="{{ route('home') }}" class="checkout-page__logo">
        <img src="{{ asset('images/logo.png') }}" alt="Retro Junk" class="checkout-page__logo-img">
    </a>

    <div class="checkout-page__layout">
        {{-- Form --}}
        <div class="checkout-form">
            <form action="{{ route('checkout.store') }}" method="POST" id="checkoutForm">
                @csrf

                {{-- Contact --}}
                <div class="form-section">
                    <div class="form-section__header">
                        <h2>Contact</h2>
                        @guest
                        <a href="{{ route('login') }}">Sign in</a>
                        @endguest
                    </div>
                    <div class="form-group">
                        <input type="email" name="email" class="form-input" placeholder="Email"
                               value="{{ auth()->user()->email ?? old('email') }}" required>
                        @error('email')<span class="form-error">{{ $message }}</span>@enderror
                    </div>
                </div>

                {{-- Delivery --}}
                <div class="form-section">
                    <h2>Delivery</h2>

                    @auth
                    @if(auth()->user()->addresses->count() > 0)
                    <div class="saved-address-list" id="savedAddressList">
                        @foreach(auth()->user()->addresses as $addr)
                        <label class="saved-address-option">
                            <input type="radio" name="address_choice" value="{{ $addr->id }}"
                                data-name="{{ $addr->name }}"
                                data-phone="{{ $addr->phone }}"
                                data-address="{{ $addr->address }}"
                                data-province="{{ $addr->province }}"
                                data-city="{{ $addr->city }}"
                                data-district="{{ $addr->district }}"
                                data-subdistrict="{{ $addr->subdistrict }}"
                                data-postal="{{ $addr->postal_code }}"
                                {{ $loop->first ? 'checked' : '' }}>
                            <div class="saved-address-option__content">
                                <div class="saved-address-option__label">
                                    {{ $addr->label }}
                                    @if($addr->is_default)<span class="saved-address-option__badge">Utama</span>@endif
                                </div>
                                <div class="saved-address-option__detail">
                                    {{ $addr->name }} — {{ $addr->phone }}<br>
                                    {{ Str::limit($addr->address, 60) }}, {{ $addr->district }}, {{ $addr->city }}
                                </div>
                            </div>
                        </label>
                        @endforeach

                        <label class="saved-address-option">
                            <input type="radio" name="address_choice" value="new">
                            <div class="saved-address-option__content">
                                <div class="saved-address-option__label">+ Alamat Baru</div>
                                <div class="saved-address-option__detail">Isi alamat pengiriman baru</div>
                            </div>
                        </label>
                    </div>
                    @endif
                    @endauth

                    <div id="manualAddressFields">
                        <div class="form-group">
                            <input type="text" name="name" id="nameInput" class="form-input" placeholder="Nama Lengkap"
                                value="{{ auth()->user()->name ?? old('name') }}" required>
                            @error('name')<span class="form-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group">
                            <input type="text" name="address" id="addressInput" class="form-input" placeholder="Alamat Lengkap (Jalan, No. Rumah, RT/RW)"
                                value="{{ old('address') }}" required>
                            @error('address')<span class="form-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group" style="position: relative;">
                            <input type="text" id="destinationSearch" class="form-input"
                                placeholder="Cari kecamatan/kota tujuan (contoh: Samarinda Ulu)"
                                value="{{ old('subdistrict') }}" autocomplete="off">
                            <div id="destinationResults" class="destination-results" style="display:none;"></div>
                            <div id="destinationSelected" class="destination-selected" style="display:none;"></div>

                            <input type="hidden" name="destination_id" id="destinationIdInput" value="{{ old('destination_id') }}">
                            <input type="hidden" name="province" id="provinceInput" value="{{ old('province') }}">
                            <input type="hidden" name="city" id="cityInput" value="{{ old('city') }}">
                            <input type="hidden" name="district" id="districtInput" value="{{ old('district') }}">
                            <input type="hidden" name="subdistrict" id="subdistrictInput" value="{{ old('subdistrict') }}">
                            <input type="hidden" name="postal_code" id="postalInput" value="{{ old('postal_code') }}">

                            @error('destination_id')<span class="form-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group">
                            <input type="tel" name="phone" id="phoneInput" class="form-input" placeholder="Nomor HP (contoh: 081234567890)"
                                value="{{ old('phone') }}" pattern="[0-9]{10,15}" maxlength="15"
                                inputmode="numeric" required
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            @error('phone')<span class="form-error">{{ $message }}</span>@enderror
                        </div>

                        @auth
                        <label class="save-address-check" id="saveAddressCheckWrap">
                            <input type="checkbox" name="save_address" value="1" id="saveAddressCheck">
                            Simpan alamat ini untuk checkout berikutnya
                        </label>
                        @endauth
                    </div>
                </div>

                {{-- Shipping Info --}}
                <div class="form-section" id="shippingSection" style="display:none;">
                    <h2>Pengiriman</h2>
                    <div class="shipping-info" id="shippingInfo">
                        <div class="shipping-info__loading">Menghitung ongkos kirim...</div>
                    </div>
                </div>

                {{-- Payment Method --}}
                <div class="form-section">
                    <h2>Metode Pembayaran</h2>

                    <div class="payment-options">
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="qris" required>
                            <div class="payment-option__content">
                                <div class="payment-option__icon">
                                    <svg width="32" height="32" viewBox="0 0 32 32" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="3" width="7" height="7"/>
                                        <rect x="22" y="3" width="7" height="7"/>
                                        <rect x="3" y="22" width="7" height="7"/>
                                        <rect x="13" y="13" width="6" height="6"/>
                                        <line x1="13" y1="3" x2="19" y2="3"/>
                                        <line x1="13" y1="9" x2="13" y2="3"/>
                                        <line x1="22" y1="13" x2="29" y2="13"/>
                                        <line x1="22" y1="22" x2="29" y2="22"/>
                                        <line x1="3" y1="13" x2="10" y2="13"/>
                                        <line x1="13" y1="22" x2="13" y2="29"/>
                                    </svg>
                                </div>
                                <div class="payment-option__text">
                                    <p class="payment-option__title">QRIS</p>
                                    <p class="payment-option__desc">Scan QR, bayar via e-wallet apa saja (GoPay, OVO, Dana, dll)</p>
                                </div>
                                <div class="payment-option__check">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                        <polyline points="20 6 9 17 4 12"/>
                                    </svg>
                                </div>
                            </div>
                        </label>

                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="bank_transfer" required>
                            <div class="payment-option__content">
                                <div class="payment-option__icon">
                                    <svg width="32" height="32" viewBox="0 0 32 32" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="2" y="6" width="28" height="20" rx="2"/>
                                        <line x1="2" y1="12" x2="30" y2="12"/>
                                        <line x1="8" y1="20" x2="14" y2="20"/>
                                    </svg>
                                </div>
                                <div class="payment-option__text">
                                    <p class="payment-option__title">Transfer Bank</p>
                                    <p class="payment-option__desc">Transfer manual ke rekening BCA / Mandiri</p>
                                </div>
                                <div class="payment-option__check">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                        <polyline points="20 6 9 17 4 12"/>
                                    </svg>
                                </div>
                            </div>
                        </label>

                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="cod" required>
                            <div class="payment-option__content">
                                <div class="payment-option__icon">
                                    <svg width="32" height="32" viewBox="0 0 32 32" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M5 9l4-5h14l4 5"/>
                                        <path d="M5 9v17a2 2 0 002 2h18a2 2 0 002-2V9"/>
                                        <line x1="5" y1="9" x2="27" y2="9"/>
                                        <path d="M11 13a5 5 0 0010 0"/>
                                    </svg>
                                </div>
                                <div class="payment-option__text">
                                    <p class="payment-option__title">Cash on Delivery (COD)</p>
                                    <p class="payment-option__desc">Bayar tunai saat barang diterima</p>
                                </div>
                                <div class="payment-option__check">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                        <polyline points="20 6 9 17 4 12"/>
                                    </svg>
                                </div>
                            </div>
                        </label>
                    </div>
                    @error('payment_method')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <button type="submit" class="btn-payment">Lanjut ke Pembayaran</button>
            </form>
        </div>

        {{-- Order Summary --}}
        <div class="checkout-summary">
            @foreach($cart as $item)
            <div class="checkout-item">
                <div class="checkout-item__img-wrap">
                    <div class="checkout-item__img">
                        <img src="{{ asset($item['image']) }}" alt="{{ $item['name'] }}"
                             onerror="this.src='{{ asset('images/placeholder.jpg') }}'">
                    </div>
                    <span class="checkout-item__qty">1</span>
                </div>
                <div class="checkout-item__info">
                    <p>{{ $item['name'] }}</p>
                </div>
                <div class="checkout-item__price">
                    <p>IDR {{ number_format($item['price'], 0, ',', '.') }}</p>
                </div>
            </div>
            @endforeach

            <div class="checkout-total">
                <div class="checkout-total__row">
                    <span>Subtotal</span>
                    <span id="summarySubtotal">IDR {{ number_format($total, 0, ',', '.') }}</span>
                </div>
                <div class="checkout-total__row">
                    <span>Pengiriman</span>
                    <span id="summaryShipping">Pilih wilayah dulu</span>
                </div>
                <div class="checkout-total__row checkout-total__row--grand">
                    <span>Total</span>
                    <span id="summaryTotal">IDR {{ number_format($total, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
const subtotalAmount = {{ $total }};

function formatRupiah(num) {
    return 'IDR ' + Number(num).toLocaleString('id-ID');
}

// ===================== DESTINATION SEARCH (RajaOngkir) =====================
const destinationSearch = document.getElementById('destinationSearch');
const destinationResults = document.getElementById('destinationResults');
const destinationSelected = document.getElementById('destinationSelected');
const destinationIdInput = document.getElementById('destinationIdInput');

let searchDebounce = null;

function selectDestination(item) {
    destinationIdInput.value = item.id;
    document.getElementById('provinceInput').value = item.province_name || '';
    document.getElementById('cityInput').value = item.city_name || '';
    document.getElementById('districtInput').value = item.district_name || '';
    document.getElementById('subdistrictInput').value = item.subdistrict_name || '';
    document.getElementById('postalInput').value = item.zip_code || '';

    destinationSearch.value = '';
    destinationResults.style.display = 'none';
    destinationSelected.style.display = 'block';
    destinationSelected.innerHTML = `
        <span>📍 ${item.label}</span>
        <button type="button" id="changeDestinationBtn">Ganti</button>
    `;
    document.getElementById('changeDestinationBtn').addEventListener('click', () => {
        destinationSelected.style.display = 'none';
        destinationSearch.style.display = 'block';
        destinationSearch.focus();
    });
    destinationSearch.style.display = 'none';

    fetchShippingOptions(item.id);
}

async function searchDestinations(query) {
    if (query.length < 3) {
        destinationResults.style.display = 'none';
        return;
    }
    try {
        const response = await fetch(`{{ route('shipping.search') }}?q=${encodeURIComponent(query)}`);
        const json = await response.json();
        const items = json.data || [];

        if (items.length === 0) {
            destinationResults.innerHTML = '<div class="destination-results__empty">Tidak ditemukan</div>';
        } else {
            destinationResults.innerHTML = items.map(item => `
                <div class="destination-results__item" data-item='${JSON.stringify(item).replace(/'/g, "&apos;")}'>
                    ${item.label}
                </div>
            `).join('');
            destinationResults.querySelectorAll('.destination-results__item').forEach(el => {
                el.addEventListener('click', () => selectDestination(JSON.parse(el.dataset.item.replace(/&apos;/g, "'"))));
            });
        }
        destinationResults.style.display = 'block';
    } catch (error) {
        console.error('Gagal mencari tujuan:', error);
    }
}

destinationSearch.addEventListener('input', function () {
    clearTimeout(searchDebounce);
    const query = this.value.trim();
    searchDebounce = setTimeout(() => searchDestinations(query), 350);
});

document.addEventListener('click', (e) => {
    if (!e.target.closest('#destinationSearch') && !e.target.closest('#destinationResults')) {
        destinationResults.style.display = 'none';
    }
});

// ===================== SHIPPING COST (opsi kurir) =====================
let selectedShippingOption = null;

async function fetchShippingOptions(destinationId) {
    const shippingSection = document.getElementById('shippingSection');
    const shippingInfo = document.getElementById('shippingInfo');
    const summaryShipping = document.getElementById('summaryShipping');
    const summaryTotal = document.getElementById('summaryTotal');

    shippingSection.style.display = 'block';
    shippingInfo.innerHTML = '<div class="shipping-info__loading">Menghitung ongkos kirim...</div>';
    summaryShipping.textContent = 'Menghitung...';

    try {
        const response = await fetch(`{{ route('shipping.cost') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    || document.querySelector('input[name="_token"]').value,
            },
            body: JSON.stringify({ destination: destinationId }),
        });
        const json = await response.json();

        if (!response.ok) {
            shippingInfo.innerHTML = `<p class="shipping-info__error">${json.message || 'Gagal menghitung ongkir.'}</p>`;
            summaryShipping.textContent = '-';
            return;
        }

        renderShippingOptions(json.data);
    } catch (error) {
        console.error('Gagal menghitung ongkir:', error);
        shippingInfo.innerHTML = '<p class="shipping-info__error">Gagal menghitung ongkir. Coba lagi.</p>';
    }
}

function renderShippingOptions(options) {
    const shippingInfo = document.getElementById('shippingInfo');

    shippingInfo.innerHTML = options.map((opt, i) => `
        <label class="shipping-option">
            <input type="radio" name="shipping_option" value="${i}" ${i === 0 ? 'checked' : ''}>
            <div class="shipping-option__content">
                <div class="shipping-option__text">
                    <div class="shipping-option__name">${opt.courier_name} - ${opt.service}</div>
                    <div class="shipping-option__desc">${opt.description || ''} · Estimasi ${opt.etd} hari</div>
                </div>
                <div class="shipping-option__cost">${formatRupiah(opt.cost)}</div>
            </div>
        </label>
    `).join('');

    shippingInfo.querySelectorAll('input[name="shipping_option"]').forEach(radio => {
        radio.addEventListener('change', () => applyShippingOption(options[radio.value]));
    });

    applyShippingOption(options[0]);
}

function applyShippingOption(option) {
    selectedShippingOption = option;

    document.getElementById('summaryShipping').textContent = formatRupiah(option.cost);
    document.getElementById('summaryTotal').textContent = formatRupiah(subtotalAmount + option.cost);

    setHiddenInput('shippingCostInput', 'shipping_cost', option.cost);
    setHiddenInput('shippingCourierInput', 'courier', option.courier_code);
    setHiddenInput('shippingServiceInput', 'shipping_service', option.service);
    setHiddenInput('shippingZoneInput', 'shipping_zone', `${option.courier_name} - ${option.service}`);
}

function setHiddenInput(id, name, value) {
    let input = document.getElementById(id);
    if (!input) {
        input = document.createElement('input');
        input.type = 'hidden';
        input.id = id;
        input.name = name;
        document.getElementById('checkoutForm').appendChild(input);
    }
    input.value = value;
}

// ===================== SAVED ADDRESS HANDLING =====================
const addressChoiceRadios = document.querySelectorAll('input[name="address_choice"]');
const saveAddressCheckWrap = document.getElementById('saveAddressCheckWrap');

async function applySavedAddress(radio) {
    const d = radio.dataset;
    document.getElementById('nameInput').value = d.name || '';
    document.getElementById('addressInput').value = d.address || '';
    document.getElementById('phoneInput').value = d.phone || '';

    // Alamat tersimpan tidak menyimpan destination ID RajaOngkir, jadi
    // dicari ulang otomatis berdasarkan nama kecamatan/kota yang tersimpan.
    const query = d.subdistrict || d.district || d.city;
    if (query) {
        try {
            const response = await fetch(`{{ route('shipping.search') }}?q=${encodeURIComponent(query)}`);
            const json = await response.json();
            const items = json.data || [];
            const match = items.find(i => i.city_name?.toUpperCase() === (d.city || '').toUpperCase()) || items[0];
            if (match) selectDestination(match);
        } catch (error) {
            console.error('Gagal mencocokkan alamat tersimpan:', error);
        }
    }

    if (saveAddressCheckWrap) saveAddressCheckWrap.style.display = 'none';
}

function clearAddressFields() {
    document.getElementById('addressInput').value = '';
    document.getElementById('phoneInput').value = '';
    destinationIdInput.value = '';
    document.getElementById('postalInput').value = '';
    destinationSelected.style.display = 'none';
    destinationSearch.style.display = 'block';
    document.getElementById('shippingSection').style.display = 'none';
    document.getElementById('summaryShipping').textContent = 'Pilih wilayah dulu';
    document.getElementById('summaryTotal').textContent = formatRupiah(subtotalAmount);
    if (saveAddressCheckWrap) saveAddressCheckWrap.style.display = 'flex';
}

addressChoiceRadios.forEach(radio => {
    radio.addEventListener('change', function () {
        if (this.value === 'new') {
            clearAddressFields();
        } else {
            applySavedAddress(this);
        }
    });
});

// Jangan biarkan submit tanpa alamat tujuan terpilih (input hidden
// "required" tidak divalidasi browser secara konsisten)
document.getElementById('checkoutForm').addEventListener('submit', function (e) {
    if (!destinationIdInput.value) {
        e.preventDefault();
        alert('Pilih dulu kecamatan/kota tujuan pengiriman.');
        destinationSearch.focus();
    }
});

// Initialize
(async () => {
    const checkedRadio = document.querySelector('input[name="address_choice"]:checked');
    if (checkedRadio && checkedRadio.value !== 'new') {
        await applySavedAddress(checkedRadio);
    }
})();
</script>
@endsection