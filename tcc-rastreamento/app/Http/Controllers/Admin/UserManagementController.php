<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::with('roles')
            ->orderBy('name')
            ->paginate(15);

        $availableRoles = ['enfermagem', 'cme', 'admin'];

        return view('admin.users.index', compact('users', 'availableRoles'));
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => ['required', Rule::in(['enfermagem', 'cme', 'admin'])],
        ]);

        $newRole = $request->role;

        // Impede o admin de tirar o próprio admin
        if (auth()->id() === $user->id && $newRole !== 'admin') {
            return back()->with('error', 'Você não pode remover seu próprio papel de administrador.');
        }

        // Evita ficar sem nenhum admin
        if ($user->hasRole('admin') && $newRole !== 'admin') {
            $adminCount = User::role('admin')->count();

            if ($adminCount <= 1) {
                return back()->with('error', 'O sistema precisa ter pelo menos um administrador.');
            }
        }

        $user->syncRoles([$newRole]);

        return back()->with('success', 'Papel atualizado com sucesso.');
    }
}
