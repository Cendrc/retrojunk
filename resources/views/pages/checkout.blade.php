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

                        <div class="form-group">
                            <select name="province" id="provinceSelect" class="form-input" required>
                                <option value="">Pilih Provinsi</option>
                            </select>
                            @error('province')<span class="form-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <select name="city" id="citySelect" class="form-input" required disabled>
                                    <option value="">Pilih Kota/Kabupaten</option>
                                </select>
                                @error('city')<span class="form-error">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-group">
                                <select name="district" id="districtSelect" class="form-input" required disabled>
                                    <option value="">Pilih Kecamatan</option>
                                </select>
                                @error('district')<span class="form-error">{{ $message }}</span>@enderror
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <input type="tel" name="phone" id="phoneInput" class="form-input" placeholder="Nomor HP (contoh: 081234567890)"
                                    value="{{ old('phone') }}" pattern="[0-9]{10,15}" maxlength="15" 
                                    inputmode="numeric" required
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                @error('phone')<span class="form-error">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-group">
                                <input type="text" name="postal_code" id="postalInput" class="form-input" placeholder="Kode Pos"
                                    value="{{ old('postal_code') }}" pattern="[0-9]{5}" maxlength="5"
                                    inputmode="numeric" required
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                @error('postal_code')<span class="form-error">{{ $message }}</span>@enderror
                            </div>
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
                <div class="checkout-item__img">
                    <img src="{{ asset($item['image']) }}" alt="{{ $item['name'] }}"
                         onerror="this.src='{{ asset('images/placeholder.jpg') }}'">
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
const API_BASE = 'https://www.emsifa.com/api-wilayah-indonesia/api';

const provinceSelect = document.getElementById('provinceSelect');
const citySelect = document.getElementById('citySelect');
const districtSelect = document.getElementById('districtSelect');

// Load Provinces saat halaman dibuka
async function loadProvinces() {
    try {
        const response = await fetch(`${API_BASE}/provinces.json`);
        const provinces = await response.json();
        
        // Sort by name
        provinces.sort((a, b) => a.name.localeCompare(b.name));
        
        provinces.forEach(province => {
            const option = document.createElement('option');
            option.value = province.name;
            option.dataset.id = province.id;
            option.textContent = province.name;
            provinceSelect.appendChild(option);
        });
    } catch (error) {
        console.error('Failed to load provinces:', error);
        provinceSelect.innerHTML = '<option value="">Gagal memuat data — refresh halaman</option>';
    }
}

// Load Cities saat provinsi dipilih
provinceSelect.addEventListener('change', async function () {
    const selectedOption = this.options[this.selectedIndex];
    const provinceId = selectedOption.dataset.id;
    
    // Reset city & district
    citySelect.innerHTML = '<option value="">Memuat...</option>';
    citySelect.disabled = true;
    districtSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
    districtSelect.disabled = true;
    
    if (!provinceId) {
        citySelect.innerHTML = '<option value="">Pilih Kota/Kabupaten</option>';
        return;
    }
    
    try {
        const response = await fetch(`${API_BASE}/regencies/${provinceId}.json`);
        const cities = await response.json();
        
        cities.sort((a, b) => a.name.localeCompare(b.name));
        
        citySelect.innerHTML = '<option value="">Pilih Kota/Kabupaten</option>';
        cities.forEach(city => {
            const option = document.createElement('option');
            option.value = city.name;
            option.dataset.id = city.id;
            option.textContent = city.name;
            citySelect.appendChild(option);
        });
        citySelect.disabled = false;
    } catch (error) {
        console.error('Failed to load cities:', error);
        citySelect.innerHTML = '<option value="">Gagal memuat</option>';
    }
});

// Load Districts saat kota dipilih
citySelect.addEventListener('change', async function () {
    const selectedOption = this.options[this.selectedIndex];
    const cityId = selectedOption.dataset.id;
    
    districtSelect.innerHTML = '<option value="">Memuat...</option>';
    districtSelect.disabled = true;
    
    if (!cityId) {
        districtSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
        return;
    }
    
    try {
        const response = await fetch(`${API_BASE}/districts/${cityId}.json`);
        const districts = await response.json();
        
        districts.sort((a, b) => a.name.localeCompare(b.name));
        
        districtSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
        districts.forEach(district => {
            const option = document.createElement('option');
            option.value = district.name;
            option.dataset.id = district.id;
            option.textContent = district.name;
            districtSelect.appendChild(option);
        });
        districtSelect.disabled = false;
    } catch (error) {
        console.error('Failed to load districts:', error);
        districtSelect.innerHTML = '<option value="">Gagal memuat</option>';
    }
});

    // ===================== SHIPPING CALCULATOR =====================
    const subtotalAmount = {{ $total }};

    const shippingZones = {
        'KALIMANTAN TIMUR_SAMARINDA': { name: 'Samarinda', cost: 10000, estimated: '1-2 hari' },
        'KALIMANTAN TIMUR': { name: 'Kalimantan Timur', cost: 20000, estimated: '2-3 hari' },
        'KALIMANTAN BARAT': { name: 'Kalimantan', cost: 35000, estimated: '3-5 hari' },
        'KALIMANTAN TENGAH': { name: 'Kalimantan', cost: 35000, estimated: '3-5 hari' },
        'KALIMANTAN SELATAN': { name: 'Kalimantan', cost: 35000, estimated: '3-5 hari' },
        'KALIMANTAN UTARA': { name: 'Kalimantan', cost: 35000, estimated: '3-5 hari' },
        'DKI JAKARTA': { name: 'Jawa & Bali', cost: 30000, estimated: '3-5 hari' },
        'JAWA BARAT': { name: 'Jawa & Bali', cost: 30000, estimated: '3-5 hari' },
        'JAWA TENGAH': { name: 'Jawa & Bali', cost: 30000, estimated: '3-5 hari' },
        'JAWA TIMUR': { name: 'Jawa & Bali', cost: 30000, estimated: '3-5 hari' },
        'DI YOGYAKARTA': { name: 'Jawa & Bali', cost: 30000, estimated: '3-5 hari' },
        'BANTEN': { name: 'Jawa & Bali', cost: 30000, estimated: '3-5 hari' },
        'BALI': { name: 'Jawa & Bali', cost: 30000, estimated: '3-5 hari' },
        'ACEH': { name: 'Sumatera', cost: 40000, estimated: '4-6 hari' },
        'SUMATERA UTARA': { name: 'Sumatera', cost: 40000, estimated: '4-6 hari' },
        'SUMATERA BARAT': { name: 'Sumatera', cost: 40000, estimated: '4-6 hari' },
        'RIAU': { name: 'Sumatera', cost: 40000, estimated: '4-6 hari' },
        'KEPULAUAN RIAU': { name: 'Sumatera', cost: 40000, estimated: '4-6 hari' },
        'JAMBI': { name: 'Sumatera', cost: 40000, estimated: '4-6 hari' },
        'SUMATERA SELATAN': { name: 'Sumatera', cost: 40000, estimated: '4-6 hari' },
        'KEPULAUAN BANGKA BELITUNG': { name: 'Sumatera', cost: 40000, estimated: '4-6 hari' },
        'BENGKULU': { name: 'Sumatera', cost: 40000, estimated: '4-6 hari' },
        'LAMPUNG': { name: 'Sumatera', cost: 40000, estimated: '4-6 hari' },
        'SULAWESI UTARA': { name: 'Sulawesi & NTB', cost: 40000, estimated: '4-6 hari' },
        'GORONTALO': { name: 'Sulawesi & NTB', cost: 40000, estimated: '4-6 hari' },
        'SULAWESI TENGAH': { name: 'Sulawesi & NTB', cost: 40000, estimated: '4-6 hari' },
        'SULAWESI BARAT': { name: 'Sulawesi & NTB', cost: 40000, estimated: '4-6 hari' },
        'SULAWESI SELATAN': { name: 'Sulawesi & NTB', cost: 40000, estimated: '4-6 hari' },
        'SULAWESI TENGGARA': { name: 'Sulawesi & NTB', cost: 40000, estimated: '4-6 hari' },
        'NUSA TENGGARA BARAT': { name: 'Sulawesi & NTB', cost: 40000, estimated: '4-6 hari' },
        'MALUKU': { name: 'Indonesia Timur', cost: 60000, estimated: '5-10 hari' },
        'MALUKU UTARA': { name: 'Indonesia Timur', cost: 60000, estimated: '5-10 hari' },
        'PAPUA': { name: 'Indonesia Timur', cost: 60000, estimated: '5-10 hari' },
        'PAPUA BARAT': { name: 'Indonesia Timur', cost: 60000, estimated: '5-10 hari' },
        'NUSA TENGGARA TIMUR': { name: 'Indonesia Timur', cost: 60000, estimated: '5-10 hari' },
    };

    function formatRupiah(num) {
        return 'IDR ' + Number(num).toLocaleString('id-ID');
    }

    function calculateShipping() {
        const province = provinceSelect.value.toUpperCase();
        const city = citySelect.value.toUpperCase();
        
        const shippingSection = document.getElementById('shippingSection');
        const shippingInfo = document.getElementById('shippingInfo');
        const summaryShipping = document.getElementById('summaryShipping');
        const summaryTotal = document.getElementById('summaryTotal');
        
        if (!province) {
            shippingSection.style.display = 'none';
            summaryShipping.textContent = 'Pilih wilayah dulu';
            summaryTotal.textContent = formatRupiah(subtotalAmount);
            return;
        }
        
        let shippingData = null;
        let isFreeShipping = false;
        
        // Cek Samarinda (free shipping > 500k)
        if (city.includes('SAMARINDA')) {
            if (subtotalAmount >= 500000) {
                shippingData = { name: 'Samarinda (GRATIS ONGKIR!)', cost: 0, estimated: '1-2 hari' };
                isFreeShipping = true;
            } else {
                shippingData = shippingZones['KALIMANTAN TIMUR_SAMARINDA'];
            }
        } else {
            shippingData = shippingZones[province];
        }
        
        if (!shippingData) {
            shippingData = { name: 'Wilayah Lainnya', cost: 50000, estimated: '5-10 hari' };
        }
        
        const total = subtotalAmount + shippingData.cost;
        
        shippingSection.style.display = 'block';
        
        if (isFreeShipping) {
            shippingInfo.innerHTML = `
                <div class="shipping-info__zone shipping-info__zone--free">
                    <div class="shipping-info__name">
                        🎉 ${shippingData.name}
                    </div>
                    <div class="shipping-info__details">
                        <span>Estimasi: ${shippingData.estimated}</span>
                        <span class="shipping-info__cost shipping-info__cost--free">GRATIS</span>
                    </div>
                </div>
            `;
            summaryShipping.innerHTML = '<span style="color: #5c6b3a; font-weight: 600;">GRATIS</span>';
        } else {
            shippingInfo.innerHTML = `
                <div class="shipping-info__zone">
                    <div class="shipping-info__name">📦 ${shippingData.name}</div>
                    <div class="shipping-info__details">
                        <span>Estimasi: ${shippingData.estimated}</span>
                        <span class="shipping-info__cost">${formatRupiah(shippingData.cost)}</span>
                    </div>
                </div>
                ${city.includes('SAMARINDA') && subtotalAmount < 500000 ? 
                    `<p class="shipping-info__promo">💡 Belanja minimum ${formatRupiah(500000)} untuk Samarinda dapat GRATIS ONGKIR! Kurang ${formatRupiah(500000 - subtotalAmount)} lagi.</p>` 
                    : ''}
            `;
            summaryShipping.textContent = formatRupiah(shippingData.cost);
        }
        
        summaryTotal.textContent = formatRupiah(total);
        
        // Tambah hidden input untuk shipping cost
        let shippingInput = document.getElementById('shippingCostInput');
        if (!shippingInput) {
            shippingInput = document.createElement('input');
            shippingInput.type = 'hidden';
            shippingInput.id = 'shippingCostInput';
            shippingInput.name = 'shipping_cost';
            document.getElementById('checkoutForm').appendChild(shippingInput);
        }
        shippingInput.value = shippingData.cost;
        
        let zoneInput = document.getElementById('shippingZoneInput');
        if (!zoneInput) {
            zoneInput = document.createElement('input');
            zoneInput.type = 'hidden';
            zoneInput.id = 'shippingZoneInput';
            zoneInput.name = 'shipping_zone';
            document.getElementById('checkoutForm').appendChild(zoneInput);
        }
        zoneInput.value = shippingData.name;
    }

    // Trigger calculate setiap kali provinsi atau kota berubah
    provinceSelect.addEventListener('change', () => {
        setTimeout(calculateShipping, 100);
    });
    citySelect.addEventListener('change', () => {
        setTimeout(calculateShipping, 100);
    });

    // ===================== SAVED ADDRESS HANDLING =====================
    const addressChoiceRadios = document.querySelectorAll('input[name="address_choice"]');
    const saveAddressCheckWrap = document.getElementById('saveAddressCheckWrap');

    function waitForOptions(selectEl, timeout) {
        return new Promise(resolve => {
            const start = Date.now();
            const check = () => {
                if (!selectEl.disabled && selectEl.options.length > 1) return resolve();
                if (Date.now() - start > timeout) return resolve();
                setTimeout(check, 150);
            };
            check();
        });
    }

    async function selectProvinceByName(name) {
        const opt = [...provinceSelect.options].find(o => o.value.toUpperCase() === (name || '').toUpperCase());
        if (!opt) return;
        provinceSelect.value = opt.value;
        provinceSelect.dispatchEvent(new Event('change'));
        await waitForOptions(citySelect, 4000);
    }

    async function selectCityByName(name) {
        const opt = [...citySelect.options].find(o => o.value.toUpperCase() === (name || '').toUpperCase());
        if (!opt) return;
        citySelect.value = opt.value;
        citySelect.dispatchEvent(new Event('change'));
        await waitForOptions(districtSelect, 4000);
    }

    function selectDistrictByName(name) {
        const opt = [...districtSelect.options].find(o => o.value.toUpperCase() === (name || '').toUpperCase());
        if (opt) districtSelect.value = opt.value;
    }

    async function applySavedAddress(radio) {
        const d = radio.dataset;
        document.getElementById('nameInput').value = d.name || '';
        document.getElementById('addressInput').value = d.address || '';
        document.getElementById('phoneInput').value = d.phone || '';
        document.getElementById('postalInput').value = d.postal || '';

        await selectProvinceByName(d.province);
        await selectCityByName(d.city);
        selectDistrictByName(d.district);

        calculateShipping();

        if (saveAddressCheckWrap) saveAddressCheckWrap.style.display = 'none';
    }

    function clearAddressFields() {
        document.getElementById('addressInput').value = '';
        document.getElementById('phoneInput').value = '';
        document.getElementById('postalInput').value = '';
        provinceSelect.value = '';
        citySelect.innerHTML = '<option value="">Pilih Kota/Kabupaten</option>';
        citySelect.disabled = true;
        districtSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
        districtSelect.disabled = true;
        calculateShipping();
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

// Initialize
(async () => {
    await loadProvinces();
    const checkedRadio = document.querySelector('input[name="address_choice"]:checked');
    if (checkedRadio && checkedRadio.value !== 'new') {
        await applySavedAddress(checkedRadio);
    }
})();
</script>
@endsection