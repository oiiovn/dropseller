<?php

namespace App\Jobs;

use App\Models\CheckSo;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use DOMDocument;
use DOMXPath;

class CheckPhoneJob implements ShouldQueue
{

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $recordId;

    public function __construct($recordId)
    {
        $this->recordId = $recordId;
    }

    public function handle(): void
    {
        $record = CheckSo::find($this->recordId);

        if (!$record) {
            Log::warning("❌ Không tìm thấy bản ghi ID: {$this->recordId}");
            return;
        }

        $username = $record->username;

        // Gửi request GET để lấy bảng kết quả
        $response = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36',
        ])->withCookies([
            'user_login' => '32f4744fe9893659c458b50aa4c8c34dd97478e0877bcf00e8d8430144df871d'
        ], 'hawksocia.com')->get('https://hawksocia.com/client/store-fanpage-orders');

        if (!$response->successful()) {
            Log::error("❌ Không lấy được HTML cho {$username}");
            $record->update(['status' => 'fail']);
            return;
        }

        $html = $response->body();
        file_put_contents(storage_path('logs/checkfanpage.html'), $html); // để kiểm tra nếu cần

        // Phân tích HTML bằng DOM
        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->loadHTML($html);
        $xpath = new DOMXPath($dom);

        // Lấy tất cả dòng <tr> trong bảng
        $rows = $xpath->query('//table//tr');

        foreach ($rows as $row) {
            $cells = $row->getElementsByTagName('td');
            if ($cells->length < 4) continue;

            $tdUsername = trim($cells->item(1)->textContent);
            if (strtolower($tdUsername) !== strtolower($username)) continue;

            // ✅ Tách status từ thẻ <strong> bên trong <td>
            $statusNode = (new DOMXPath($row->ownerDocument))->query('.//td[3]//strong', $row);
            $status = $statusNode->length > 0 ? strtolower(trim($statusNode->item(0)->textContent)) : 'unknown';

            // ✅ Tách số điện thoại từ cuối cột 4
            $rawPhone = trim($cells->item(3)->textContent);
            preg_match('/\d{8,15}/', $rawPhone, $phoneMatch);
            $phone = $phoneMatch[0] ?? null;

            $record->update([
                'phone' => $phone,
                'phone' => $status,
                'exists' => $status === 'success',

            ]);

            Log::info("✅ Đã lưu đúng: {$username} | {$phone} | {$status}");
            return;
        }





        Log::warning("❌ Không tìm thấy dòng chứa {$username}");
        $record->update(['status' => 'fail']);
    }
}
