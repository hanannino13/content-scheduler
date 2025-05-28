<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Platform;
use Illuminate\Http\Request;

class PlatformController extends Controller
{
    public function index()
    {
        $platforms = Platform::all();
        return view('settings.platforms', compact('platforms'));
    }
}