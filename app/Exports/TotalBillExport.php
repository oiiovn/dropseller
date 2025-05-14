<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class TotalBillExport implements FromView
{
    protected $startDate, $endDate, $total_dropship, $total_dropship_web, $total_program, $share_total_program;

    public function __construct($startDate, $endDate, $total_dropship, $total_dropship_web, $total_program, $share_total_program)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->total_dropship = $total_dropship;
        $this->total_dropship_web = $total_dropship_web;
        $this->total_program = $total_program;
        $this->share_total_program = $share_total_program;
    }

    public function view(): View
    {
        return view('exports.total_bill_excel', [
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'total_dropship' => $this->total_dropship,
            'total_dropship_web' => $this->total_dropship_web,
            'total_program' => $this->total_program,
            'share_total_program' => $this->share_total_program,
        ]);
    }
}
