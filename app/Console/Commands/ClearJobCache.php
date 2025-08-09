<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ClearJobCache extends Command
{
    /**
     * Tên command
     *
     * @var string
     */
    protected $signature = 'job:clear-cache';

    /**
     * Mô tả command
     *
     * @var string
     */
    protected $description = 'Xoá cache và flush queue job';

    /**
     * Thực thi command
     */
    public function handle()
    {
        $this->info('Bắt đầu xoá cache và job queue...');

        Artisan::call('cache:clear');
        Artisan::call('queue:flush');

        $this->info('Đã xoá cache và job queue thành công!');
    }
}
