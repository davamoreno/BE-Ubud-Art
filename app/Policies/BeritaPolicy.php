<?php

namespace App\Policies;

use App\Models\Berita;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class BeritaPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
          return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Berita $berita): bool
    {
          return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
         return $user->can('create-berita')
            ? Response::allow()
            : Response::deny('Anda tidak memiliki izin untuk membuat berita.');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Berita $berita): Response
    {
         return $user->can('update-berita')
            ? Response::allow()
            : Response::deny('Anda tidak memiliki izin untuk mengubah berita.');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Berita $berita): Response
    {
          return $user->can('delete-berita') 
            ? Response::allow()
            : Response::deny('Anda tidak memiliki izin untuk menghapus berita.');;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Berita $berita): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Berita $berita): bool
    {
        return false;
    }
}
