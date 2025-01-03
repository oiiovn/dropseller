<?php

namespace App\Http\Controllers;

use App\Services\ProductReportService;
use Illuminate\Http\Request;

class ProductReportController extends Controller
{
    protected $productReportService;

    public function __construct(ProductReportService $productReportService)
    {
        $this->productReportService = $productReportService;
    }

    public function index(Request $request)
    {
        $timeStart = $request->input('time_start');
        $timeEnd = $request->input('time_end');

        $report = $this->productReportService->getProductReport($timeStart, $timeEnd);

        return view('product.report', compact('report'));
    }
}