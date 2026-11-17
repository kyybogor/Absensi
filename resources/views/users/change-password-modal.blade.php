@push('modals')
{{-- Modal Ganti Password Karyawan (Admin) - Styled with Inline Precision CSS --}}
<div id="changePasswordModal"
     style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; width: 100vw; height: 100vh; background-color: rgba(27, 27, 24, 0.78); backdrop-filter: blur(3px); -webkit-backdrop-filter: blur(3px); z-index: 999999; align-items: center; justify-content: center; padding: 16px; box-sizing: border-box;"
     onclick="closeChangePasswordModal(event)"
     aria-labelledby="modalTitle"
     role="dialog"
     aria-modal="true">
    
    <div style="background: #FAF7F2; border: 1px solid #DDD5C7; width: 100%; max-width: 460px; border-radius: 2px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); overflow: hidden; box-sizing: border-box; animation: modalFadeIn 0.18s ease-out;"
         onclick="event.stopPropagation()">
        
        {{-- Header Modal --}}
        <div style="background: #ECE5DA; border-bottom: 1px solid #DDD5C7; padding: 14px 20px; display: flex; align-items: center; justify-content: space-between;">
            <div>
                <span style="font-family: 'IBM Plex Mono', monospace; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; color: #57574E; display: block; margin-bottom: 2px;">
                    Administrasi Pengguna
                </span>
                <h3 id="modalTitle" style="font-family: 'Bricolage Grotesque', 'IBM Plex Sans', sans-serif; font-size: 16px; font-weight: 800; text-transform: uppercase; letter-spacing: -0.01em; color: #1B1B18; margin: 0;">
                    Ubah Kata Sandi Karyawan
                </h3>
            </div>
            <button type="button"
                    onclick="closeChangePasswordModal()"
                    style="border: 1px solid #DDD5C7; background: #FAF7F2; color: #57574E; font-family: 'IBM Plex Mono', monospace; font-size: 13px; width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center; border-radius: 2px; cursor: pointer; transition: all 0.15s ease;"
                    onmouseover="this.style.borderColor='#C2542D'; this.style.color='#C2542D'; this.style.background='#FBECE7';"
                    onmouseout="this.style.borderColor='#DDD5C7'; this.style.color='#57574E'; this.style.background='#FAF7F2';"
                    title="Tutup (Esc)">
                ✕
            </button>
        </div>

        {{-- Info User Target --}}
        <div style="background: #F4EFE6; border-bottom: 1px solid #DDD5C7; padding: 10px 20px; display: flex; align-items: center; gap: 8px; font-family: 'IBM Plex Mono', monospace; font-size: 11px;">
            <span style="color: #57574E; text-transform: uppercase; letter-spacing: 0.05em;">Target Akun:</span>
            <span id="targetUsernameDisplay" style="color: #1B1B18; font-weight: 700; background: #E5EEE8; border: 1px solid #1F3D2B; padding: 2px 8px; border-radius: 2px; letter-spacing: 0.02em;">
                -
            </span>
        </div>

        {{-- Isi Formulir --}}
        <form id="formChangePassword" onsubmit="submitChangePassword(event)" style="padding: 20px; margin: 0; display: flex; flex-direction: column; gap: 16px; box-sizing: border-box;">
            <input type="hidden" id="targetUserId" value="">

            {{-- Pesan Error Realtime --}}
            <div id="modalErrorContainer" style="display: none; padding: 10px 14px; background: #FBECE7; border: 1px solid #C2542D; color: #C2542D; font-family: 'IBM Plex Mono', monospace; font-size: 11px; border-radius: 2px;">
                <span style="font-weight: 700; display: block; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.05em;">Perhatian:</span>
                <ul id="modalErrorList" style="margin: 0; padding-left: 18px; line-height: 1.5;"></ul>
            </div>

            {{-- Pesan Berhasil Realtime --}}
            <div id="modalSuccessContainer" style="display: none; padding: 10px 14px; background: #E5EEE8; border: 1px solid #1F3D2B; color: #1F3D2B; font-family: 'IBM Plex Mono', monospace; font-size: 11px; border-radius: 2px; align-items: center; gap: 8px;">
                <svg style="width: 16px; height: 16px; flex-shrink: 0;" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span id="modalSuccessText">Kata sandi berhasil diperbarui.</span>
            </div>

            {{-- Input Kata Sandi Baru --}}
            <div>
                <label for="new_password" style="display: block; font-family: 'IBM Plex Mono', monospace; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #1B1B18; margin-bottom: 6px;">
                    Kata Sandi Baru (Minimal 8 Karakter)
                </label>
                <div style="position: relative; width: 100%; display: flex; align-items: center;">
                    <input
                        type="password"
                        id="new_password"
                        name="password"
                        required
                        minlength="8"
                        placeholder="••••••••"
                        autocomplete="new-password"
                        style="width: 100%; box-sizing: border-box; background: #FAF7F2; border: 1px solid #DDD5C7; color: #1B1B18; font-family: 'IBM Plex Sans', sans-serif; font-size: 13px; padding: 9px 70px 9px 12px; border-radius: 2px; outline: none; transition: border-color 0.15s ease;"
                        onfocus="this.style.borderColor='#1F3D2B'; this.style.boxShadow='0 0 0 1px #1F3D2B';"
                        onblur="this.style.borderColor='#DDD5C7'; this.style.boxShadow='none';"
                    />
                    <button
                        type="button"
                        onclick="togglePasswordVisibility('new_password')"
                        tabindex="-1"
                        style="position: absolute; right: 6px; top: 50%; transform: translateY(-50%); background: #ECE5DA; border: 1px solid #DDD5C7; color: #57574E; font-family: 'IBM Plex Mono', monospace; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; padding: 4px 8px; border-radius: 2px; cursor: pointer; user-select: none;"
                        onmouseover="this.style.borderColor='#1B1B18'; this.style.color='#1B1B18';"
                        onmouseout="this.style.borderColor='#DDD5C7'; this.style.color='#57574E';">
                        <span id="eye_new_password">Lihat</span>
                    </button>
                </div>
            </div>

            {{-- Input Konfirmasi Kata Sandi --}}
            <div>
                <label for="new_password_confirmation" style="display: block; font-family: 'IBM Plex Mono', monospace; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #1B1B18; margin-bottom: 6px;">
                    Ulangi Kata Sandi Baru
                </label>
                <div style="position: relative; width: 100%; display: flex; align-items: center;">
                    <input
                        type="password"
                        id="new_password_confirmation"
                        name="password_confirmation"
                        required
                        minlength="8"
                        placeholder="••••••••"
                        autocomplete="new-password"
                        style="width: 100%; box-sizing: border-box; background: #FAF7F2; border: 1px solid #DDD5C7; color: #1B1B18; font-family: 'IBM Plex Sans', sans-serif; font-size: 13px; padding: 9px 70px 9px 12px; border-radius: 2px; outline: none; transition: border-color 0.15s ease;"
                        onfocus="this.style.borderColor='#1F3D2B'; this.style.boxShadow='0 0 0 1px #1F3D2B';"
                        onblur="this.style.borderColor='#DDD5C7'; this.style.boxShadow='none';"
                    />
                    <button
                        type="button"
                        onclick="togglePasswordVisibility('new_password_confirmation')"
                        tabindex="-1"
                        style="position: absolute; right: 6px; top: 50%; transform: translateY(-50%); background: #ECE5DA; border: 1px solid #DDD5C7; color: #57574E; font-family: 'IBM Plex Mono', monospace; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; padding: 4px 8px; border-radius: 2px; cursor: pointer; user-select: none;"
                        onmouseover="this.style.borderColor='#1B1B18'; this.style.color='#1B1B18';"
                        onmouseout="this.style.borderColor='#DDD5C7'; this.style.color='#57574E';">
                        <span id="eye_new_password_confirmation">Lihat</span>
                    </button>
                </div>
            </div>

            {{-- Tombol Aksi --}}
            <div style="padding-top: 14px; border-top: 1px solid #DDD5C7; display: flex; align-items: center; justify-content: flex-end; gap: 8px;">
                <button type="button"
                        onclick="closeChangePasswordModal()"
                        style="font-family: 'IBM Plex Mono', monospace; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; padding: 8px 16px; border: 1px solid #DDD5C7; background: transparent; color: #57574E; border-radius: 2px; cursor: pointer; transition: all 0.15s ease;"
                        onmouseover="this.style.background='#ECE5DA'; this.style.color='#1B1B18';"
                        onmouseout="this.style.background='transparent'; this.style.color='#57574E';">
                    Batal
                </button>
                <button type="submit"
                        id="btnSubmitChangePassword"
                        style="font-family: 'IBM Plex Mono', monospace; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; padding: 8px 18px; border: 1px solid #1F3D2B; background: #1F3D2B; color: #FAF7F2; border-radius: 2px; cursor: pointer; transition: all 0.15s ease; display: inline-flex; align-items: center; justify-content: center; gap: 8px;"
                        onmouseover="if(!this.disabled) this.style.background='#162E20';"
                        onmouseout="if(!this.disabled) this.style.background='#1F3D2B';">
                    <svg id="spinnerChangePassword" style="display: none; animation: spin 1s linear infinite; width: 14px; height: 14px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path style="opacity: 0.75;" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                    <span id="btnTextChangePassword">Simpan Kata Sandi</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endpush

<style>
    @keyframes modalFadeIn {
        from {
            opacity: 0;
            transform: scale(0.97) translateY(-6px);
        }
        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
</style>

@push('scripts')
<script>
    // Pindahkan elemen modal langsung ke <body> agar terbebas dari overflow/transform layout parent
    function ensureModalInBody() {
        const modal = document.getElementById('changePasswordModal');
        if (modal && modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', ensureModalInBody);
    } else {
        ensureModalInBody();
    }

    function openChangePasswordModal(userId, username) {
        ensureModalInBody();

        document.getElementById('targetUserId').value = userId;
        document.getElementById('targetUsernameDisplay').textContent = username;

        // Reset formulir & pesan
        const form = document.getElementById('formChangePassword');
        form.reset();
        
        // Kembalikan tipe input ke password
        document.getElementById('new_password').type = 'password';
        document.getElementById('new_password_confirmation').type = 'password';
        document.getElementById('eye_new_password').textContent = 'Lihat';
        document.getElementById('eye_new_password_confirmation').textContent = 'Lihat';

        hideModalMessages();

        // Tampilkan modal full-screen
        const modal = document.getElementById('changePasswordModal');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';

        setTimeout(() => {
            const input = document.getElementById('new_password');
            if (input) input.focus();
        }, 80);
    }

    function closeChangePasswordModal(event) {
        const modal = document.getElementById('changePasswordModal');
        if (modal) {
            modal.style.display = 'none';
        }
        document.body.style.overflow = '';
        hideModalMessages();
    }

    function hideModalMessages() {
        const errorContainer = document.getElementById('modalErrorContainer');
        const successContainer = document.getElementById('modalSuccessContainer');
        if (errorContainer) errorContainer.style.display = 'none';
        if (successContainer) successContainer.style.display = 'none';
    }

    function togglePasswordVisibility(fieldId) {
        const input = document.getElementById(fieldId);
        const label = document.getElementById('eye_' + fieldId);
        if (input.type === 'password') {
            input.type = 'text';
            label.textContent = 'Tutup';
        } else {
            input.type = 'password';
            label.textContent = 'Lihat';
        }
    }

    async function submitChangePassword(event) {
        event.preventDefault();

        const userId = document.getElementById('targetUserId').value;
        const password = document.getElementById('new_password').value;
        const passwordConfirmation = document.getElementById('new_password_confirmation').value;
        const btn = document.getElementById('btnSubmitChangePassword');
        const spinner = document.getElementById('spinnerChangePassword');
        const btnText = document.getElementById('btnTextChangePassword');
        const errorContainer = document.getElementById('modalErrorContainer');
        const errorList = document.getElementById('modalErrorList');
        const successContainer = document.getElementById('modalSuccessContainer');
        const successText = document.getElementById('modalSuccessText');

        hideModalMessages();

        // Validasi frontend awal
        if (password.length < 8) {
            errorList.innerHTML = '<li>Kata sandi baru minimal 8 karakter.</li>';
            errorContainer.style.display = 'block';
            return;
        }

        if (password !== passwordConfirmation) {
            errorList.innerHTML = '<li>Konfirmasi kata sandi tidak cocok.</li>';
            errorContainer.style.display = 'block';
            return;
        }

        // Set state tombol loading
        btn.disabled = true;
        btn.style.opacity = '0.6';
        btn.style.cursor = 'not-allowed';
        spinner.style.display = 'inline-block';
        btnText.textContent = 'Menyimpan...';

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const response = await fetch(`/admin/users/${userId}/password`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || ''
                },
                body: JSON.stringify({
                    password: password,
                    password_confirmation: passwordConfirmation
                })
            });

            const data = await response.json();

            if (response.ok && data.success) {
                // Tampilkan pesan sukses di modal
                successText.textContent = data.message || 'Kata sandi berhasil diperbarui.';
                successContainer.style.display = 'flex';
                document.getElementById('formChangePassword').reset();

                // Munculkan notifikasi toast di halaman index
                if (typeof showToastNotification === 'function') {
                    showToastNotification(data.message);
                }

                // Tutup modal setelah jeda singkat
                setTimeout(() => {
                    closeChangePasswordModal();
                }, 1100);
            } else {
                // Tangani pesan error dari validasi server (422)
                let messages = [];
                if (data.errors) {
                    for (const key in data.errors) {
                        data.errors[key].forEach(msg => messages.push(msg));
                    }
                } else if (data.message) {
                    messages.push(data.message);
                } else {
                    messages.push('Terjadi kesalahan saat memperbarui kata sandi.');
                }

                errorList.innerHTML = messages.map(msg => `<li>${msg}</li>`).join('');
                errorContainer.style.display = 'block';
            }
        } catch (error) {
            errorList.innerHTML = '<li>Gagal menghubungi server. Periksa koneksi Anda dan coba lagi.</li>';
            errorContainer.style.display = 'block';
        } finally {
            btn.disabled = false;
            btn.style.opacity = '1';
            btn.style.cursor = 'pointer';
            spinner.style.display = 'none';
            btnText.textContent = 'Simpan Kata Sandi';
        }
    }

    // Listener tombol ESC global
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('changePasswordModal');
            if (modal && modal.style.display === 'flex') {
                closeChangePasswordModal();
            }
        }
    });
</script>
@endpush
