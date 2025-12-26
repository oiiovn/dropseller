<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Order;
use App\Exports\TotalBillExport;
use App\Models\ProgramShop;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Transaction;
class BillwebController extends Controller
{

    public function view_total_bill(Request $request)
    {
        $startDate = $request->input('start_date')
            ? Carbon::parse($request->input('start_date'))->startOfDay()
            : Carbon::now()->startOfMonth();

        $endDate = $request->input('end_date')
            ? Carbon::parse($request->input('end_date'))->endOfDay()
            : Carbon::now()->endOfDay();

        $total_dropship = Order::whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_dropship');
        $total_dropship_web = $total_dropship / 5 / 2;

        $total_program = Transaction::where('bank', 'PSP')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');
        $share_total_program = $total_program / 2 / 2;
        return view('billweb.totalbill', compact('startDate', 'endDate', 'total_dropship_web', 'total_dropship', 'share_total_program', 'total_program'));
    }
    public function exportTotalBill(Request $request)
    {
        $startDate = Carbon::parse($request->input('start_date'))->startOfDay();
        $endDate = Carbon::parse($request->input('end_date'))->endOfDay();

        $total_dropship = Order::whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_dropship');
        $total_dropship_web = $total_dropship / 5 / 2;

        $total_program = ProgramShop::where('status_payment', 'Đã thanh toán')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_payment');
        $share_total_program = $total_program / 2 / 2;

        return Excel::download(
            new TotalBillExport(
                $startDate,
                $endDate,
                $total_dropship,
                $total_dropship_web,
                $total_program,
                $share_total_program
            ),
            'total_dropship.xlsx'
        );
    }
}
