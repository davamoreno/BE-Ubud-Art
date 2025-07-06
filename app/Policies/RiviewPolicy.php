<?php
// File: app/Policies/RiviewPolicy.php

namespace App\Policies;

use App\Models\Riview; // Pastikan nama model sesuai (dengan typo)
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RiviewPolicy
{
    /**
     * Bolehkan user/tamu melihat daftar riview?
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Bolehkan user/tamu melihat detail satu riview?
     */
    public function view(?User $user, Riview $riview): bool
    {
        return true;
    }

    /**
     * Bolehkan user membuat riview? (Biasanya Customer)
     */
    public function create(User $user): Response
    {
        return $user->can('create-riview')
            ? Response::allow()
            : Response::deny('Anda tidak memiliki izin untuk membuat riview.');
    }

    /**
     * Bolehkan user mengupdate riview?
     * Logika tambahan: User hanya boleh update riview miliknya sendiri.
     */
    public function update(User $user, Riview $riview): Response
    {
        // Pertama, cek apakah user punya permission dasar untuk 'update-riview'
        if (!$user->can('update-riview')) {
            return Response::deny('Anda tidak memiliki izin untuk mengubah riview.');
        }

        // Jika punya izin, cek lagi apakah riview ini miliknya
        return $user->id === $riview->user_id
            ? Response::allow()
            : Response::deny('Anda hanya dapat mengubah riview milik Anda sendiri.');
    }

    /**
     * Bolehkan user menghapus riview?
     * Logika tambahan: User hanya boleh hapus riview miliknya sendiri.
     */
    public function delete(User $user, Riview $riview): Response
    {
        // Pertama, cek apakah user punya permission dasar untuk 'delete-riview'
        if (!$user->can('delete-riview')) {
            return Response::deny('Anda tidak memiliki izin untuk menghapus riview.');
        }
        
        // Jika punya izin, cek lagi apakah riview ini miliknya
        return $user->id === $riview->user_id
            ? Response::allow()
            : Response::deny('Anda hanya dapat menghapus riview milik Anda sendiri.');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Riview $riview): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Riview $riview): bool
    {
        return false;
    }
}