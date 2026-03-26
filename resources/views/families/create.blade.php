<x-app-layout>
    @push('leaflet_create')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
            integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    @endpush
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Keluarga') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <section class="bg-white dark:bg-gray-900">
                    <div class="py-8 lg:py-16 px-4 mx-auto max-w-screen-md">
                        <form action="{{ route('family.store') }}" method="POST" class="space-y-4 form" id="main-form"
                            enctype="multipart/form-data">
                            @csrf
                            @if ($errors->any())
                                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                                    <strong>Terjadi kesalahan:</strong>
                                    <ul class="mt-2 list-disc list-inside">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- Lokasi Keluarga -->
                            <div>
                                <label for="family_location"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Lokasi
                                    Keluarga <span class="text-red-500">*</span></label>
                                <div id="map"
                                    class="lg:w-full max-w-full sm:w-full mx-auto h-52 rounded-xl border-2 border-orange-300 mb-5">
                                </div>

                                <label for="kelurahan_id"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kelurahan/Desa<span
                                        class="text-red-500">*</span></label>
                                <select id="kelurahan_id" name="kelurahan_id"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                    <option value="">--- Pilih Kelurahan/Desa ---</option>
                                    @foreach ($kelurahans as $kel)
                                        <option value="{{ $kel->id }}"
                                            {{ old('kelurahan_id') == $kel->id ? 'selected' : '' }}>
                                            {{ $kel->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('kelurahan_id')
                                    <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="rt_rw_id"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">RT/RW/DUSUN<span
                                        class="text-red-500">*</span></label>
                                <select id="rt_rw_id" name="rt_rw_id"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                    <option value="">--- Pilih RT/RW/DUSUN ---</option>
                                </select>
                                @error('rt_rw_id')
                                    <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Alamat KTP -->
                            <div class="">
                                <label for="ktp_address"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Alamat
                                    sesuai Kartu Tanda Penduduk (KTP) <span class="text-red-500">*</span></label>
                                <input value="{{ old('ktp_address') }}" type="text" id="ktp_address"
                                    name="ktp_address"
                                    class="@error('ktp_address') is-invalid @enderror block p-3 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 shadow-sm focus:ring-primary-500 focus:border-primary-500"
                                    placeholder="" required>
                                @error('ktp_address')
                                    <div class="pt-2">
                                        <span class="text-red-500 text-sm">Kesalahan input, {{ $message }}</span>
                                    </div>
                                @enderror
                            </div>

                            <!-- Kota Asal -->
                            <div class="">
                                <label for="city_address"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Jika
                                    Kelurahan Domisili Berbeda dengan KTP, sebutkan asal Kota/Kabupaten<span
                                        class="text-red-500">*</span></label>
                                <input value="{{ old('city_address') }}" type="text" id="city_address"
                                    name="city_address"
                                    class="@error('city_address') is-invalid @enderror block p-3 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 shadow-sm focus:ring-primary-500 focus:border-primary-500"
                                    placeholder="isikan '-' jika KTP Kelurahan Domisili Sama" required>
                                @error('city_address')
                                    <div class="pt-2">
                                        <span class="text-red-500 text-sm">Kesalahan input, {{ $message }}</span>
                                    </div>
                                @enderror
                            </div>

                            <!-- Kepemilikan KK -->
                            <div class="">
                                <label for="ownership_kk"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kepemilikan
                                    Kartu Keluarga (KK) <span class="text-red-500">*</span></label>
                                <select id="ownership_kk" name="ownership_kk"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                    <option value="">--- Pilih Status Kepemilikan ---</option>
                                    <option value="KK" {{ old('ownership_kk') == 'KK' ? 'selected' : '' }}>KK
                                    </option>
                                    <option value="Non KK" {{ old('ownership_kk') == 'Non KK' ? 'selected' : '' }}>Non
                                        KK</option>
                                </select>
                                @error('ownership_kk')
                                    <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Nomor KK -->
                            <div class="">
                                <label for="number_kk"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Nomor Kartu
                                    Keluarga <span class="text-red-500">*</span></label>
                                <input value="{{ old('number_kk') }}" type="text" id="number_kk" name="number_kk"
                                    class="@error('number_kk') is-invalid @enderror block p-3 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 shadow-sm focus:ring-primary-500 focus:border-primary-500"
                                    placeholder="" required>
                                @error('number_kk')
                                    <div class="pt-2">
                                        <span class="text-red-500 text-sm">Kesalahan input, {{ $message }}</span>
                                    </div>
                                @enderror
                            </div>

                            <!-- Jumlah Anggota -->
                            <div class="">
                                <label for="number_of_family_member"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jumlah Anggota
                                    Keluarga <span class="text-red-500">*</span></label>
                                <input type="number" id="number_of_family_member" min="1" max="15"
                                    step="1" name="number_of_family_member"
                                    value="{{ old('number_of_family_member') }}"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                    placeholder="Minimal anggota keluarga 1" required />
                                @error('number_of_family_member')
                                    <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Bantuan Sosial -->
                            <div class="">
                                <label for="bpnt"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Menerima
                                    Bantuan Pangan Non-Tunai (BPNT) <span class="text-red-500">*</span></label>
                                <select id="bpnt" name="bpnt"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                    <option value="">--- Pilih Status ---</option>
                                    <option value="Ya" {{ old('bpnt') == 'Ya' ? 'selected' : '' }}>Ya</option>
                                    <option value="Tidak" {{ old('bpnt') == 'Tidak' ? 'selected' : '' }}>Tidak
                                    </option>
                                </select>
                                @error('bpnt')
                                    <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="">
                                <label for="pkh"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Menerima
                                    Program Keluarga Harapan (PKH) <span class="text-red-500">*</span></label>
                                <select id="pkh" name="pkh"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                    <option value="">--- Pilih Status ---</option>
                                    <option value="Ya" {{ old('pkh') == 'Ya' ? 'selected' : '' }}>Ya</option>
                                    <option value="Tidak" {{ old('pkh') == 'Tidak' ? 'selected' : '' }}>Tidak
                                    </option>
                                </select>
                                @error('pkh')
                                    <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="">
                                <label for="blt_elderly"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Menerima BLT
                                    Lansia <span class="text-red-500">*</span></label>
                                <select id="blt_elderly" name="blt_elderly"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                    <option value="">--- Pilih Status ---</option>
                                    <option value="Ya" {{ old('blt_elderly') == 'Ya' ? 'selected' : '' }}>Ya
                                    </option>
                                    <option value="Tidak" {{ old('blt_elderly') == 'Tidak' ? 'selected' : '' }}>Tidak
                                    </option>
                                </select>
                                @error('blt_elderly')
                                    <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="">
                                <label for="blt_village"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Menerima BLT
                                    Dana Desa <span class="text-red-500">*</span></label>
                                <select id="blt_village" name="blt_village"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                    <option value="">--- Pilih Status ---</option>
                                    <option value="Ya" {{ old('blt_village') == 'Ya' ? 'selected' : '' }}>Ya
                                    </option>
                                    <option value="Tidak" {{ old('blt_village') == 'Tidak' ? 'selected' : '' }}>Tidak
                                    </option>
                                </select>
                                @error('blt_village')
                                    <p class="mt-2 text-xs text-red-600 h-">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Bantuan Lainnya -->
                            <div>
                                <label for="other_assistance"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Apakah
                                    menerima bantuan lainnya? Jika YA, sebutkan <span
                                        class="text-red-500">*</span></label>
                                <input value="{{ old('other_assistance') }}" type="text" id="other_assistance"
                                    name="other_assistance"
                                    class="@error('other_assistance') is-invalid @enderror block p-3 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 shadow-sm focus:ring-primary-500 focus:border-primary-500"
                                    placeholder="Tuliskan dengan lengkap" required>
                                @error('other_assistance')
                                    <div class="pt-2">
                                        <span class="text-red-500 text-sm">Kesalahan input, {{ $message }}</span>
                                    </div>
                                @enderror
                            </div>

                            <div class="pb-4" id="photo-container">
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                    for="house_photo">
                                    Upload Foto Rumah
                                </label>
                                <div id="photo-preview" class="mb-3"></div>
                                <div id="photo-loader" class="mb-3"></div>
                                <input
                                    class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none dark:bg-gray-700 dark:text-gray-400"
                                    id="house_photo" name="house_photo" type="file"
                                    accept="image/png, image/jpeg, image/jpg">

                                <input type="hidden" name="temp_photo" id="temp_photo"
                                    value="{{ old('temp_photo') }}">
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-300" id="file_input_help">
                                    PNG, JPG, JPEG (MAX. 5MB).
                                </p>
                                @error('house_photo')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Container untuk Anggota Keluarga -->
                            <div id="members-container" class="space-y-6 mt-6"></div>

                            <!-- Hidden Inputs -->
                            <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
                            <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">

                            <!-- Tombol Aksi -->
                            <div class="flex justify-between space-x-5">
                                <button type="button" onclick="window.location.href='{{ route('family.index') }}'"
                                    class="w-full py-3 px-5 text-sm font-medium text-center text-white rounded-lg bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300">Kembali</button>
                                <button type="submit"
                                    class="w-full py-3 px-5 text-sm font-medium text-center text-white rounded-lg bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300">Submit</button>
                            </div>
                        </form>
                    </div>
                </section>
            </div>
        </div>
    </div>

    @push('leaflet_create_js')
        {{-- Data old members dari server --}}
        <script>
            // Data old members dari server
            const oldMembers = @json(old('members', []));
            const oldNumber = {{ old('number_of_family_member', 0) }};
            const container = document.getElementById('members-container');

            document.addEventListener('DOMContentLoaded', function() {
                const numberInput = document.getElementById('number_of_family_member');

                // Set nilai input number jika ada old value
                if (oldNumber > 0) {
                    numberInput.value = oldNumber;

                    // Generate form members dengan data old
                    generateMemberForms(oldMembers);
                }

                // Event listener untuk perubahan jumlah
                numberInput.addEventListener('input', function() {
                    const total = parseInt(this.value) || 0;
                    generateMemberForms(Array(total).fill({}));
                });
            });

            function generateMemberForms(membersData) {
                container.innerHTML = '';

                membersData.forEach((data, index) => {
                    container.innerHTML += createMemberForm(index, data);
                });

                setTimeout(() => {
                    initDatepickers();

                    for (let i = 0; i < membersData.length; i++) {
                        updateStepButtons(i);
                        setupEmploymentStatusListener(i); // TAMBAHKAN INI
                    }
                }, 200);

                setTimeout(() => {
                    validateKepalaKeluarga();
                }, 300);
            }

            // Fungsi untuk mengatur tampilan Blok C berdasarkan employment_status
            function toggleBlokC(memberIndex) {
                const statusSelect = document.querySelector(`select[name="members[${memberIndex}][employment_status]"]`);
                const blokCDiv = document.querySelector(`.step-${memberIndex}-2`);

                if (!statusSelect || !blokCDiv) return;

                const value = statusSelect.value;

                // AMBIL SEMUA FIELD (BUKAN YANG REQUIRED SAJA)
                const fields = blokCDiv.querySelectorAll('input, select');

                if (value === 'Bekerja') {
                    fields.forEach(field => {
                        field.setAttribute('required', 'required');
                    });
                } else {
                    fields.forEach(field => {
                        field.removeAttribute('required');
                    });
                }

                updateStepButtons(memberIndex);
            }

            // Fungsi untuk setup event listener pada status pekerjaan
            function setupEmploymentStatusListener(memberIndex) {
                const statusSelect = document.querySelector(`select[name="members[${memberIndex}][employment_status]"]`);
                if (statusSelect) {
                    // Hapus listener lama (pakai clone untuk menghindari duplikasi)
                    const newSelect = statusSelect.cloneNode(true);
                    statusSelect.parentNode.replaceChild(newSelect, statusSelect);

                    newSelect.addEventListener('change', function() {
                        toggleBlokC(memberIndex);

                        // Update warna tombol
                        updateStepButtons(memberIndex);
                    });

                    // Panggil sekali untuk inisialisasi
                    toggleBlokC(memberIndex);
                }
            }

            function createMemberForm(index, oldData = {}) {
                return `
                <div class="border rounded-xl p-6 shadow bg-gray-50 member-card" data-index="${index}">
                    <h2 class="text-lg font-bold mb-4 flex items-center">
                        Anggota Keluarga Ke ${index + 1}
                    </h2>

                    <!-- STEP NAV -->
                    <div class="flex gap-2 mb-6 flex-wrap">
                        <button type="button" onclick="showStep(${index},0)" class="step-btn text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br font-medium rounded-lg text-sm px-4 py-2.5">Blok A</button>
                        <button type="button" onclick="showStep(${index},1)" class="step-btn text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br font-medium rounded-lg text-sm px-4 py-2.5">Blok B</button>
                        <button type="button" onclick="showStep(${index},2)" class="step-btn text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br font-medium rounded-lg text-sm px-4 py-2.5">Blok C</button>
                        <button type="button" onclick="showStep(${index},3)" class="step-btn text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br font-medium rounded-lg text-sm px-4 py-2.5">Blok D</button>
                    </div>

                    <!-- BLOK A -->
                    <div class="step step-${index}-0">
                        <h3 class="font-semibold mb-3">A. DATA IDENTITAS DASAR</h3>

                        <!-- Nama Lengkap -->
                        <div class="pb-4">
                            <label class="block mb-2 text-sm font-medium text-gray-900">Nama Lengkap (Sesuai KTP) <span class="text-red-500">*</span></label>
                            <input type="text" name="members[${index}][full_name]" value="${escapeHtml(oldData.full_name || '')}"
                                class="block p-3 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300" required>
                        </div>

                        <!-- NIK -->
                        <div class="pb-4">
                            <label class="block mb-2 text-sm font-medium text-gray-900">Nomor Induk Kependudukan (NIK) <span class="text-red-500">*</span></label>
                            <input type="text" name="members[${index}][nik]" value="${escapeHtml(oldData.nik || '')}"
                                class="block p-3 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300" required>
                        </div>

                        <!-- Status dalam keluarga -->
                        <div class="pb-4">
                            <label class="block mb-2 text-sm font-medium text-gray-900">Status dalam keluarga <span class="text-red-500">*</span></label>
                            <select name="members[${index}][status_in_family]" class="bg-gray-50 border border-gray-300 rounded-lg w-full p-2.5 status-family-select" required>
                                <option value="">--- Pilih status Keluarga ---</option>
                                ${generateOptions([
                    'Kepala Keluarga', 'Istri/Suami', 'Anak', 'Menantu',
                    'Cucu', 'Orang Tua/Mertua', 'Pembantu', 'Sopir', 'Family lainnya'
                ], oldData.status_in_family)}
                            </select>
                        </div>

                        <!-- Tempat Lahir -->
                        <div class="pb-4">
                            <label class="block mb-2 text-sm font-medium text-gray-900">Tempat Lahir <span class="text-red-500">*</span></label>
                            <input type="text" name="members[${index}][place_of_birth]" value="${escapeHtml(oldData.place_of_birth || '')}"
                                class="block p-3 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300" required>
                        </div>

                        <!-- Tanggal Lahir -->
                        <div class="pb-4">
                            <label class="block mb-2 text-sm font-medium text-gray-900">Tanggal Lahir <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                                    </svg>
                                </div>
                                <input type="text" name="members[${index}][date_of_birth]" id="date_of_birth_${index}"
                                    value="${escapeHtml(oldData.date_of_birth || '')}"
                                    datepicker datepicker-buttons datepicker-autohide
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full ps-10 p-2.5 datepicker-input"
                                    placeholder="mm/dd/yyyy" required>
                            </div>
                        </div>

                        <!-- Jenis Kelamin -->
                        <div class="pb-4">
                            <label class="block mb-2 text-sm font-medium text-gray-900">Jenis kelamin <span class="text-red-500">*</span></label>
                            <select name="members[${index}][gender]" class="bg-gray-50 border border-gray-300 rounded-lg w-full p-2.5" required>
                                <option value="">--- Pilih Jenis Kelamin ---</option>
                                ${generateOptions(['Laki-laki', 'Perempuan'], oldData.gender)}
                            </select>
                        </div>

                        <button type="button" onclick="nextStep(${index},1)"
                            class="w-full py-3 px-5 text-sm font-medium text-center text-white rounded-lg bg-primary-700 hover:bg-primary-800">Selanjutnya</button>
                    </div>

                    <!-- BLOK B -->
                    <div class="step step-${index}-1 hidden">
                        <h3 class="font-semibold mb-3">B. KARAKTERISTIK SOSIAL EKONOMI</h3>

                        <!-- Status Perkawinan -->
                        <div class="pb-4">
                            <label class="block mb-2 text-sm font-medium text-gray-900">Status Perkawinan <span class="text-red-500">*</span></label>
                            <select name="members[${index}][marital_status]" class="bg-gray-50 border border-gray-300 rounded-lg w-full p-2.5" required>
                                <option value="">--- Pilih Status Perkawinan ---</option>
                                ${generateOptions(['Belum kawin', 'Kawin', 'Cerai hidup', 'Cerai mati'], oldData.marital_status)}
                            </select>
                        </div>

                        <!-- Suku -->
                        <div class="pb-4">
                            <label class="block mb-2 text-sm font-medium text-gray-900">Suku <span class="text-red-500">*</span></label>
                            <select name="members[${index}][ethnic]" class="bg-gray-50 border border-gray-300 rounded-lg w-full p-2.5" required>
                                <option value="">--- Pilih Suku ---</option>
                                ${generateOptions(['Melayu', 'Jawa', 'Tionghoa', 'Batak', 'Minang', 'Sunda', 'Flores', 'Dayak', 'Bugis', 'Lainnya'], oldData.ethnic)}
                            </select>
                        </div>

                        <!-- Pendidikan Tertinggi -->
                        <div class="pb-4">
                            <label class="block mb-2 text-sm font-medium text-gray-900">Pendidikan Tertinggi yang Pernah/Sedang Diikuti <span class="text-red-500">*</span></label>
                            <select name="members[${index}][highest_education_level]" class="bg-gray-50 border border-gray-300 rounded-lg w-full p-2.5" required>
                                <option value="">--- Pilih Pendidikan ---</option>
                                ${generateOptions([
                    'Belum Pernah Bersekolah', 'Tidak Punya Ijazah', 'SD/MI/SDLB/PAKET A',
                    'SMP/MTS/SMPLB/PAKET B', 'SMA/MA/SMLB/PAKET C', 'SMK/MAK',
                    'Diploma I/II/III', 'S1/Diploma IV', 'S2', 'S3'
                ], oldData.highest_education_level)}
                            </select>
                        </div>

                        <!-- Ijazah Terakhir -->
                        <div class="pb-4">
                            <label class="block mb-2 text-sm font-medium text-gray-900">Ijazah Terakhir <span class="text-red-500">*</span></label>
                            <select name="members[${index}][highest_education_certificate]" class="bg-gray-50 border border-gray-300 rounded-lg w-full p-2.5" required>
                                <option value="">--- Pilih Ijazah ---</option>
                                ${generateOptions([
                    'Belum Pernah Bersekolah', 'Tidak Punya Ijazah', 'SD/MI/SDLB/PAKET A',
                    'SMP/MTS/SMPLB/PAKET B', 'SMA/MA/SMLB/PAKET C', 'SMK/MAK',
                    'Diploma I/II/III', 'S1/Diploma IV', 'S2', 'S3'
                ], oldData.highest_education_certificate)}
                            </select>
                        </div>

                        <!-- Status Ketenagakerjaan -->
                        <div class="pb-4">
                            <label class="block mb-2 text-sm font-medium text-gray-900">Status Ketenagakerjaan <span class="text-red-500">*</span></label>
                            <select name="members[${index}][employment_status]" class="bg-gray-50 border border-gray-300 rounded-lg w-full p-2.5" required>
                                <option value="">--- Pilih Status Ketenagakerjaan ---</option>
                                ${generateOptions(['Bekerja', 'Sekolah', 'Mengurus Rumah Tangga', 'Lainnya', 'Tidak Bekerja'], oldData.employment_status)}
                            </select>
                        </div>

                        <div class="flex justify-between space-x-5">
                            <button type="button" onclick="nextStep(${index},0)"
                                class="w-full py-3 px-5 text-sm font-medium text-center text-white rounded-lg bg-primary-700 hover:bg-primary-800">Kembali</button>
                            <button type="button" onclick="nextStep(${index},2)"
                                class="w-full py-3 px-5 text-sm font-medium text-center text-white rounded-lg bg-primary-700 hover:bg-primary-800">Selanjutnya</button>
                        </div>
                    </div>

                    <!-- BLOK C -->
                    <div class="step step-${index}-2 hidden">
                        <h3 class="font-semibold mb-3">C. KETENAGAKERJAAN</h3>

                        <!-- Pekerjaan Utama -->
                        <div class="pb-4">
                            <label class="block mb-2 text-sm font-medium text-gray-900">Pekerjaan Utama <span class="text-red-500">*</span></label>
                            <input type="text" name="members[${index}][main_occupation]" value="${escapeHtml(oldData.main_occupation || '')}"
                                class="block p-3 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300" ${oldData.employment_status === 'Bekerja' ? 'required' : ''}>
                        </div>

                        <!-- Status Dalam Pekerjaan -->
                        <div class="pb-4">
                            <label class="block mb-2 text-sm font-medium text-gray-900">Status Dalam Pekerjaan <span class="text-red-500">*</span></label>
                            <select name="members[${index}][employment_position]" class="bg-gray-50 border border-gray-300 rounded-lg w-full p-2.5" ${oldData.employment_status === 'Bekerja' ? 'required' : ''}>
                                <option value="">--- Pilih Status Dalam Pekerjaan ---</option>
                                ${generateOptions([
                    'Berusaha', 'Buruh/Karyawan/Pegawai Swasta',
                    'PNS/TNI/POLRI/Pegawai BUMN/Pegawai BUMD/Pejabat Negara',
                    'Pekerja bebas (Freelance)', 'Pekerja tidak dibayar'
                ], oldData.employment_position)}
                            </select>
                        </div>

                        <div class="flex justify-between space-x-5">
                            <button type="button" onclick="prevStep(${index},1)"
                                class="w-full py-3 px-5 text-sm font-medium text-center text-white rounded-lg bg-primary-700 hover:bg-primary-800">Kembali</button>
                            <button type="button" onclick="nextStep(${index},3)"
                                class="w-full py-3 px-5 text-sm font-medium text-center text-white rounded-lg bg-primary-700 hover:bg-primary-800">Selanjutnya</button>
                        </div>
                    </div>

                    <!-- BLOK D -->
                    <div class="step step-${index}-3 hidden">
                        <h3 class="font-semibold mb-3">D. KESEHATAN & DISABILITAS</h3>

                        <!-- Jaminan Kesehatan -->
                        <div class="pb-4">
                            <label class="block mb-2 text-sm font-medium text-gray-900">Jaminan Kesehatan yang dimiliki <span class="text-red-500">*</span></label>
                            <select name="members[${index}][health_insurance]" class="bg-gray-50 border border-gray-300 rounded-lg w-full p-2.5" required>
                                <option value="">--- Pilih Jaminan Kesehatan ---</option>
                                ${generateOptions([
                    'BPJS Kesehatan PBI', 'BPJS Kesehatan Non PBI/Mandiri',
                    'JAMKESDA', 'Tidak memiliki'
                ], oldData.health_insurance)}
                            </select>
                        </div>

                        <!-- Status Stunting -->
                        <div class="pb-4">
                            <label class="block mb-2 text-sm font-medium text-gray-900">Status Stunting <span class="text-red-500">*</span></label>
                            <select name="members[${index}][stunting]" class="bg-gray-50 border border-gray-300 rounded-lg w-full p-2.5" required>
                                <option value="">--- Pilih Status Stunting ---</option>
                                ${generateOptions(['Ya', 'Tidak'], oldData.stunting)}
                            </select>
                        </div>

                        <!-- Status Disabilitas -->
                        <div class="pb-4">
                            <label class="block mb-2 text-sm font-medium text-gray-900">Status Disabilitas <span class="text-red-500">*</span></label>
                            <select name="members[${index}][disability]" class="bg-gray-50 border border-gray-300 rounded-lg w-full p-2.5" required>
                                <option value="">--- Pilih Status Disabilitas ---</option>
                                ${generateOptions(['Ya', 'Tidak'], oldData.disability)}
                            </select>
                        </div>

                        <button type="button" onclick="prevStep(${index},2)"
                            class="w-full py-3 px-5 text-sm font-medium text-center text-white rounded-lg bg-primary-700 hover:bg-primary-800">Kembali</button>
                    </div>
                </div>
            `;
            }

            function isStepComplete(memberIndex, stepIndex) {
                const stepDiv = document.querySelector(`.step-${memberIndex}-${stepIndex}`);
                if (!stepDiv) return false;

                // Khusus untuk Blok C (step 2)
                if (stepIndex === 2) {
                    const statusSelect = document.querySelector(`select[name="members[${memberIndex}][employment_status]"]`);
                    const value = statusSelect ? statusSelect.value : '';

                    // kalau belum pilih → belum complete
                    if (!value) return false;

                    // kalau sudah pilih tapi bukan bekerja → complete
                    if (value !== 'Bekerja') return true;
                }

                // Cari semua input dan select yang required di step ini
                const fields = stepDiv.querySelectorAll('input[required], select[required]');

                // Kalau tidak ada field required, anggap complete
                if (fields.length === 0) return true;

                // Cek satu per satu
                for (let field of fields) {
                    if (!field.value || field.value.trim() === '') {
                        return false; // Ada yang kosong
                    }
                }

                return true; // Semua terisi
            }

            function updateStepButtons(memberIndex) {
                for (let step = 0; step <= 3; step++) {

                    const buttons = document.querySelectorAll(
                        `button[onclick*="showStep(${memberIndex},${step})"]`
                    );

                    if (step === 2) {
                        const statusSelect = document.querySelector(
                            `select[name="members[${memberIndex}][employment_status]"]`
                        );

                        const value = statusSelect ? statusSelect.value : '';

                        buttons.forEach(button => {
                            // RESET SEMUA STYLE
                            button.classList.remove(
                                'from-green-500', 'via-green-600', 'to-green-700',
                                'from-blue-500', 'via-blue-600', 'to-blue-700',
                                'from-gray-400', 'via-gray-500', 'to-gray-600',
                                'cursor-not-allowed', 'opacity-60'
                            );

                            // ❌ BELUM PILIH → tetap biru normal
                            if (!value) {
                                button.disabled = false;
                                button.classList.add('from-blue-500', 'via-blue-600', 'to-blue-700');
                            }

                            // ❌ TIDAK BEKERJA → disable + abu
                            else if (value !== 'Bekerja') {
                                button.disabled = true;
                                button.classList.add(
                                    'from-gray-400', 'via-gray-500', 'to-gray-600',
                                    'cursor-not-allowed', 'opacity-60'
                                );
                            }

                            // ✅ BEKERJA → normal logic
                            else {
                                button.disabled = false;

                                if (isStepComplete(memberIndex, step)) {
                                    button.classList.add('from-green-500', 'via-green-600', 'to-green-700');
                                } else {
                                    button.classList.add('from-blue-500', 'via-blue-600', 'to-blue-700');
                                }
                            }
                        });

                        continue; // skip ke step berikutnya
                    }

                    // STEP LAIN (A, B, D)
                    buttons.forEach(button => {
                        button.classList.remove(
                            'from-green-500', 'via-green-600', 'to-green-700',
                            'from-blue-500', 'via-blue-600', 'to-blue-700'
                        );

                        if (isStepComplete(memberIndex, step)) {
                            button.classList.add('from-green-500', 'via-green-600', 'to-green-700');
                        } else {
                            button.classList.add('from-blue-500', 'via-blue-600', 'to-blue-700');
                        }
                    });
                }
            }

            // EVENT LISTENER - Update warna saat input berubah
            document.addEventListener('input', function(e) {
                const memberCard = e.target.closest('.member-card');
                if (!memberCard) return;

                const memberIndex = memberCard.dataset.index;
                if (memberIndex !== undefined) {
                    updateStepButtons(parseInt(memberIndex));
                }
            });

            document.addEventListener('change', function(e) {
                const memberCard = e.target.closest('.member-card');
                if (!memberCard) return;

                const memberIndex = memberCard.dataset.index;
                if (memberIndex !== undefined) {
                    updateStepButtons(parseInt(memberIndex));
                }
            });

            // Helper function untuk generate options select
            function generateOptions(options, selectedValue) {
                return options.map(opt =>
                    `<option value="${opt}" ${selectedValue === opt ? 'selected' : ''}>${opt}</option>`
                ).join('');
            }

            // Escape HTML untuk keamanan
            function escapeHtml(text) {
                if (!text) return '';
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            function showStep(member, step, isPrev = false) {
                if (step === 2) {
                    const statusSelect = document.querySelector(`select[name="members[${member}][employment_status]"]`);
                    const value = statusSelect ? statusSelect.value : '';
                    if (value && value !== 'Bekerja') {
                        if (isPrev) {
                            step = 1;
                        } else {
                            step = 3;
                        }
                    }
                }

                document.querySelectorAll(`.step-${member}-0, .step-${member}-1, .step-${member}-2, .step-${member}-3`)
                    .forEach(el => el.classList.add('hidden'));
                document.querySelector(`.step-${member}-${step}`).classList.remove('hidden');
            }

            function nextStep(member, step) {
                showStep(member, step, false);
            }

            function prevStep(member, step) {
                showStep(member, step, true);
            }

            // Inisialisasi datepicker
            function initDatepickers() {
                document.querySelectorAll('.datepicker-input').forEach(el => {
                    if (!el.hasAttribute('data-datepicker-initialized')) {
                        try {
                            new Datepicker(el, {
                                format: 'mm/dd/yyyy',
                                autohide: true,
                                buttons: true,
                                todayBtn: true,
                                clearBtn: true
                            });
                            el.setAttribute('data-datepicker-initialized', 'true');
                        } catch (e) {
                            console.log('Datepicker error:', e);
                        }
                    }
                });
            }

            // Validasi Kepala Keluarga
            function validateKepalaKeluarga(showMessage = false) {
                const selects = document.querySelectorAll('select[name$="[status_in_family]"]');
                let kepalaCount = 0;

                selects.forEach(select => {
                    if (select.value === 'Kepala Keluarga') {
                        kepalaCount++;
                    }
                });

                const oldError = document.querySelector('.kepala-error-message');
                if (oldError) oldError.remove();

                if (selects.length > 0 && showMessage) {
                    if (kepalaCount === 0) {
                        showKepalaError('Harus ada 1 Kepala Keluarga');
                        return false;
                    }
                    if (kepalaCount > 1) {
                        showKepalaError('Tidak boleh lebih dari 1 Kepala Keluarga');
                        return false;
                    }
                }

                return kepalaCount === 1;
            }

            function showKepalaError(message) {
                const container = document.getElementById('members-container');
                const errorDiv = document.createElement('div');
                errorDiv.className = 'kepala-error-message p-3 mb-4 bg-red-100 border border-red-400 text-red-700 rounded';
                errorDiv.textContent = message;
                container.parentNode.insertBefore(errorDiv, container);
            }

            // Event listener untuk validasi (hitung saja, tanpa pesan)
            document.addEventListener('change', function(e) {
                if (e.target && e.target.name && e.target.name.includes('status_in_family')) {
                    validateKepalaKeluarga(false);
                }
            });

            // Validasi sebelum submit (tampilkan pesan)
            document.querySelector('form').addEventListener('submit', function(e) {
                if (!validateKepalaKeluarga(true)) {
                    e.preventDefault();
                    document.querySelector('.kepala-error-message')?.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
            });
        </script>

        <script>
            window.sr = ScrollReveal({
                duration: 500,
                distance: '0px',
                easing: 'ease-in-out'
            });

            sr.reveal('.form', {
                opacity: 0,
                origin: 'bottom',
                reset: false
            });
        </script>
        <script>
            let map = null;
            let marker = null;
            let locationAccepted = false;
            let latitudeInput = document.getElementById('latitude');
            let longitudeInput = document.getElementById('longitude');

            function disableSubmit() {
                const submitBtn = document.querySelector('#main-form button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                }
            }

            function enableSubmit() {
                const submitBtn = document.querySelector('#main-form button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            }

            function showWarningMessage(message, isPermanent = false) {
                const form = document.getElementById('main-form');
                if (!form) return;

                let warningMsg = document.getElementById('location-warning');
                if (warningMsg) warningMsg.remove();

                warningMsg = document.createElement('div');
                warningMsg.id = 'location-warning';
                warningMsg.className = 'bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4';

                if (isPermanent) {
                    warningMsg.innerHTML = `
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div class="flex-1">
                    <p class="font-semibold">⚠️ Akses Lokasi Diperlukan!</p>
                    <p class="text-sm mt-1">${message}</p>
                    <div class="mt-3 text-sm bg-yellow-50 p-3 rounded border border-yellow-200">
                        <p class="font-medium mb-2">Cara mengaktifkan kembali akses lokasi:</p>
                        <ol class="list-decimal list-inside space-y-1">
                            <li>Klik ikon <strong>🔒 / 🛡️ / 🌐</strong> di sebelah kiri address bar</li>
                            <li>Cari menu <strong>"Site settings"</strong> atau <strong>"Pengaturan situs"</strong></li>
                            <li>Cari pengaturan <strong>"Location"</strong> atau <strong>"Lokasi"</strong></li>
                            <li>Ubah dari <strong>"Blokir"</strong> menjadi <strong>"Izinkan"</strong></li>
                            <li>Refresh halaman dengan tombol di bawah</li>
                        </ol>
                    </div>
                    <button type="button" onclick="location.reload()" class="mt-3 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm font-medium transition">
                        🔄 Refresh Halaman
                    </button>
                </div>
            </div>
        `;
                } else {
                    warningMsg.innerHTML = `
            <div class="flex items-center justify-between flex-wrap gap-3">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span class="font-medium">⚠️ ${message}</span>
                </div>
                <button type="button" onclick="requestLocationAgain()" class="bg-yellow-500 text-white px-4 py-1.5 rounded-lg hover:bg-yellow-600 text-sm font-medium transition">
                    Coba Lagi
                </button>
            </div>
        `;
                }

                const errorDiv = form.querySelector('.bg-red-100');
                if (errorDiv) {
                    form.insertBefore(warningMsg, errorDiv.nextSibling);
                } else {
                    form.insertBefore(warningMsg, form.firstChild);
                }
            }

            function updateSubmitStatus() {
                if (!locationAccepted) {
                    disableSubmit();
                } else {
                    enableSubmit();
                    const warningMsg = document.getElementById('location-warning');
                    if (warningMsg) warningMsg.remove();
                }
            }

            window.requestLocationAgain = function() {
                locationAccepted = false;
                const warningMsg = document.getElementById('location-warning');
                if (warningMsg) warningMsg.remove();
                disableSubmit();
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(locationSuccess, locationError);
                }
            };

            function locationSuccess(position) {
                locationAccepted = true;
                const userLat = position.coords.latitude;
                const userLng = position.coords.longitude;

                if (map) {
                    map.setView([userLat, userLng], 13);
                    marker.setLatLng([userLat, userLng]);
                } else {
                    map = L.map('map').setView([userLat, userLng], 13);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '© OpenStreetMap contributors'
                    }).addTo(map);

                    marker = L.marker([userLat, userLng], {
                            draggable: true
                        }).addTo(map)
                        .bindPopup('📍 Lokasi Anda').openPopup();

                    marker.on('dragend', function(e) {
                        const pos = e.target.getLatLng();
                        latitudeInput.value = pos.lat;
                        longitudeInput.value = pos.lng;
                    });

                    map.on('click', function(e) {
                        marker.setLatLng(e.latlng);
                        latitudeInput.value = e.latlng.lat;
                        longitudeInput.value = e.latlng.lng;
                    });
                }

                marker.bindPopup('📍 Lokasi Anda').openPopup();
                latitudeInput.value = userLat;
                longitudeInput.value = userLng;
                updateSubmitStatus();
            }

            function locationError(error) {
                locationAccepted = false;

                const defaultLat = 0.996857;
                const defaultLng = 104.5126331;

                if (map) {
                    map.setView([defaultLat, defaultLng], 13);
                    if (marker) marker.setLatLng([defaultLat, defaultLng]);
                } else {
                    map = L.map('map').setView([defaultLat, defaultLng], 13);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '© OpenStreetMap contributors'
                    }).addTo(map);
                    marker = L.marker([defaultLat, defaultLng]).addTo(map)
                        .bindPopup('📍 Lokasi default').openPopup();
                }

                latitudeInput.value = defaultLat;
                longitudeInput.value = defaultLng;

                if (error.code === 1) {
                    showWarningMessage('Anda telah memblokir akses lokasi untuk website ini.', true);
                } else if (error.code === 2) {
                    showWarningMessage('Informasi lokasi tidak tersedia.', false);
                } else if (error.code === 3) {
                    showWarningMessage('Waktu permintaan lokasi habis. Silakan coba lagi.', false);
                } else {
                    showWarningMessage('Gagal mendapatkan akses lokasi. Silakan coba lagi.', false);
                }

                disableSubmit();
            }

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(locationSuccess, locationError);
            } else {
                locationError({
                    code: 0,
                    message: 'Browser tidak mendukung geolocation'
                });
            }
        </script>
        <script>
            // FUNGSI REMOVE TEMP PHOTO (SUDAH ADA)
            function removeTempPhoto() {
                const tempId = document.getElementById('temp_photo').value;

                if (tempId) {
                    fetch('{{ route('delete.temp') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            filename: tempId + '.*'
                        })
                    });
                }

                document.getElementById('photo-preview').innerHTML = '';
                document.getElementById('house_photo').value = '';
                document.getElementById('temp_photo').value = '';
            }
        </script>
        @if (old('temp_photo'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const filename = "{{ old('temp_photo') }}";

                    if (filename) {
                        const imageUrl = `/storage/temp/${filename}`;

                        const preview = document.getElementById('photo-preview');
                        preview.innerHTML = `
            <div class="relative inline-block">
                <img src="${imageUrl}" 
                    class="h-20 w-20 object-cover rounded border border-gray-300">
                <button type="button" 
                        onclick="removeTempPhoto()" 
                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1">
                    X
                </button>
            </div>
        `;
                    }
                });
            </script>
        @endif
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                @if (old('temp_photo'))
                    const filename = "{{ old('temp_photo') }}";
                    const imageUrl = `/storage/temp/${filename}`;

                    document.getElementById('temp_photo').value = filename;

                    const preview = document.getElementById('photo-preview');
                    preview.innerHTML = `
            <div class="relative inline-block">
                <img src="${imageUrl}" class="h-20 w-20 object-cover rounded border border-gray-300">
                <button type="button" onclick="removeTempPhoto()" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1">X</button>
            </div>
        `;
                @endif
            });

            // ============================================
            // UPLOAD FOTO BARU (DENGAN LOADER)
            // ============================================
            document.getElementById('house_photo').addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (!file) return;

                if (file.size > 5 * 1024 * 1024) {
                    alert('File terlalu besar! Maksimal 5MB');
                    this.value = '';
                    return;
                }

                // TAMPILKAN LOADER
                const loaderDiv = document.getElementById('photo-loader');
                loaderDiv.innerHTML = `
                    <div class="loading-wrapper">
                        <div class="simple-spinner"></div>
                        <span class="loading-text">Mengupload foto...</span>
                    </div>
                `;

                // PREVIEW LOKAL
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('photo-preview');
                    preview.innerHTML =
                        `<img src="${e.target.result}" class="h-20 w-20 object-cover rounded border border-gray-300">`;
                };
                reader.readAsDataURL(file);

                // UPLOAD KE TEMP
                const formData = new FormData();
                formData.append('file', file);

                fetch('{{ route('upload.temp') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('temp_photo').value = data.filename;
                            loaderDiv.innerHTML = ''; // HAPUS LOADER

                            // UPDATE PREVIEW DENGAN URL ASLI
                            const preview = document.getElementById('photo-preview');
                            preview.innerHTML = `
                                <div class="relative inline-block">
                                    <img src="${data.url}" class="h-20 w-20 object-cover rounded border border-gray-300">
                                    <button type="button" onclick="removeTempPhoto()" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1">X</button>
                                </div>
                            `;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        loaderDiv.innerHTML = '<p class="text-sm text-red-600 text-center">Gagal upload</p>';
                    });
            });

            // ============================================
            // FUNGSI REMOVE TEMP PHOTO
            // ============================================
            function removeTempPhoto() {
                const tempId = document.getElementById('temp_photo').value;
                if (tempId) {
                    fetch('{{ route('delete.temp') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            filename: tempId
                        })
                    });
                }
                document.getElementById('photo-preview').innerHTML = '';
                document.getElementById('photo-loader').innerHTML = '';
                document.getElementById('house_photo').value = '';
                document.getElementById('temp_photo').value = '';
            }
        </script>
        <script>
            // ============================================
            // DEPENDENT DROPDOWN KELURAHAN -> RT/RW DENGAN LOADER
            // ============================================
            document.getElementById('kelurahan_id').addEventListener('change', function() {
                const kelurahanId = this.value;
                const rtRwSelect = document.getElementById('rt_rw_id');

                // TAMPILKAN LOADING DI DROPDOWN
                rtRwSelect.innerHTML = '<option value="">Loading RT/RW...</option>';
                rtRwSelect.disabled = true;

                if (kelurahanId) {
                    fetch(`/get-rt-by-kelurahan/${kelurahanId}`)
                        .then(response => response.json())
                        .then(data => {
                            // HAPUS LOADING, TAMPILKAN DATA
                            rtRwSelect.innerHTML = '<option value="">--- Pilih RT/RW ---</option>';

                            data.forEach(item => {
                                const option = document.createElement('option');
                                option.value = item.id;
                                option.textContent = `[${item.kode_sls}] RT ${item.rt} RW ${item.rw}`;
                                rtRwSelect.appendChild(option);
                            });

                            rtRwSelect.disabled = false;
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            rtRwSelect.innerHTML = '<option value="">Gagal memuat data</option>';
                            rtRwSelect.disabled = false;
                        });
                } else {
                    // KALAU KELURAHAN KOSONG
                    rtRwSelect.innerHTML = '<option value="">--- Pilih RT/RW ---</option>';
                    rtRwSelect.disabled = false;
                }
            });

            // OLD VALUE UNTUK KELURAHAN (PAS ERROR)
            @if (old('kelurahan_id'))
                document.addEventListener('DOMContentLoaded', function() {
                    const kelurahanId = "{{ old('kelurahan_id') }}";
                    const rtRwId = "{{ old('rt_rw_id') }}";

                    if (kelurahanId) {
                        // SET KELURAHAN
                        document.getElementById('kelurahan_id').value = kelurahanId;

                        // TRIGGER CHANGE UNTUK LOAD RT/RW
                        const event = new Event('change');
                        document.getElementById('kelurahan_id').dispatchEvent(event);

                        // SET RT/RW SETELAH DATA LOAD
                        const checkLoaded = setInterval(function() {
                            const rtRwSelect = document.getElementById('rt_rw_id');
                            if (rtRwSelect.options.length > 1) { // SUDAH TERLOAD
                                rtRwSelect.value = rtRwId;
                                clearInterval(checkLoaded);
                            }
                        }, 100);
                    }
                });
            @endif
        </script>
        <script src="https://cdn.jsdelivr.net/npm/flowbite@4.0.1/dist/flowbite.min.js"></script>
    @endpush
</x-app-layout>
