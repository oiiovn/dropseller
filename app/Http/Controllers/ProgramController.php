<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use App\Models\Shop;
use App\Models\Program;
use App\Models\ProgramShop;

class ProgramController extends Controller
{
    function program()
    {
        $shops = Shop::where('platform', 'Tiktok')->get();
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

        return redirect()->back()->with('success', 'Chương trình đã được tạo thành công!');
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
        $registered = ProgramShop::whereIn('shop_id', $userShopIds)->get()->groupBy('program_id');
        $allShops = Shop::whereIn('shop_id', $userShopIds)->get()->keyBy('shop_id');
        $programs = [];
        $programShops = [];
    
        $registeredPrograms = [];
        $registeredProgramShops = [];
    
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
    
            if ($hasRegistered) {
                $registeredPrograms[] = $program;
                $registeredProgramShops[$program->id] = $shopsForUser;
            }
        }
    
        return view('program.list_program', [
            'programs' => $programs,
            'programShops' => $programShops,
            'registeredPrograms' => $registeredPrograms,
            'registeredProgramShops' => $registeredProgramShops,
        ]);
    }
    



    public function createProgramShop(Request $request)
    {
        $user = Auth::user();
        $programId = $request['program_id'];
        $inserted = 0;
        $skipped = 0;
        $program = Program::findOrFail($programId);
    
        $products = json_decode($program->products, true);
        $productCount = is_array($products) ? count($products) : 0;
    
        $price_per_product = 2000; // bạn có thể đặt biến cấu hình nếu sau này thay đổi
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
            $message = "Đã thêm Shop vào chương trình đăng sản phẩm.";
        }
        if ($skipped > 0) {
            $message = " {$skipped} Shop của bạn đã đăng kí chương trình này vui lòng không đăng kí lại.";
        }

        return redirect()->back()->with('success', $message);
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
}
