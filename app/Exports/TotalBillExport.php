<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class TotalBillExport implements FromView
{
    protected $startDate, $endDate, $total_dropship;

    public function __construct($startDate, $endDate, $total_dropship)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->total_dropship = $total_dropship;
    }

    public function view(): View
    {
        return view('exports.total_bill_excel', [
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'total_dropship' => $this->total_dropship,
        ]);
    }
}
