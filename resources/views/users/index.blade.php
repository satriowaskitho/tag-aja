<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Pengguna') }}
        </h2>
    </x-slot>

    <!-- NOTIFIKASI SUKSES -->
    @if (session('status'))
        <div class="max-w-7xl mx-auto py-1 px-4 sm:px-6 lg:px-8 items-start justify-start z-50 relative">
            <div id='alert'
                class="fixed alert auto-dismiss-alert w-auto flex items-center p-2 mt-1 text-base text-green-800 border border-green-300 rounded-lg bg-green-50"
                role="alert">
                <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                    fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                </svg>
                <span class="sr-only">Info</span>
                <div class="message-alert">
                    <span class="font-medium">Yey!</span> {{ session('status') }}
                </div>
            </div>
        </div>
    @endif

    <!-- NOTIFIKASI ERROR / VALIDASI -->
    @if (session('error') || $errors->any())
        <div class="max-w-7xl mx-auto py-1 px-4 sm:px-6 lg:px-8 items-start justify-start z-50 relative">
            <div class="fixed alert auto-dismiss-alert w-auto flex items-center p-2 mt-1 text-base text-red-800 border border-red-300 rounded-lg bg-red-50"
                role="alert">
                <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                    fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM10 14a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3ZM10 5a1 1 0 0 1 1 1v4a1 1 0 1 1-2 0V6a1 1 0 0 1 1-1Z" />
                </svg>
                <span class="sr-only">Error</span>
                <div class="message-alert">
                    <span class="font-medium">Gagal!</span> 
                    @if(session('error'))
                        {{ session('error') }}
                    @else
                        Ada kesalahan pada input data (contoh: email sudah dipakai, password kurang dari 6 karakter). Silakan coba lagi.
                    @endif
                </div>
            </div>
        </div>
    @endif

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <section class="bg-gray-50 py-4 antialiased dark:bg-gray-900 md:py-6 md:px-4">
                    <div class="mx-auto max-w-screen-xl">

                        <!-- SEARCH & TOMBOL TAMBAH -->
                        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mb-4">
                            <div class="relative w-full sm:w-96">
                                <input type="text" id="search-input" placeholder="Cari nama atau email..."
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                <svg class="absolute right-3 top-2.5 w-5 h-5 text-gray-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <button onclick="openAddModal()"
                                class="bg-primary-700 text-white px-4 py-2 rounded-lg hover:bg-primary-800 text-sm">
                                + Tambah Pengguna
                            </button>
                        </div>

                        <!-- TABEL USER -->
                        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                            <table class="w-full text-sm text-left text-gray-700 border border-gray-200">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-3">No</th>
                                        <th class="px-4 py-3">Nama</th>
                                        <th class="px-4 py-3">Email</th>
                                        <th class="px-4 py-3">Role</th>
                                        <th class="px-4 py-3">Kelurahan</th>
                                        <th class="px-4 py-3">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="user-table-body">
                                    @foreach ($users as $index => $user)
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="px-4 py-3">{{ $loop->iteration }}</td>
                                            <td class="px-4 py-3 font-medium">{{ $user->name }}</td>
                                            <td class="px-4 py-3">{{ $user->email }}</td>
                                            <td class="px-4 py-3">
                                                @php
                                                    $roleClass = match ($user->role) {
                                                        'admin' => 'bg-red-100 text-red-700',
                                                        'supervisor' => 'bg-yellow-100 text-yellow-700',
                                                        default => 'bg-blue-100 text-blue-700',
                                                    };
                                                @endphp
                                                <span
                                                    class="px-2 py-1 rounded-full text-xs font-semibold {{ $roleClass }}">
                                                    {{ $user->role }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3">{{ $user->kelurahan->nama ?? '-' }}</td>
                                            <td class="px-4 py-3">
                                                <div class="flex space-x-2">
                                                    <button onclick="openEditModal({{ $user->id }})"
                                                        class="text-blue-600 hover:underline text-sm">Edit</button>
                                                    <button
                                                        onclick="openDeleteModal({{ $user->id }}, '{{ $user->name }}')"
                                                        class="text-red-600 hover:underline text-sm">Hapus</button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- PAGINATION -->
                        <div class="flex items-center justify-between bg-gray-50 px-4 py-3 sm:px-6 mt-4 rounded-b-lg">
                            <div class="flex flex-1 justify-between sm:hidden">
                                <button id="prev-btn-mobile"
                                    class="px-4 py-2 border rounded-md bg-white text-sm font-medium hover:bg-gray-50">Previous</button>
                                <button id="next-btn-mobile"
                                    class="px-4 py-2 border rounded-md bg-white text-sm font-medium hover:bg-gray-50">Next</button>
                            </div>
                            <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-gray-700">
                                        Showing <span class="font-semibold" id="start-entry">1</span>
                                        to <span class="font-semibold" id="end-entry">10</span>
                                        of <span class="font-semibold" id="total-entries">{{ $users->count() }}</span>
                                        entries
                                    </p>
                                </div>
                                <div>
                                    <nav class="inline-flex rounded-md shadow-sm overflow-hidden">
                                        <button id="prev-btn"
                                            class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-700 bg-white border border-gray-300 hover:bg-gray-100 disabled:opacity-40 disabled:cursor-not-allowed">
                                            <span class="sr-only">Previous</span>
                                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </button>

                                        <div id="page-numbers" class="flex -space-x-px"></div>

                                        <button id="next-btn"
                                            class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-700 bg-white border border-gray-300 hover:bg-gray-100 disabled:opacity-40 disabled:cursor-not-allowed">
                                            <span class="sr-only">Next</span>
                                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <!-- MODAL TAMBAH/EDIT USER -->
    <div id="user-modal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black bg-opacity-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
                <div class="flex justify-between items-center p-4 border-b">
                    <h3 class="text-lg font-semibold" id="modal-title">Tambah Pengguna</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">&times;</button>
                </div>
                <form id="user-form" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="form-method" value="POST">
                    <input type="hidden" name="user_id" id="user-id">
                    <div class="p-4 space-y-3">
                        <div><label class="block text-sm font-medium">Nama Lengkap</label><input type="text"
                                name="name" id="user-name" required class="w-full border rounded-lg px-3 py-2">
                        </div>
                        <div><label class="block text-sm font-medium">Email</label><input type="email"
                                name="email" id="user-email" required class="w-full border rounded-lg px-3 py-2">
                        </div>
                        <div><label class="block text-sm font-medium">Password</label><input type="password"
                                name="password" id="user-password" class="w-full border rounded-lg px-3 py-2">
                            <p class="text-xs text-gray-500 mt-1" id="password-hint">Kosongkan jika tidak ingin
                                mengubah password</p>
                        </div>
                        <div><label class="block text-sm font-medium">Role</label><select name="role"
                                id="user-role" required class="w-full border rounded-lg px-3 py-2">
                                <option value="admin">Admin</option>
                                <option value="supervisor">Supervisor</option>
                                <option value="petugas">Petugas</option>
                            </select></div>
                        <div><label class="block text-sm font-medium">Kelurahan</label><select name="kelurahan_id"
                                id="user-kelurahan" class="w-full border rounded-lg px-3 py-2">
                                <option value="">- Pilih Kelurahan -</option>
                                @foreach ($kelurahans as $kel)
                                    <option value="{{ $kel->id }}">{{ $kel->nama }}</option>
                                @endforeach
                            </select></div>
                        <div><label class="block text-sm font-medium">RT/RW</label><select name="rt_rw_id"
                                id="user-rt" class="w-full border rounded-lg px-3 py-2">
                                <option value="">- Pilih RT/RW -</option>
                            </select></div>
                    </div>
                    <div class="flex justify-end gap-2 p-4 border-t"><button type="button" onclick="closeModal()"
                            class="px-4 py-2 bg-gray-300 rounded-lg">Batal</button><button type="submit"
                            class="px-4 py-2 bg-primary-700 text-white rounded-lg">Simpan</button></div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL HAPUS -->
    <div id="delete-modal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black bg-opacity-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
                <div class="p-4 text-center">
                    <svg class="mx-auto mb-4 text-gray-400 w-12 h-12" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <h3 class="mb-5 text-lg font-normal text-gray-500">Yakin ingin menghapus <span
                            id="delete-user-name" class="font-semibold text-gray-700"></span>?</h3>
                    <form id="delete-form" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="text-white bg-red-600 hover:bg-red-800 rounded-lg px-5 py-2.5 text-center">Ya,
                            hapus!</button>
                        <button type="button" onclick="closeDeleteModal()"
                            class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 bg-white rounded-lg border border-gray-200 hover:bg-gray-100">Batal</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('leaflet_create_js')
        {{-- Scroll Reveal --}}
        <script>
            window.sr = ScrollReveal({
                duration: 300,
                distance: '50px',
                easing: 'ease-out'
            });

            sr.reveal('.alert', {
                interval: 200,
                origin: 'bottom',
                reset: false
            })

            sr.reveal('.message-alert', {
                interval: 200,
                delay: 500,
                origin: 'left',
                reset: true,
            })
            // sr.reveal('.shop-item', {interval: 300, origin:'bottom', reset:false})
        </script>
        <script>
            // Pastikan alert ditampilkan hanya jika ada session status
            document.addEventListener('DOMContentLoaded', function() {
                // Ambil semua elemen alert
                const alerts = document.querySelectorAll('.auto-dismiss-alert');

                alerts.forEach(alert => {
                    // Tunggu 3 detik, kemudian animasikan ke opacity 0
                    setTimeout(function() {
                        alert.classList.add('transition-opacity', 'duration-1000', 'opacity-0'); 
                        
                        // Setelah animasi selesai (1 detik), hapus elemen dari DOM
                        setTimeout(function() {
                            alert.remove();
                        }, 1000); 
                    }, 4000); 
                });
            });
        </script>
        <script>
            let currentPage = 1;
            const itemsPerPage = 10;

            let allRows = [];
            let filteredRows = [];

            document.addEventListener('DOMContentLoaded', () => {
                allRows = Array.from(document.querySelectorAll('#user-table-body tr'));
                filteredRows = [...allRows];

                renderTable();
            });

            // RENDER TABLE
            function renderTable() {
                const total = filteredRows.length;
                const totalPages = Math.ceil(total / itemsPerPage);

                const start = (currentPage - 1) * itemsPerPage;
                const end = start + itemsPerPage;

                allRows.forEach(row => row.style.display = 'none');

                filteredRows.slice(start, end).forEach(row => {
                    row.style.display = '';
                });

                // INFO
                document.getElementById('start-entry').innerText = total ? start + 1 : 0;
                document.getElementById('end-entry').innerText = Math.min(end, total);
                document.getElementById('total-entries').innerText = total;

                updatePagination(totalPages);
            }

            // BUTTON NUMBER
            function updatePagination(totalPages) {
                const container = document.getElementById('page-numbers');
                container.innerHTML = '';

                if (totalPages === 0) return;

                for (let i = 1; i <= totalPages; i++) {
                    const btn = document.createElement('button');
                    btn.textContent = i;

                    btn.className =
                        `relative inline-flex items-center px-3 py-2 text-sm font-semibold border border-gray-300
                        ${i === currentPage 
                            ? 'bg-primary-700 text-white' 
                            : 'bg-white text-gray-700 hover:bg-gray-100'}`;

                    btn.onclick = () => {
                        currentPage = i;
                        renderTable();
                    };

                    container.appendChild(btn);
                }

                document.getElementById('prev-btn').disabled = currentPage === 1;
                document.getElementById('next-btn').disabled = currentPage === totalPages;
            }

            // PREV NEXT
            document.getElementById('prev-btn').onclick = () => {
                if (currentPage > 1) {
                    currentPage--;
                    renderTable();
                }
            };

            document.getElementById('next-btn').onclick = () => {
                const totalPages = Math.ceil(filteredRows.length / itemsPerPage);
                if (currentPage < totalPages) {
                    currentPage++;
                    renderTable();
                }
            };

            // SEARCH
            document.getElementById('search-input').addEventListener('input', function() {
                const keyword = this.value.toLowerCase();

                filteredRows = allRows.filter(row =>
                    row.innerText.toLowerCase().includes(keyword)
                );

                currentPage = 1;
                renderTable();
            });

            document.getElementById('prev-btn-mobile').onclick = () => {
                if (currentPage > 1) {
                    currentPage--;
                    renderTable();
                }
            };

            document.getElementById('next-btn-mobile').onclick = () => {
                const totalPages = Math.ceil(filteredRows.length / itemsPerPage);
                if (currentPage < totalPages) {
                    currentPage++;
                    renderTable();
                }
            };

            // SEARCH FRONTEND (FILTER BARIS TABEL)
            document.getElementById('search-input').addEventListener('input', function() {
                const keyword = this.value.toLowerCase();

                filteredRows = allRows.filter(row =>
                    row.innerText.toLowerCase().includes(keyword)
                );

                currentPage = 1;
                renderTable();
            });

            // MODAL TAMBAH/EDIT
            const modal = document.getElementById('user-modal');
            const deleteModal = document.getElementById('delete-modal');
            const form = document.getElementById('user-form');

            function openAddModal() {
                document.getElementById('modal-title').innerText = 'Tambah Pengguna';
                document.getElementById('form-method').value = 'POST';
                form.action = "{{ route('user.store') }}";
                document.getElementById('user-id').value = '';
                document.getElementById('user-name').value = '';
                document.getElementById('user-email').value = '';
                document.getElementById('user-password').value = '';
                document.getElementById('user-role').value = 'petugas';
                document.getElementById('user-kelurahan').value = '';
                loadRtByKelurahan(null, null);
                modal.classList.remove('hidden');
            }

            function openEditModal(id) {
                fetch(`/user/${id}/edit`)
                    .then(res => res.json())
                    .then(user => {
                        document.getElementById('modal-title').innerText = 'Edit Pengguna';
                        document.getElementById('form-method').value = 'PUT';
                        form.action = `/user/${user.id}`;
                        document.getElementById('user-id').value = user.id;
                        document.getElementById('user-name').value = user.name;
                        document.getElementById('user-email').value = user.email;
                        document.getElementById('user-role').value = user.role;
                        document.getElementById('user-kelurahan').value = user.kelurahan_id || '';
                        loadRtByKelurahan(user.kelurahan_id, user.rt_rw_id);
                        modal.classList.remove('hidden');
                    });
            }

            function loadRtByKelurahan(kelurahanId, selectedRtId = null) {
                const rtSelect = document.getElementById('user-rt');
                rtSelect.innerHTML = '<option value="">- Pilih RT/RW -</option>';
                if (!kelurahanId) return;
                fetch(`/get-rt-by-kelurahan/${kelurahanId}`)
                    .then(res => res.json())
                    .then(data => {
                        data.forEach(item => {
                            const opt = document.createElement('option');
                            opt.value = item.id;
                            opt.textContent = `RT ${item.rt} / RW ${item.rw}`;
                            if (selectedRtId == item.id) opt.selected = true;
                            rtSelect.appendChild(opt);
                        });
                    });
            }

            document.getElementById('user-kelurahan').addEventListener('change', function() {
                loadRtByKelurahan(this.value);
            });

            function closeModal() {
                modal.classList.add('hidden');
            }

            // MODAL HAPUS
            let deleteUserId = null;

            function openDeleteModal(id, name) {
                deleteUserId = id;
                document.getElementById('delete-user-name').innerText = name;
                document.getElementById('delete-form').action = `/user.delete/${id}`;
                deleteModal.classList.remove('hidden');
            }

            function closeDeleteModal() {
                deleteModal.classList.add('hidden');
            }
        </script>
    @endpush
</x-app-layout>
