<?php

namespace App\Http\Controllers\Admin\AdminHalamanController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminHalamanController extends Controller
{
    public function Index()
    {
        return view('Admin.admin-halaman-utama.AdminHalamanUtama');
    }
}
