<?php

namespace App\Console\Commands;

use App\Services\Crm\DebtAlertService;
use Illuminate\Console\Command;

class CrmCheckOverdueDebts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crm:check-overdue-debts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Đồng bộ trạng thái công nợ quá hạn và cảnh báo CRM';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $result = app(DebtAlertService::class)->syncOverdueDebts();

        $this->info(sprintf(
            'Overdue debts synced: %d records, amount %s円',
            $result['overdue_count'],
            number_format((float) $result['overdue_amount_jpy'], 0, '.', ',')
        ));

        return self::SUCCESS;
    }
}
