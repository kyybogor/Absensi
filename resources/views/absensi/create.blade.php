<x-layouts.app title="Absen Sekarang">
    @php
        $sudahMasuk = (bool) $absensiHariIni;
        $sudahPulang = $absensiHariIni && $absensiHariIni->sudahAbsenPulang();
        $siapPulang = $sudahMasuk && ! $sudahPulang;
    @endphp

    <div class="space-y-6">
        {{-- Header Halaman & Jam Digital Besar --}}
        <div class="bg-[#FAF7F2] border border-[#DDD5C7] p-6 md:p-8 rounded-[2px]">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-6 border-b border-[#DDD5C7]">
                <div>
                    <span class="text-xs font-mono uppercase tracking-widest text-[#57574E] block mb-1">
                        Pencatatan Kehadiran Karyawan
                    </span>
                    <h1 class="text-2xl md:text-3xl font-extrabold text-[#1B1B18] uppercase tracking-tight">
                        @if ($sudahPulang)
                            Kehadiran Hari Ini Lengkap
                        @elseif ($siapPulang)
                            Absen Pulang
                        @else
                            Absen Masuk
                        @endif
                    </h1>
                    <p class="text-xs text-[#57574E] mt-1">
                        Waktu server Jakarta (WIB) berlaku mutlak sebagai penentu status waktu kehadiran.
                    </p>
                </div>

                {{-- Batas Jam Masuk --}}
                <div class="bg-[#F4EFE6] border border-[#DDD5C7] px-4 py-3 rounded-[2px] md:text-right">
                    <span class="text-[10px] font-mono uppercase tracking-wider text-[#57574E] block">
                        Batas Jam Masuk Hari Ini
                    </span>
                    <span class="text-lg font-mono font-bold text-[#1F3D2B]">
                        {{ $batasJamFormat }}
                    </span>
                </div>
            </div>

            {{-- Jam Digital Besar Real-time --}}
            <div class="pt-6 flex flex-col sm:flex-row sm:items-baseline justify-between gap-3">
                <div>
                    <div id="liveClockMain" class="font-mono text-4xl sm:text-6xl md:text-7xl font-bold tracking-tight text-[#1F3D2B]">
                        --:--:-- WIB
                    </div>
                    <div id="liveDateMain" class="text-xs md:text-sm font-mono text-[#57574E] mt-1">
                        {{ $tanggalLabel }}
                    </div>
                </div>

                @if ($sudahPulang)
                    <div class="inline-flex items-center space-x-2">
                        <span class="w-2.5 h-2.5 bg-[#1F3D2B] rounded-full inline-block"></span>
                        <span class="text-xs font-mono font-semibold uppercase tracking-wider text-[#1F3D2B]">
                            Absen Masuk dan Pulang Lengkap
                        </span>
                    </div>
                @elseif ($siapPulang)
                    <div class="inline-flex items-center space-x-2">
                        <span class="w-2.5 h-2.5 bg-[#1F3D2B] rounded-full inline-block animate-pulse"></span>
                        <span class="text-xs font-mono font-semibold uppercase tracking-wider text-[#1F3D2B]">
                            Sudah Masuk &bull; Silakan Absen Pulang
                        </span>
                    </div>
                @else
                    <div class="inline-flex items-center space-x-2">
                        <span class="w-2.5 h-2.5 bg-[#C2542D] rounded-full inline-block animate-pulse"></span>
                        <span class="text-xs font-mono uppercase tracking-wider text-[#C2542D]">
                            Belum absen hari ini.
                        </span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Ringkasan Absen Masuk (Tampil jika sudah masuk) --}}
        @if ($sudahMasuk)
            <x-card title="Ringkasan Kehadiran Hari Ini" subtitle="Data absensi Anda tersimpan resmi dalam sistem kantor.">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-start">
                    {{-- Detail Kehadiran --}}
                    <div class="md:col-span-7 space-y-4">
                        <div class="border border-[#DDD5C7] bg-[#F4EFE6] rounded-[2px] p-4 divide-y divide-[#DDD5C7]">
                            <div class="flex justify-between py-2 text-xs">
                                <span class="font-mono text-[#57574E] uppercase">Nama Karyawan</span>
                                <span class="font-medium text-[#1B1B18]">{{ auth()->user()->name }}</span>
                            </div>
                            <div class="flex justify-between py-2 text-xs">
                                <span class="font-mono text-[#57574E] uppercase">Tanggal</span>
                                <span class="font-mono text-[#1B1B18]">{{ $absensiHariIni->tanggal->translatedFormat('d F Y') }}</span>
                            </div>
                            <div class="flex justify-between py-2 text-xs">
                                <span class="font-mono text-[#57574E] uppercase">Jam Masuk</span>
                                <span class="font-mono font-bold text-[#1B1B18]">{{ substr($absensiHariIni->jam_masuk, 0, 5) }} WIB</span>
                            </div>
                            <div class="flex justify-between py-2 text-xs items-center">
                                <span class="font-mono text-[#57574E] uppercase">Status Masuk</span>
                                <div>
                                    <x-badge-status :status="$absensiHariIni->status" />
                                </div>
                            </div>
                            <div class="flex justify-between py-2 text-xs">
                                <span class="font-mono text-[#57574E] uppercase">Keterlambatan</span>
                                <span class="font-mono font-medium {{ $absensiHariIni->status->isTelat() ? 'text-[#C2542D]' : 'text-[#1F3D2B]' }}">
                                    {{ $absensiHariIni->keterlambatan_format }}
                                </span>
                            </div>
                            <div class="flex justify-between py-2 text-xs items-center">
                                <span class="font-mono text-[#57574E] uppercase">Jam Pulang</span>
                                <span class="font-mono font-bold {{ $sudahPulang ? 'text-[#1F3D2B]' : 'text-[#57574E]' }}">
                                    @if ($sudahPulang)
                                        {{ substr($absensiHariIni->jam_pulang, 0, 5) }} WIB
                                    @else
                                        Belum Absen Pulang
                                    @endif
                                </span>
                            </div>
                        </div>

                        <div class="text-xs font-mono text-[#57574E] flex items-center space-x-3 pt-1">
                            <a href="{{ route('rekap.index') }}" class="underline hover:text-[#1B1B18] uppercase">
                                Buka Rekap Absensi &rarr;
                            </a>
                        </div>
                    </div>

                    {{-- Foto Bukti --}}
                    <div class="md:col-span-5 grid grid-cols-1 sm:grid-cols-2 gap-3">
                        {{-- Foto Masuk --}}
                        <div class="border border-[#DDD5C7] bg-[#F4EFE6] p-2.5 rounded-[2px]">
                            <span class="block text-[10px] font-mono uppercase tracking-wider text-[#57574E] mb-1.5 font-medium">
                                Foto Absen Masuk
                            </span>
                            <div class="aspect-4/3 w-full bg-[#1B1B18] overflow-hidden rounded-[2px] border border-[#DDD5C7]">
                                <img src="{{ $absensiHariIni->foto_url }}"
                                     alt="Foto Masuk {{ auth()->user()->name }}"
                                     class="w-full h-full object-cover">
                            </div>
                            <span class="block text-[9px] font-mono text-[#57574E] mt-1 text-center">
                                Masuk: {{ substr($absensiHariIni->jam_masuk, 0, 5) }} WIB
                            </span>
                        </div>

                        {{-- Foto Pulang --}}
                        <div class="border border-[#DDD5C7] bg-[#F4EFE6] p-2.5 rounded-[2px]">
                            <span class="block text-[10px] font-mono uppercase tracking-wider text-[#57574E] mb-1.5 font-medium">
                                Foto Absen Pulang
                            </span>
                            @if ($sudahPulang && $absensiHariIni->foto_pulang)
                                <div class="aspect-4/3 w-full bg-[#1B1B18] overflow-hidden rounded-[2px] border border-[#DDD5C7]">
                                    <img src="{{ $absensiHariIni->foto_pulang_url }}"
                                         alt="Foto Pulang {{ auth()->user()->name }}"
                                         class="w-full h-full object-cover">
                                </div>
                                <span class="block text-[9px] font-mono text-[#1F3D2B] mt-1 text-center font-medium">
                                    Pulang: {{ substr($absensiHariIni->jam_pulang, 0, 5) }} WIB
                                </span>
                            @else
                                <div class="aspect-4/3 w-full bg-[#ECE5DA] border border-dashed border-[#DDD5C7] rounded-[2px] flex items-center justify-center p-3 text-center">
                                    <span class="text-[10px] font-mono text-[#57574E]">
                                        Menunggu pengambilan foto pulang
                                    </span>
                                </div>
                                <span class="block text-[9px] font-mono text-[#57574E] mt-1 text-center">
                                    Belum tersimpan
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </x-card>
        @endif

        {{-- Form Kamera (Tampil jika belum masuk ATAU siap pulang) --}}
        @if (! $sudahPulang)
            @php
                $isActionPulang = $siapPulang;
                $cardTitle = $isActionPulang ? 'Verifikasi Kamera Absen Pulang' : 'Verifikasi Kamera Absen Masuk';
                $cardSubtitle = $isActionPulang
                    ? 'Ambil potret wajah langsung menggunakan kamera sebelum meninggalkan kantor.'
                    : 'Ambil potret wajah langsung menggunakan kamera perangkat Anda. Foto wajib diambil sebelum absensi dapat disimpan.';
                $formAction = $isActionPulang ? route('absensi.pulang') : route('absensi.store');
                $buttonLabel = $isActionPulang ? 'Simpan Absen Pulang' : 'Simpan Absen Masuk';
            @endphp

            <x-card :title="$cardTitle" :subtitle="$cardSubtitle">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                    {{-- Area Tampilan Kamera / Preview Foto --}}
                    <div class="md:col-span-7">
                        <div class="relative bg-[#1B1B18] border border-[#DDD5C7] rounded-[2px] overflow-hidden aspect-4/3 flex items-center justify-center">
                            {{-- Video Streaming Kamera --}}
                            <video id="videoElement"
                                   autoplay
                                   playsinline
                                   muted
                                   class="w-full h-full object-cover"></video>

                            {{-- Canvas Tersembunyi untuk Snapshot Frame --}}
                            <canvas id="photoCanvas" class="hidden"></canvas>

                            {{-- Preview Gambar Setelah Diambil --}}
                            <img id="photoResult"
                                 alt="Hasil Foto Absensi"
                                 class="hidden w-full h-full object-cover">

                            {{-- Pesan Peringatan Error Kamera --}}
                            <div id="cameraErrorBox" class="hidden absolute inset-0 bg-[#1B1B18] text-[#FAF7F2] p-6 flex flex-col justify-center items-center text-center">
                                <span class="font-mono text-xs text-[#C2542D] uppercase tracking-wider font-semibold mb-2">
                                    Akses Kamera Terkendala
                                </span>
                                <p id="cameraErrorMessage" class="text-xs text-[#CFDACF] max-w-sm leading-relaxed">
                                    Memeriksa izin kamera...
                                </p>
                                <button type="button"
                                        id="btnRetryCamera"
                                        class="mt-4 px-3 py-1.5 text-xs font-mono uppercase border border-[#CFDACF] text-[#FAF7F2] hover:bg-[#2D543B] transition rounded-[2px]">
                                    Coba Lagi
                                </button>
                            </div>

                            {{-- Indikator Status Kamera --}}
                            <div id="cameraStatusBadge" class="absolute top-3 left-3 px-2 py-0.5 bg-[#1B1B18]/80 border border-[#DDD5C7]/30 text-[10px] font-mono uppercase tracking-widest text-[#FAF7F2] rounded-[2px]">
                                Kamera Siap
                            </div>
                        </div>

                        {{-- Tombol Kontrol Kamera --}}
                        <div class="mt-4 flex flex-wrap gap-2">
                            <x-button type="button" id="btnCapture" variant="primary" class="w-full sm:w-auto">
                                Ambil Foto
                            </x-button>

                            <x-button type="button" id="btnRetake" variant="secondary" class="hidden w-full sm:w-auto">
                                Foto Ulang
                            </x-button>
                        </div>
                    </div>

                    {{-- Panel Konfirmasi & Tombol Kirim --}}
                    <div class="md:col-span-5 flex flex-col justify-between border-t md:border-t-0 md:border-l border-[#DDD5C7] pt-6 md:pt-0 md:pl-6">
                        <div class="space-y-4">
                            <span class="text-xs font-mono uppercase tracking-wider text-[#57574E] block">
                                Ketentuan {{ $isActionPulang ? 'Absen Pulang' : 'Absen Masuk' }}
                            </span>

                            <div class="space-y-2 text-xs text-[#57574E] leading-relaxed">
                                <div class="flex items-start space-x-2">
                                    <span class="font-mono text-[#1F3D2B] font-bold">1.</span>
                                    <span>Wajah harus terlihat jelas menghadap kamera saat pengambilan foto.</span>
                                </div>
                                <div class="flex items-start space-x-2">
                                    <span class="font-mono text-[#1F3D2B] font-bold">2.</span>
                                    @if ($isActionPulang)
                                        <span>Absen pulang mencatat jam kepulangan resmi hari ini secara permanen.</span>
                                    @else
                                        <span>Satu karyawan hanya dapat melakukan absensi masuk 1 kali per hari.</span>
                                    @endif
                                </div>
                                <div class="flex items-start space-x-2">
                                    <span class="font-mono text-[#1F3D2B] font-bold">3.</span>
                                    <span>Waktu yang dicatat menggunakan waktu server Jakarta (WIB).</span>
                                </div>
                            </div>

                            <div id="photoStatusNotice" class="p-3 bg-[#F4EFE6] border border-[#DDD5C7] rounded-[2px] text-xs font-mono text-[#57574E]">
                                Foto belum diambil. Klik tombol "Ambil Foto" di samping.
                            </div>
                        </div>

                        {{-- Formulir Kirim Absensi --}}
                        <form id="attendanceForm" method="POST" action="{{ $formAction }}" enctype="multipart/form-data" class="mt-6">
                            @csrf
                            <input type="file" id="fotoFileInput" name="foto" class="hidden" accept="image/jpeg,image/png">
                            <input type="hidden" id="fotoBase64Input" name="foto_base64">

                            <x-button type="submit" id="btnSubmitAttendance" variant="{{ $isActionPulang ? 'primary' : 'accent' }}" class="w-full py-3" disabled>
                                {{ $buttonLabel }}
                            </x-button>
                            <span class="block text-[10px] font-mono text-center text-[#57574E] mt-2">
                                Tombol aktif otomatis setelah foto berhasil diambil.
                            </span>
                        </form>
                    </div>
                </div>
            </x-card>
        @endif
    </div>

    @push('scripts')
    <script>
        (function() {
            // 1. Script Jam Real-Time Berjalan (WIB)
            function updateClock() {
                const now = new Date();
                const options = {
                    timeZone: 'Asia/Jakarta',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: false
                };
                const formatter = new Intl.DateTimeFormat('id-ID', options);
                const clockEl = document.getElementById('liveClockMain');
                if (clockEl) {
                    clockEl.textContent = formatter.format(now) + ' WIB';
                }
            }
            updateClock();
            setInterval(updateClock, 1000);

            // 2. Logika Kamera & Pengambilan Foto (Hanya jika form kamera aktif)
            const video = document.getElementById('videoElement');
            if (!video) return; // Form kamera tidak aktif (sudah lengkap masuk & pulang)

            const canvas = document.getElementById('photoCanvas');
            const photoResult = document.getElementById('photoResult');
            const btnCapture = document.getElementById('btnCapture');
            const btnRetake = document.getElementById('btnRetake');
            const btnSubmit = document.getElementById('btnSubmitAttendance');
            const fotoFileInput = document.getElementById('fotoFileInput');
            const fotoBase64Input = document.getElementById('fotoBase64Input');
            const statusNotice = document.getElementById('photoStatusNotice');
            const cameraStatusBadge = document.getElementById('cameraStatusBadge');
            const cameraErrorBox = document.getElementById('cameraErrorBox');
            const cameraErrorMessage = document.getElementById('cameraErrorMessage');
            const btnRetryCamera = document.getElementById('btnRetryCamera');

            let mediaStream = null;

            async function startCamera() {
                hideCameraError();
                cameraStatusBadge.textContent = 'Menghubungkan Kamera...';

                if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                    showCameraError('Peramban ini tidak mendukung akses kamera secara langsung, atau Anda sedang tidak membukanya melalui localhost/HTTPS.');
                    return;
                }

                try {
                    mediaStream = await navigator.mediaDevices.getUserMedia({
                        video: {
                            facingMode: 'user',
                            width: { ideal: 1280 },
                            height: { ideal: 960 }
                        },
                        audio: false
                    });
                    video.srcObject = mediaStream;
                    await video.play();
                    cameraStatusBadge.textContent = 'Kamera Aktif';
                    btnCapture.disabled = false;
                } catch (err) {
                    console.error('Error kamera:', err);
                    let pesan = 'Kamera tidak dapat diakses.';
                    if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
                        pesan = 'Izin akses kamera ditolak. Silakan klik ikon gembok atau kamera di bilah alamat peramban Anda untuk memberikan izin.';
                    } else if (err.name === 'NotFoundError' || err.name === 'DevicesNotFoundError') {
                        pesan = 'Tidak ditemukan perangkat kamera pada komputer atau ponsel Anda.';
                    } else if (err.name === 'NotReadableError' || err.name === 'TrackStartError') {
                        pesan = 'Kamera sedang digunakan oleh aplikasi lain. Tutup aplikasi tersebut dan coba lagi.';
                    } else {
                        pesan = 'Gagal mengakses kamera: ' + (err.message || 'Pastikan dibuka melalui localhost atau protokol HTTPS.');
                    }
                    showCameraError(pesan);
                }
            }

            function showCameraError(msg) {
                cameraErrorMessage.textContent = msg;
                cameraErrorBox.classList.remove('hidden');
                cameraStatusBadge.textContent = 'Kamera Mati';
                btnCapture.disabled = true;
            }

            function hideCameraError() {
                cameraErrorBox.classList.add('hidden');
            }

            if (btnRetryCamera) {
                btnRetryCamera.addEventListener('click', startCamera);
            }

            startCamera();

            btnCapture.addEventListener('click', function() {
                if (!video.videoWidth || !video.videoHeight) {
                    alert('Video kamera belum siap. Silakan tunggu 1-2 detik.');
                    return;
                }

                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

                const dataUrl = canvas.toDataURL('image/jpeg', 0.88);

                photoResult.src = dataUrl;
                photoResult.classList.remove('hidden');
                video.classList.add('hidden');

                fotoBase64Input.value = dataUrl;

                canvas.toBlob(function(blob) {
                    if (blob) {
                        try {
                            const file = new File([blob], 'foto_absen_' + Date.now() + '.jpg', { type: 'image/jpeg' });
                            const dataTransfer = new DataTransfer();
                            dataTransfer.items.add(file);
                            fotoFileInput.files = dataTransfer.files;
                        } catch (e) {
                            console.warn('DataTransfer fallback ke base64:', e);
                        }
                    }
                }, 'image/jpeg', 0.88);

                btnCapture.classList.add('hidden');
                btnRetake.classList.remove('hidden');
                btnSubmit.disabled = false;
                cameraStatusBadge.textContent = 'Foto Siap Disimpan';

                statusNotice.className = 'p-3 bg-[#E5EEE8] border border-[#1F3D2B] rounded-[2px] text-xs font-mono text-[#1F3D2B]';
                statusNotice.textContent = 'Foto wajah telah berhasil ditangkap. Klik tombol di bawah untuk menyimpan absensi.';
            });

            btnRetake.addEventListener('click', function() {
                photoResult.classList.add('hidden');
                video.classList.remove('hidden');
                fotoFileInput.value = '';
                fotoBase64Input.value = '';

                btnCapture.classList.remove('hidden');
                btnRetake.classList.add('hidden');
                btnSubmit.disabled = true;
                cameraStatusBadge.textContent = 'Kamera Aktif';

                statusNotice.className = 'p-3 bg-[#F4EFE6] border border-[#DDD5C7] rounded-[2px] text-xs font-mono text-[#57574E]';
                statusNotice.textContent = 'Foto dibatalkan. Silakan ambil foto kembali.';
            });

            window.addEventListener('beforeunload', function() {
                if (mediaStream) {
                    mediaStream.getTracks().forEach(track => track.stop());
                }
            });
        })();
    </script>
    @endpush
</x-layouts.app>
