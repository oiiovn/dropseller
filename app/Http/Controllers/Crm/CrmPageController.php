<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CrmPageController extends Controller
{
    public function __invoke(Request $request)
    {
        if (trim($request->path(), '/') === 'crm' && $request->user()?->hasCrmRole('admin')) {
            return redirect('/admin');
        }

        return view('crm.index');
    }
}
