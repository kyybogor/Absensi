<?php

namespace App\Http\Controllers;

use App\Actions\SimpanUserAction;
use App\Enums\Role;
use App\Http\Requests\SimpanUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
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
}
