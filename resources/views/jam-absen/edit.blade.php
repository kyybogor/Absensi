<x-layouts.app title="Atur Jam Absen">
    <div class="max-w-2xl mx-auto space-y-6">
        {{-- Header Halaman --}}
        <div class="pb-4 border-b border-[#DDD5C7]">
            <span class="text-xs font-mono uppercase tracking-widest text-[#57574E] block mb-1">
                Konfigurasi Operasional
            </span>
            <h1 class="text-2xl md:text-3xl font-extrabold text-[#1B1B18] uppercase tracking-tight">
                Atur Batas Jam Masuk
            </h1>
            <p class="text-xs text-[#57574E] mt-1">
                Tentukan batas toleransi waktu kedatangan karyawan. Karyawan yang absen melampaui jam ini dinyatakan terlambat.
            </p>
        </div>

        {{-- Kartu Batas Jam Masuk Saat Ini --}}
        <div class="bg-[#FAF7F2] border border-[#DDD5C7] p-5 rounded-[2px] flex items-center justify-between">
            <div>
                <span class="text-[11px] font-mono uppercase tracking-wider text-[#57574E] block">
                    Batas Jam Masuk Saat Ini
                </span>
                <span class="text-3xl font-mono font-bold text-[#1F3D2B]">
                    {{ $batasFormat }}
                </span>
            </div>
            <span class="px-2.5 py-1 text-xs font-mono uppercase border border-[#1F3D2B] text-[#1F3D2B] bg-[#E5EEE8] rounded-[2px]">
                Status Aktif
            </span>
        </div>

        {{-- Formulir Pembaruan Jam Absen --}}
        <x-card title="Ubah Batas Waktu Masuk" subtitle="Perubahan jam masuk hanya berlaku untuk absensi berikutnya. Data absensi yang telah tersimpan tidak akan terpengaruh.">
            <form method="POST" action="{{ route('jam-absen.update') }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label for="jam_absen" class="block font-mono text-xs uppercase tracking-wider text-[#1B1B18] mb-1.5 font-medium">
                        Batas Jam Masuk Baru (Format 24 Jam)
                    </label>
                    <div class="max-w-xs">
                        <x-input
                            id="jam_absen"
                            name="jam_absen"
                            type="time"
                            step="60"
                            value="{{ old('jam_absen', $jamMenit) }}"
                            required
                            :error="$errors->has('jam_absen')"
                        />
                    </div>
                    <span class="block text-[11px] font-mono text-[#57574E] mt-1.5">
                        Contoh: Masukkan <code class="bg-[#ECE5DA] px-1 py-0.5 rounded-[2px]">08:00</code> untuk batas pukul delapan pagi WIB.
                    </span>
                    @error('jam_absen')
                        <p class="text-xs font-mono text-[#C2542D] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-2 flex items-center space-x-3">
                    <x-button type="submit" variant="primary">
                        Simpan Perubahan Batas Jam
                    </x-button>
                    <a href="{{ route('absensi.create') }}"
                       class="text-xs font-mono uppercase tracking-wider text-[#57574E] hover:text-[#1B1B18] px-3 py-2">
                        Batal
                    </a>
                </div>
            </form>
        </x-card>
    </div>
</x-layouts.app>
