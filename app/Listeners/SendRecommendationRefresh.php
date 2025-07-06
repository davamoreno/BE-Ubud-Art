<?php
namespace App\Listeners;
use App\Events\ProductAltered;
use Illuminate\Contracts\Queue\ShouldQueue; // Membuat listener berjalan di antrian
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Redis;

class SendRecommendationRefresh implements ShouldQueue // Implementasi ShouldQueue
{
    public function handle(ProductAltered $event): void
    {
        Redis::publish('recommendation-updates', 'refresh');
    }
}