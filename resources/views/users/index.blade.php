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

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            {{-- Kolom Kiri: Formulir Pendaftaran Akun Baru --}}
            <div class="lg:col-span-5">
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

            {{-- Kolom Kanan: Daftar Akun Terdaftar --}}
            <div class="lg:col-span-7">
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
                                    <th class="py-2.5 px-4 font-semibold">Username</th>
                                    <th class="py-2.5 px-4 font-semibold">Email</th>
                                    <th class="py-2.5 px-4 font-semibold">Peran</th>
                                    <th class="py-2.5 px-4 font-semibold">Terdaftar</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#DDD5C7] text-xs">
                                @forelse ($users as $user)
                                    <tr class="hover:bg-[#F4EFE6] transition duration-75">
                                        <td class="py-3 px-4 font-medium text-[#1B1B18]">
                                            {{ $user->username ?? $user->name }}
                                            @if ($user->id === auth()->id())
                                                <span class="text-[10px] font-mono text-[#1F3D2B] ml-1">(Anda)</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 font-mono text-[#57574E]">
                                            {{ $user->email ?? '—' }}
                                        </td>
                                        <td class="py-3 px-4">
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
                                        <td class="py-3 px-4 font-mono text-[11px] text-[#57574E]">
                                            {{ $user->created_at?->translatedFormat('d/m/Y') ?? '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-6 px-4 text-center font-mono text-xs text-[#57574E]">
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
</x-layouts.app>
