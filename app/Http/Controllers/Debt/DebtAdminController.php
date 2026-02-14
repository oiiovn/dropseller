<?php

namespace App\Http\Controllers\Debt;

use App\Http\Controllers\Controller;
use App\Models\DebtCreditor;
use App\Models\DebtDistribution;
use App\Models\DebtMonthlyIncome;
use App\Models\DebtRepaymentPlan;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class DebtAdminController extends Controller
{
    public function index()
    {
        $debtorId = auth()->id();
        $creditors = DebtCreditor::with(['user', 'repaymentPlans' => fn ($q) => $q->where('is_active', true)])
            ->where('debtor_user_id', $debtorId)
            ->orderBy('id')
            ->get();
        return view('debt.admin.index', compact('creditors'));
    }

    public function summary()
    {
        $debtorId = auth()->id();
        $creditors = DebtCreditor::with('user')
            ->where('debtor_user_id', $debtorId)
            ->get();
        $totalDebt = (float) $creditors->sum('total_debt');
        $totalPaid = (float) DebtDistribution::whereHas('debtCreditor', fn ($q) => $q->where('debtor_user_id', $debtorId))
            ->where('status', 'paid')
            ->sum('amount');
        $totalRemaining = max(0, $totalDebt - $totalPaid);
        $pendingCount = DebtDistribution::whereHas('debtCreditor', fn ($q) => $q->where('debtor_user_id', $debtorId))
            ->where('status', 'pending')
            ->count();
        $paidCount = DebtDistribution::whereHas('debtCreditor', fn ($q) => $q->where('debtor_user_id', $debtorId))
            ->where('status', 'paid')
            ->count();
        foreach ($creditors as $c) {
            $c->paid = (float) $c->distributions()->where('status', 'paid')->sum('amount');
            $c->remaining = max(0, (float) $c->total_debt - $c->paid);
        }
        return view('debt.admin.summary', compact(
            'creditors', 'totalDebt', 'totalPaid', 'totalRemaining',
            'pendingCount', 'paidCount'
        ));
    }

    public function createCreditor()
    {
        return view('debt.admin.creditor-form', ['creditor' => null]);
    }

    public function storeCreditor(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'personal_code' => 'required|string|max:50|unique:users,personal_code',
            'phone' => 'nullable|string|max:20',
            'total_debt' => 'nullable|numeric|min:0',
            'restructuring_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);
        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'referral_code' => 'D' . strtoupper(substr(md5(uniqid()), 0, 8)),
                'personal_code' => $request->personal_code,
            ]);
            DebtCreditor::create([
                'user_id' => $user->id,
                'debtor_user_id' => auth()->id(),
                'total_debt' => $request->total_debt ?? 0,
                'phone' => $request->phone,
                'restructuring_date' => $request->restructuring_date,
                'notes' => $request->notes,
            ]);
        });
        return redirect()->route('debt.admin.index')->with('success', 'Đã tạo chủ nợ và tài khoản.');
    }

    public function editCreditor(DebtCreditor $creditor)
    {
        $this->authorizeDebtor($creditor);
        return view('debt.admin.creditor-form', ['creditor' => $creditor]);
    }

    public function updateCreditor(Request $request, DebtCreditor $creditor)
    {
        $this->authorizeDebtor($creditor);
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($creditor->user_id)],
            'password' => 'nullable|string|min:8',
            'personal_code' => ['required', 'string', 'max:50', Rule::unique('users', 'personal_code')->ignore($creditor->user_id)],
            'phone' => 'nullable|string|max:20',
            'total_debt' => 'nullable|numeric|min:0',
            'restructuring_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);
        $creditor->user->update([
            'name' => $request->name,
            'email' => $request->email,
            'personal_code' => $request->personal_code,
        ] + ($request->filled('password') ? ['password' => Hash::make($request->password)] : []));
        $creditor->update([
            'total_debt' => $request->total_debt ?? $creditor->total_debt,
            'phone' => $request->phone,
            'restructuring_date' => $request->restructuring_date,
            'notes' => $request->notes,
        ]);
        return redirect()->route('debt.admin.index')->with('success', 'Đã cập nhật chủ nợ.');
    }

    public function destroyCreditor(DebtCreditor $creditor)
    {
        $this->authorizeDebtor($creditor);
        $user = $creditor->user;
        $creditor->distributions()->delete();
        $creditor->repaymentPlans()->delete();
        $creditor->delete();
        $user->delete();
        return redirect()->route('debt.admin.index')->with('success', 'Đã xóa chủ nợ.');
    }

    public function repaymentPlans(DebtCreditor $creditor)
    {
        $this->authorizeDebtor($creditor);
        $plans = $creditor->repaymentPlans()->orderByDesc('id')->get();
        return view('debt.admin.repayment-plans', compact('creditor', 'plans'));
    }

    public function storeRepaymentPlan(Request $request, DebtCreditor $creditor)
    {
        $this->authorizeDebtor($creditor);
        $request->validate([
            'monthly_percent' => 'required|numeric|min:0|max:100',
            'pay_day_of_month' => 'required|integer|min:1|max:31',
        ]);
        $creditor->repaymentPlans()->update(['is_active' => false]);
        $creditor->repaymentPlans()->create([
            'monthly_percent' => $request->monthly_percent,
            'pay_day_of_month' => $request->pay_day_of_month,
            'is_active' => true,
        ]);
        return redirect()->route('debt.admin.repayment-plans', $creditor)->with('success', 'Đã lưu kế hoạch trả (áp dụng toàn bộ các tháng).');
    }

    public function monthlyIncomes()
    {
        $incomes = DebtMonthlyIncome::with('distributions.debtCreditor.user')
            ->where('debtor_user_id', auth()->id())
            ->orderByRaw("
                CASE
                    WHEN year = YEAR(CURDATE()) AND month = MONTH(CURDATE()) THEN 0
                    WHEN year > YEAR(CURDATE()) OR (year = YEAR(CURDATE()) AND month > MONTH(CURDATE())) THEN 1
                    ELSE 2
                END,
                year ASC,
                month ASC
            ")
            ->paginate(30);
        return view('debt.admin.monthly-incomes', compact('incomes'));
    }

    public function createMonthlyIncome()
    {
        return view('debt.admin.monthly-income-form', ['income' => null]);
    }

    public function editMonthlyIncome(DebtMonthlyIncome $income)
    {
        if ($income->debtor_user_id !== auth()->id()) {
            abort(403);
        }
        return view('debt.admin.monthly-income-form', ['income' => $income]);
    }

    public function storeMonthlyIncome(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:2100',
            'amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);
        $exists = DebtMonthlyIncome::where('debtor_user_id', auth()->id())
            ->where('month', $request->month)
            ->where('year', $request->year)
            ->exists();
        if ($exists) {
            return redirect()->back()->withErrors(['month' => 'Đã có thu nhập tháng ' . $request->month . '/' . $request->year . '.']);
        }
        DebtMonthlyIncome::create([
            'debtor_user_id' => auth()->id(),
            'month' => $request->month,
            'year' => $request->year,
            'amount' => $request->amount,
            'notes' => $request->notes,
        ]);
        return redirect()->route('debt.admin.monthly-incomes')->with('success', 'Đã nhập thu nhập. Bấm "Phân bổ" để tạo kế hoạch trả theo tỉ lệ từng chủ nợ.');
    }

    public function createBulk60Months()
    {
        return view('debt.admin.monthly-income-bulk-60');
    }

    public function storeBulk60Months(Request $request)
    {
        $request->validate([
            'start_month' => 'required|integer|min:1|max:12',
            'start_year' => 'required|integer|min:2020|max:2100',
            'amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);
        $debtorId = auth()->id();
        $month = (int) $request->start_month;
        $year = (int) $request->start_year;
        $amount = (float) $request->amount;
        $notes = $request->notes;
        $created = 0;
        $skipped = 0;
        for ($i = 0; $i < 60; $i++) {
            $exists = DebtMonthlyIncome::where('debtor_user_id', $debtorId)
                ->where('month', $month)
                ->where('year', $year)
                ->exists();
            if (!$exists) {
                DebtMonthlyIncome::create([
                    'debtor_user_id' => $debtorId,
                    'month' => $month,
                    'year' => $year,
                    'amount' => $amount,
                    'notes' => $notes,
                ]);
                $created++;
            } else {
                $skipped++;
            }
            $month++;
            if ($month > 12) {
                $month = 1;
                $year++;
            }
        }
        $msg = "Đã tạo {$created} tháng thu nhập.";
        if ($skipped > 0) {
            $msg .= " Bỏ qua {$skipped} tháng đã tồn tại.";
        }
        return redirect()->route('debt.admin.monthly-incomes')->with('success', $msg);
    }

    public function updateMonthlyIncome(Request $request, DebtMonthlyIncome $income)
    {
        if ($income->debtor_user_id !== auth()->id()) {
            abort(403);
        }
        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:2100',
            'amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);
        $exists = DebtMonthlyIncome::where('debtor_user_id', auth()->id())
            ->where('month', $request->month)
            ->where('year', $request->year)
            ->where('id', '!=', $income->id)
            ->exists();
        if ($exists) {
            return redirect()->back()->withErrors(['month' => 'Đã có thu nhập tháng ' . $request->month . '/' . $request->year . '.']);
        }
        $income->update([
            'month' => $request->month,
            'year' => $request->year,
            'amount' => $request->amount,
            'notes' => $request->notes,
        ]);
        return redirect()->route('debt.admin.monthly-incomes')->with('success', 'Đã cập nhật thu nhập tháng.');
    }

    public function distributeMonthlyIncome(DebtMonthlyIncome $income)
    {
        if ($income->debtor_user_id !== auth()->id()) {
            abort(403);
        }
        $income->distributions()->where('status', 'pending')->delete();
        $this->distributeIncome($income);
        $count = $income->distributions()->count();
        return redirect()->route('debt.admin.monthly-incomes')->with('success', "Đã phân bổ tháng {$income->month}/{$income->year} cho {$count} chủ nợ theo tỉ lệ kế hoạch trả.");
    }

    public function deleteDistributions(DebtMonthlyIncome $income)
    {
        if ($income->debtor_user_id !== auth()->id()) {
            abort(403);
        }
        $count = $income->distributions()->count();
        $income->distributions()->delete();
        return redirect()->route('debt.admin.monthly-incomes')->with('success', "Đã xóa {$count} phân bổ tháng {$income->month}/{$income->year}. Bạn có thể bấm Phân bổ để tạo lại.");
    }

    public function deleteDistributionsBulk(Request $request)
    {
        $request->validate(['income_ids' => 'required|array', 'income_ids.*' => 'integer|exists:debt_monthly_incomes,id']);
        $debtorId = auth()->id();
        $deleted = 0;
        foreach ($request->income_ids as $id) {
            $income = DebtMonthlyIncome::find($id);
            if (!$income || $income->debtor_user_id !== $debtorId) {
                continue;
            }
            $deleted += $income->distributions()->count();
            $income->distributions()->delete();
        }
        return redirect()->route('debt.admin.monthly-incomes')->with('success', "Đã xóa phân bổ của {$deleted} dòng. Bạn có thể bấm Phân bổ hàng loạt để tạo lại.");
    }

    public function distributeBulk(Request $request)
    {
        $request->validate(['income_ids' => 'required|array', 'income_ids.*' => 'integer|exists:debt_monthly_incomes,id']);
        $debtorId = auth()->id();
        $done = 0;
        foreach ($request->income_ids as $id) {
            $income = DebtMonthlyIncome::find($id);
            if (!$income || $income->debtor_user_id !== $debtorId) {
                continue;
            }
            $income->distributions()->where('status', 'pending')->delete();
            $this->distributeIncome($income);
            $done++;
        }
        return redirect()->route('debt.admin.monthly-incomes')->with('success', "Đã phân bổ hàng loạt {$done} tháng theo tỉ lệ kế hoạch trả.");
    }

    protected function distributeIncome(DebtMonthlyIncome $income)
    {
        $creditors = DebtCreditor::with(['repaymentPlans' => fn ($q) => $q->where('is_active', true)])
            ->where('debtor_user_id', $income->debtor_user_id)
            ->get();
        $amount = (float) $income->amount;
        foreach ($creditors as $creditor) {
            if ($income->distributions()->where('debt_creditor_id', $creditor->id)->exists()) {
                continue;
            }
            $plan = $creditor->repaymentPlans->first();
            if (!$plan) {
                continue;
            }
            $percent = (float) $plan->monthly_percent;
            $share = round($amount * $percent / 100, 2);
            if ($share <= 0) {
                continue;
            }
            $code = 'TN-' . $creditor->id . '-' . $income->year . '-' . str_pad((string) $income->month, 2, '0', STR_PAD_LEFT) . '-' . strtoupper(substr(uniqid(), -4));
            DebtDistribution::create([
                'debt_monthly_income_id' => $income->id,
                'debt_creditor_id' => $creditor->id,
                'amount' => $share,
                'percent_applied' => $percent,
                'transaction_code' => $code,
                'status' => 'pending',
            ]);
        }
    }

    public function distributions()
    {
        $monthlyIncomes = DebtMonthlyIncome::with(['distributions.debtCreditor.user'])
            ->where('debtor_user_id', auth()->id())
            ->whereHas('distributions')
            ->orderByRaw("
                CASE
                    WHEN year = YEAR(CURDATE()) AND month = MONTH(CURDATE()) THEN 0
                    WHEN year > YEAR(CURDATE()) OR (year = YEAR(CURDATE()) AND month > MONTH(CURDATE())) THEN 1
                    ELSE 2
                END,
                year ASC,
                month ASC
            ")
            ->paginate(12);
        return view('debt.admin.distributions', compact('monthlyIncomes'));
    }

    public function paymentHistory()
    {
        $payments = DebtDistribution::with(['debtCreditor.user', 'debtMonthlyIncome'])
            ->whereHas('debtCreditor', fn ($q) => $q->where('debtor_user_id', auth()->id()))
            ->where('status', 'paid')
            ->join('debt_monthly_incomes', 'debt_distributions.debt_monthly_income_id', '=', 'debt_monthly_incomes.id')
            ->orderByRaw("
                CASE
                    WHEN debt_monthly_incomes.year = YEAR(CURDATE()) AND debt_monthly_incomes.month = MONTH(CURDATE()) THEN 0
                    WHEN debt_monthly_incomes.year > YEAR(CURDATE()) OR (debt_monthly_incomes.year = YEAR(CURDATE()) AND debt_monthly_incomes.month > MONTH(CURDATE())) THEN 1
                    ELSE 2
                END,
                debt_monthly_incomes.year ASC,
                debt_monthly_incomes.month ASC,
                debt_distributions.paid_at DESC
            ")
            ->select('debt_distributions.*')
            ->paginate(30);
        return view('debt.admin.payment-history', compact('payments'));
    }

    public function markPaid(Request $request, DebtDistribution $distribution)
    {
        if ($distribution->status === 'paid') {
            return redirect()->back()
                ->with('error', 'Phân bổ đã thanh toán không được thay đổi.')
                ->with('expand_income_id', $distribution->debt_monthly_income_id);
        }
        $this->authorizeDebtor($distribution->debtCreditor);
        $request->validate([
            'bank_transaction_ref' => 'nullable|string|max:255',
            'amount' => 'nullable|numeric|min:0',
        ]);
        $data = [
            'status' => 'paid',
            'paid_at' => now(),
            'bank_transaction_ref' => $request->bank_transaction_ref,
        ];
        if ($request->filled('amount') && is_numeric($request->amount)) {
            $data['amount'] = (float) $request->amount;
        }
        $distribution->update($data);
        return redirect()->back()
            ->with('success', 'Đã ghi nhận thanh toán.')
            ->with('expand_income_id', $distribution->debt_monthly_income_id);
    }

    public function bankHistoryPay2s(Request $request)
    {
        $tz = 'Asia/Ho_Chi_Minh';
        $beginInput = $request->input('begin', now($tz)->subDays(30)->format('Y-m-d'));
        $endInput = $request->input('end', now($tz)->format('Y-m-d'));
        try {
            if (str_contains($beginInput, '-') && preg_match('/^\d{4}-\d{2}-\d{2}$/', $beginInput)) {
                $startDate = \Carbon\Carbon::parse($beginInput . ' 00:00:00', $tz);
                $endDate = \Carbon\Carbon::parse($endInput . ' 23:59:59', $tz);
            } else {
                $startDate = \Carbon\Carbon::createFromFormat('d/m/Y', $beginInput, $tz)->startOfDay();
                $endDate = \Carbon\Carbon::createFromFormat('d/m/Y', $endInput, $tz)->endOfDay();
            }
        } catch (\Exception $e) {
            $startDate = now($tz)->subDays(30)->startOfDay();
            $endDate = now($tz)->endOfDay();
        }
        $beginYmd = $startDate->format('Y-m-d');
        $endYmd = $endDate->format('Y-m-d');

        $query = Transaction::whereBetween('transaction_date', [$startDate->toDateString(), $endDate->toDateString()]);

        $accountsStr = $request->input('bank_accounts', config('pay2s.bank_accounts'));
        if (!empty($accountsStr)) {
            $accounts = array_filter(array_map('trim', explode(',', $accountsStr)));
            if (!empty($accounts)) {
                $query->whereIn('account_number', $accounts);
            }
        }

        $transactions = $query->orderByDesc('transaction_date')->paginate(50)->withQueryString();

        // Dashboard SHOPEEPAY & Grab (cùng bộ lọc ngày + tài khoản)
        $baseFilter = Transaction::whereBetween('transaction_date', [$startDate->toDateString(), $endDate->toDateString()]);
        if (!empty($accountsStr)) {
            $accs = array_filter(array_map('trim', explode(',', $accountsStr)));
            if (!empty($accs)) {
                $baseFilter->whereIn('account_number', $accs);
            }
        }
        $shopeepayIn   = (clone $baseFilter)->where(function ($q) { $q->where('bank', 'SHOPEEPAY')->orWhere('description', 'like', '%SHOPEEPAY%'); })->where('type', 'IN')->sum('amount');
        $shopeepayOut  = (clone $baseFilter)->where(function ($q) { $q->where('bank', 'SHOPEEPAY')->orWhere('description', 'like', '%SHOPEEPAY%'); })->where('type', 'OUT')->sum('amount');
        $shopeepayCount = (clone $baseFilter)->where(function ($q) { $q->where('bank', 'SHOPEEPAY')->orWhere('description', 'like', '%SHOPEEPAY%'); })->count();
        $grabIn   = (clone $baseFilter)->where(function ($q) { $q->where('bank', 'Grab')->orWhere('description', 'like', '%Grab%'); })->where('type', 'IN')->sum('amount');
        $grabOut  = (clone $baseFilter)->where(function ($q) { $q->where('bank', 'Grab')->orWhere('description', 'like', '%Grab%'); })->where('type', 'OUT')->sum('amount');
        $grabCount = (clone $baseFilter)->where(function ($q) { $q->where('bank', 'Grab')->orWhere('description', 'like', '%Grab%'); })->count();
        $cafeIn   = (clone $baseFilter)->where(function ($q) { $q->where('bank', 'CAFE')->orWhere('description', 'like', '%CAFE%'); })->where('type', 'IN')->sum('amount');
        $cafeOut  = (clone $baseFilter)->where(function ($q) { $q->where('bank', 'CAFE')->orWhere('description', 'like', '%CAFE%'); })->where('type', 'OUT')->sum('amount');
        $cafeCount = (clone $baseFilter)->where(function ($q) { $q->where('bank', 'CAFE')->orWhere('description', 'like', '%CAFE%'); })->count();
        // Vốn Food: mã dạng BC0"xxxx" (description chứa BC0)
        $vonFoodIn   = (clone $baseFilter)->where('description', 'like', '%BC0%')->where('type', 'IN')->sum('amount');
        $vonFoodOut  = (clone $baseFilter)->where('description', 'like', '%BC0%')->where('type', 'OUT')->sum('amount');
        $vonFoodCount = (clone $baseFilter)->where('description', 'like', '%BC0%')->count();
        // BE-: mã dạng BE-xxxx (description chứa BE-)
        $beIn   = (clone $baseFilter)->where('description', 'like', '%BE-%')->where('type', 'IN')->sum('amount');
        $beOut  = (clone $baseFilter)->where('description', 'like', '%BE-%')->where('type', 'OUT')->sum('amount');
        $beCount = (clone $baseFilter)->where('description', 'like', '%BE-%')->count();

        return view('debt.admin.bank-history-pay2s', [
            'transactions' => $transactions,
            'begin_ymd' => $beginYmd,
            'end_ymd' => $endYmd,
            'bank_accounts' => $accountsStr,
            'shopeepay_in' => $shopeepayIn,
            'shopeepay_out' => $shopeepayOut,
            'shopeepay_count' => $shopeepayCount,
            'grab_in' => $grabIn,
            'grab_out' => $grabOut,
            'grab_count' => $grabCount,
            'cafe_in' => $cafeIn,
            'cafe_out' => $cafeOut,
            'cafe_count' => $cafeCount,
            'von_food_in' => $vonFoodIn,
            'von_food_out' => $vonFoodOut,
            'von_food_count' => $vonFoodCount,
            'be_in' => $beIn,
            'be_out' => $beOut,
            'be_count' => $beCount,
        ]);
    }

    protected function authorizeDebtor(DebtCreditor $creditor): void
    {
        if ($creditor->debtor_user_id !== auth()->id()) {
            abort(403);
        }
    }
}
