<?php

namespace App\Actions;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SimpanUserAction
{
    /**
     * Buat user baru dengan password yang di-hash menggunakan bcrypt.
     *
     * @param  array{username: string, name?: ?string, email?: ?string, password: string, role: Role|string}  $data
     */
    public function execute(array $data): User
    {
        return User::create([
            'name' => $data['name'] ?? $data['username'],
            'username' => $data['username'],
            'email' => $data['email'] ?? null,
            'password' => Hash::make($data['password']),
            'role' => $data['role'] instanceof Role ? $data['role'] : Role::from($data['role']),
        ]);
    }
}
