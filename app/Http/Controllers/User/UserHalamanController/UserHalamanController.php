<?php

namespace App\Http\Controllers\User\UserHalamanController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserHalamanController extends Controller
{
    public function Index()
    {
        return view('User.user-halaman-utama.UserHalamanUtama');
    }
}
