<x-app-layout>
    @push('leaflet_create')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
            integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    @endpush
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Keluarga') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <section class="bg-white dark:bg-gray-900">
                    <div class="py-8 lg:py-16 px-4 mx-auto max-w-screen-md">
                        <form action="{{ route('family.update', $family->id) }}" method="POST" class="space-y-4 form"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
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
                                            {{ $family->kelurahan_id == $kel->id ? 'selected' : '' }}>
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
                                    @foreach ($rtRws as $rt)
                                        <option value="{{ $rt->id }}"
                                            {{ $family->rt_rw_id == $rt->id ? 'selected' : '' }}>
                                            [{{ $rt->kode_sls }}] RT {{ $rt->rt }} / RW {{ $rt->rw }}
                                        </option>
                                    @endforeach
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
                                <input value="{{ old('ktp_address', $family->ktp_address) }}" type="text"
                                    id="ktp_address" name="ktp_address"
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
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Jika KTP NON
                                    TELUK LOBAM, sebutkan asal Kota/Kabupaten <span
                                        class="text-red-500">*</span></label>
                                <input value="{{ old('city_address', $family->city_address) }}" type="text"
                                    id="city_address" name="city_address"
                                    class="@error('city_address') is-invalid @enderror block p-3 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 shadow-sm focus:ring-primary-500 focus:border-primary-500"
                                    placeholder="isikan '-' jika KTP Teluk Lobam" required>
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
                                    <option value="KK"
                                        {{ old('ownership_kk', $family->ownership_kk) == 'KK' ? 'selected' : '' }}>KK
                                    </option>
                                    <option value="Non KK"
                                        {{ old('ownership_kk', $family->ownership_kk) == 'Non KK' ? 'selected' : '' }}>
                                        Non
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
                                <input value="{{ old('number_kk', $family->number_kk) }}" type="text" id="number_kk"
                                    name="number_kk"
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
                                    value="{{ old('number_of_family_member', $family->number_of_family_member) }}"
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
                                    <option value="Ya" {{ old('bpnt', $family->bpnt) == 'Ya' ? 'selected' : '' }}>
                                        Ya</option>
                                    <option value="Tidak"
                                        {{ old('bpnt', $family->bpnt) == 'Tidak' ? 'selected' : '' }}>Tidak
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
                                    <option value="Ya" {{ old('pkh', $family->pkh) == 'Ya' ? 'selected' : '' }}>
                                        Ya</option>
                                    <option value="Tidak"
                                        {{ old('pkh', $family->pkh) == 'Tidak' ? 'selected' : '' }}>Tidak
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
                                    <option value="Ya"
                                        {{ old('blt_elderly', $family->blt_elderly) == 'Ya' ? 'selected' : '' }}>Ya
                                    </option>
                                    <option value="Tidak"
                                        {{ old('blt_elderly', $family->blt_elderly) == 'Tidak' ? 'selected' : '' }}>
                                        Tidak
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
                                    <option value="Ya"
                                        {{ old('blt_village', $family->blt_village) == 'Ya' ? 'selected' : '' }}>Ya
                                    </option>
                                    <option value="Tidak"
                                        {{ old('blt_village', $family->blt_village) == 'Tidak' ? 'selected' : '' }}>
                                        Tidak
                                    </option>
                                </select>
                                @error('blt_village')
                                    <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Bantuan Lainnya -->
                            <div>
                                <label for="other_assistance"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Apakah
                                    menerima bantuan lainnya? Jika YA, sebutkan <span
                                        class="text-red-500">*</span></label>
                                <input value="{{ old('other_assistance', $family->other_assistance) }}"
                                    type="text" id="other_assistance" name="other_assistance"
                                    class="@error('other_assistance') is-invalid @enderror block p-3 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 shadow-sm focus:ring-primary-500 focus:border-primary-500"
                                    placeholder="Tuliskan dengan lengkap" required>
                                @error('other_assistance')
                                    <div class="pt-2">
                                        <span class="text-red-500 text-sm">Kesalahan input, {{ $message }}</span>
                                    </div>
                                @enderror
                            </div>
                            <div class="mb-4 p-4 bg-gray-50 rounded-lg border border-gray-200" id="photo-container">
                                <div class="flex items-start gap-4">
                                    {{-- FOTO (AKAN KOSONG KALAU BELUM ADA) --}}
                                    <img src="{{ $family->house_photo ? asset('storage/' . $family->house_photo) : '' }}"
                                        class="h-24 w-24 object-cover rounded-lg border border-gray-300"
                                        id="current-photo"
                                        style="{{ $family->house_photo ? '' : 'display: none;' }}">

                                    {{-- INFO --}}
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-700 mb-1" id="photo-label">
                                            {{ $family->house_photo ? 'Foto Saat Ini' : 'Belum ada foto' }}
                                        </p>
                                        @if (!$family->house_photo)
                                            <p class="text-xs text-gray-500">Upload foto untuk menambahkan</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div id="photo-preview" class="mb-3"></div>
                            <div class="pb-4">
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                    for="file_input">
                                    Upload Foto Rumah
                                </label>
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
            const oldMembers = @json($family->members);
            const oldNumber = {{ $family->number_of_family_member }};
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

            function setupEmploymentStatusListener(memberIndex) {
                const statusSelect = document.querySelector(`select[name="members[${memberIndex}][employment_status]"]`);

                if (!statusSelect) return;

                statusSelect.addEventListener('change', function() {
                    toggleBlokC(memberIndex);
                });

                // 🔥 INIT SAAT LOAD (INI PENTING DI EDIT)
                toggleBlokC(memberIndex);
            }

            function generateMemberForms(membersData) {
                container.innerHTML = '';

                membersData.forEach((data, index) => {
                    container.innerHTML += createMemberForm(index, data);
                });

                // Inisialisasi datepicker setelah form digenerate
                setTimeout(() => {
                    initDatepickers();

                    // Update warna tombol untuk semua member
                    for (let i = 0; i < membersData.length; i++) {
                        setupEmploymentStatusListener(i);
                        updateStepButtons(i);
                    }
                }, 200);

                // Validasi kepala keluarga setelah form digenerate
                setTimeout(() => {
                    validateKepalaKeluarga();
                }, 300);
            }

            function toggleBlokC(memberIndex) {
                const statusSelect = document.querySelector(`select[name="members[${memberIndex}][employment_status]"]`);
                const blokCDiv = document.querySelector(`.step-${memberIndex}-2`);

                if (!statusSelect || !blokCDiv) return;

                const isWorking = statusSelect.value === 'Bekerja';

                const mainOccupation = blokCDiv.querySelector(`input[name="members[${memberIndex}][main_occupation]"]`);
                const employmentPosition = blokCDiv.querySelector(
                    `select[name="members[${memberIndex}][employment_position]"]`);

                if (isWorking) {
                    mainOccupation.setAttribute('required', 'required');
                    employmentPosition.setAttribute('required', 'required');
                } else {
                    mainOccupation.removeAttribute('required');
                    employmentPosition.removeAttribute('required');

                    // 🔥 BONUS: kosongkan value
                    mainOccupation.value = '';
                    employmentPosition.value = '';
                }

                updateStepButtons(memberIndex);
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
                                'Cucu', 'Orang tua/Mertua', 'Pembantu', 'Sopir', 'Family lainnya'
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
                            class="block p-3 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300" required>
                    </div>
                    
                    <!-- Status Dalam Pekerjaan -->
                    <div class="pb-4">
                        <label class="block mb-2 text-sm font-medium text-gray-900">Status Dalam Pekerjaan <span class="text-red-500">*</span></label>
                        <select name="members[${index}][employment_position]" class="bg-gray-50 border border-gray-300 rounded-lg w-full p-2.5" required>
                            <option value="">--- Pilih Status Dalam Pekerjaan ---</option>
                            ${generateOptions([
                                'Berusaha', 'Buruh/Karyawan/Pegawai Swasta',
                                'PNS/TNI/POLRI/Pegawai BUMN/Pegawai BUMD/Pejabat Negara',
                                'Pekerja bebas (Freelance)', 'Pekerja tidak dibayar'
                            ], oldData.employment_position)}
                        </select>
                    </div>
                    
                    <div class="flex justify-between space-x-5">
                        <button type="button" onclick="nextStep(${index},1)"
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
                    
                    <button type="button" onclick="nextStep(${index},2)"
                        class="w-full py-3 px-5 text-sm font-medium text-center text-white rounded-lg bg-primary-700 hover:bg-primary-800">Kembali</button>
                </div>
            </div>
        `;
            }

            function isStepComplete(memberIndex, stepIndex) {
                const stepDiv = document.querySelector(`.step-${memberIndex}-${stepIndex}`);
                if (!stepDiv) return false;

                if (stepIndex === 2) {
                    const statusSelect = document.querySelector(`select[name="members[${memberIndex}][employment_status]"]`);
                    const value = statusSelect ? statusSelect.value : '';

                    if (!value) return false;
                    if (value !== 'Bekerja') return true;
                }

                const fields = stepDiv.querySelectorAll('input[required], select[required]');

                if (fields.length === 0) return true;

                for (let field of fields) {
                    if (!field.value || field.value.trim() === '') {
                        return false;
                    }
                }

                return true;
            }

            // FUNGSI UPDATE WARNA TOMBOL
            function updateStepButtons(memberIndex) {
                for (let step = 0; step <= 3; step++) {

                    const buttons = document.querySelectorAll(
                        `button[onclick*="showStep(${memberIndex},${step})"]`
                    );

                    buttons.forEach(button => {

                        // RESET semua style dulu
                        button.classList.remove(
                            'from-green-500', 'via-green-600', 'to-green-700',
                            'from-blue-500', 'via-blue-600', 'to-blue-700',
                            'from-gray-400', 'via-gray-500', 'to-gray-600',
                            'cursor-not-allowed', 'opacity-60'
                        );

                        // 🔥 KHUSUS BLOK C
                        if (step === 2) {
                            const statusSelect = document.querySelector(
                                `select[name="members[${memberIndex}][employment_status]"]`
                            );

                            const value = statusSelect ? statusSelect.value : '';

                            // ❌ kalau belum pilih ATAU tidak bekerja → disable
                            if (!value || value !== 'Bekerja') {
                                button.classList.add(
                                    'from-gray-400', 'via-gray-500', 'to-gray-600',
                                    'cursor-not-allowed', 'opacity-60'
                                );

                                button.disabled = true;
                                return; // ⛔ stop di sini, jangan lanjut ke logic bawah
                            } else {
                                button.disabled = false;
                            }
                        }

                        // ✅ NORMAL LOGIC (selain Blok C atau kalau bekerja)
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

            // Fungsi navigasi step
            function showStep(member, step, isPrev = false) {
                if (step === 2) {
                    const statusSelect = document.querySelector(`select[name="members[${member}][employment_status]"]`);
                    const value = statusSelect ? statusSelect.value : '';

                    if (value && value !== 'Bekerja') {
                        step = isPrev ? 1 : 3;
                    }
                }

                document.querySelectorAll(`.step-${member}-0, .step-${member}-1, .step-${member}-2, .step-${member}-3`)
                    .forEach(el => el.classList.add('hidden'));

                document.querySelector(`.step-${member}-${step}`).classList.remove('hidden');
            }

            function nextStep(member, step) {
                showStep(member, step);
            }

            function prevStep(member, step) {
                showStep(member, step);
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
            // Geolocation
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const userLat = position.coords.latitude;
                        const userLng = position.coords.longitude;

                        const map = L.map('map').setView([userLat, userLng], 13);

                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            maxZoom: 19,
                            attribution: '© OpenStreetMap contributors'
                        }).addTo(map);

                        const marker = L.marker([userLat, userLng], {
                                draggable: true
                            }).addTo(map)
                            .bindPopup('Anda berada di sini!').openPopup();

                        marker.on('dragend', function(e) {
                            const pos = e.target.getLatLng();
                            document.getElementById('latitude').value = pos.lat;
                            document.getElementById('longitude').value = pos.lng;
                        });

                        map.on('click', function(e) {
                            marker.setLatLng(e.latlng);
                            document.getElementById('latitude').value = e.latlng.lat;
                            document.getElementById('longitude').value = e.latlng.lng;
                        });

                        document.getElementById('latitude').value = userLat;
                        document.getElementById('longitude').value = userLng;
                    },
                    function(error) {
                        console.error('Geolocation Error:', error.message);

                        const defaultLat = -6.1751;
                        const defaultLng = 106.8650;

                        const map = L.map('map').setView([defaultLat, defaultLng], 13);

                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            maxZoom: 19,
                            attribution: '© OpenStreetMap contributors'
                        }).addTo(map);

                        L.marker([defaultLat, defaultLng]).addTo(map)
                            .bindPopup('Lokasi default').openPopup();

                        document.getElementById('latitude').value = defaultLat;
                        document.getElementById('longitude').value = defaultLng;
                    }
                );
            } else {
                const defaultLat = -6.1751;
                const defaultLng = 106.8650;

                const map = L.map('map').setView([defaultLat, defaultLng], 13);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '© OpenStreetMap contributors'
                }).addTo(map);

                L.marker([defaultLat, defaultLng]).addTo(map)
                    .bindPopup('Lokasi default').openPopup();

                document.getElementById('latitude').value = defaultLat;
                document.getElementById('longitude').value = defaultLng;
            }
        </script>
        <script>
            // CEK OLD TEMP PHOTO (PAS ERROR VALIDASI)
            document.addEventListener('DOMContentLoaded', function() {
                @if (old('temp_photo'))
                    const filename = "{{ old('temp_photo') }}";
                    const imageUrl = `/storage/temp/${filename}`;

                    document.getElementById('temp_photo').value = filename;

                    const currentPhoto = document.getElementById('current-photo');
                    const photoLabel = document.getElementById('photo-label');

                    if (currentPhoto) {
                        currentPhoto.src = imageUrl;
                        currentPhoto.style.display = 'block';
                        photoLabel.textContent = 'Foto Baru (Preview)';
                    }
                @endif
            });

            // UPLOAD FOTO BARU (SATU AJA!)
            document.getElementById('house_photo').addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (!file) return;

                if (file.size > 5 * 1024 * 1024) {
                    alert('File terlalu besar! Maksimal 5MB');
                    this.value = '';
                    return;
                }

                // TAMPILKAN LOADING DI PREVIEW CONTAINER
                const previewContainer = document.getElementById('photo-preview');
                previewContainer.innerHTML = `
        <div class="flex items-center space-x-2 p-2 bg-gray-100 rounded">
            <div class="loader w-6 h-6 border-2 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
            <span class="text-sm text-gray-600">Mengupload...</span>
        </div>
    `;

                // PREVIEW LOKAL (LANGSUNG DI CURRENT-PHOTO)
                const reader = new FileReader();
                reader.onload = function(e) {
                    const currentPhoto = document.getElementById('current-photo');
                    const photoLabel = document.getElementById('photo-label');

                    currentPhoto.src = e.target.result;
                    currentPhoto.style.display = 'block';
                    photoLabel.textContent = 'Foto Baru (Preview)';
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

                            // HAPUS LOADING, TAPI PREVIEW SUDAH ADA DI CURRENT-PHOTO
                            previewContainer.innerHTML = '';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        previewContainer.innerHTML = `<p class="text-sm text-red-600">Gagal upload. Coba lagi.</p>`;
                    });
            });

            // FUNGSI REMOVE TEMP PHOTO
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
                                option.textContent = `[${item.kode_sls}] RT ${item.rt} / RW ${item.rw}`;
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

            document.addEventListener('DOMContentLoaded', function() {

                const kelurahanSelect = document.getElementById('kelurahan_id');
                const rtRwSelect = document.getElementById('rt_rw_id');

                const selectedKelurahan = "{{ old('kelurahan_id', $family->kelurahan_id ?? '') }}";
                const selectedRtRw = "{{ old('rt_rw_id', $family->rt_rw_id ?? '') }}";

                if (selectedKelurahan) {

                    fetch(`/get-rt-by-kelurahan/${selectedKelurahan}`)
                        .then(response => response.json())
                        .then(data => {

                            rtRwSelect.innerHTML = '<option value="">--- Pilih RT/RW ---</option>';

                            data.forEach(item => {

                                const option = document.createElement('option');
                                option.value = item.id;
                                option.textContent = `[${item.kode_sls}] RT ${item.rt} / RW ${item.rw}`;

                                if (item.id == selectedRtRw) {
                                    option.selected = true;
                                }

                                rtRwSelect.appendChild(option);
                            });

                        });

                }

            });
        </script>
        <script src="https://cdn.jsdelivr.net/npm/flowbite@4.0.1/dist/flowbite.min.js"></script>
    @endpush
</x-app-layout>
