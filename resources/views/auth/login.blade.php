<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk — Absensi Karyawan</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,600;700;800&family=IBM+Plex+Mono:ital,wght@0,400;0,500;0,600;1,400&family=IBM+Plex+Sans:ital,wght@0,400;0,500;0,600;1,400&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F4EFE6] text-[#1B1B18] font-sans min-h-screen flex selection:bg-[#1F3D2B] selection:text-[#FAF7F2]">
    <div class="w-full min-h-screen flex flex-col md:flex-row">
        {{-- Panel Kiri Asimetris (Hijau Tua Utilitarian) --}}
        <div class="w-full md:w-5/12 lg:w-4/12 bg-[#1F3D2B] text-[#FAF7F2] p-8 md:p-12 lg:p-16 flex flex-col justify-between border-b md:border-b-0 md:border-r border-[#162E20]">
            <div>
                <div class="inline-block px-2 py-0.5 border border-[#40684F] text-[10px] font-mono tracking-widest uppercase text-[#FAF7F2] mb-6 rounded-[2px]">
                    Buku Kehadiran Digital
                </div>
                <h1 class="font-heading font-extrabold text-3xl md:text-4xl lg:text-5xl tracking-tight leading-tight uppercase">
                    Absensi Karyawan
                </h1>
                <p class="mt-4 text-xs font-sans text-[#CFDACF] leading-relaxed max-w-sm">
                    Pencatatan waktu kehadiran resmi harian karyawan berbasis verifikasi kamera dan sinkronisasi waktu server Jakarta (WIB).
                </p>
            </div>

            {{-- Jam Digital Real-time di Panel Kiri --}}
            <div class="my-10 md:my-0 py-6 border-y border-[#2D543B]">
                <div class="text-[11px] font-mono uppercase tracking-widest text-[#9BB1A1] mb-1">
                    Waktu Server Aktif (WIB)
                </div>
                <div id="liveClockLogin" class="font-mono text-3xl lg:text-4xl font-bold tracking-tight text-[#FAF7F2]">
                    --:--:-- WIB
                </div>
                <div id="liveDateLogin" class="text-xs font-mono text-[#CFDACF] mt-2">
                    {{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('l, d F Y') }}
                </div>
            </div>

            <div class="text-[11px] font-mono text-[#8EA694]">
                <span>Sistem Presensi V1.0</span>
                <span class="mx-2">&bull;</span>
                <span>Protokol Keamanan Aktif</span>
            </div>
        </div>

        {{-- Panel Kanan: Form Login Bersih dengan Whitespace Lega --}}
        <div class="w-full md:w-7/12 lg:w-8/12 flex items-center justify-center p-6 md:p-12 lg:p-20 bg-[#F4EFE6]">
            <div class="w-full max-w-md">
                <div class="mb-8">
                    <span class="text-xs font-mono text-[#57574E] uppercase tracking-wider block mb-1">Autentikasi Akun</span>
                    <h2 class="font-heading font-bold text-2xl md:text-3xl text-[#1B1B18] tracking-tight">
                        Masuk ke Sistem
                    </h2>
                    <p class="text-xs text-[#57574E] mt-1">
                        Masukkan username atau email beserta kata sandi yang telah terdaftar pada sistem kantor.
                    </p>
                </div>

                @if ($errors->any())
                    <div class="mb-6 p-4 bg-[#FBECE7] border border-[#C2542D] text-[#C2542D] text-xs rounded-[2px]">
                        <span class="font-mono font-semibold uppercase tracking-wider block mb-1">Gagal Masuk</span>
                        <ul class="list-disc list-inside space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="login" class="block font-mono text-xs uppercase tracking-wider text-[#1B1B18] mb-1.5 font-medium">
                            Username atau Email
                        </label>
                        <x-input
                            id="login"
                            name="login"
                            type="text"
                            value="{{ old('login') }}"
                            placeholder="username atau nama@kantor.test"
                            required
                            autofocus
                            :error="$errors->has('login') || $errors->has('email')"
                        />
                    </div>

                    <div>
                        <label for="password" class="block font-mono text-xs uppercase tracking-wider text-[#1B1B18] mb-1.5 font-medium">
                            Kata Sandi
                        </label>
                        <x-input
                            id="password"
                            name="password"
                            type="password"
                            placeholder="••••••••"
                            required
                            :error="$errors->has('password')"
                        />
                    </div>

                    <div class="flex items-center justify-between text-xs pt-1">
                        <label class="inline-flex items-center space-x-2 cursor-pointer select-none">
                            <input type="checkbox" name="remember" class="accent-[#1F3D2B] rounded-[2px] w-3.5 h-3.5 border-[#DDD5C7]">
                            <span class="text-[#57574E] font-mono text-[11px]">Ingat sesi masuk ini</span>
                        </label>
                    </div>

                    <div class="pt-2">
                        <x-button type="submit" variant="primary" class="w-full py-3">
                            Masuk Sekarang
                        </x-button>
                    </div>
                </form>

                <div class="mt-8 pt-6 border-t border-[#DDD5C7] text-[11px] text-[#57574E] font-mono leading-relaxed">
                    <p class="font-semibold text-[#1B1B18] uppercase tracking-wider mb-1">Perhatian Keamanan</p>
                    <p>Sistem dibatasi maksimal 5 percobaan per menit. Seluruh rekaman waktu disinkronkan secara langsung dengan server.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Script Jam Real-Time Berjalan (WIB) --}}
    <script>
        (function() {
            function updateClock() {
                const now = new Date();
                // Konversi waktu ke Asia/Jakarta (UTC+7)
                const options = {
                    timeZone: 'Asia/Jakarta',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: false
                };
                const formatter = new Intl.DateTimeFormat('id-ID', options);
                const clockEl = document.getElementById('liveClockLogin');
                if (clockEl) {
                    clockEl.textContent = formatter.format(now) + ' WIB';
                }
            }
            updateClock();
            setInterval(updateClock, 1000);
        })();
    </script>
</body>
</html>
