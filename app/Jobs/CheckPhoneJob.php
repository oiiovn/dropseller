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
    libxml_use_internal_errors(true);
    $dom = new DOMDocument();
    $dom->loadHTML($html);
    $xpath = new DOMXPath($dom);

    $rows = $xpath->query('//table//tr');
    $found = false;

    foreach ($rows as $row) {
        if (!($row instanceof \DOMElement)) continue;

        $cells = $row->getElementsByTagName('td');
        if ($cells->length < 4) continue;

        $tdUsername = trim($cells->item(1)->textContent);
        if (strtolower($tdUsername) !== strtolower($username)) continue;

        // Đã tìm thấy dòng phù hợp
        $found = true;

        $statusNode = (new DOMXPath($row->ownerDocument))->query('.//td[3]//strong', $row);
        $statusRaw = $statusNode->length > 0 ? trim($statusNode->item(0)->textContent) : 'unknown';
        $status = trim($statusRaw);

        if (ctype_digit($status) && strlen($status) >= 8 && strlen($status) <= 15) {
            $record->update([
                'phone' => $status,
                'status' => 'success',
                'exists' => 1,
            ]);
            Log::info("✅ Đã lưu số điện thoại: {$record->username} | {$status}");
        } else {
            $record->update([
                'phone' => $status,
                'status' => 'fail',
                'exists' => 0,
            ]);
            Log::info("ℹ️ Không phải số điện thoại: {$record->username} | {$status}");
        }

        break; // ✅ chỉ xử lý dòng đầu tiên khớp username
    }

    if (!$found) {
        $record->update([
            'status' => 'fail',
            'phone' => $status,
            'exists' => 0,
        ]);
        file_put_contents(storage_path("logs/fanpage_{$username}.html"), $html);
        Log::warning("❌ Không tìm thấy dòng chứa {$username}");
    }
}

}
