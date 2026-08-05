<x-guest-layout>
    @section('title', 'Daftar Bengkel Mitra')

    {{-- Heading --}}
    <div style="margin-bottom:24px;">
        <h2 style="font-size:22px;font-weight:700;color:#F4F4F5;letter-spacing:-0.02em;">Daftar Bengkel Mitra</h2>
        <p style="color:#71717A;font-size:14px;margin-top:4px;">Daftarkan bengkel Anda ke platform Maintify</p>
    </div>

    @push('head')
    <script>
        const indonesiaProvincesData = {
            "Aceh": ["Banda Aceh", "Langsa", "Lhokseumawe", "Sabang", "Subulussalam", "Aceh Barat", "Aceh Barat Daya", "Aceh Besar", "Aceh Jaya", "Aceh Selatan", "Aceh Singkil", "Aceh Tamiang", "Aceh Tengah", "Aceh Tenggara", "Aceh Timur", "Aceh Utara", "Bener Meriah", "Bireuen", "Gayo Lues", "Nagan Raya", "Pidie", "Pidie Jaya", "Simeulue"],
            "Sumatera Utara": ["Medan", "Binjai", "Gunungsitoli", "Padangsidimpuan", "Pematangsiantar", "Sibolga", "Tanjungbalai", "Tebing Tinggi", "Asahan", "Batu Bara", "Dairi", "Deli Serdang", "Humbang Hasundutan", "Karo", "Labuhanbatu", "Labuhanbatu Selatan", "Labuhanbatu Utara", "Langkat", "Mandailing Natal", "Nias", "Nias Barat", "Nias Selatan", "Nias Utara", "Padang Lawas", "Padang Lawas Utara", "Pakpak Bharat", "Samosir", "Serdang Bedagai", "Simalungun", "Tapanuli Selatan", "Tapanuli Tengah", "Tapanuli Utara", "Toba"],
            "Sumatera Barat": ["Padang", "Bukittinggi", "Padang Panjang", "Pariaman", "Payakumbuh", "Sawahlunto", "Solok", "Agam", "Dharmasraya", "Kepulauan Mentawai", "Lima Puluh Kota", "Padang Pariaman", "Pasaman", "Pasaman Barat", "Pesisir Selatan", "Sijunjung", "Solok", "Solok Selatan", "Tanah Datar"],
            "Riau": ["Pekanbaru", "Dumai", "Bengkalis", "Indragiri Hilir", "Indragiri Hulu", "Kampar", "Kepulauan Meranti", "Kuantan Singingi", "Pelalawan", "Rokan Hilir", "Rokan Hulu", "Siak"],
            "Kepulauan Riau": ["Batam", "Tanjungpinang", "Bintan", "Karimun", "Kepulauan Anambas", "Lingga", "Natuna"],
            "Jambi": ["Jambi", "Sungai Penuh", "Batanghari", "Bungo", "Kerinci", "Merangin", "Muaro Jambi", "Sarolangun", "Tanjung Jabung Barat", "Tanjung Jabung Timur", "Tebo"],
            "Sumatera Selatan": ["Palembang", "Lubuklinggau", "Pagar Alam", "Prabumulih", "Banyuasin", "Empat Lawang", "Lahat", "Muara Enim", "Musi Banyuasin", "Musi Rawas", "Musi Rawas Utara", "Ogan Ilir", "Ogan Komering Ilir", "Ogan Komering Ulu", "Ogan Komering Ulu Selatan", "Ogan Komering Ulu Timur", "Penukal Abab Lematang Ilir"],
            "Bangka Belitung": ["Pangkalpinang", "Bangka", "Bangka Barat", "Bangka Selatan", "Bangka Tengah", "Belitung", "Belitung Timur"],
            "Bengkulu": ["Bengkulu", "Bengkulu Selatan", "Bengkulu Tengah", "Bengkulu Utara", "Kaur", "Kepahiang", "Lebong", "Mukomuko", "Rejang Lebong", "Seluma"],
            "Lampung": ["Bandar Lampung", "Metro", "Lampung Barat", "Lampung Selatan", "Lampung Tengah", "Lampung Timur", "Lampung Utara", "Mesuji", "Pesawaran", "Pesisir Barat", "Pringsewu", "Tanggamus", "Tulang Bawang", "Tulang Bawang Barat", "Way Kanan"],
            "DKI Jakarta": ["Jakarta Barat", "Jakarta Pusat", "Jakarta Selatan", "Jakarta Timur", "Jakarta Utara", "Kepulauan Seribu"],
            "Jawa Barat": ["Bandung", "Bekasi", "Bogor", "Cimahi", "Cirebon", "Depok", "Sukabumi", "Tasikmalaya", "Banjar", "Bandung Barat", "Ciamis", "Cianjur", "Garut", "Indramayu", "Karawang", "Kuningan", "Majalengka", "Pangandaran", "Purwakarta", "Subang", "Sumedang"],
            "Banten": ["Tangerang", "Tangerang Selatan", "Serang", "Cilegon", "Lebak", "Pandeglang"],
            "Jawa Tengah": ["Semarang", "Surakarta (Solo)", "Magelang", "Pekalongan", "Salatiga", "Tegal", "Banjarnegara", "Banyumas", "Batang", "Blora", "Boyolali", "Brebes", "Cilacap", "Demak", "Grobogan", "Jepara", "Karanganyar", "Kebumen", "Kendal", "Klaten", "Kudus", "Pati", "Pemalang", "Purbalingga", "Purworejo", "Rembang", "Sragen", "Sukoharjo", "Temanggung", "Wonogiri", "Wonosobo"],
            "DI Yogyakarta": ["Yogyakarta", "Bantul", "Gunungkidul", "Kulon Progo", "Sleman"],
            "Jawa Timur": ["Surabaya", "Batu", "Blitar", "Kediri", "Madiun", "Malang", "Mojokerto", "Pasuruan", "Probolinggo", "Bangkalan", "Banyuwangi", "Bojonegoro", "Bondowoso", "Gresik", "Jember", "Jombang", "Lamongan", "Lumajang", "Magetan", "Nganjuk", "Ngawi", "Pacitan", "Pamekasan", "Ponorogo", "Sampang", "Sidoarjo", "Situbondo", "Sumenep", "Trenggalek", "Tuban", "Tulungagung"],
            "Bali": ["Denpasar", "Badung", "Bangli", "Buleleng", "Gianyar", "Jembrana", "Karangasem", "Klungkung", "Tabanan"],
            "Nusa Tenggara Barat": ["Mataram", "Bima", "Dompu", "Lombok Barat", "Lombok Tengah", "Lombok Timur", "Lombok Utara", "Sumbawa", "Sumbawa Barat"],
            "Nusa Tenggara Timur": ["Kupang", "Alor", "Belu", "Ende", "Flores Timur", "Lembata", "Malaka", "Manggarai", "Manggarai Barat", "Manggarai Timur", "Nagekeo", "Ngada", "Rote Ndao", "Sabu Raijua", "Sikka", "Sumba Barat", "Sumba Barat Daya", "Sumba Tengah", "Sumba Timur", "Timor Tengah Selatan", "Timor Tengah Utara"],
            "Kalimantan Barat": ["Pontianak", "Singkawang", "Bengkayang", "Kapuas Hulu", "Kayong Utara", "Ketapang", "Kubu Raya", "Lapak", "Melawi", "Mempawah", "Sambas", "Sintang"],
            "Kalimantan Tengah": ["Palangkaraya", "Barito Selatan", "Barito Timur", "Barito Utara", "Gunung Mas", "Kapuas", "Katingan", "Kotawaringin Barat", "Kotawaringin Timur", "Lamandau", "Murung Raya", "Pulang Pisau", "Sukamara", "Seruyan"],
            "Kalimantan Selatan": ["Banjarmasin", "Banjarbaru", "Balangan", "Banjar", "Barito Kuala", "Hulu Sungai Selatan", "Hulu Sungai Tengah", "Hulu Sungai Utara", "Kotabaru", "Tabalong", "Tanah Bumbu", "Tanah Laut", "Tapin"],
            "Kalimantan Timur": ["Samarinda", "Balikpapan", "Bontang", "Berau", "Kutai Barat", "Kutai Kartanegara", "Kutai Timur", "Mahakam Ulu", "Paser", "Penajam Paser Utara"],
            "Kalimantan Utara": ["Tarakan", "Bulungan", "Malinau", "Nunukan", "Tana Tidung"],
            "Sulawesi Utara": ["Manado", "Bitung", "Kotamobagu", "Tomohon", "Bolaang Mongondow", "Bolaang Mongondow Selatan", "Bolaang Mongondow Timur", "Bolaang Mongondow Utara", "Kepulauan Sangihe", "Kepulauan Siau Tagulandang Biaro", "Kepulauan Talaud", "Minahasa", "Minahasa Selatan", "Minahasa Tenggara", "Minahasa Utara"],
            "Sulawesi Tengah": ["Palu", "Banggai", "Banggai Kepulauan", "Banggai Laut", "Buol", "Donggala", "Morowali", "Morowali Utara", "Parigi Moutong", "Poso", "Sigi", "Tojo Una-Una", "Toli-Toli"],
            "Sulawesi Selatan": ["Makassar", "Palopo", "Parepare", "Bantaeng", "Barru", "Bone", "Bulukumba", "Enrekang", "Gowa", "Jeneponto", "Kepulauan Selayar", "Luwu", "Luwu Timur", "Luwu Utara", "Maros", "Pangkajene dan Kepulauan", "Pinrang", "Sidenreng Rappang", "Sinjai", "Soppeng", "Takalar", "Tana Toraja", "Toraja Utara", "Wajo"],
            "Sulawesi Tenggara": ["Kendari", "Baubau", "Bombana", "Buton", "Buton Selatan", "Buton Tengah", "Buton Utara", "Kolaka", "Kolaka Timur", "Kolaka Utara", "Konawe", "Konawe Kepulauan", "Konawe Selatan", "Konawe Utara", "Muna", "Muna Barat", "Wakatobi"],
            "Gorontalo": ["Gorontalo", "Boalemo", "Bone Bolango", "Gorontalo Utara", "Pohuwato"],
            "Sulawesi Barat": ["Mamuju", "Majene", "Mamasa", "Mamuju Tengah", "Pasangkayu", "Polewali Mandar"],
            "Maluku": ["Ambon", "Tual", "Buru", "Buru Selatan", "Kepulauan Aru", "Kepulauan Tanimbar", "Maluku Barat Daya", "Maluku Tengah", "Maluku Tenggara", "Seram Bagian Barat", "Seram Bagian Timur"],
            "Maluku Utara": ["Ternate", "Tidore Kepulauan", "Halmahera Barat", "Halmahera Selatan", "Halmahera Tengah", "Halmahera Timur", "Halmahera Utara", "Kepulauan Sula", "Pulau Morotai", "Pulau Taliabu"],
            "Papua": ["Jayapura", "Biak Numfor", "Keerom", "Kepulauan Yapen", "Mamberamo Raya", "Sarmi", "Supiori", "Waropen"],
            "Papua Barat": ["Manokwari", "Fakfak", "Kaimana", "Manokwari Selatan", "Pegunungan Arfak", "Teluk Bintuni", "Teluk Wondama"],
            "Papua Selatan": ["Merauke", "Asmat", "Boven Digoel", "Mappi"],
            "Papua Tengah": ["Nabire", "Deiyai", "Dogiyai", "Intan Jaya", "Mimika", "Paniai", "Puncak", "Puncak Jaya"],
            "Papua Pegunungan": ["Jayawijaya", "Lanny Jaya", "Mamberamo Tengah", "Nduga", "Pegunungan Bintang", "Yahukimo", "Yalimo"],
            "Papua Barat Daya": ["Sorong", "Maybrat", "Raja Ampat", "Sorong Selatan", "Tambrauw"]
        };

        function registerWorkshopForm() {
            const oldProv = @json(old('province', ''));
            const oldCity = @json(old('city', ''));

            let initSelectedProv = '';
            let initCustomProv = '';
            if (oldProv) {
                if (indonesiaProvincesData[oldProv]) {
                    initSelectedProv = oldProv;
                } else {
                    initSelectedProv = 'Lainnya';
                    initCustomProv = oldProv;
                }
            }

            let initSelectedCity = '';
            let initCustomCity = '';
            if (oldCity) {
                if (initSelectedProv && initSelectedProv !== 'Lainnya' && (indonesiaProvincesData[initSelectedProv] || []).includes(oldCity)) {
                    initSelectedCity = oldCity;
                } else {
                    initSelectedCity = 'Lainnya';
                    initCustomCity = oldCity;
                }
            }

            return {
                step: {{ $errors->hasAny(['owner_name', 'email', 'owner_ktp_number']) ? 1 : ($errors->hasAny(['workshop_name', 'phone', 'address', 'city', 'province', 'operational_hours']) ? 2 : ($errors->hasAny(['legal_document', 'password', 'password_confirmation']) ? 3 : 1)) }},
                owner_name: @json(old('owner_name', '')),
                email: @json(old('email', '')),
                owner_ktp_number: @json(old('owner_ktp_number', '')),
                workshop_name: @json(old('workshop_name', '')),
                phone: @json(old('phone', '')),
                address: @json(old('address', '')),

                selected_province: initSelectedProv,
                custom_province: initCustomProv,
                selected_city: initSelectedCity,
                custom_city: initCustomCity,

                latitude: @json(old('latitude', '')),
                longitude: @json(old('longitude', '')),
                isLocating: false,

                getLocation() {
                    if (!navigator.geolocation) {
                        alert('Browser Anda tidak mendukung fitur geolokasi GPS.');
                        return;
                    }
                    this.isLocating = true;
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            this.latitude = position.coords.latitude.toFixed(6);
                            this.longitude = position.coords.longitude.toFixed(6);
                            this.isLocating = false;
                        },
                        (error) => {
                            this.isLocating = false;
                            alert('Gagal mengambil lokasi: ' + (error.message || 'Izin akses lokasi ditolak.'));
                        },
                        { enableHighAccuracy: true, timeout: 10000 }
                    );
                },

                operational_hours: @json(old('operational_hours', '')),
                provincesData: indonesiaProvincesData,

                get provinceOptions() {
                    return Object.keys(this.provincesData);
                },

                get cityOptions() {
                    if (!this.selected_province || this.selected_province === 'Lainnya') {
                        return [];
                    }
                    return this.provincesData[this.selected_province] || [];
                },

                get effectiveProvince() {
                    if (this.selected_province === 'Lainnya') {
                        return this.custom_province;
                    }
                    return this.selected_province;
                },

                get effectiveCity() {
                    if (this.selected_city === 'Lainnya' || this.selected_province === 'Lainnya') {
                        return this.custom_city;
                    }
                    return this.selected_city;
                },

                onProvinceChange() {
                    this.selected_city = '';
                    this.custom_city = '';
                    if (this.selected_province !== 'Lainnya') {
                        this.custom_province = '';
                    }
                },

                onCityChange() {
                    if (this.selected_city !== 'Lainnya') {
                        this.custom_city = '';
                    }
                },

                validateStep1() {
                    return this.owner_name.trim() !== '' &&
                           this.email.trim() !== '' &&
                           this.owner_ktp_number.trim().length === 16;
                },
                validateStep2() {
                    return this.workshop_name.trim() !== '' &&
                           this.phone.trim() !== '' &&
                           this.address.trim() !== '' &&
                           this.effectiveProvince.trim() !== '' &&
                           this.effectiveCity.trim() !== '' &&
                           this.operational_hours.trim() !== '';
                }
            };
        }
    </script>
    @endpush

    <div x-data="registerWorkshopForm()">
        {{-- Stepper Progress --}}
        <div style="margin-bottom:28px;position:relative;padding:0 12px;">
            <!-- Line background -->
            <div style="position:absolute;top:18px;left:36px;right:36px;height:2px;background:#2E3030;z-index:0;"></div>
            <!-- Line active -->
            <div :style="'width: calc(' + ((step - 1) * 50) + '% - ' + ((step - 1) * 18) + 'px)'"
                 style="position:absolute;top:18px;left:36px;height:2px;background:linear-gradient(90deg, #ff9aa4, #ff5f71);z-index:0;transition:width 300ms ease-in-out;"></div>

            <div style="display:flex;justify-content:space-between;align-items:center;position:relative;z-index:1;">
                <!-- Step 1 -->
                <div style="display:flex;flex-direction:column;align-items:center;cursor:pointer;flex:1;" @click="step = 1">
                    <div :style="step >= 1 ? 'background:#410008;border-color:#ff9aa4;color:#ff9aa4;box-shadow:0 0 12px rgba(255,154,164,0.35);' : 'background:#1E2020;border-color:#2E3030;color:#71717A;'"
                         style="width:36px;height:36px;min-width:36px;border-radius:50%;border:2px solid;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;transition:all 300ms ease;box-sizing:border-box;">
                        1
                    </div>
                    <span style="font-size:12px;font-weight:600;margin-top:8px;letter-spacing:0.01em;white-space:nowrap;" :style="step >= 1 ? 'color:#F4F4F5;' : 'color:#71717A;'">Pemilik</span>
                </div>

                <!-- Step 2 -->
                <div style="display:flex;flex-direction:column;align-items:center;cursor:pointer;flex:1;" @click="if (validateStep1()) step = 2">
                    <div :style="step >= 2 ? 'background:#410008;border-color:#ff9aa4;color:#ff9aa4;box-shadow:0 0 12px rgba(255,154,164,0.35);' : 'background:#1E2020;border-color:#2E3030;color:#71717A;'"
                         style="width:36px;height:36px;min-width:36px;border-radius:50%;border:2px solid;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;transition:all 300ms ease;box-sizing:border-box;">
                        2
                    </div>
                    <span style="font-size:12px;font-weight:600;margin-top:8px;letter-spacing:0.01em;white-space:nowrap;" :style="step >= 2 ? 'color:#F4F4F5;' : 'color:#71717A;'">Bengkel</span>
                </div>

                <!-- Step 3 -->
                <div style="display:flex;flex-direction:column;align-items:center;cursor:pointer;flex:1;" @click="if (validateStep1() && validateStep2()) step = 3">
                    <div :style="step >= 3 ? 'background:#410008;border-color:#ff9aa4;color:#ff9aa4;box-shadow:0 0 12px rgba(255,154,164,0.35);' : 'background:#1E2020;border-color:#2E3030;color:#71717A;'"
                         style="width:36px;height:36px;min-width:36px;border-radius:50%;border:2px solid;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;transition:all 300ms ease;box-sizing:border-box;">
                        3
                    </div>
                    <span style="font-size:12px;font-weight:600;margin-top:8px;letter-spacing:0.01em;white-space:nowrap;" :style="step >= 3 ? 'color:#F4F4F5;' : 'color:#71717A;'">Dokumen</span>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('register.workshop') }}" enctype="multipart/form-data" style="display:flex;flex-direction:column;gap:18px;">
            @csrf

            {{-- STEP 1: Informasi Pemilik --}}
            <div x-show="step === 1" x-transition.fade class="space-y-4">
                <div style="background-color:rgba(255,255,255,0.02);border:1px solid #2E3030;border-radius:12px;padding:16px;">
                    <p style="font-size:11px;font-weight:600;color:#71717A;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:12px;">Langkah 1: Informasi Pemilik</p>

                    {{-- Nama Pemilik --}}
                    <div class="form-group" style="margin-bottom:12px;">
                        <label for="owner_name" class="form-label">Nama Pemilik / Pengelola</label>
                        <input id="owner_name" type="text" name="owner_name" x-model="owner_name"
                            required autofocus placeholder="Nama lengkap pemilik"
                            class="form-input {{ $errors->has('owner_name') ? 'form-input-error' : '' }}" />
                        @error('owner_name') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    {{-- Email Pemilik --}}
                    <div class="form-group" style="margin-bottom:12px;">
                        <label for="email" class="form-label">Email Perusahaan / Pemilik</label>
                        <input id="email" type="email" name="email" x-model="email"
                            required placeholder="nama@email.com"
                            class="form-input {{ $errors->has('email') ? 'form-input-error' : '' }}" />
                        @error('email') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    {{-- NIK KTP --}}
                    <div class="form-group">
                        <label for="owner_ktp_number" class="form-label">Nomor KTP (NIK) Pemilik</label>
                        <input id="owner_ktp_number" type="text" name="owner_ktp_number" x-model="owner_ktp_number"
                            required placeholder="16 digit NIK KTP" maxlength="16"
                            class="form-input {{ $errors->has('owner_ktp_number') ? 'form-input-error' : '' }}"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')" />
                        @error('owner_ktp_number') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <button type="button" @click="if (validateStep1()) { step = 2 } else { alert('Mohon lengkapi data pemilik dengan benar (KTP harus 16 digit).') }" class="btn-primary" style="width:100%;justify-content:center;margin-top:4px;">
                    Lanjutkan
                    <svg style="width:16px;height:16px;margin-left:4px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>

            {{-- STEP 2: Informasi Bengkel --}}
            <div x-show="step === 2" x-transition.fade class="space-y-4" style="display:none;">
                <div style="background-color:rgba(255,255,255,0.02);border:1px solid #2E3030;border-radius:12px;padding:16px;">
                    <p style="font-size:11px;font-weight:600;color:#71717A;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:12px;">Langkah 2: Informasi Bengkel</p>

                    {{-- Nama Bengkel --}}
                    <div class="form-group" style="margin-bottom:12px;">
                        <label for="workshop_name" class="form-label">Nama Bengkel</label>
                        <input id="workshop_name" type="text" name="workshop_name" x-model="workshop_name"
                            required placeholder="Contoh: Bengkel Jaya Motor"
                            class="form-input {{ $errors->has('workshop_name') ? 'form-input-error' : '' }}" />
                        @error('workshop_name') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    {{-- Nomor Telepon Bengkel --}}
                    <div class="form-group" style="margin-bottom:12px;">
                        <label for="phone" class="form-label">Nomor Telepon Bengkel</label>
                        <input id="phone" type="tel" name="phone" x-model="phone"
                            required placeholder="Contoh: 08123456789"
                            class="form-input {{ $errors->has('phone') ? 'form-input-error' : '' }}" />
                        @error('phone') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    {{-- Alamat Lengkap --}}
                    <div class="form-group" style="margin-bottom:12px;">
                        <label for="address" class="form-label">Alamat Lengkap Bengkel</label>
                        <textarea id="address" name="address" x-model="address" required rows="2"
                            placeholder="Jl. Contoh No. 123, Kelurahan, Kecamatan"
                            class="form-textarea {{ $errors->has('address') ? 'form-input-error' : '' }}" style="min-height:70px;"></textarea>
                        @error('address') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    {{-- Provinsi & Kota/Kabupaten --}}
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
                        {{-- Provinsi --}}
                        <div class="form-group">
                            <label for="select_province" class="form-label">Provinsi</label>
                            <select id="select_province"
                                    x-model="selected_province"
                                    @change="onProvinceChange()"
                                    class="form-input {{ $errors->has('province') ? 'form-input-error' : '' }}">
                                <option value="">-- Pilih Provinsi --</option>
                                <template x-for="prov in provinceOptions" :key="prov">
                                    <option :value="prov" x-text="prov"></option>
                                </template>
                                <option value="Lainnya">Lainnya (Isi Manual)</option>
                            </select>

                            <div x-show="selected_province === 'Lainnya'" style="margin-top:8px;">
                                <input type="text"
                                       x-model="custom_province"
                                       placeholder="Ketik nama provinsi..."
                                       class="form-input" />
                            </div>

                            <input type="hidden" name="province" :value="effectiveProvince" />
                            @error('province') <p class="form-error">{{ $message }}</p> @enderror
                        </div>

                        {{-- Kota / Kabupaten --}}
                        <div class="form-group">
                            <label for="select_city" class="form-label">Kota / Kabupaten</label>
                            <select id="select_city"
                                    x-model="selected_city"
                                    @change="onCityChange()"
                                    :disabled="!selected_province"
                                    class="form-input {{ $errors->has('city') ? 'form-input-error' : '' }}">
                                <option value="">-- Pilih Kota / Kab --</option>
                                <template x-for="c in cityOptions" :key="c">
                                    <option :value="c" x-text="c"></option>
                                </template>
                                <option value="Lainnya" x-show="selected_province">Lainnya (Isi Manual)</option>
                            </select>

                            <div x-show="selected_city === 'Lainnya' || selected_province === 'Lainnya'" style="margin-top:8px;">
                                <input type="text"
                                       x-model="custom_city"
                                       placeholder="Ketik nama kota / kabupaten..."
                                       class="form-input" />
                            </div>

                            <input type="hidden" name="city" :value="effectiveCity" />
                            @error('city') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Titik Koordinat (Latitude & Longitude) --}}
                    <div style="margin-bottom:12px;background:rgba(255,255,255,0.015);border:1px solid #2E3030;border-radius:10px;padding:12px;">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;flex-wrap:wrap;gap:8px;">
                            <div>
                                <label class="form-label" style="margin-bottom:2px;display:block;">Titik Koordinat Lokasi Bengkel (Opsional)</label>
                                <p style="font-size:11px;color:#71717A;margin:0;">Diperlukan agar posisi bengkel muncul di Peta Maintify</p>
                            </div>
                            <button type="button"
                                    @click="getLocation()"
                                    :disabled="isLocating"
                                    class="btn-primary"
                                    style="padding:6px 12px;font-size:11px;background:#1E293B;border-color:#334155;color:#38BDF8;height:auto;">
                                <svg x-show="!isLocating" style="width:14px;height:14px;margin-right:4px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span x-text="isLocating ? 'Mengambil GPS...' : 'Ambil GPS Saat Ini'"></span>
                            </button>
                        </div>

                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                            <div class="form-group">
                                <label for="latitude" class="form-label" style="font-size:11px;">Latitude (Garis Lintang)</label>
                                <input id="latitude" type="number" step="any" name="latitude" x-model="latitude"
                                    placeholder="Contoh: -6.2088"
                                    class="form-input {{ $errors->has('latitude') ? 'form-input-error' : '' }}" />
                                @error('latitude') <p class="form-error">{{ $message }}</p> @enderror
                            </div>
                            <div class="form-group">
                                <label for="longitude" class="form-label" style="font-size:11px;">Longitude (Garis Bujur)</label>
                                <input id="longitude" type="number" step="any" name="longitude" x-model="longitude"
                                    placeholder="Contoh: 106.8456"
                                    class="form-input {{ $errors->has('longitude') ? 'form-input-error' : '' }}" />
                                @error('longitude') <p class="form-error">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Jam Operasional --}}
                    <div class="form-group">
                        <label for="operational_hours" class="form-label">Jam Operasional</label>
                        <input id="operational_hours" type="text" name="operational_hours" x-model="operational_hours"
                            required placeholder="Contoh: Senin - Sabtu: 08:00 - 17:00"
                            class="form-input {{ $errors->has('operational_hours') ? 'form-input-error' : '' }}" />
                        @error('operational_hours') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 2fr;gap:12px;margin-top:4px;">
                    <button type="button" @click="step = 1" class="btn-primary" style="justify-content:center;background:#2E3030;border-color:#3A3D3D;">
                        Kembali
                    </button>
                    <button type="button" @click="if (validateStep2()) { step = 3 } else { alert('Mohon lengkapi semua data bengkel (Provinsi dan Kota/Kabupaten harus diisi).') }" class="btn-primary" style="justify-content:center;">
                        Lanjutkan
                        <svg style="width:16px;height:16px;margin-left:4px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- STEP 3: Dokumen & Password --}}
            <div x-show="step === 3" x-transition.fade class="space-y-4" style="display:none;">
                <div style="background-color:rgba(255,255,255,0.02);border:1px solid #2E3030;border-radius:12px;padding:16px;">
                    <p style="font-size:11px;font-weight:600;color:#71717A;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:12px;">Langkah 3: Dokumen & Password</p>

                    {{-- Dokumen Legalitas --}}
                    <div class="form-group" style="margin-bottom:12px;">
                        <label for="legal_document" class="form-label">Dokumen Legalitas (NIB/SIUP/KTP Pemilik)</label>
                        <input id="legal_document" type="file" name="legal_document"
                            required accept=".pdf,.jpg,.jpeg,.png"
                            class="form-input {{ $errors->has('legal_document') ? 'form-input-error' : '' }}" style="padding-top:8px;" />
                        <p style="font-size:11px;color:#71717A;margin-top:4px;">Format: PDF, JPG, JPEG, PNG (Maks. 10MB)</p>
                        @error('legal_document') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    {{-- Kata Sandi --}}
                    <div class="form-group" style="margin-bottom:12px;" x-data="{ show: false }">
                        <label for="password" class="form-label">Password Akun</label>
                        <div style="position:relative;margin-top:4px;">
                            <input id="password" :type="show ? 'text' : 'password'" name="password"
                                required autocomplete="new-password" placeholder="Min. 8 karakter"
                                class="form-input {{ $errors->has('password') ? 'form-input-error' : '' }}" style="padding-right:42px;" />
                            <button type="button" @click="show = !show"
                                    style="position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;padding:0;cursor:pointer;color:#71717A;">
                                <svg x-show="!show" style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg x-show="show" style="width:16px;height:16px;display:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                        @error('password') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    {{-- Konfirmasi Kata Sandi --}}
                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password Akun</label>
                        <input id="password_confirmation" type="password" name="password_confirmation"
                            required autocomplete="new-password" placeholder="Ulangi password"
                            class="form-input {{ $errors->has('password_confirmation') ? 'form-input-error' : '' }}" />
                        @error('password_confirmation') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Info Approval --}}
                <div style="background-color:rgba(245,158,11,0.08);border:1px solid rgba(245,158,11,0.2);border-radius:10px;padding:12px 14px;display:flex;gap:10px;align-items:flex-start;">
                    <svg style="width:16px;height:16px;color:#fbbf24;flex-shrink:0;margin-top:2px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p style="font-size:12px;color:#fbbf24;line-height:1.5;margin:0;">
                        Pendaftaran bengkel memerlukan verifikasi dari admin Maintify. Proses verifikasi membutuhkan 1-2 hari kerja.
                    </p>
                </div>

                <div style="display:grid;grid-template-columns:1fr 2fr;gap:12px;margin-top:4px;">
                    <button type="button" @click="step = 2" class="btn-primary" style="justify-content:center;background:#2E3030;border-color:#3A3D3D;">
                        Kembali
                    </button>
                    <button type="submit" id="btn-register-workshop" class="btn-primary" style="justify-content:center;">
                        <svg style="width:16px;height:16px;margin-right:4px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Daftar Sebagai Bengkel Mitra
                    </button>
                </div>
            </div>

            {{-- Login Link --}}
            <p style="text-align:center;font-size:13px;color:#71717A;margin-top:12px;">
                Sudah punya akun?
                <a href="{{ route('login') }}"
                   style="color:#ff9aa4;font-weight:600;margin-left:4px;transition:color 150ms;"
                   onmouseover="this.style.color='#ff5f71'"
                   onmouseout="this.style.color='#ff9aa4'">
                    Masuk di sini
                </a>
            </p>
        </form>
    </div>
</x-guest-layout>
