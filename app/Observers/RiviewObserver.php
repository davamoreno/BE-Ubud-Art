<?php

namespace App\Observers;

use App\Models\Riview;

class RiviewObserver
{
    // Aksi ini berjalan SETELAH sebuah review berhasil dibuat.
    public function created(Riview $riview): void
    {
        $riview->produk->recalculateRating();
    }

    // Aksi ini berjalan SETELAH sebuah review berhasil di-update.
    public function updated(Riview $riview): void
    {
        // Hanya kalkulasi ulang jika ratingnya berubah
        if ($riview->isDirty('rating')) {
            $riview->produk->recalculateRating();
        }
    }

    // Aksi ini berjalan SETELAH sebuah review berhasil dihapus.
    public function deleted(Riview $riview): void
    {
        $riview->produk->recalculateRating();
    }

    /**
     * Handle the Riview "restored" event.
     */
    public function restored(Riview $riview): void
    {
        //
    }

    /**
     * Handle the Riview "force deleted" event.
     */
    public function forceDeleted(Riview $riview): void
    {
        //
    }
}
