<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    public function change(Request $request)
    {
        Session::put('admin_language', $request->language);
        app()->setLocale($request->language);

        return redirect()->back();
    }
}
