<?php
// File: app/Policies/ProdukPolicy.php

namespace App\Policies;

use App\Models\Produk;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProdukPolicy
{
    use HandlesAuthorization;
    /**
     * Bolehkan user/tamu melihat daftar produk?
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Bolehkan user/tamu melihat detail satu produk?
     */
    public function view(?User $user, Produk $produk): bool
    {
        return true;
    }

    /**
     * Bolehkan user membuat produk? (Biasanya Admin)
     */
    public function create(User $user): Response
    {
        return $user->can('create-produk')
            ? Response::allow()
            : Response::deny('Anda tidak memiliki izin untuk membuat produk.');
    }

    /**
     * Bolehkan user mengupdate produk? (Biasanya Admin)
     */
    public function update(User $user, Produk $produk): Response
    {
        return $user->can('update-produk')
            ? Response::allow()
            : Response::deny('Anda tidak memiliki izin untuk mengubah produk.');
    }

    /**
     * Bolehkan user menghapus produk? (Biasanya Admin)
     */
    public function delete(User $user, Produk $produk): Response
    {
        return $user->can('delete-produk')
            ? Response::allow()
            : Response::deny('Anda tidak memiliki izin untuk menghapus produk.');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Produk $produk): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Produk $produk): bool
    {
        return false;
    }
}