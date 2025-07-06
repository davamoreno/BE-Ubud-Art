<?php

namespace App\Observers;

use App\Models\Produk;

class ProdukObserver
{
    /**
     * Menangani event "updated" pada Produk.
     * Ini akan terpicu setelah RiviewObserver mengupdate rating produk.
     */
    public function updated(Produk $produk): void
    {
        // Jika kolom 'rating' pada produk berubah...
        if ($produk->isDirty('rating')) {
            // ...dan jika produk ini memiliki toko...
            if ($produk->toko) {
                // ...maka hitung ulang rating toko tersebut.
                $produk->toko->recalculateRating();
            }
        }
    }

    /**
     * Menangani event "created" pada Produk.
     * Saat produk baru dibuat, rating toko juga perlu diupdate.
     */
    public function created(Produk $produk): void
    {
        if ($produk->toko) {
            $produk->toko->recalculateRating();
        }
    }

    /**
     * Menangani event "deleted" pada Produk.
     * Saat produk dihapus, rating toko juga perlu diupdate.
     */
    public function deleted(Produk $produk): void
    {
        if ($produk->toko) {
            $produk->toko->recalculateRating();
        }
    }

    public function restored(Produk $produk): void
    {
        //
    }

    /**
     * Handle the Produk "force deleted" event.
     */
    public function forceDeleted(Produk $produk): void
    {
        //
    }
}
