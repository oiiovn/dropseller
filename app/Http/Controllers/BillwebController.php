<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Order;
use App\Exports\TotalBillExport;
use Maatwebsite\Excel\Facades\Excel;

class BillwebController extends Controller
{

    public function view_total_bill(Request $request)
    {
        $startDate = $request->input('start_date')
            ? Carbon::parse($request->input('start_date'))->startOfDay()
            : Carbon::now()->subMonthNoOverflow()->startOfMonth();
        $endDate = $request->input('end_date')
            ? Carbon::parse($request->input('end_date'))->endOfDay()
            : Carbon::now()->subMonthNoOverflow()->endOfMonth();
        $total_dropship = Order::whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_dropship');
        $total_dropship_web = $total_dropship/5/2;
        if ($request->has('export')) {
            return Excel::download(new TotalBillExport($startDate, $endDate, $total_dropship,$total_dropship_web), 'total_dropship.xlsx');
        }
        return view('billweb.totalbill', compact('startDate', 'endDate', 'total_dropship_web', 'total_dropship'));
    }
    public function exportTotalBill(Request $request)
    {
        $startDate = Carbon::parse($request->input('start_date'))->startOfDay();
        $endDate = Carbon::parse($request->input('end_date'))->endOfDay();
        $total_dropship = Order::whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_dropship');
        $total_dropship_web = $total_dropship/5/2;
        return Excel::download(new TotalBillExport($startDate, $endDate, $total_dropship,$total_dropship_web), 'total_dropship.xlsx');
    }
}
