<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- STATISTIK CARD -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <!-- Card Total Keluarga -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Keluarga</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $totalKeluarga }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card Total Anggota -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Anggota</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $totalAnggota }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card Total RT/RW -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total RT/RW</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $totalRtRw }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card Total Kelurahan -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Kelurahan</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $totalKelurahan }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- STATISTIK BANTUAN -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Statistik Penerima Bantuan</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- BPNT -->
                    <div class="border rounded-lg p-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-600">BPNT</span>
                            <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">{{ $bpntCount }} Keluarga</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $bpntPercentage }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ $bpntPercentage }}% dari total keluarga</p>
                    </div>

                    <!-- PKH -->
                    <div class="border rounded-lg p-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-600">PKH</span>
                            <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">{{ $pkhCount }} Keluarga</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-green-600 h-2.5 rounded-full" style="width: {{ $pkhPercentage }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ $pkhPercentage }}% dari total keluarga</p>
                    </div>

                    <!-- BLT Lansia -->
                    <div class="border rounded-lg p-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-600">BLT Lansia</span>
                            <span class="text-xs bg-purple-100 text-purple-800 px-2 py-1 rounded-full">{{ $bltElderlyCount }} Keluarga</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-purple-600 h-2.5 rounded-full" style="width: {{ $bltElderlyPercentage }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ $bltElderlyPercentage }}% dari total keluarga</p>
                    </div>

                    <!-- BLT Desa -->
                    <div class="border rounded-lg p-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-600">BLT Desa</span>
                            <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full">{{ $bltVillageCount }} Keluarga</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-yellow-600 h-2.5 rounded-full" style="width: {{ $bltVillagePercentage }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ $bltVillagePercentage }}% dari total keluarga</p>
                    </div>
                </div>
            </div>

            <!-- GRAFIK SEDERHANA (OPSIONAL) -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- 5 Keluarga Terbaru -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">5 Keluarga terbaru dibuat oleh :</h3>
                    <div class="space-y-3">
                        @foreach($latestFamilies as $family)
                        <div class="flex items-center justify-between border-b pb-2">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $family->creator->name ?? 'Tidak diketahui' }}</p>
                                <p class="text-xs text-gray-500">{{ $family->kelurahan->nama ?? '-' }} - {{ $family->rtRw ? 'RT '.$family->rtRw->rt.' RW '.$family->rtRw->rw : '-' }}</p>
                            </div>
                            <span class="text-xs bg-gray-100 px-2 py-1 rounded">{{ $family->created_at->diffForHumans() }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- 5 Anggota Terbaru -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">5 Anggota Keluarga Terbaru</h3>
                    <div class="space-y-3">
                        @foreach($latestMembers as $member)
                        <div class="flex items-center justify-between border-b pb-2">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $member->full_name }}</p>
                                <p class="text-xs text-gray-500">{{ $member->status_in_family }} - {{ $member->family->kelurahan->nama ?? '-' }}</p>
                            </div>
                            <span class="text-xs bg-gray-100 px-2 py-1 rounded">{{ $member->created_at->diffForHumans() }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>