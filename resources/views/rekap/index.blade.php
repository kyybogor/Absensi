<x-layouts.app title="Rekap Absensi">
    <div class="space-y-6">
        {{-- Pesan Flash Status Sukses & Error --}}
        @if (session('status_sukses'))
            <div class="p-3 bg-[#E5EEE8] border border-[#1F3D2B] text-[#1F3D2B] text-xs font-mono rounded-[2px] flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#1F3D2B]" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span>{{ session('status_sukses') }}</span>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="p-3 bg-[#FBECE7] border border-[#C2542D] text-[#C2542D] text-xs font-mono rounded-[2px]">
                <span class="font-bold block mb-1 uppercase tracking-wider">Perhatian:</span>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Judul Halaman & Tombol Aksi Admin --}}
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 pb-4 border-b border-[#DDD5C7]">
            <div>
                <span class="text-xs font-mono uppercase tracking-widest text-[#57574E] block mb-1">
                    Buku Induk Presensi
                </span>
                <h1 class="text-2xl md:text-3xl font-extrabold text-[#1B1B18] uppercase tracking-tight">
                    Rekap Absensi
                </h1>
                <p class="text-xs text-[#57574E] mt-1">
                    @if ($isAdmin)
                        Menampilkan rekaman seluruh karyawan terdaftar dalam sistem. Admin berwenang menentukan dan mengubah status izin kehadiran.
                    @else
                        Menampilkan rekaman riwayat absensi pribadi Anda.
                    @endif
                </p>
            </div>

            @if ($isAdmin)
                <div>
                    <button type="button"
                            onclick="openCatatIzinModal()"
                            class="inline-flex items-center space-x-1.5 px-3 py-2 bg-[#1F3D2B] hover:bg-[#162E20] text-[#FAF7F2] text-xs font-mono uppercase tracking-wider rounded-[2px] transition shadow-sm cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        <span>Catat Izin Karyawan</span>
                    </button>
                </div>
            @endif
        </div>

        {{-- Ringkasan Statistik (4 Metrik Ledger: Hadir, Izin, Telat, Total Keterlambatan Disusun 1 Baris di Layar Lebar, 2x2 di HP) --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 sm:gap-3 md:gap-4">
            {{-- Jumlah Hadir --}}
            <div class="bg-[#FAF7F2] border border-[#DDD5C7] p-3 sm:p-4 rounded-[2px]">
                <span class="text-[10px] sm:text-[11px] font-mono uppercase tracking-wider text-[#57574E] block mb-1">
                    Jumlah Hadir
                </span>
                <div class="font-mono text-xl sm:text-2xl lg:text-3xl font-bold text-[#1F3D2B]">
                    {{ $ringkasan['total_hadir'] }}
                    <span class="text-xs font-normal text-[#57574E]">hari</span>
                </div>
            </div>

            {{-- Jumlah Izin --}}
            <div class="bg-[#FAF7F2] border border-[#DDD5C7] p-3 sm:p-4 rounded-[2px]">
                <span class="text-[10px] sm:text-[11px] font-mono uppercase tracking-wider text-[#57574E] block mb-1">
                    Jumlah Izin
                </span>
                <div class="font-mono text-xl sm:text-2xl lg:text-3xl font-bold {{ $ringkasan['total_izin'] > 0 ? 'text-[#2B527E]' : 'text-[#57574E]' }}">
                    {{ $ringkasan['total_izin'] }}
                    <span class="text-xs font-normal text-[#57574E]">hari</span>
                </div>
            </div>

            {{-- Jumlah Telat --}}
            <div class="bg-[#FAF7F2] border border-[#DDD5C7] p-3 sm:p-4 rounded-[2px]">
                <span class="text-[10px] sm:text-[11px] font-mono uppercase tracking-wider text-[#57574E] block mb-1">
                    Jumlah Telat
                </span>
                <div class="font-mono text-xl sm:text-2xl lg:text-3xl font-bold {{ $ringkasan['total_telat'] > 0 ? 'text-[#C2542D]' : 'text-[#1F3D2B]' }}">
                    {{ $ringkasan['total_telat'] }}
                    <span class="text-xs font-normal text-[#57574E]">kali</span>
                </div>
            </div>

            {{-- Total Keterlambatan --}}
            <div class="bg-[#FAF7F2] border border-[#DDD5C7] p-3 sm:p-4 rounded-[2px]">
                <span class="text-[10px] sm:text-[11px] font-mono uppercase tracking-wider text-[#57574E] block mb-1">
                    Total Keterlambatan
                </span>
                <div class="font-mono text-base sm:text-xl lg:text-2xl font-bold text-[#1B1B18] truncate" title="{{ $ringkasan['format_keterlambatan'] }}">
                    {{ $ringkasan['format_keterlambatan'] }}
                </div>
            </div>
        </div>

        {{-- Filter Rentang Tanggal & Karyawan (Admin) --}}
        <div class="bg-[#FAF7F2] border border-[#DDD5C7] p-4 rounded-[2px]">
            <form method="GET" action="{{ route('rekap.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-end">
                {{-- Filter Dari Tanggal --}}
                <div class="lg:col-span-3">
                    <label for="filterDari" class="block font-mono text-[11px] uppercase tracking-wider text-[#57574E] mb-1">
                        Dari Tanggal
                    </label>
                    <x-input
                        id="filterDari"
                        name="dari"
                        type="date"
                        value="{{ request('dari') }}"
                    />
                </div>

                {{-- Filter Sampai Tanggal --}}
                <div class="lg:col-span-3">
                    <label for="filterSampai" class="block font-mono text-[11px] uppercase tracking-wider text-[#57574E] mb-1">
                        Sampai Tanggal
                    </label>
                    <x-input
                        id="filterSampai"
                        name="sampai"
                        type="date"
                        value="{{ request('sampai') }}"
                    />
                </div>

                {{-- Khusus Admin: Filter Karyawan --}}
                @if ($isAdmin)
                    <div class="lg:col-span-3">
                        <label for="filterUser" class="block font-mono text-[11px] uppercase tracking-wider text-[#57574E] mb-1">
                            Pilih Karyawan
                        </label>
                        <select id="filterUser"
                                name="user_id"
                                class="w-full bg-[#FAF7F2] border border-[#DDD5C7] text-[#1B1B18] text-sm px-3 py-2 rounded-[2px] transition duration-150 focus:outline-none focus:ring-1 focus:ring-[#1F3D2B] focus:border-[#1F3D2B]">
                            <option value="">Semua Karyawan</option>
                            @foreach ($karyawanList as $karyawan)
                                <option value="{{ $karyawan->id }}" {{ (string) request('user_id') === (string) $karyawan->id ? 'selected' : '' }}>
                                    {{ $karyawan->name }}{{ $karyawan->email ? ' (' . $karyawan->email . ')' : ' (@' . $karyawan->username . ')' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                {{-- Tombol Aksi Filter --}}
                <div class="{{ $isAdmin ? 'lg:col-span-3' : 'lg:col-span-6' }} flex items-center space-x-2 pt-1 sm:pt-0">
                    <x-button type="submit" variant="primary" class="flex-1 py-2">
                        Filter
                    </x-button>
                    @if (request()->hasAny(['dari', 'sampai', 'user_id']))
                        <a href="{{ route('rekap.index') }}"
                           class="inline-flex items-center justify-center font-mono text-xs uppercase tracking-wider px-3 py-2 rounded-[2px] border border-[#DDD5C7] text-[#57574E] hover:text-[#1B1B18] hover:border-[#1B1B18] bg-transparent">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Tabel Rekap Absensi Bergaya Buku Besar --}}
        <div class="bg-[#FAF7F2] border border-[#DDD5C7] rounded-[2px] overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b-2 border-[#DDD5C7] bg-[#ECE5DA] text-[11px] font-mono uppercase tracking-wider text-[#1B1B18]">
                            <th class="py-3 px-4 font-semibold">Tanggal</th>
                            <th class="py-3 px-4 font-semibold">Nama Karyawan</th>
                            <th class="py-3 px-4 font-semibold">Jam Masuk</th>
                            <th class="py-3 px-4 font-semibold">Jam Pulang</th>
                            <th class="py-3 px-4 font-semibold">Status</th>
                            <th class="py-3 px-4 font-semibold">Keterlambatan</th>
                            <th class="py-3 px-4 font-semibold text-center">Foto Bukti</th>
                            @if ($isAdmin)
                                <th class="py-3 px-4 font-semibold text-center">Kelola</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#DDD5C7] text-xs">
                        @forelse ($absensiList as $item)
                            <tr class="hover:bg-[#F4EFE6] transition duration-75">
                                {{-- Tanggal --}}
                                <td class="py-3.5 px-4 font-mono font-medium text-[#1B1B18] whitespace-nowrap">
                                    {{ $item->tanggal->translatedFormat('d/m/Y') }}
                                    <span class="block text-[10px] text-[#57574E]">
                                        {{ $item->tanggal->translatedFormat('l') }}
                                    </span>
                                </td>

                                {{-- Nama Karyawan --}}
                                <td class="py-3.5 px-4 text-[#1B1B18]">
                                    <span class="font-medium block">{{ $item->user->name }}</span>
                                    <span class="font-mono text-[10px] text-[#57574E] block">{{ $item->user->email ?? ('@' . ($item->user->username ?? '')) }}</span>
                                </td>

                                {{-- Jam Masuk --}}
                                <td class="py-3.5 px-4 font-mono text-[#1B1B18] whitespace-nowrap">
                                    @if ($item->jam_masuk)
                                        <span class="font-bold">{{ substr($item->jam_masuk, 0, 5) }}</span>
                                        <span class="text-[10px] text-[#57574E]">WIB</span>
                                    @elseif ($item->status->isIzin())
                                        <span class="text-[11px] text-[#2B527E] font-mono font-medium">— (Izin)</span>
                                    @else
                                        <span class="text-[11px] text-[#8E8B82] font-mono italic">—</span>
                                    @endif
                                </td>

                                {{-- Jam Pulang --}}
                                <td class="py-3.5 px-4 font-mono text-[#1B1B18] whitespace-nowrap">
                                    @if ($item->jam_pulang)
                                        <span class="font-bold text-[#1F3D2B]">{{ substr($item->jam_pulang, 0, 5) }}</span>
                                        <span class="text-[10px] text-[#57574E]">WIB</span>
                                    @elseif ($item->status->isIzin())
                                        <span class="text-[11px] text-[#2B527E] font-mono font-medium">— (Izin)</span>
                                    @else
                                        <span class="text-[11px] text-[#8E8B82] font-mono italic">Belum pulang</span>
                                    @endif
                                </td>

                                {{-- Status & Keterangan --}}
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <x-badge-status :status="$item->status" />
                                    @if ($item->keterangan)
                                        <span class="block text-[10px] font-mono text-[#57574E] mt-0.5 truncate max-w-[150px]" title="{{ $item->keterangan }}">
                                            Ket: {{ $item->keterangan }}
                                        </span>
                                    @endif
                                </td>

                                {{-- Keterlambatan --}}
                                <td class="py-3.5 px-4 font-mono whitespace-nowrap">
                                    @if ($item->status->isIzin())
                                        <span class="text-[#2B527E] font-medium">— (Izin)</span>
                                    @elseif ($item->status->isTelat())
                                        <span class="text-[#C2542D] font-medium">+{{ $item->keterlambatan_format }}</span>
                                    @else
                                        <span class="text-[#1F3D2B]">Tepat Waktu</span>
                                    @endif
                                </td>

                                {{-- Thumbnail Foto --}}
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    @php
                                        $pm = $item->pose_masuk_detail;
                                        $pp = $item->pose_pulang_detail;
                                    @endphp
                                    <div class="inline-flex items-center justify-center space-x-1.5">
                                        @if ($item->foto)
                                            <button type="button"
                                                    onclick="openPhotoModal('{{ $item->foto_url }}', '{{ addslashes($item->user->name) }}', '{{ $item->tanggal->translatedFormat('d F Y') }}', 'Masuk: {{ substr($item->jam_masuk, 0, 5) }} WIB', '{{ $item->status->label() }}', '{{ $pm ? addslashes($pm['nama']) : '' }}', '{{ $pm ? addslashes($pm['deskripsi']) : '' }}', '{{ $pm ? asset($pm['gambar']) : '' }}', '{{ $pm ? $pm['icon'] : '' }}')"
                                                    title="Foto Masuk {{ $pm ? ' - Pose: ' . $pm['nama'] : '' }}"
                                                    class="group inline-block cursor-pointer focus:outline-none text-center">
                                                <div class="relative inline-block">
                                                    <img src="{{ $item->foto_url }}"
                                                         alt="Foto Masuk {{ $item->user->name }}"
                                                         class="w-9 h-9 object-cover rounded-[2px] border border-[#DDD5C7] group-hover:border-[#1F3D2B] transition">
                                                    @if ($pm)
                                                        <span class="absolute -top-1.5 -right-1.5 bg-[#FAF7F2] border border-[#DDD5C7] text-[10px] leading-none rounded-full w-4 h-4 flex items-center justify-center shadow-xs">
                                                            {{ $pm['icon'] }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <span class="sr-only">Foto Masuk</span>
                                            </button>
                                        @endif
                                        @if ($item->foto_pulang)
                                            <button type="button"
                                                    onclick="openPhotoModal('{{ $item->foto_pulang_url }}', '{{ addslashes($item->user->name) }}', '{{ $item->tanggal->translatedFormat('d F Y') }}', 'Pulang: {{ substr($item->jam_pulang, 0, 5) }} WIB', 'Selesai', '{{ $pp ? addslashes($pp['nama']) : '' }}', '{{ $pp ? addslashes($pp['deskripsi']) : '' }}', '{{ $pp ? asset($pp['gambar']) : '' }}', '{{ $pp ? $pp['icon'] : '' }}')"
                                                    title="Foto Pulang {{ $pp ? ' - Pose: ' . $pp['nama'] : '' }}"
                                                    class="group inline-block cursor-pointer focus:outline-none text-center">
                                                <div class="relative inline-block">
                                                    <img src="{{ $item->foto_pulang_url }}"
                                                         alt="Foto Pulang {{ $item->user->name }}"
                                                         class="w-9 h-9 object-cover rounded-[2px] border border-[#DDD5C7] group-hover:border-[#1F3D2B] transition">
                                                    @if ($pp)
                                                        <span class="absolute -top-1.5 -right-1.5 bg-[#FAF7F2] border border-[#DDD5C7] text-[10px] leading-none rounded-full w-4 h-4 flex items-center justify-center shadow-xs">
                                                            {{ $pp['icon'] }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <span class="sr-only">Foto Pulang</span>
                                            </button>
                                        @endif
                                        @if (! $item->foto && ! $item->foto_pulang)
                                            <span class="text-[10px] font-mono text-[#8E8B82] italic">Tanpa foto</span>
                                        @endif
                                    </div>
                                    @if ($pm)
                                        <span class="block text-[9px] font-mono text-[#57574E] mt-0.5 truncate max-w-[130px] mx-auto" title="Tantangan Masuk: {{ $pm['nama'] }}">
                                            {{ $pm['icon'] }} {{ $pm['nama'] }}
                                        </span>
                                    @endif
                                </td>

                                {{-- Aksi Admin (Ubah Status) --}}
                                @if ($isAdmin)
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <button type="button"
                                                onclick="openStatusModal({{ $item->id }}, '{{ addslashes($item->user->name) }}', '{{ $item->tanggal->translatedFormat('d F Y') }}', '{{ $item->status->value }}', '{{ addslashes($item->keterangan ?? '') }}', {{ $item->telat_menit }})"
                                                class="inline-flex items-center px-2 py-1 text-[11px] font-mono font-medium border border-[#DDD5C7] hover:border-[#1F3D2B] hover:text-[#1F3D2B] bg-[#FAF7F2] text-[#57574E] rounded-[2px] transition cursor-pointer"
                                                title="Ubah Status Absensi">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                            Ubah
                                        </button>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $isAdmin ? '8' : '7' }}" class="py-8 px-4 text-center font-mono text-xs text-[#57574E]">
                                    Tidak ada catatan absensi yang sesuai dengan kriteria pencarian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($absensiList->hasPages())
                <div class="p-4 border-t border-[#DDD5C7] bg-[#FAF7F2]">
                    {{ $absensiList->links() }}
                </div>
            @endif
        </div>
    </div>

    @push('modals')
    {{-- MODAL 1: Lightbox Foto Perbesar & Komparasi Pose --}}
    <div id="photoLightboxModal"
         class="hidden fixed inset-0 z-50 bg-[#1B1B18]/80 backdrop-blur-xs overflow-y-auto p-2 sm:p-4 md:p-6"
         style="width: 100vw; max-width: 100vw; height: 100vh; max-height: 100vh; box-sizing: border-box;"
         onclick="closePhotoModal(event)">
        <div class="min-h-full w-full max-w-full flex items-center justify-center p-1 sm:p-2 box-border">
            <div id="photoModalCard"
                 class="bg-[#FAF7F2] border border-[#DDD5C7] w-full rounded-[2px] overflow-hidden p-3.5 sm:p-5 shadow-2xl relative my-auto min-w-0 box-border"
                 style="max-width: calc(100vw - 24px); width: 100%; box-sizing: border-box;"
                 onclick="event.stopPropagation()">
                <div class="flex items-center justify-between gap-2 pb-2.5 sm:pb-3 border-b border-[#DDD5C7] mb-3 sm:mb-4">
                    <div class="min-w-0 flex-1">
                        <h3 id="modalEmployeeName" class="font-bold text-sm sm:text-base text-[#1B1B18] truncate"></h3>
                        <p id="modalMetaInfo" class="text-[11px] sm:text-xs font-mono text-[#57574E] mt-0.5 truncate"></p>
                    </div>
                    <button type="button"
                            onclick="closePhotoModal()"
                            class="shrink-0 text-xs font-mono px-2.5 py-1 border border-[#DDD5C7] hover:border-[#C2542D] hover:text-[#C2542D] text-[#57574E] bg-[#FAF7F2] rounded-[2px] cursor-pointer">
                        <span class="sm:hidden">✕ Tutup</span><span class="hidden sm:inline">Tutup [ESC]</span>
                    </button>
                </div>

                {{-- Komparasi Dua Kolom Simetris & Selaras (Stack Vertikal di HP, 2 Kolom di Desktop) --}}
                <div id="modalPhotoGrid" class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4 items-stretch min-w-0 w-full" style="width: 100%; max-width: 100%;">
                    {{-- Kolom 1: Foto Absensi Karyawan --}}
                    <div class="bg-[#F4EFE6] border border-[#DDD5C7] p-2.5 sm:p-3 rounded-[2px] space-y-2 flex flex-col justify-between min-w-0 w-full box-border" style="overflow: hidden; max-width: 100%;">
                        <div>
                            <div class="flex items-center justify-between pb-1 border-b border-[#DDD5C7]">
                                <span class="text-[10px] font-mono uppercase font-bold text-[#1F3D2B] flex items-center space-x-1">
                                    <span>📸</span>
                                    <span>Foto Absensi Karyawan</span>
                                </span>
                                <span id="modalPhotoBadgeStatus" class="text-[10px] font-mono font-semibold text-[#1F3D2B] bg-[#E5EEE8] px-1.5 py-0.5 rounded-[2px] border border-[#1F3D2B]/30">
                                    Hadir
                                </span>
                            </div>

                            <div class="mt-1.5">
                                <h4 id="modalPhotoSubTitle" class="font-bold text-xs font-mono text-[#1B1B18] truncate">Tangkapan Kamera Langsung</h4>
                                <p id="modalPhotoMetaDetail" class="text-[11px] text-[#57574E] mt-0.5 leading-tight truncate">Bukti foto saat absensi dilakukan</p>
                            </div>
                        </div>

                        <div class="w-full bg-black overflow-hidden rounded-[2px] border border-[#DDD5C7] flex items-center justify-center" style="aspect-ratio: 4/3; max-width: 100%;">
                            <img id="modalPhotoImage" src="" alt="Bukti Foto Absensi" class="w-full h-full object-cover block" style="max-width: 100%; aspect-ratio: 4/3;">
                        </div>

                        <div class="text-[10px] font-mono text-[#57574E] bg-[#ECE5DA] p-1.5 rounded-[2px] border border-[#DDD5C7] text-center font-medium truncate">
                            📷 Foto asli tersimpan dalam server
                        </div>
                    </div>

                    {{-- Kolom 2: Referensi Pose Orang Asli --}}
                    <div id="modalPoseBox" class="bg-[#F4EFE6] border border-[#DDD5C7] p-2.5 sm:p-3 rounded-[2px] space-y-2 flex flex-col justify-between min-w-0 w-full box-border" style="overflow: hidden; max-width: 100%;">
                        <div>
                            <div class="flex items-center justify-between pb-1 border-b border-[#DDD5C7]">
                                <span class="text-[10px] font-mono uppercase font-bold text-[#1F3D2B] flex items-center space-x-1">
                                    <span>🛡️</span>
                                    <span>Referensi Pose Tantangan</span>
                                </span>
                                <span id="modalPoseIcon" class="text-base"></span>
                            </div>

                            <div class="mt-1.5">
                                <h4 id="modalPoseNama" class="font-bold text-xs font-mono text-[#1B1B18] truncate"></h4>
                                <p id="modalPoseDeskripsi" class="text-[11px] text-[#57574E] mt-0.5 leading-tight truncate"></p>
                            </div>
                        </div>

                        <div class="w-full bg-white overflow-hidden rounded-[2px] border border-[#DDD5C7] flex items-center justify-center" style="aspect-ratio: 4/3; max-width: 100%;">
                            <img id="modalPoseRefImage" src="" alt="Referensi Pose Asli" class="w-full h-full object-cover block" style="max-width: 100%; aspect-ratio: 4/3;">
                        </div>

                        <div class="text-[10px] font-mono text-[#1F3D2B] bg-[#E5EEE8] p-1.5 rounded-[2px] border border-[#1F3D2B]/30 text-center font-medium truncate">
                            ✓ Cocokkan pose foto karyawan dengan pose di atas
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($isAdmin)
        {{-- MODAL 2: Ubah Status Kehadiran --}}
        <div id="statusModal"
             class="hidden fixed inset-0 z-50 bg-[#1B1B18]/80 backdrop-blur-xs overflow-y-auto p-2 sm:p-4 md:p-6"
             style="width: 100vw; max-width: 100vw; height: 100vh; max-height: 100vh; box-sizing: border-box;"
             onclick="closeStatusModal(event)">
            <div class="min-h-full w-full max-w-full flex items-center justify-center p-1 sm:p-2 box-border">
                <div class="bg-[#FAF7F2] border border-[#DDD5C7] w-full rounded-[2px] overflow-hidden p-4 sm:p-6 shadow-2xl relative my-auto min-w-0 box-border"
                     style="max-width: calc(100vw - 24px); width: 100%; max-width: 480px; box-sizing: border-box;"
                     onclick="event.stopPropagation()">
                    <div class="flex items-center justify-between pb-3 border-b border-[#DDD5C7] mb-4">
                        <div>
                            <span class="text-[10px] font-mono uppercase tracking-widest text-[#57574E] block">Otoritas Admin</span>
                            <h3 class="font-bold text-base text-[#1B1B18]">Ubah Status Kehadiran</h3>
                        </div>
                        <button type="button"
                                onclick="closeStatusModal()"
                                class="text-xs font-mono px-2 py-1 border border-[#DDD5C7] hover:border-[#C2542D] hover:text-[#C2542D] text-[#57574E] rounded-[2px] cursor-pointer">
                            ✕
                        </button>
                    </div>

                    <form id="formUpdateStatus" method="POST" action="" class="space-y-4">
                        @csrf
                        @method('PATCH')

                        {{-- Informasi Singkat --}}
                        <div class="bg-[#ECE5DA] p-3 rounded-[2px] font-mono text-xs text-[#1B1B18] space-y-1">
                            <div><span class="text-[#57574E]">Karyawan:</span> <span id="statusModalName" class="font-bold"></span></div>
                            <div><span class="text-[#57574E]">Tanggal:</span> <span id="statusModalDate" class="font-bold"></span></div>
                        </div>

                        {{-- Pilihan Status --}}
                        <div>
                            <label for="selectStatus" class="block font-mono text-xs uppercase tracking-wider text-[#1B1B18] mb-1 font-medium">
                                Pilih Status Baru
                            </label>
                            <select id="selectStatus"
                                    name="status"
                                    onchange="handleStatusChange(this.value)"
                                    class="w-full bg-[#FAF7F2] border border-[#DDD5C7] text-[#1B1B18] text-sm px-3 py-2 rounded-[2px] transition focus:outline-none focus:ring-1 focus:ring-[#1F3D2B] focus:border-[#1F3D2B]">
                                <option value="izin">Izin (Disetujui Admin)</option>
                                <option value="tepat_waktu">Tepat Waktu</option>
                                <option value="telat">Telat</option>
                            </select>
                        </div>

                        {{-- Input Keterlambatan (hanya aktif jika Telat) --}}
                        <div id="containerTelatMenit" class="hidden">
                            <label for="inputTelatMenit" class="block font-mono text-xs uppercase tracking-wider text-[#1B1B18] mb-1 font-medium">
                                Keterlambatan (Menit)
                            </label>
                            <x-input
                                id="inputTelatMenit"
                                name="telat_menit"
                                type="number"
                                min="0"
                                placeholder="Contoh: 15"
                            />
                        </div>

                        {{-- Keterangan / Alasan --}}
                        <div>
                            <label for="inputKeterangan" class="block font-mono text-xs uppercase tracking-wider text-[#1B1B18] mb-1 font-medium">
                                Keterangan / Alasan (Opsional)
                            </label>
                            <x-input
                                id="inputKeterangan"
                                name="keterangan"
                                type="text"
                                placeholder="Contoh: Izin sakit dengan surat dokter / dinas luar"
                            />
                        </div>

                        <div class="pt-2 flex items-center justify-end space-x-2">
                            <button type="button"
                                    onclick="closeStatusModal()"
                                    class="px-3 py-2 text-xs font-mono uppercase tracking-wider border border-[#DDD5C7] text-[#57574E] hover:text-[#1B1B18] rounded-[2px] cursor-pointer">
                                Batal
                            </button>
                            <x-button type="submit" variant="primary" class="py-2 px-4">
                                Simpan Perubahan
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- MODAL 3: Catat Izin Karyawan Langsung --}}
        <div id="catatIzinModal"
             class="hidden fixed inset-0 z-50 bg-[#1B1B18]/80 backdrop-blur-xs overflow-y-auto p-2 sm:p-4 md:p-6"
             style="width: 100vw; max-width: 100vw; height: 100vh; max-height: 100vh; box-sizing: border-box;"
             onclick="closeCatatIzinModal(event)">
            <div class="min-h-full w-full max-w-full flex items-center justify-center p-1 sm:p-2 box-border">
                <div class="bg-[#FAF7F2] border border-[#DDD5C7] w-full rounded-[2px] overflow-hidden p-4 sm:p-6 shadow-2xl relative my-auto min-w-0 box-border"
                     style="max-width: calc(100vw - 24px); width: 100%; max-width: 480px; box-sizing: border-box;"
                     onclick="event.stopPropagation()">
                    <div class="flex items-center justify-between pb-3 border-b border-[#DDD5C7] mb-4">
                        <div>
                            <span class="text-[10px] font-mono uppercase tracking-widest text-[#57574E] block">Formulir Admin</span>
                            <h3 class="font-bold text-base text-[#1B1B18]">Catat Izin Karyawan</h3>
                        </div>
                        <button type="button"
                                onclick="closeCatatIzinModal()"
                                class="text-xs font-mono px-2 py-1 border border-[#DDD5C7] hover:border-[#C2542D] hover:text-[#C2542D] text-[#57574E] rounded-[2px] cursor-pointer">
                            ✕
                        </button>
                    </div>

                    <form method="POST" action="{{ route('admin.absensi.store-izin') }}" class="space-y-4">
                        @csrf

                        {{-- Pilih Karyawan --}}
                        <div>
                            <label for="izinUserId" class="block font-mono text-xs uppercase tracking-wider text-[#1B1B18] mb-1 font-medium">
                                Pilih Karyawan
                            </label>
                            <select id="izinUserId"
                                    name="user_id"
                                    required
                                    class="w-full bg-[#FAF7F2] border border-[#DDD5C7] text-[#1B1B18] text-sm px-3 py-2 rounded-[2px] transition focus:outline-none focus:ring-1 focus:ring-[#1F3D2B] focus:border-[#1F3D2B]">
                                <option value="">-- Pilih Karyawan --</option>
                                @foreach ($karyawanList as $karyawan)
                                    <option value="{{ $karyawan->id }}">
                                        {{ $karyawan->name }}{{ $karyawan->email ? ' (' . $karyawan->email . ')' : ' (@' . $karyawan->username . ')' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Tanggal Izin --}}
                        <div>
                            <label for="izinTanggal" class="block font-mono text-xs uppercase tracking-wider text-[#1B1B18] mb-1 font-medium">
                                Tanggal Izin
                            </label>
                            <x-input
                                id="izinTanggal"
                                name="tanggal"
                                type="date"
                                value="{{ now()->toDateString() }}"
                                required
                            />
                        </div>

                        {{-- Keterangan Izin --}}
                        <div>
                            <label for="izinKeterangan" class="block font-mono text-xs uppercase tracking-wider text-[#1B1B18] mb-1 font-medium">
                                Keterangan / Alasan Izin
                            </label>
                            <x-input
                                id="izinKeterangan"
                                name="keterangan"
                                type="text"
                                placeholder="Contoh: Sakit / Dinas luar / Urusan keluarga"
                            />
                        </div>

                        <div class="pt-2 flex items-center justify-end space-x-2">
                            <button type="button"
                                    onclick="closeCatatIzinModal()"
                                    class="px-3 py-2 text-xs font-mono uppercase tracking-wider border border-[#DDD5C7] text-[#57574E] hover:text-[#1B1B18] rounded-[2px] cursor-pointer">
                                Batal
                            </button>
                            <x-button type="submit" variant="primary" class="py-2 px-4">
                                Simpan Data Izin
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
    @endpush

    @push('scripts')
    <script>
        // Helper: pastikan modal berada di document.body agar backdrop menutupi seluruh layar
        function ensureModalInBody(id) {
            const el = document.getElementById(id);
            if (el && el.parentElement !== document.body) {
                document.body.appendChild(el);
            }
            return el;
        }

        // Modal Lightbox Foto & Komparasi Pose
        function openPhotoModal(imageUrl, name, date, time, status, poseNama, poseDeskripsi, poseGambar, poseIcon) {
            const modal = ensureModalInBody('photoLightboxModal');
            document.getElementById('modalPhotoImage').src = imageUrl;
            document.getElementById('modalEmployeeName').textContent = name;
            document.getElementById('modalMetaInfo').textContent = date + ' • ' + time;

            // Update Kolom Kiri Foto Karyawan
            const badgeStatus = document.getElementById('modalPhotoBadgeStatus');
            if (badgeStatus) {
                badgeStatus.textContent = status || 'Hadir';
            }
            const photoSubTitle = document.getElementById('modalPhotoSubTitle');
            if (photoSubTitle) {
                photoSubTitle.textContent = time;
            }
            const photoMetaDetail = document.getElementById('modalPhotoMetaDetail');
            if (photoMetaDetail) {
                photoMetaDetail.textContent = 'Status: ' + (status || 'Hadir') + ' pada ' + date;
            }

            const poseBox = document.getElementById('modalPoseBox');
            const photoCard = document.getElementById('photoModalCard');
            const photoGrid = document.getElementById('modalPhotoGrid');

            const isMobile = window.innerWidth < 768;
            if (poseNama && poseGambar) {
                document.getElementById('modalPoseNama').textContent = poseNama;
                document.getElementById('modalPoseDeskripsi').textContent = poseDeskripsi;
                document.getElementById('modalPoseRefImage').src = poseGambar;
                document.getElementById('modalPoseIcon').textContent = poseIcon || '✌️';
                poseBox.classList.remove('hidden');
                photoCard.style.maxWidth = isMobile ? 'calc(100vw - 24px)' : '768px';
                photoCard.className = 'bg-[#FAF7F2] border border-[#DDD5C7] w-full rounded-[2px] overflow-hidden p-3.5 sm:p-5 shadow-2xl relative my-auto min-w-0 box-border';
                photoGrid.className = 'grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4 items-stretch min-w-0 w-full';
            } else {
                poseBox.classList.add('hidden');
                photoCard.style.maxWidth = isMobile ? 'calc(100vw - 24px)' : '480px';
                photoCard.className = 'bg-[#FAF7F2] border border-[#DDD5C7] w-full rounded-[2px] overflow-hidden p-3.5 sm:p-5 shadow-2xl relative my-auto min-w-0 box-border';
                photoGrid.className = 'grid grid-cols-1 gap-3 sm:gap-4 items-stretch min-w-0 w-full';
            }

            document.body.classList.add('overflow-hidden');
            document.body.style.overflow = 'hidden';
            modal.classList.remove('hidden');
        }

        function closePhotoModal(event) {
            document.getElementById('photoLightboxModal').classList.add('hidden');
            document.getElementById('modalPhotoImage').src = '';
            document.getElementById('modalPoseRefImage').src = '';
            document.body.classList.remove('overflow-hidden');
            document.body.style.overflow = '';
        }

        // Modal Ubah Status (Admin)
        function openStatusModal(id, name, date, currentStatus, keterangan, telatMenit) {
            const modal = ensureModalInBody('statusModal');
            const form = document.getElementById('formUpdateStatus');
            form.action = '/admin/absensi/' + id + '/status';
            document.getElementById('statusModalName').textContent = name;
            document.getElementById('statusModalDate').textContent = date;
            document.getElementById('selectStatus').value = currentStatus;
            document.getElementById('inputKeterangan').value = keterangan || '';
            document.getElementById('inputTelatMenit').value = telatMenit || 0;

            handleStatusChange(currentStatus);
            document.body.classList.add('overflow-hidden');
            document.body.style.overflow = 'hidden';
            modal.classList.remove('hidden');
        }

        function closeStatusModal(event) {
            document.getElementById('statusModal').classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            document.body.style.overflow = '';
        }

        function handleStatusChange(status) {
            const container = document.getElementById('containerTelatMenit');
            if (status === 'telat') {
                container.classList.remove('hidden');
            } else {
                container.classList.add('hidden');
            }
        }

        // Modal Catat Izin Langsung (Admin)
        function openCatatIzinModal() {
            const modal = ensureModalInBody('catatIzinModal');
            document.body.classList.add('overflow-hidden');
            document.body.style.overflow = 'hidden';
            modal.classList.remove('hidden');
        }

        function closeCatatIzinModal(event) {
            document.getElementById('catatIzinModal').classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            document.body.style.overflow = '';
        }

        // Global ESC key listener to close modals
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closePhotoModal();
                if (document.getElementById('statusModal')) {
                    closeStatusModal();
                }
                if (document.getElementById('catatIzinModal')) {
                    closeCatatIzinModal();
                }
            }
        });
    </script>
    @endpush
</x-layouts.app>
