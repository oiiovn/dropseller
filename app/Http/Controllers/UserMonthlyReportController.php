<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserMonthlyReport;

class UserMonthlyReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       $userMonthlyReports = UserMonthlyReport::orderBy('created_at', 'desc')->get();

        return view('admin.quyet_toan', compact('userMonthlyReports'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
  public function update(Request $request, UserMonthlyReport $userMonthlyReport)
{
    try {
        $data = $request->validate([
            'khau_trang' => 'required|numeric',
            'tien_thuc_te' => 'required|numeric',
            'total_chi' => 'nullable|numeric',
        ]);
        $data['tien_phai_thanh_toan'] = $data['total_chi'] - ($data['tien_thuc_te'] + $data['khau_trang']);
        $userMonthlyReport->update($data);
        return redirect()->route('user-monthly-reports.index')->with('success', 'Cập nhật thành công');
    } catch (\Throwable $e) {
        return redirect()->route('user-monthly-reports.index')->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
    }
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Process payment for settlements
     */
    public function processPayment(Request $request)
    {
        try {
            $validated = $request->validate([
                'month' => 'required|string|regex:/^\d{4}-\d{2}$/',
            ]);

            $month = $validated['month'];

            // Gọi command thanh toán quyết toán
            \Illuminate\Support\Facades\Artisan::call('auto:settle-monthly', [
                'month' => $month
            ]);

            $output = \Illuminate\Support\Facades\Artisan::output();

            return redirect()->route('user-monthly-reports.index')
                ->with('success', "✅ Đã chạy thanh toán quyết toán tháng {$month} thành công! {$output}");
        } catch (\Exception $e) {
            return redirect()->route('user-monthly-reports.index')
                ->with('error', '❌ Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
