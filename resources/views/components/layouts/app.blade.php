<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Absensi Karyawan' }} — Sistem Kehadiran Kantor</title>

    {{-- Google Fonts: Bricolage Grotesque, IBM Plex Sans, IBM Plex Mono --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,600;700;800&family=IBM+Plex+Mono:ital,wght@0,400;0,500;0,600;1,400&family=IBM+Plex+Sans:ital,wght@0,400;0,500;0,600;1,400&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F4EFE6] text-[#1B1B18] font-sans min-h-screen flex flex-col selection:bg-[#1F3D2B] selection:text-[#FAF7F2]">
    {{-- Header / Navigasi Buku Besar Kantor --}}
    <header class="border-b border-[#DDD5C7] bg-[#FAF7F2] sticky top-0 z-40">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            <div class="flex items-center justify-between h-16">
                {{-- Identitas Aplikasi --}}
                <div class="flex items-center space-x-3">
                    <a href="{{ route('absensi.create') }}" class="flex items-center space-x-2 text-decoration-none">
                        <span class="w-3 h-3 bg-[#1F3D2B] inline-block"></span>
                        <span class="font-heading font-extrabold text-base sm:text-lg tracking-tight uppercase text-[#1B1B18]">
                            Absensi Karyawan
                        </span>
                    </a>
                    @if (auth()->check())
                        <span class="hidden sm:inline-block px-1.5 py-0.5 text-[10px] font-mono uppercase tracking-widest border border-[#BDB4A3] text-[#57574E] rounded-[2px]">
                            {{ auth()->user()->role->label() }}
                        </span>
                    @endif
                </div>

                {{-- Navigasi Teks --}}
                @auth
                    <nav class="hidden md:flex items-center space-x-6 text-xs font-mono uppercase tracking-wider">
                        <a href="{{ route('absensi.create') }}"
                           class="py-1 transition border-b-2 {{ request()->routeIs('absensi.*') ? 'border-[#1F3D2B] text-[#1F3D2B] font-semibold' : 'border-transparent text-[#57574E] hover:text-[#1B1B18]' }}">
                            Absen Sekarang
                        </a>
                        <a href="{{ route('rekap.index') }}"
                           class="py-1 transition border-b-2 {{ request()->routeIs('rekap.*') ? 'border-[#1F3D2B] text-[#1F3D2B] font-semibold' : 'border-transparent text-[#57574E] hover:text-[#1B1B18]' }}">
                            Rekap Absensi
                        </a>

                        @if (auth()->user()->isAdmin())
                            <span class="text-[#BDB4A3]">/</span>
                            <a href="{{ route('users.index') }}"
                               class="py-1 transition border-b-2 {{ request()->routeIs('users.*') ? 'border-[#1F3D2B] text-[#1F3D2B] font-semibold' : 'border-transparent text-[#57574E] hover:text-[#1B1B18]' }}">
                                Tambah Akun
                            </a>
                            <a href="{{ route('jam-absen.edit') }}"
                               class="py-1 transition border-b-2 {{ request()->routeIs('jam-absen.*') ? 'border-[#1F3D2B] text-[#1F3D2B] font-semibold' : 'border-transparent text-[#57574E] hover:text-[#1B1B18]' }}">
                                Atur Jam Absen
                            </a>
                        @endif
                    </nav>

                    {{-- Profil & Tombol Logout --}}
                    <div class="flex items-center space-x-3 text-xs">
                        <div class="hidden sm:block text-right">
                            <span class="block font-medium text-[#1B1B18] leading-tight">{{ auth()->user()->name }}</span>
                            <span class="block text-[11px] font-mono text-[#57574E]">{{ auth()->user()->email ?? ('@' . auth()->user()->username) }}</span>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="border border-[#DDD5C7] hover:border-[#C2542D] hover:text-[#C2542D] text-[#57574E] font-mono text-xs uppercase px-2.5 py-1.5 rounded-[2px] transition duration-150 cursor-pointer">
                                Keluar
                            </button>
                        </form>
                    </div>
                @endauth
            </div>

            {{-- Navigasi Mobile --}}
            @auth
                <div class="md:hidden py-2.5 border-t border-[#DDD5C7] flex items-center justify-between text-[11px] font-mono uppercase tracking-wider overflow-x-auto">
                    <div class="flex space-x-4">
                        <a href="{{ route('absensi.create') }}" class="{{ request()->routeIs('absensi.*') ? 'text-[#1F3D2B] font-bold underline' : 'text-[#57574E]' }}">
                            Absen
                        </a>
                        <a href="{{ route('rekap.index') }}" class="{{ request()->routeIs('rekap.*') ? 'text-[#1F3D2B] font-bold underline' : 'text-[#57574E]' }}">
                            Rekap
                        </a>
                        @if (auth()->user()->isAdmin())
                            <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'text-[#1F3D2B] font-bold underline' : 'text-[#57574E]' }}">
                                Akun
                            </a>
                            <a href="{{ route('jam-absen.edit') }}" class="{{ request()->routeIs('jam-absen.*') ? 'text-[#1F3D2B] font-bold underline' : 'text-[#57574E]' }}">
                                Jam Masuk
                            </a>
                        @endif
                    </div>
                </div>
            @endauth
        </div>
    </header>

    {{-- Isi Konten Halaman --}}
    <main class="flex-1 max-w-6xl w-full mx-auto px-4 sm:px-6 py-6 md:py-8 animate-ledger-in">
        {{-- Pesan Status Berhasil --}}
        @if (session('status_sukses'))
            <div class="mb-6 p-4 bg-[#E5EEE8] border border-[#1F3D2B] text-[#1F3D2B] text-sm rounded-[2px] flex items-start justify-between">
                <div>
                    <span class="font-mono text-xs font-semibold uppercase tracking-wider block mb-0.5">Konfirmasi</span>
                    <span>{{ session('status_sukses') }}</span>
                </div>
            </div>
        @endif

        {{-- Pesan Error Umum --}}
        @if ($errors->any())
            <div class="mb-6 p-4 bg-[#FBECE7] border border-[#C2542D] text-[#C2542D] text-sm rounded-[2px]">
                <span class="font-mono text-xs font-semibold uppercase tracking-wider block mb-1">Perhatian</span>
                <ul class="list-disc list-inside space-y-0.5 text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{ $slot }}
    </main>

    {{-- Footer Sederhana --}}
    <footer class="border-t border-[#DDD5C7] py-4 bg-[#FAF7F2] mt-auto">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 flex flex-col sm:flex-row justify-between items-center text-xs font-mono text-[#57574E] gap-2">
            <span>SISTEM ABSENSI KARYAWAN &copy; {{ date('Y') }}</span>
            <span class="text-[11px]">Zona Waktu Server: Asia/Jakarta (WIB)</span>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
