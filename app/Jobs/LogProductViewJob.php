<?php

namespace App\Jobs;

use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class LogProductViewJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct( public int $userId, public int $productId)
    {}
    /**
     * Execute the job.
     */
    public function handle(): void
    {
         DB::table('user_product_views')->insert([
            'user_id' => $this->userId,
            'produk_id' => $this->productId,
            'viewed_at' => now()
        ]);
    }
}
