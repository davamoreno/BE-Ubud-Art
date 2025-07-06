<?php
// File: app/Policies/TokoPolicy.php

namespace App\Policies;

use App\Models\Toko;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TokoPolicy
{
    /**
     * Bolehkan user/tamu melihat daftar toko?
     */
    public function viewAny(?User $user): bool
    {
        // Umumnya daftar toko bersifat publik
        return true;
    }

    /**
     * Bolehkan user/tamu melihat detail satu toko?
     */
    public function view(?User $user, Toko $toko): bool
    {
        // Umumnya detail toko bersifat publik
        return true;
    }

    /**
     * Bolehkan user membuat toko? (Biasanya Admin)
     */
    public function create(User $user): Response
    {
        return $user->can('create-toko')
            ? Response::allow()
            : Response::deny('Anda tidak memiliki izin untuk membuat toko.');
    }

    /**
     * Bolehkan user mengupdate toko? (Biasanya Admin)
     */
    public function update(User $user, Toko $toko): Response
    {
        return $user->can('update-toko')
            ? Response::allow()
            : Response::deny('Anda tidak memiliki izin untuk mengubah toko.');
    }

    /**
     * Bolehkan user menghapus toko? (Biasanya Admin)
     */
    public function delete(User $user, Toko $toko): Response
    {
        return $user->can('delete-toko')
            ? Response::allow()
            : Response::deny('Anda tidak memiliki izin untuk menghapus toko.');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Toko $toko): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Toko $toko): bool
    {
        return false;
    }
}