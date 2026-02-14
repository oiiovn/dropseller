<?php

namespace App\Http\Controllers\Debt;

use App\Http\Controllers\Controller;
use App\Models\DebtMonthlyIncome;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class DebtController extends Controller
{
    public function showCodeVerify()
    {
        $user = Auth::user();
        if (!$user || !$user->isDebtSystemUser()) {
            return redirect()->route('dashboard');
        }
        if (session('debt_code_verified_at')) {
            return redirect()->route('debt.dashboard');
        }
        return view('debt.code-verify');
    }

    public function verifyCode(Request $request)
    {
        $request->validate(['personal_code' => 'required|string']);
        $user = Auth::user();
        if (!$user || !$user->isDebtSystemUser()) {
            return redirect()->route('dashboard');
        }
        if ((string) $user->personal_code !== (string) $request->personal_code) {
            return redirect()->back()->withErrors(['personal_code' => 'Mã số cá nhân không đúng.']);
        }
        session(['debt_code_verified_at' => now()->toDateTimeString()]);
        return redirect()->route('debt.dashboard');
    }

    public function dashboard()
    {
        $user = Auth::user();
        $creditor = $user->debtCreditorProfile();
        if (!$creditor) {
            return view('debt.dashboard', [
                'creditor' => null,
                'distributions' => collect(),
                'totalPaid' => 0,
                'remaining' => 0,
                'progressPercent' => 0,
                'averageMonthlyPayment' => 0,
                'estimatedCompletionMonth' => null,
                'years' => [],
                'selectedYear' => null,
                'selectedStatus' => null,
                'expectedMonthlyAmount' => 0,
            ]);
        }
        $incomeIds = $creditor->distributions()->pluck('debt_monthly_income_id')->unique();
        $years = DebtMonthlyIncome::whereIn('id', $incomeIds)->orderBy('year')->pluck('year')->unique()->values()->toArray();
        $selectedYear = request('year') ? (int) request('year') : null;
        $selectedStatus = request('status', '');
        $distributionsQuery = $creditor->distributions()
            ->with('debtMonthlyIncome')
            ->join('debt_monthly_incomes', 'debt_distributions.debt_monthly_income_id', '=', 'debt_monthly_incomes.id');
        if ($selectedYear) {
            $distributionsQuery->where('debt_monthly_incomes.year', $selectedYear);
        }
        if ($selectedStatus === 'paid') {
            $distributionsQuery->where('debt_distributions.status', 'paid');
        } elseif ($selectedStatus === 'cho') {
            $distributionsQuery->where('debt_distributions.status', 'pending')
                ->whereRaw('debt_monthly_incomes.year = YEAR(CURDATE())')
                ->whereRaw('debt_monthly_incomes.month = MONTH(CURDATE())');
        } elseif ($selectedStatus === 'du_kien') {
            $distributionsQuery->where('debt_distributions.status', 'pending')
                ->whereRaw('(debt_monthly_incomes.year > YEAR(CURDATE()) OR (debt_monthly_incomes.year = YEAR(CURDATE()) AND debt_monthly_incomes.month > MONTH(CURDATE())))');
        }
        $distributions = $distributionsQuery
            ->orderByRaw("
                CASE
                    WHEN debt_monthly_incomes.year = YEAR(CURDATE()) AND debt_monthly_incomes.month = MONTH(CURDATE()) THEN 0
                    WHEN debt_monthly_incomes.year > YEAR(CURDATE()) OR (debt_monthly_incomes.year = YEAR(CURDATE()) AND debt_monthly_incomes.month > MONTH(CURDATE())) THEN 1
                    ELSE 2
                END,
                debt_monthly_incomes.year ASC,
                debt_monthly_incomes.month ASC
            ")
            ->select('debt_distributions.*')
            ->limit(50)
            ->get();
        $paidDistributions = $creditor->distributions()->where('status', 'paid')->get();
        $totalPaid = (float) $paidDistributions->sum('amount');
        $totalDebt = (float) $creditor->total_debt;
        $remaining = max(0, $totalDebt - $totalPaid);
        $progressPercent = $totalDebt > 0 ? min(100, round($totalPaid / $totalDebt * 100, 2)) : 0;
        $averageMonthlyPayment = $paidDistributions->isEmpty() ? 0 : (float) $paidDistributions->avg('amount');
        $expectedMonthlyAmount = (float) $creditor->distributions()->avg('amount');
        if ($expectedMonthlyAmount <= 0) {
            $expectedMonthlyAmount = $averageMonthlyPayment;
        }
        $estimatedCompletionMonth = null;
        if ($remaining > 0 && $expectedMonthlyAmount > 0) {
            $monthsLeft = (int) ceil($remaining / $expectedMonthlyAmount);
            $estimatedCompletionMonth = now()->startOfMonth()->addMonths($monthsLeft);
        }
        return view('debt.dashboard', [
            'creditor' => $creditor,
            'distributions' => $distributions,
            'totalPaid' => $totalPaid,
            'remaining' => $remaining,
            'progressPercent' => $progressPercent,
            'averageMonthlyPayment' => $averageMonthlyPayment,
            'expectedMonthlyAmount' => $expectedMonthlyAmount,
            'estimatedCompletionMonth' => $estimatedCompletionMonth,
            'years' => $years,
            'selectedYear' => $selectedYear,
            'selectedStatus' => $selectedStatus,
        ]);
    }

    public function showNoticeRestructuring()
    {
        $creditorName = Auth::user()->name ?: 'Anh/Chị';
        return view('debt.notice-restructuring', compact('creditorName'));
    }

    public function showAccountForm()
    {
        return view('debt.account', ['user' => Auth::user()]);
    }

    public function updateAccount(Request $request)
    {
        $user = Auth::user();
        $rules = [
            'current_password' => 'required|current_password',
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'email_confirmation' => ['required_with:email', 'same:email'],
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ];
        $request->validate($rules, ['email.unique' => 'Email này đã được sử dụng.']);

        $messages = [];
        if ($request->filled('email') && $request->email !== $user->email) {
            $user->update(['email' => $request->email]);
            $messages[] = 'Đã đổi email.';
        }
        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
            $messages[] = 'Đã đổi mật khẩu.';
        }
        if (empty($messages)) {
            return redirect()->back()->with('info', 'Không có thay đổi.');
        }
        return redirect()->route('debt.dashboard')->with('success', implode(' ', $messages));
    }
}
