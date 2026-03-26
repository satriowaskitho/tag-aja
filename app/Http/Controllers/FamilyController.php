<?php

namespace App\Http\Controllers;

use App\Models\Family;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\RtRw;
use App\Models\Kelurahan;


class FamilyController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // BUAT QUERY
        $query = Family::with(['kelurahan', 'rtRw', 'members', 'creator']);

        // TERAPKAN FILTER BERDASARKAN ROLE
        if ($user->role === 'petugas') {
            $query->where('created_by', $user->id);
        } elseif ($user->role === 'supervisor') {
            $query->where('kelurahan_id', $user->kelurahan_id);
        }
        // admin: tanpa filter (semua data)

        // EKSEKUSI QUERY
        $families = $query->get();

        // KIRIM KE VIEW
        return view('families.index', compact('families'));
    }

    public function create()
    {
        $kelurahans = Kelurahan::all();
        return view('families.create', compact('kelurahans'));
    }

    public function store(Request $request)
    {
        // dd($request->all());

        // 1. CEK UKURAN FILE DULU (SEBELUM VALIDASI LARAVEL)
        if ($request->hasFile('house_photo') && $request->file('house_photo')->getSize() > 5242880) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['house_photo' => 'Ukuran gambar terlalu besar, maksimal 5MB']);
        }
        $messages = [
            // FAMILY
            'number_kk.required' => 'Nomor Kartu Keluarga wajib diisi',
            'number_kk.digits' => 'Nomor Kartu Keluarga harus 16 digit',
            'number_kk.numeric' => 'Nomor Kartu Keluarga harus berupa angka',
            'number_kk.unique' => 'Nomor Kartu Keluarga sudah terdaftar',
            'ownership_kk.required' => 'Kepemilikan Kartu Keluarga wajib diisi',
            'number_of_family_member.required' => 'Jumlah anggota wajib diisi',
            'number_of_family_member.numeric' => 'Jumlah anggota harus berupa angka',
            'kelurahan_id.required' => 'Kelurahan/Desa harus diisi',
            'rt_rw_id.required' => 'Alamat RT harus diisi',
            'ktp_address.required' => 'Alamat KTP harus diisi',
            'city_address.required' => 'asal Kabupaten/Kota harus diisi',
            'bpnt.required' => 'Bantuan BPNT Harus Diisi',
            'pkh.required' => 'Bantuan PKH Harus Diisi',
            'blt_elderly.required' => 'Bantuan BLT Lansia Harus Diisi',
            'blt_village.required' => 'Bantuan BLT Dana Desa Harus Diisi',
            'other_assistance.required' => 'Bantuan Lainnya Harus Diisi',
            'house_photo.image' => 'File harus berupa gambar',
            'house_photo.mimes' => 'Format gambar harus jpeg, png, atau jpg',

            // MEMBER
            'members.*.full_name.required' => 'Nama ART wajib diisi',
            'members.*.full_name.min' => 'Nama minimal :min karakter',
            'members.*.full_name.max' => 'Nama maksimal :max karakter',
            'members.*.nik.required' => 'NIK wajib diisi',
            'members.*.nik.digits' => 'NIK harus 16 digit',
            'members.*.nik.numeric' => 'NIK harus berupa angka',
            'members.*.nik.unique' => 'NIK sudah terdaftar dalam keluarga lain',
            'members.*.status_in_family.required' => 'Status dalam keluarga harus diisi',
            'members.*.place_of_birth.required' => 'Tempat lahir harus diisi',
            'members.*.date_of_birth.required' => 'Tangga lahir harus diisi',
            'members.*.date_of_birth.date_format' => 'Format tanggal harus bulan/tanggal/tahun (contoh: 12/31/2025)',
            'members.*.gender.required' => 'Jenis-kelamin harus diisi',
            'members.*.marital_status.required' => 'Status perkawinan harus diisi',
            'members.*.ethnic.required' => 'Suku harus diisi',
            'members.*.highest_education_level.required' => 'Pendidikan tertinggi harus diisi',
            'members.*.highest_education_certificate.required' => 'Ijazah terakhir harus diisi',
            'members.*.employment_status.required' => 'Status ketenagakerjaan harus diisi',
            'members.*.health_insurance.required' => 'Jaminan Kesehatan harus diisi',
            'members.*.stunting.required' => 'Status Stunting harus diisi',
            'members.*.disability.required' => 'Status disabilitas harus diisi',
            'members.*.main_occupation.required_if' => 'Pekerjaan Utama wajib diisi jika status bekerja',
            'members.*.employment_position.required_if' => 'Status Dalam Pekerjaan wajib diisi jika status bekerja',
        ];

        $validated = $request->validate([
            // FAMILY
            'number_kk' => 'required|numeric|digits:16|unique:families,number_kk',
            'ownership_kk' => 'required',
            'number_of_family_member' => 'required|numeric',
            'kelurahan_id' => 'required|exists:kelurahans,id',
            'rt_rw_id' => 'required|exists:rt_rws,id',
            'ktp_address' => 'required',
            'city_address' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'bpnt' => 'required',
            'pkh' => 'required',
            'blt_elderly' => 'required',
            'blt_village' => 'required',
            'other_assistance' => 'required',
            'house_photo' => 'nullable|image|mimes:jpeg,png,jpg',
            'temp_photo' => 'nullable|string',

            // MEMBERS
            'members' => 'required|array',
            'members.*.full_name' => 'required|string|min:2|max:255',
            'members.*.nik' => 'required|numeric|digits:16|unique:family_members,nik',
            'members.*.status_in_family' => 'required|in:Kepala Keluarga,Istri/Suami,Anak,Menantu,Cucu,Orang Tua/Mertua,Pembantu,Sopir,Family lainnya',
            'members.*.place_of_birth' => 'required',
            'members.*.date_of_birth' => 'required|date_format:m/d/Y',
            'members.*.gender' => 'required',
            'members.*.marital_status' => 'required',
            'members.*.ethnic' => 'required',
            'members.*.highest_education_level' => 'required',
            'members.*.highest_education_certificate' => 'required',
            'members.*.employment_status' => 'required',
            'members.*.employment_position' => 'nullable',
            'members.*.main_occupation' => 'nullable',
            'members.*.health_insurance' => 'required',
            'members.*.stunting' => 'required',
            'members.*.disability' => 'required',
        ], $messages);

        // Validasi tambahan: Hanya 1 Kepala Keluarga
        $kepalaCount = 0;
        foreach ($validated['members'] as $member) {
            if ($member['status_in_family'] === 'Kepala Keluarga') {
                $kepalaCount++;
            }
        }

        if ($kepalaCount !== 1) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['members' => 'Hanya ada Satu Kepala Keluarga']);
        }

        // Validasi NIK unik dalam 1 keluarga
        $niks = array_column($validated['members'], 'nik');
        if (count($niks) !== count(array_unique($niks))) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['members' => 'NIK tidak boleh ada yang sama dalam satu keluarga']);
        }

        // Validasi conditional untuk main_occupation dan employment_position
        foreach ($validated['members'] as $index => $member) {
            if ($member['employment_status'] === 'Bekerja') {
                // Jika status bekerja, maka main_occupation dan employment_position WAJIB diisi
                if (empty($member['main_occupation'])) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(["members.$index.main_occupation" => 'Pekerjaan Utama wajib diisi jika status bekerja']);
                }
                if (empty($member['employment_position'])) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(["members.$index.employment_position" => 'Status Dalam Pekerjaan wajib diisi jika status bekerja']);
                }
            }
        }

        DB::beginTransaction();

        try {
            $path = null;

            // CEK TEMP PHOTO DULU
            if ($request->has('temp_photo') && !empty($request->temp_photo)) {
                $tempId = $request->temp_photo;
                $files = Storage::disk('public')->files('temp');

                foreach ($files as $file) {
                    if (strpos($file, $tempId) !== false) {
                        // Dapatkan ekstensi file
                        $extension = pathinfo($file, PATHINFO_EXTENSION);

                        // Cari nama kepala keluarga
                        $kepalaKeluarga = '';
                        foreach ($validated['members'] as $member) {
                            if ($member['status_in_family'] === 'Kepala Keluarga') {
                                $kepalaKeluarga = $member['full_name'];
                                break;
                            }
                        }

                        $namaBersih = Str::slug($kepalaKeluarga ?: 'keluarga', '-');
                        $timestamp = Carbon::now()->format('Ymd-His');
                        $namaFile = $namaBersih . '-' . $timestamp . '.' . $extension;

                        // Pindahkan dari temp ke house-photos
                        $newPath = 'house-photos/' . $namaFile;
                        Storage::disk('public')->move($file, $newPath);
                        $path = $newPath;

                        break;
                    }
                }
            }
            // KALAU TIDAK ADA TEMP, CEK UPLOAD LANGSUNG
            elseif ($request->hasFile('house_photo')) {
                $file = $request->file('house_photo');

                // Cari nama kepala keluarga
                $kepalaKeluarga = '';
                foreach ($validated['members'] as $member) {
                    if ($member['status_in_family'] === 'Kepala Keluarga') {
                        $kepalaKeluarga = $member['full_name'];
                        break;
                    }
                }

                // Format: NamaBersih-TahunBulanTanggal-JamMenitDetik.ekstensi
                $namaBersih = Str::slug($kepalaKeluarga ?: 'keluarga', '-');
                $timestamp = Carbon::now()->format('Ymd-His');
                $extension = $file->getClientOriginalExtension();
                $namaFile = $namaBersih . '-' . $timestamp . '.' . $extension;

                // Cek ukuran file
                $size = $file->getSize();

                // Jika lebih dari 1MB, kompres
                if ($size > 1048576) { // 1MB
                    // Baca file asli
                    $imageData = file_get_contents($file->getRealPath());
                    $image = imagecreatefromstring($imageData);

                    // Simpan hasil kompres ke buffer
                    ob_start();
                    if (strtolower($extension) == 'png') {
                        imagepng($image, null, 6); // PNG kompres level 6
                    } else {
                        imagejpeg($image, null, 60); // JPEG kompres 60%
                    }
                    $compressedData = ob_get_clean();

                    // Hapus image dari memory
                    $image = null;

                    // Simpan file hasil kompres
                    Storage::disk('public')->put('house-photos/' . $namaFile, $compressedData);
                    $path = 'house-photos/' . $namaFile;
                } else {
                    // File sudah kecil, simpan langsung
                    $path = $file->storeAs('house-photos', $namaFile, 'public');
                }
            }

            // Buat slug berdasarkan nama
            // $slug = Str::slug($request->name);

            // Simpan data keluarga
            $newFamily = new Family();
            $newFamily->number_kk = $validated['number_kk'];
            $newFamily->ownership_kk = $validated['ownership_kk'];
            $newFamily->number_of_family_member = $validated['number_of_family_member'];
            $newFamily->kelurahan_id = $validated['kelurahan_id'];
            $newFamily->rt_rw_id = $validated['rt_rw_id'];
            $newFamily->ktp_address = $validated['ktp_address'];
            $newFamily->city_address = $validated['city_address'];
            $newFamily->latitude = $validated['latitude'];
            $newFamily->longitude = $validated['longitude'];
            $newFamily->pkh = $validated['pkh'];
            $newFamily->bpnt = $validated['bpnt'];
            $newFamily->blt_elderly = $validated['blt_elderly'];
            $newFamily->blt_village = $validated['blt_village'];
            $newFamily->other_assistance = $validated['other_assistance'];
            $newFamily->house_photo = $path; // Simpan path file
            // $newFamily->slug = $slug; // Menyimpan slug
            $newFamily->created_by = Auth::id();
            $newFamily->save();
            // return redirect()->route('farmers.index')->with(['success' => 'Data Berhasil Disimpan!']);

            // Simpan banyak ART
            foreach ($validated['members'] as $member) {
                $dateOfBirth = \Carbon\Carbon::createFromFormat('m/d/Y', $member['date_of_birth'])->format('Y-m-d');
                $newFamily->members()->create([

                    // BLOK A
                    'full_name' => $member['full_name'],
                    'nik' => $member['nik'],
                    'status_in_family' => $member['status_in_family'],
                    'place_of_birth' => $member['place_of_birth'],
                    'date_of_birth' => $dateOfBirth,
                    'gender' => $member['gender'],

                    // BLOK B
                    'marital_status' => $member['marital_status'],
                    'ethnic' => $member['ethnic'],
                    'highest_education_level' => $member['highest_education_level'],
                    'highest_education_certificate' => $member['highest_education_certificate'],
                    'employment_status' => $member['employment_status'],

                    // BLOK C
                    'main_occupation' => $member['main_occupation'],
                    'employment_position' => $member['employment_position'],

                    // BLOK D
                    'health_insurance' => $member['health_insurance'],
                    'stunting' => $member['stunting'],
                    'disability' => $member['disability'],
                ]);
            }

            DB::commit();

            return redirect()->route('family.index')->with('status', 'Keluarga berhasil Disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();

            // Hapus file jika upload gagal
            if (isset($path) && $path && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }

            // Hapus file temp kalau ada (TAMBAHKAN INI)
            if ($request->has('temp_photo')) {
                $files = Storage::disk('public')->files('temp');
                foreach ($files as $file) {
                    if (strpos($file, $request->temp_photo) !== false) {
                        Storage::disk('public')->delete($file);
                        break;
                    }
                }
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $family = Family::with('members')->findOrFail($id);

        // KONVERSI TANGGAL
        foreach ($family->members as $member) {
            $member->date_of_birth = \Carbon\Carbon::parse($member->date_of_birth)->format('m/d/Y');
        }

        // AMBIL SEMUA KELURAHAN
        $kelurahans = Kelurahan::all();

        // AMBIL RT/RW BERDASARKAN KELURAHAN FAMILY
        $rtRws = RtRw::where('kelurahan_id', $family->kelurahan_id)->get();

        return view('families.edit', compact('family', 'kelurahans', 'rtRws'));
    }

    public function update(Request $request, $id)
    {
        // 1. CEK UKURAN FILE DULU (SEBELUM VALIDASI LARAVEL)
        if ($request->hasFile('house_photo') && $request->file('house_photo')->getSize() > 5242880) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['house_photo' => 'Ukuran gambar terlalu besar, maksimal 5MB']);
        }

        $messages = [
            // FAMILY
            'number_kk.required' => 'Nomor Kartu Keluarga wajib diisi',
            'number_kk.digits' => 'Nomor Kartu Keluarga harus 16 digit',
            'number_kk.numeric' => 'Nomor Kartu Keluarga harus berupa angka',
            'number_kk.unique' => 'Nomor Kartu Keluarga sudah terdaftar',
            'ownership_kk.required' => 'Kepemilikan Kartu Keluarga wajib diisi',
            'number_of_family_member.required' => 'Jumlah anggota wajib diisi',
            'number_of_family_member.numeric' => 'Jumlah anggota harus berupa angka',
            'kelurahan_id.required' => 'Kelurahan/Desa harus diisi',
            'rt_rw_id.required' => 'Alamat RT harus diisi',
            'ktp_address.required' => 'Alamat KTP harus diisi',
            'city_address.required' => 'asal Kabupaten/Kota harus diisi',
            'bpnt.required' => 'Bantuan BPNT Harus Diisi',
            'pkh.required' => 'Bantuan PKH Harus Diisi',
            'blt_elderly.required' => 'Bantuan BLT Lansia Harus Diisi',
            'blt_village.required' => 'Bantuan BLT Dana Desa Harus Diisi',
            'other_assistance.required' => 'Bantuan Lainnya Harus Diisi',
            'house_photo.image' => 'File harus berupa gambar',
            'house_photo.mimes' => 'Format gambar harus jpeg, png, atau jpg',

            // MEMBER
            'members.*.full_name.required' => 'Nama ART wajib diisi',
            'members.*.full_name.min' => 'Nama minimal :min karakter',
            'members.*.full_name.max' => 'Nama maksimal :max karakter',
            'members.*.nik.required' => 'NIK wajib diisi',
            'members.*.nik.digits' => 'NIK harus 16 digit',
            'members.*.nik.numeric' => 'NIK harus berupa angka',
            'members.*.nik.unique' => 'NIK sudah terdaftar dalam keluarga lain',
            'members.*.status_in_family.required' => 'Status dalam keluarga harus diisi',
            'members.*.place_of_birth.required' => 'Tempat lahir harus diisi',
            'members.*.date_of_birth.required' => 'Tangga lahir harus diisi',
            'members.*.date_of_birth.date_format' => 'Format tanggal harus bulan/tanggal/tahun (contoh: 12/31/2025)',
            'members.*.gender.required' => 'Jenis-kelamin harus diisi',
            'members.*.marital_status.required' => 'Status perkawinan harus diisi',
            'members.*.ethnic.required' => 'Suku harus diisi',
            'members.*.highest_education_level.required' => 'Pendidikan tertinggi harus diisi',
            'members.*.highest_education_certificate.required' => 'Ijazah terakhir harus diisi',
            'members.*.employment_status.required' => 'Status ketenagakerjaan harus diisi',
            'members.*.health_insurance.required' => 'Jaminan Kesehatan harus diisi',
            'members.*.stunting.required' => 'Status Stunting harus diisi',
            'members.*.disability.required' => 'Status disabilitas harus diisi',
            'members.*.main_occupation.required_if' => 'Pekerjaan Utama wajib diisi jika status bekerja',
            'members.*.employment_position.required_if' => 'Status Dalam Pekerjaan wajib diisi jika status bekerja',
        ];

        $validated = $request->validate([
            // FAMILY - UNTUK UPDATE, TAMBAH EXCEPTION DI UNIQUE
            'number_kk' => 'required|numeric|digits:16|unique:families,number_kk,' . $id,
            'ownership_kk' => 'required',
            'number_of_family_member' => 'required|numeric|min:1|max:15',
            'kelurahan_id' => 'required|exists:kelurahans,id',
            'rt_rw_id' => 'required|exists:rt_rws,id',
            'ktp_address' => 'required',
            'city_address' => 'required',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'bpnt' => 'required|in:Ya,Tidak',
            'pkh' => 'required|in:Ya,Tidak',
            'blt_elderly' => 'required|in:Ya,Tidak',
            'blt_village' => 'required|in:Ya,Tidak',
            'other_assistance' => 'required',
            'house_photo' => 'nullable|image|mimes:jpeg,png,jpg',

            // MEMBERS
            'members' => 'required|array',
            'members.*.full_name' => 'required|string|min:2|max:255',
            'members.*.nik' => 'required|numeric|digits:16',
            'members.*.status_in_family' => 'required|in:Kepala Keluarga,Istri/Suami,Anak,Menantu,Cucu,Orang Tua/Mertua,Pembantu,Sopir,Family lainnya',
            'members.*.place_of_birth' => 'required',
            'members.*.date_of_birth' => 'required|date_format:m/d/Y',
            'members.*.gender' => 'required|in:Laki-laki,Perempuan',
            'members.*.marital_status' => 'required|in:Belum kawin,Kawin,Cerai hidup,Cerai mati',
            'members.*.ethnic' => 'required',
            'members.*.highest_education_level' => 'required',
            'members.*.highest_education_certificate' => 'required',
            'members.*.employment_status' => 'required',
            'members.*.employment_position' => 'nullable',
            'members.*.main_occupation' => 'nullable',
            'members.*.health_insurance' => 'required',
            'members.*.stunting' => 'required|in:Ya,Tidak',
            'members.*.disability' => 'required|in:Ya,Tidak',
        ], $messages);

        // Validasi tambahan: Hanya 1 Kepala Keluarga
        $kepalaCount = 0;
        foreach ($validated['members'] as $member) {
            if ($member['status_in_family'] === 'Kepala Keluarga') {
                $kepalaCount++;
            }
        }

        if ($kepalaCount !== 1) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['members' => 'Hanya ada Satu Kepala Keluarga']);
        }

        // Validasi NIK unik dalam 1 keluarga
        $niks = array_column($validated['members'], 'nik');
        if (count($niks) !== count(array_unique($niks))) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['members' => 'NIK tidak boleh ada yang sama dalam satu keluarga']);
        }

        foreach ($validated['members'] as &$member) {
            if ($member['employment_status'] !== 'Bekerja') {
                $member['main_occupation'] = null;
                $member['employment_position'] = null;
            }
        }
        unset($member);

        // Validasi conditional untuk main_occupation dan employment_position
        foreach ($validated['members'] as $index => $member) {
            if ($member['employment_status'] === 'Bekerja') {
                // Jika status bekerja, maka main_occupation dan employment_position WAJIB diisi
                if (empty($member['main_occupation'])) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(["members.$index.main_occupation" => 'Pekerjaan Utama wajib diisi jika status bekerja']);
                }
                if (empty($member['employment_position'])) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(["members.$index.employment_position" => 'Status Dalam Pekerjaan wajib diisi jika status bekerja']);
                }
            }
        }

        DB::beginTransaction();

        try {
            $family = Family::findOrFail($id);

            // PROSES UPLOAD FOTO (JIKA ADA FILE BARU)
            $path = $family->house_photo; // Pakai foto lama sebagai default
            // CEK TEMP PHOTO DULU (TAMBAHKAN BLOK INI)
            if ($request->has('temp_photo') && !empty($request->temp_photo)) {
                $tempId = $request->temp_photo;
                $files = Storage::disk('public')->files('temp');

                foreach ($files as $file) {
                    if (strpos($file, $tempId) !== false) {
                        $extension = pathinfo($file, PATHINFO_EXTENSION);

                        // Cari nama kepala keluarga
                        $kepalaKeluarga = '';
                        foreach ($validated['members'] as $member) {
                            if ($member['status_in_family'] === 'Kepala Keluarga') {
                                $kepalaKeluarga = $member['full_name'];
                                break;
                            }
                        }

                        $namaBersih = Str::slug($kepalaKeluarga ?: 'keluarga', '-');
                        $timestamp = Carbon::now()->format('Ymd-His');
                        $namaFile = $namaBersih . '-' . $timestamp . '.' . $extension;

                        // HAPUS FOTO LAMA (JIKA ADA)
                        if ($family->house_photo && Storage::disk('public')->exists($family->house_photo)) {
                            Storage::disk('public')->delete($family->house_photo);
                        }

                        // PINDAHKAN FILE DARI TEMP KE HOUSE-PHOTOS
                        $newPath = 'house-photos/' . $namaFile;
                        Storage::disk('public')->move($file, $newPath);
                        $path = $newPath;

                        break;
                    }
                }
            }
            // KALAU TIDAK ADA TEMP, CEK UPLOAD LANGSUNG
            elseif ($request->hasFile('house_photo')) {
                $file = $request->file('house_photo');

                // Cari nama kepala keluarga
                $kepalaKeluarga = '';
                foreach ($validated['members'] as $member) {
                    if ($member['status_in_family'] === 'Kepala Keluarga') {
                        $kepalaKeluarga = $member['full_name'];
                        break;
                    }
                }

                // Format nama file
                $namaBersih = Str::slug($kepalaKeluarga ?: 'keluarga', '-');
                $timestamp = Carbon::now()->format('Ymd-His');
                $extension = $file->getClientOriginalExtension();
                $namaFile = $namaBersih . '-' . $timestamp . '.' . $extension;

                // Cek ukuran file
                $size = $file->getSize();

                // HAPUS FOTO LAMA (JIKA ADA)
                if ($family->house_photo && Storage::disk('public')->exists($family->house_photo)) {
                    Storage::disk('public')->delete($family->house_photo);
                }

                // Jika lebih dari 1MB, kompres
                if ($size > 1048576) { // 1MB
                    // Baca file asli
                    $imageData = file_get_contents($file->getRealPath());
                    $image = imagecreatefromstring($imageData);

                    // Simpan hasil kompres ke buffer
                    ob_start();
                    if (strtolower($extension) == 'png') {
                        imagepng($image, null, 6); // PNG kompres level 6
                    } else {
                        imagejpeg($image, null, 60); // JPEG kompres 60%
                    }
                    $compressedData = ob_get_clean();

                    // Hapus image dari memory
                    $image = null;

                    // Simpan file hasil kompres
                    Storage::disk('public')->put('house-photos/' . $namaFile, $compressedData);
                    $path = 'house-photos/' . $namaFile;
                } else {
                    // File sudah kecil, simpan langsung
                    $path = $file->storeAs('house-photos', $namaFile, 'public');
                }
            }

            // UPDATE DATA KELUARGA
            $family->number_kk = $validated['number_kk'];
            $family->ownership_kk = $validated['ownership_kk'];
            $family->number_of_family_member = $validated['number_of_family_member'];
            $family->kelurahan_id = $validated['kelurahan_id'];
            $family->rt_rw_id = $validated['rt_rw_id'];
            $family->ktp_address = $validated['ktp_address'];
            $family->city_address = $validated['city_address'];
            $family->latitude = $validated['latitude'];
            $family->longitude = $validated['longitude'];
            $family->pkh = $validated['pkh'];
            $family->bpnt = $validated['bpnt'];
            $family->blt_elderly = $validated['blt_elderly'];
            $family->blt_village = $validated['blt_village'];
            $family->other_assistance = $validated['other_assistance'];
            $family->house_photo = $path; // Update path foto (baru atau lama)
            $family->save();

            // HAPUS SEMUA ANGGOTA LAMA
            $family->members()->delete();

            // TAMBAH ANGGOTA BARU
            foreach ($validated['members'] as $member) {
                $dateOfBirth = \Carbon\Carbon::createFromFormat('m/d/Y', $member['date_of_birth'])->format('Y-m-d');
                $family->members()->create([
                    // BLOK A
                    'full_name' => $member['full_name'],
                    'nik' => $member['nik'],
                    'status_in_family' => $member['status_in_family'],
                    'place_of_birth' => $member['place_of_birth'],
                    'date_of_birth' => $dateOfBirth,
                    'gender' => $member['gender'],

                    // BLOK B
                    'marital_status' => $member['marital_status'],
                    'ethnic' => $member['ethnic'],
                    'highest_education_level' => $member['highest_education_level'],
                    'highest_education_certificate' => $member['highest_education_certificate'],
                    'employment_status' => $member['employment_status'],

                    // BLOK C
                    'main_occupation' => $member['main_occupation'],
                    'employment_position' => $member['employment_position'],

                    // BLOK D
                    'health_insurance' => $member['health_insurance'],
                    'stunting' => $member['stunting'],
                    'disability' => $member['disability'],
                ]);
            }

            DB::commit();

            return redirect()->route('family.index')->with('status', 'Keluarga berhasil Diupdate!');
        } catch (\Exception $e) {
            DB::rollBack();

            // Hapus file baru jika upload gagal di tengah jalan
            if (isset($path) && $path != $family->house_photo && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mengupdate data: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        // Mencari data shop berdasarkan ID yang diteruskan melalui URL
        $family = Family::findOrFail($id);

        // Hapus semua anggota keluarga dulu
        $family->members()->delete();

        // Menghapus data shop
        $family->delete();

        // Mengalihkan ke halaman sebelumnya dengan pesan sukses
        return redirect()->route('family.index')->with('status', 'Keluarga berhasil dihapus!');
    }
    public function getRtByKelurahan($kelurahanId)
    {
        $rtRwList = RtRw::where('kelurahan_id', $kelurahanId)->get();
        return response()->json($rtRwList);
    }

    public function export()
    {
        $user = Auth::user();
        $query = Family::with(['kelurahan', 'rtRw', 'creator']);

        if ($user->role === 'petugas') {
            $query->where('created_by', $user->id);
        } elseif ($user->role === 'supervisor') {
            $query->where('kelurahan_id', $user->kelurahan_id);
        }

        $families = $query->get();

        $filename = "data-keluarga-" . date('Y-m-d') . ".csv";
        $handle = fopen('php://temp', 'w+');

        // TAMBAHKAN BOM UTF-8
        fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // HEADER
        fputcsv($handle, [
            'No. KK',
            'Kepemilikan KK',
            'Jumlah Anggota',
            'Kelurahan/Desa',
            'RT/RW',
            'Alamat KTP',
            'Kota/Kabupaten',
            'Latitude',
            'Longitude',
            'BPNT',
            'PKH',
            'BLT Lansia',
            'BLT Desa',
            'Bantuan Lain',
            'Dibuat Oleh',
        ], ';');

        foreach ($families as $family) {
            // PAKSA SEMUA JADI STRING DENGAN AWALAN "="
            fputcsv($handle, [
                '="' . $family->number_kk . '"',                    // NIK/KK biar ga ilang angka
                '="' . $family->ownership_kk . '"',
                '="' . $family->number_of_family_member . '"',
                '="' . ($family->kelurahan?->nama ?? '-') . '"',
                '="' . ($family->rtRw ? 'RT ' . $family->rtRw->rt . ' RW ' . $family->rtRw->rw : '-') . '"',
                '="' . $family->ktp_address . '"',
                '="' . $family->city_address . '"',
                '="' . $family->latitude . '"',
                '="' . $family->longitude . '"',
                '="' . $family->bpnt . '"',
                '="' . $family->pkh . '"',
                '="' . $family->blt_elderly . '"',
                '="' . $family->blt_village . '"',
                '="' . $family->other_assistance . '"',
                '="' . ($family->creator?->name ?? '-') . '"',
            ], ';');
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return response($content)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
