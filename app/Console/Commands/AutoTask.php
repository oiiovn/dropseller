<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Jobs\ProcessAutoTask;
class AutoTask extends Command
{
    protected $signature = 'auto:task';
    protected $description = 'Chạy tự động một tác vụ';

    public function __construct()
    {
        parent::__construct();
    }



    public function handle()
    {
        Log::info('Đẩy tác vụ vào hàng đợi...');
        ProcessAutoTask::dispatch();
    }
    
}
