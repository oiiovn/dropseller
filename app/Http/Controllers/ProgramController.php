<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

use App\Models\Shop;
use App\Models\Program;
use App\Models\ProgramShop;
use App\Models\Transaction;

class ProgramController extends Controller
{
    function program()
    {
        $shops = Shop::all();
        return view('program.program', compact('shops'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_program' => 'required|string|max:255',
            'description' => 'nullable|string',
            'shop_list' => 'required|array',
            'sku' => 'required|array',
            'name' => 'required|array',
            'image' => 'required|array',
        ]);

        $products = [];
        foreach ($validated['sku'] as $index => $sku) {
            if (!empty($sku)) {
                $products[] = [
                    'sku' => $sku,
                    'name' => $validated['name'][$index] ?? 'Không có tên',
                    'image' => $validated['image'][$index] ?? null,
                ];
            }
        }

        $program = new Program();
        $program->name_program = $validated['name_program'];
        $program->products = json_encode($products);
        $program->shops = $validated['shop_list'];
        $program->description = $validated['description'];
        $program->created_by = auth()->id();
        $program->updated_by = auth()->id();
        $program->save();

        function generateTransactionCode()
        {
            do {
                $random = 'FT' . str_pad(mt_rand(0, 99999999999999), 14, '0', STR_PAD_LEFT);
            } while (Transaction::where('transaction_id', $random)->exists());

            return $random;
        }
        $user = Auth::user();

        // Create a transaction
        $newTransactionId = DB::table('transactions')->max('id') + 1;
        $productCount = count(array_filter($request->sku));
        $amount = $productCount * 5000;

        Transaction::create([
            'id' => $newTransactionId,
            'bank' => 'MBB',
            'account_number' => $user->referral_code,
            'transaction_date' => now(),
            'transaction_id' => generateTransactionCode(),
            'amount' => $amount,
            'type' => 'IN',
            'description' => 'V9999 ' . $program->name_program,
        ]);

        // Tự động đăng ký gói cho các shop có trong danh sách gói
        $this->autoRegisterProgramForUser($program, $user);

        // Lấy số shop đã đăng ký để hiển thị thông báo
        $registeredShops = ProgramShop::where('program_id', $program->id)->count();

        return redirect()->back()->with('success', 
            "Chương trình đã được tạo thành công! " .
            "Đã tự động đăng ký cho {$registeredShops} shop được chọn. " .
            "Gói sẽ được thực hiện tự động trong vài giây tới."
        );
    }

    public function push_product($sku)
    {
        $cacheKey = "product_$sku";
        if (Cache::has($cacheKey)) {
            return response()->json(Cache::get($cacheKey));
        }
        $apiUrl = "https://salework.net/api/open/stock/v1/product/list";
        $clientId = "1605";
        $token = "+AXBRK19RPa6MG5wxYOhD7BPUGgibb76FnxirVzkW/9FMf9nSmJIg9OINUDk8X5L";
        $response = $this->sendApiRequest_push_product($apiUrl, $clientId, $token);
        if (!$response) {
            return response()->json(['error' => 'Không thể kết nối tới API']);
        }
        $data = json_decode($response, true);
        if (!isset($data['data']['products'])) {
            return response()->json(['error' => 'API không trả về danh sách sản phẩm']);
        }
        $products = $data['data']['products'];
        if (isset($products[$sku])) {
            Cache::put($cacheKey, $products[$sku], now()->addMinutes(10));
            return response()->json($products[$sku]);
        }
        return response()->json(['error' => 'Không tìm thấy sản phẩm với SKU: ' . $sku]);
    }

    public function list_program()
    {
        $user = Auth::user();
        $userShopIds = Shop::where('user_id', $user->id)->pluck('shop_id')->toArray();
        $programs_all = Program::where(function ($query) use ($userShopIds) {
            foreach ($userShopIds as $shopId) {
                $query->orWhereJsonContains('shops', $shopId);
            }
        })->get();
        $programs_shop_onl = ProgramShop::with('program', 'shop')
            ->where(function ($query) use ($userShopIds) {
                foreach ($userShopIds as $shopId) {
                    $query->orWhere('shop_id', $shopId);
                }
            })
            ->get();
        $registered = ProgramShop::whereIn('shop_id', $userShopIds)->get()->groupBy('program_id');
        $allShops = Shop::whereIn('shop_id', $userShopIds)->get()->keyBy('shop_id');
        $programs = [];
        $programShops = [];
        foreach ($programs_all as $program) {
            $shopIds = $program->shops ?? [];
            $shopsForUser = [];
            $hasUnregistered = false;
            $hasRegistered = false;
            foreach ($shopIds as $shopId) {
                if (isset($allShops[$shopId])) {
                    $isRegistered = isset($registered[$program->id]) &&
                        $registered[$program->id]->contains('shop_id', $shopId);
                    $shopsForUser[] = [
                        'shop_id' => $shopId,
                        'shop_name' => $allShops[$shopId]->shop_name,
                        'is_registered' => $isRegistered,
                    ];
                    if ($isRegistered) {
                        $hasRegistered = true;
                    } else {
                        $hasUnregistered = true;
                    }
                }
            }
            if ($hasUnregistered) {
                $programs[] = $program;
                $programShops[$program->id] = $shopsForUser;
            }
        }
        //   return response()->json($programs_shop_onl);
        return view('program.list_program', [
            'programs' => $programs,
            'programShops' => $programShops,
            'programs_shop_onl' => $programs_shop_onl,
        ]);
    }
    public function Program_processing()
    {
        $Programs_list = ProgramShop::with('program', 'shop')->orderByDesc('created_at')->get();
        // return response()->json($Programs_list);
        return view('program.program_processing', compact('Programs_list'));
    }
    public function changeStatus_Program(Request $request, $id)
    {
        $user = Auth::user();
        $program = ProgramShop::findOrFail($id);
        $program->status_program = $request->status_program;
        $program->confirmer = $user->id;
        $program->save();
        return redirect()->back()->with('success', 'Đã cập nhật trạng thái!');
    }
    // shop đăng kí chương trình
    public function createProgramShop(Request $request)
    {
        $programId = $request['program_id'];
        $inserted = 0;
        $skipped = 0;
        $program = Program::findOrFail($programId);
        $products = json_decode($program->products, true);
        $productCount = is_array($products) ? count($products) : 0;
        $price_per_product = 2000;
        $total_payment_per_shop = $productCount * $price_per_product;
        foreach ($request->selected_shops as $shopId) {
            $exists = ProgramShop::where('shop_id', $shopId)
                ->where('program_id', $programId)
                ->exists();
            if (!$exists) {
                ProgramShop::create([
                    'shop_id' => $shopId,
                    'program_id' => $programId,
                    'total_payment' => $total_payment_per_shop,
                    'status_program' => 'Chưa triển khai',
                    'status_payment' => 'Chưa thanh toán',
                    'payment_code' => null,
                    'confirmer' => null,
                ]);
                $inserted++;
            } else {
                $skipped++;
            }
        }
        if ($inserted > 0) {
            $message = "Đã đăng ký chương trình thành ";
        } elseif ($skipped > 0) {
            $message = "Bạn đã đăng ký cho shop chương trình này trước đó.";
        } else {
            $message = "Không có shop nào được xử lý.";
        }
        return response()->json(['message' => $message]);
    }
    private function sendApiRequest_push_product($url, $clientId, $token)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "client-id: $clientId",
            "token: $token"
        ]);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            return json_encode(['error' => "Lỗi cURL: $error_msg"]);
        }
        curl_close($ch);
        return $response;
    }

    public function affiliatePage()
    {
        return view('affiliate.affiliate');
    }

    /**
     * API để xem trạng thái đăng ký gói của tất cả shop
     */
    public function getProgramRegistrationStatus($programId)
    {
        $program = Program::findOrFail($programId);
        $programShops = ProgramShop::where('program_id', $programId)
            ->with('shop')
            ->get();

        $status = [
            'program' => [
                'id' => $program->id,
                'name' => $program->name_program,
                'products' => json_decode($program->products, true),
                'created_at' => $program->created_at
            ],
            'registrations' => [],
            'summary' => [
                'total_shops' => $programShops->count(),
                'chua_trien_khai' => $programShops->where('status_program', 'Chưa triển khai')->count(),
                'dang_trien_khai' => $programShops->where('status_program', 'Đang triển khai')->count(),
                'da_trien_khai' => $programShops->where('status_program', 'Đã triển khai')->count(),
                'total_payment' => $programShops->sum('total_payment')
            ]
        ];

        foreach ($programShops as $ps) {
            $status['registrations'][] = [
                'shop_id' => $ps->shop_id,
                'shop_name' => $ps->shop ? $ps->shop->shop_name : 'Unknown',
                'status_program' => $ps->status_program,
                'status_payment' => $ps->status_payment,
                'total_payment' => $ps->total_payment,
                'registered_at' => $ps->created_at,
                'confirmer' => $ps->confirmer
            ];
        }

        return response()->json($status);
    }

    /**
     * API để xem trạng thái thanh toán của gói
     */
    public function getProgramPaymentStatus($programId)
    {
        $program = Program::findOrFail($programId);
        $programShops = ProgramShop::where('program_id', $programId)
            ->with('shop')
            ->get();

        $paymentStatus = [
            'program' => [
                'id' => $program->id,
                'name' => $program->name_program,
                'created_at' => $program->created_at
            ],
            'payment_summary' => [
                'total_shops' => $programShops->count(),
                'chua_thanh_toan' => $programShops->where('status_payment', 'Chưa thanh toán')->count(),
                'da_thanh_toan' => $programShops->where('status_payment', 'Đã thanh toán')->count(),
                'total_amount_collected' => $programShops->where('status_payment', 'Đã thanh toán')->sum('total_payment'),
                'total_amount_pending' => $programShops->where('status_payment', 'Chưa thanh toán')->sum('total_payment')
            ],
            'shop_payments' => []
        ];

        foreach ($programShops as $ps) {
            $shop = $ps->shop;
            $shopOwner = $shop ? $shop->user : null;
            
            // Kiểm tra giao dịch trừ tiền
            $deductTransaction = null;
            if ($ps->status_payment === 'Đã thanh toán') {
                $deductTransaction = Transaction::where('account_number', $shopOwner ? $shopOwner->referral_code : '')
                    ->where('type', 'OUT')
                    ->where('description', 'like', '%' . $program->name_program . '%')
                    ->where('amount', $ps->total_payment)
                    ->first();
            }

            $paymentStatus['shop_payments'][] = [
                'shop_id' => $ps->shop_id,
                'shop_name' => $shop ? $shop->shop_name : 'Unknown',
                'shop_owner' => $shopOwner ? $shopOwner->name : 'Unknown',
                'status_program' => $ps->status_program,
                'status_payment' => $ps->status_payment,
                'total_payment' => $ps->total_payment,
                'payment_code' => $ps->payment_code,
                'payment_date' => $deductTransaction ? $deductTransaction->created_at : null,
                'transaction_id' => $deductTransaction ? $deductTransaction->id : null,
                'is_paid' => $ps->status_payment === 'Đã thanh toán'
            ];
        }

        return response()->json($paymentStatus);
    }

    /**
     * Tự động đăng ký gói cho các shop có trong danh sách gói
     */
    private function autoRegisterProgramForUser($program, $user)
    {
        // Lấy danh sách shop IDs từ gói
        $programShopIds = $program->shops ?? [];
        
        if (empty($programShopIds)) {
            \Log::info('Gói không có shop nào để đăng ký', [
                'user_id' => $user->id,
                'program_id' => $program->id
            ]);
            return;
        }

        // Lấy thông tin shop từ database
        $shops = Shop::whereIn('shop_id', $programShopIds)->get();
        
        if ($shops->isEmpty()) {
            \Log::info('Không tìm thấy shop nào trong danh sách gói', [
                'user_id' => $user->id,
                'program_id' => $program->id,
                'program_shop_ids' => $programShopIds
            ]);
            return;
        }

        $products = json_decode($program->products, true);
        $productCount = is_array($products) ? count($products) : 0;
        $price_per_product = 2000;
        $total_payment_per_shop = $productCount * $price_per_product;

        $registeredCount = 0;
        $skippedCount = 0;

        foreach ($shops as $shop) {
            // Kiểm tra xem shop đã đăng ký gói này chưa
            $exists = ProgramShop::where('shop_id', $shop->shop_id)
                ->where('program_id', $program->id)
                ->exists();

            if (!$exists) {
                $programShop = ProgramShop::create([
                    'shop_id' => $shop->shop_id,
                    'program_id' => $program->id,
                    'total_payment' => $total_payment_per_shop,
                    'status_program' => 'Chưa triển khai',
                    'status_payment' => 'Chưa thanh toán',
                    'payment_code' => null,
                    'confirmer' => null,
                ]);

                $registeredCount++;

                // Tự động chuyển sang trạng thái "Thực hiện" sau 1 giây
                $this->scheduleAutoExecute($programShop);

                \Log::info('Đã đăng ký gói cho shop', [
                    'shop_id' => $shop->shop_id,
                    'shop_name' => $shop->shop_name,
                    'program_id' => $program->id,
                    'total_payment' => $total_payment_per_shop
                ]);
            } else {
                $skippedCount++;
                \Log::info('Shop đã đăng ký gói này rồi', [
                    'shop_id' => $shop->shop_id,
                    'shop_name' => $shop->shop_name,
                    'program_id' => $program->id
                ]);
            }
        }

        \Log::info('Hoàn thành đăng ký gói tự động', [
            'user_id' => $user->id,
            'program_id' => $program->id,
            'program_shop_ids' => $programShopIds,
            'total_shops' => $shops->count(),
            'registered' => $registeredCount,
            'skipped' => $skippedCount
        ]);
    }

    /**
     * Lên lịch tự động thực hiện gói
     */
    private function scheduleAutoExecute($programShop)
    {
        // Sử dụng Laravel's dispatch để chạy job sau 1 giây
        \App\Jobs\AutoExecuteProgram::dispatch($programShop->id)->delay(now()->addSeconds(1));
    }

    /**
     * Tự động thực hiện gói (được gọi bởi job)
     */
    public function autoExecuteProgram($programShopId)
    {
        $programShop = ProgramShop::findOrFail($programShopId);
        
        // Chuyển sang trạng thái "Thực hiện"
        $programShop->status_program = 'Đang triển khai';
        $programShop->confirmer = 32; // Admin ID (CEO)
        $programShop->save();

        // Tự động chuyển sang "Hoàn thành" sau 2 giây
        \App\Jobs\AutoCompleteProgram::dispatch($programShop->id)->delay(now()->addSeconds(2));
        
        return ['status' => 'success', 'message' => 'Gói đã được thực hiện'];
    }

    /**
     * Tự động hoàn thành gói (được gọi bởi job)
     */
    public function autoCompleteProgram($programShopId)
    {
        $programShop = ProgramShop::findOrFail($programShopId);
        
        // Tạo mã thanh toán tự động: FT + 14 chữ số ngẫu nhiên (không trùng)
        $paymentCode = $this->generateUniquePaymentCode();
        
        // Chuyển sang trạng thái "Hoàn thành"
        $programShop->status_program = 'Đã triển khai';
        $programShop->status_payment = 'Đã thanh toán';
        $programShop->payment_code = $paymentCode;
        $programShop->save();

        \Log::info('Đã tạo mã thanh toán tự động', [
            'program_shop_id' => $programShopId,
            'payment_code' => $paymentCode,
            'shop_id' => $programShop->shop_id
        ]);

        // Tự động trừ tiền shop owner
        $this->autoDeductShopOwnerPayment($programShop);
        
        return ['status' => 'success', 'message' => 'Gói đã được hoàn thành'];
    }

    /**
     * Tự động trừ tiền shop owner - CHỈ KHI GÓI ĐƯỢC HOÀN THÀNH
     */
    private function autoDeductShopOwnerPayment($programShop)
    {
        $shop = $programShop->shop;
        if (!$shop) {
            \Log::warning('Không tìm thấy shop để trừ tiền', [
                'program_shop_id' => $programShop->id
            ]);
            return;
        }

        $shopOwner = $shop->user;
        if (!$shopOwner) {
            \Log::warning('Không tìm thấy shop owner để trừ tiền', [
                'program_shop_id' => $programShop->id,
                'shop_id' => $shop->shop_id
            ]);
            return;
        }

        // Kiểm tra xem đã trừ tiền chưa để tránh trừ 2 lần
        $existingTransaction = Transaction::where('description', 'like', '%' . $programShop->program->name_program . '%')
            ->where('account_number', $shopOwner->referral_code)
            ->where('type', 'OUT')
            ->where('amount', $programShop->total_payment)
            ->first();

        if ($existingTransaction) {
            \Log::info('Đã trừ tiền cho shop owner rồi', [
                'program_shop_id' => $programShop->id,
                'shop_owner' => $shopOwner->name,
                'existing_transaction_id' => $existingTransaction->id
            ]);
            return;
        }

        // Tạo giao dịch trừ tiền
        $newTransactionId = DB::table('transactions')->max('id') + 1;
        
        $transaction = Transaction::create([
            'id' => $newTransactionId,
            'bank' => 'PSP',
            'account_number' => $shopOwner->referral_code,
            'transaction_date' => now(),
            'transaction_id' => $programShop->payment_code, // Sử dụng mã thanh toán FT
            'amount' => $programShop->total_payment,
            'type' => 'OUT',
            'description' => 'Phí đăng sản phẩm - ' . $programShop->program->name_program,
        ]);

        \Log::info('Đã trừ tiền shop owner khi gói hoàn thành', [
            'program_shop_id' => $programShop->id,
            'shop_owner' => $shopOwner->name,
            'shop_name' => $shop->shop_name,
            'amount' => $programShop->total_payment,
            'payment_code' => $programShop->payment_code,
            'transaction_id' => $transaction->id,
            'transaction_code' => $transaction->transaction_id
        ]);

        // Tạo thông báo cho shop owner
        \App\Models\Notification::create([
            'user_id' => $shopOwner->id,
            'shop_id' => $shop->shop_id,
            'title' => 'Gói đăng sản phẩm đã hoàn thành',
            'message' => 'Gói "' . $programShop->program->name_program . '" đã được hoàn thành. Phí ' . number_format($programShop->total_payment) . ' VNĐ đã được trừ từ tài khoản của bạn. Mã thanh toán: ' . $programShop->payment_code,
            'is_read' => 0,
        ]);

        \Log::info('Đã tạo thông báo cho shop owner', [
            'shop_owner' => $shopOwner->name,
            'program_name' => $programShop->program->name_program,
            'amount' => $programShop->total_payment
        ]);
    }

    /**
     * Tạo mã thanh toán duy nhất: FT + 14 chữ số ngẫu nhiên
     */
    private function generateUniquePaymentCode()
    {
        $maxAttempts = 100; // Giới hạn số lần thử để tránh vòng lặp vô hạn
        $attempts = 0;
        
        do {
            $paymentCode = 'FT' . str_pad(rand(0, 99999999999999), 14, '0', STR_PAD_LEFT);
            $attempts++;
            
            if ($attempts >= $maxAttempts) {
                \Log::error('Không thể tạo mã thanh toán duy nhất sau ' . $maxAttempts . ' lần thử');
                throw new \Exception('Không thể tạo mã thanh toán duy nhất');
            }
        } while (ProgramShop::where('payment_code', $paymentCode)->exists());
        
        \Log::info('Đã tạo mã thanh toán duy nhất', [
            'payment_code' => $paymentCode,
            'attempts' => $attempts
        ]);
        
        return $paymentCode;
    }

}
