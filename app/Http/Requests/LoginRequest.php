<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if (! $this->filled('login')) {
            if ($this->filled('username')) {
                $this->merge(['login' => $this->input('username')]);
            } elseif ($this->filled('email')) {
                $this->merge(['login' => $this->input('email')]);
            }
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Pesan validasi berbahasa Indonesia.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'login.required' => 'Username atau email wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ];
    }

    /**
     * Autentikasi kredensial pengguna dengan bcrypt dan proteksi rate limiting.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $login = (string) $this->input('login');

        $user = User::query()
            ->where(function ($query) use ($login) {
                $query->whereRaw('LOWER(username) = ?', [Str::lower($login)])
                    ->orWhereRaw('LOWER(email) = ?', [Str::lower($login)]);
            })
            ->first();

        // Verifikasi password memakai Hash::check (bcrypt) bawaan Laravel
        if (! $user || ! Hash::check($this->input('password'), $user->password)) {
            RateLimiter::hit($this->throttleKey(), 60);

            throw ValidationException::withMessages([
                'login' => 'Username, email, atau kata sandi yang Anda masukkan salah.',
                'email' => 'Username, email, atau kata sandi yang Anda masukkan salah.',
            ]);
        }

        Auth::login($user, $this->boolean('remember'));
        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Pastikan request login belum melebihi batas 5 percobaan per menit.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());
        $message = sprintf('Terlalu banyak percobaan login. Silakan coba kembali dalam %d detik.', $seconds);

        throw ValidationException::withMessages([
            'login' => $message,
            'email' => $message,
        ]);
    }

    /**
     * Kunci throttle untuk rate limiter per username/email dan IP.
     */
    public function throttleKey(): string
    {
        $identifier = $this->input('login') ?? $this->input('email') ?? $this->input('username') ?? '';

        return Str::transliterate(Str::lower((string) $identifier).'|'.$this->ip());
    }
}
