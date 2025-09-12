<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\ProgramShop;
use Illuminate\Support\Facades\Http;

class AutoCompleteProgram implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $programShopId;

    /**
     * Create a new job instance.
     */
    public function __construct($programShopId)
    {
        $this->programShopId = $programShopId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Gọi trực tiếp method thay vì qua API
            $programController = new \App\Http\Controllers\ProgramController();
            $programController->autoCompleteProgram($this->programShopId);
            
            \Log::info('Auto complete program successful', [
                'program_shop_id' => $this->programShopId
            ]);
        } catch (\Exception $e) {
            \Log::error('Auto complete program exception', [
                'program_shop_id' => $this->programShopId,
                'error' => $e->getMessage()
            ]);
        }
    }
}
