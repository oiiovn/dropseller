<?php

namespace App\Exports;

use App\Models\OrderTiktok;
use Maatwebsite\Excel\Concerns\FromCollection;

class OrderTiktokExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return OrderTiktok::all();
    }
}
