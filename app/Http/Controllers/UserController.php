<?php

namespace App\Http\Controllers;

use App\Actions\SimpanUserAction;
use App\Enums\Role;
use App\Http\Requests\SimpanUserRequest;
use App\Http\Requests\UpdateUserPasswordRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Tampilkan formulir tambah akun dan daftar akun terdaftar.
     */
    public function index(): View
    {
        $users = User::query()
            ->latest()
            ->paginate(15);

        return view('users.index', [
            'users' => $users,
            'roles' => Role::cases(),
        ]);
    }

    /**
     * Simpan akun baru ke database.
     */
    public function store(SimpanUserRequest $request, SimpanUserAction $action): RedirectResponse
    {
        $user = $action->execute($request->validated());

        return redirect()->route('users.index')
            ->with('status_sukses', "Akun '{$user->username}' berhasil ditambahkan.");
    }

    /**
     * Perbarui kata sandi akun pengguna melalui permintaan API / AJAX.
     */
    public function updatePassword(UpdateUserPasswordRequest $request, User $user): JsonResponse
    {
        $user->update([
            'password' => Hash::make($request->validated('password')),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Kata sandi untuk akun '{$user->username}' berhasil diperbarui.",
        ]);
    }
}
