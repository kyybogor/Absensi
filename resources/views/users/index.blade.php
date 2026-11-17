<x-layouts.app title="Tambah Akun">
    <div class="space-y-8">
        {{-- Header Halaman --}}
        <div class="pb-4 border-b border-[#DDD5C7]">
            <span class="text-xs font-mono uppercase tracking-widest text-[#57574E] block mb-1">
                Administrasi Pengguna
            </span>
            <h1 class="text-2xl md:text-3xl font-extrabold text-[#1B1B18] uppercase tracking-tight">
                Tambah Akun & Daftar Karyawan
            </h1>
            <p class="text-xs text-[#57574E] mt-1">
                Daftarkan akun baru ke dalam sistem absensi dan kelola akses karyawan maupun administrator.
            </p>
        </div>

        {{-- Pesan Status Berhasil Realtime (AJAX) --}}
        <div id="realtimeAlertContainer" class="hidden p-3.5 bg-[#E5EEE8] border border-[#1F3D2B] text-[#1F3D2B] text-xs font-mono rounded-[2px] flex items-center justify-between shadow-2xs">
            <div class="flex items-center space-x-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0 text-[#1F3D2B]" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span id="realtimeAlertMessage"></span>
            </div>
            <button type="button"
                    onclick="document.getElementById('realtimeAlertContainer').classList.add('hidden')"
                    class="text-xs font-mono text-[#57574E] hover:text-[#1B1B18] cursor-pointer">
                ✕
            </button>
        </div>

        <div class="flex flex-col lg:flex-row gap-6 items-start">
            {{-- Kolom Kiri: Formulir Pendaftaran Akun Baru (Lebar penuh di mobile, 340px di desktop) --}}
            <div class="w-full lg:w-[340px] lg:shrink-0">
                <x-card title="Formulir Tambah Akun" subtitle="Username, peran, dan kata sandi wajib diisi. Email bersifat opsional. Kata sandi otomatis dienkripsi dengan bcrypt.">
                    <form method="POST" action="{{ route('users.store') }}" class="space-y-4">
                        @csrf

                        {{-- Username --}}
                        <div>
                            <label for="username" class="block font-mono text-xs uppercase tracking-wider text-[#1B1B18] mb-1 font-medium">
                                Username
                            </label>
                            <x-input
                                id="username"
                                name="username"
                                type="text"
                                value="{{ old('username') }}"
                                placeholder="Contoh: ahmad_fauzi"
                                required
                                :error="$errors->has('username')"
                            />
                            @error('username')
                                <p class="text-[11px] font-mono text-[#C2542D] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Alamat Email (Opsional) --}}
                        <div>
                            <label for="email" class="block font-mono text-xs uppercase tracking-wider text-[#1B1B18] mb-1 font-medium flex items-center justify-between">
                                <span>Alamat Email</span>
                                <span class="text-[10px] text-[#57574E] font-normal normal-case font-mono">(Opsional)</span>
                            </label>
                            <x-input
                                id="email"
                                name="email"
                                type="email"
                                value="{{ old('email') }}"
                                placeholder="ahmad@kantor.test (opsional)"
                                :error="$errors->has('email')"
                            />
                            @error('email')
                                <p class="text-[11px] font-mono text-[#C2542D] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Peran Akun --}}
                        <div>
                            <label for="role" class="block font-mono text-xs uppercase tracking-wider text-[#1B1B18] mb-1 font-medium">
                                Peran Akun (Role)
                            </label>
                            <select id="role"
                                    name="role"
                                    class="w-full bg-[#FAF7F2] border {{ $errors->has('role') ? 'border-[#C2542D]' : 'border-[#DDD5C7]' }} text-[#1B1B18] text-sm px-3 py-2 rounded-[2px] transition duration-150 focus:outline-none focus:ring-1 focus:ring-[#1F3D2B] focus:border-[#1F3D2B]">
                                @foreach ($roles as $roleOption)
                                    <option value="{{ $roleOption->value }}" {{ old('role', 'karyawan') === $roleOption->value ? 'selected' : '' }}>
                                        {{ $roleOption->label() }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role')
                                <p class="text-[11px] font-mono text-[#C2542D] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Kata Sandi --}}
                        <div>
                            <label for="password" class="block font-mono text-xs uppercase tracking-wider text-[#1B1B18] mb-1 font-medium">
                                Kata Sandi (Minimal 8 Karakter)
                            </label>
                            <x-input
                                id="password"
                                name="password"
                                type="password"
                                placeholder="••••••••"
                                required
                                :error="$errors->has('password')"
                            />
                            @error('password')
                                <p class="text-[11px] font-mono text-[#C2542D] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Konfirmasi Kata Sandi --}}
                        <div>
                            <label for="password_confirmation" class="block font-mono text-xs uppercase tracking-wider text-[#1B1B18] mb-1 font-medium">
                                Ulangi Kata Sandi
                            </label>
                            <x-input
                                id="password_confirmation"
                                name="password_confirmation"
                                type="password"
                                placeholder="••••••••"
                                required
                            />
                        </div>

                        <div class="pt-2">
                            <x-button type="submit" variant="primary" class="w-full py-2.5">
                                Simpan Akun Baru
                            </x-button>
                        </div>
                    </form>
                </x-card>
            </div>

            {{-- Kolom Kanan: Daftar Akun Terdaftar (Responsif penuh) --}}
            <div class="w-full flex-1 min-w-0">
                <div class="bg-[#FAF7F2] border border-[#DDD5C7] rounded-[2px] overflow-hidden">
                    <div class="p-4 border-b border-[#DDD5C7] bg-[#ECE5DA]">
                        <h2 class="text-xs font-mono font-bold uppercase tracking-wider text-[#1B1B18]">
                            Daftar Akun Terdaftar ({{ $users->total() }})
                        </h2>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-[#DDD5C7] bg-[#F4EFE6] text-[11px] font-mono uppercase tracking-wider text-[#57574E]">
                                    <th class="py-2.5 px-3 sm:px-4 font-semibold">Username</th>
                                    <th class="hidden md:table-cell py-2.5 px-4 font-semibold">Email</th>
                                    <th class="py-2.5 px-2.5 sm:px-4 font-semibold">Peran</th>
                                    <th class="hidden sm:table-cell py-2.5 px-4 font-semibold">Terdaftar</th>
                                    <th class="py-2.5 px-3 sm:px-4 font-semibold text-right" style="min-width: 130px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#DDD5C7] text-xs">
                                @forelse ($users as $user)
                                    <tr class="hover:bg-[#F4EFE6] transition duration-75">
                                        <td class="py-3 px-3 sm:px-4">
                                            <div class="font-medium text-[#1B1B18] leading-tight">
                                                {{ $user->username ?? $user->name }}
                                                @if ($user->id === auth()->id())
                                                    <span class="text-[10px] font-mono text-[#1F3D2B] ml-1 font-semibold">(Anda)</span>
                                                @endif
                                            </div>
                                            @if ($user->email)
                                                <div class="md:hidden text-[11px] font-mono text-[#57574E] mt-0.5 truncate max-w-[140px] sm:max-w-none">
                                                    {{ $user->email }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="hidden md:table-cell py-3 px-4 font-mono text-[#57574E] truncate max-w-[200px]">
                                            {{ $user->email ?? '—' }}
                                        </td>
                                        <td class="py-3 px-2.5 sm:px-4 whitespace-nowrap">
                                            @if ($user->role === \App\Enums\Role::Admin)
                                                <span class="inline-block px-1.5 py-0.5 text-[10px] font-mono font-semibold uppercase tracking-wider border border-[#1F3D2B] text-[#1F3D2B] bg-[#E5EEE8] rounded-[2px]">
                                                    Admin
                                                </span>
                                            @else
                                                <span class="inline-block px-1.5 py-0.5 text-[10px] font-mono uppercase tracking-wider border border-[#DDD5C7] text-[#57574E] bg-[#FAF7F2] rounded-[2px]">
                                                    Karyawan
                                                </span>
                                            @endif
                                        </td>
                                        <td class="hidden sm:table-cell py-3 px-4 font-mono text-[11px] text-[#57574E] whitespace-nowrap">
                                            {{ $user->created_at?->translatedFormat('d/m/Y') ?? '-' }}
                                        </td>
                                        <td class="py-3 px-3 sm:px-4 text-right whitespace-nowrap">
                                            <button type="button"
                                                    onclick="openChangePasswordModal('{{ $user->id }}', '{{ addslashes($user->username ?? $user->name) }}')"
                                                    class="inline-flex items-center justify-center space-x-1.5 px-2.5 py-1.5 sm:px-3 text-[11px] font-mono font-semibold uppercase tracking-wider text-[#1F3D2B] bg-[#E5EEE8] hover:bg-[#1F3D2B] hover:text-[#FAF7F2] border border-[#1F3D2B] rounded-[2px] transition duration-150 cursor-pointer whitespace-nowrap shadow-2xs"
                                                    title="Ganti kata sandi untuk {{ $user->username ?? $user->name }}">
                                                <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <circle cx="7.5" cy="15.5" r="5.5"></circle>
                                                    <path d="m21 2-9.6 9.6"></path>
                                                    <path d="m15.5 7.5 3 3L22 7l-3-3"></path>
                                                </svg>
                                                <span>Ganti Sandi</span>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-6 px-4 text-center font-mono text-xs text-[#57574E]">
                                            Belum ada data akun pengguna.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($users->hasPages())
                        <div class="p-4 border-t border-[#DDD5C7]">
                            {{ $users->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Ganti Password di File Terpisah --}}
    @include('users.change-password-modal')

    @push('scripts')
    <script>
        function showToastNotification(message) {
            const container = document.getElementById('realtimeAlertContainer');
            const text = document.getElementById('realtimeAlertMessage');
            if (container && text) {
                text.textContent = message;
                container.classList.remove('hidden');
                window.scrollTo({ top: 0, behavior: 'smooth' });
                setTimeout(() => {
                    container.classList.add('hidden');
                }, 6000);
            }
        }
    </script>
    @endpush
</x-layouts.app>
