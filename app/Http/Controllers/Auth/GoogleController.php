<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use GuzzleHttp\Client;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->setHttpClient(new Client(['verify' => false]))
            ->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')
                ->setHttpClient(new Client(['verify' => false]))
                ->user();
            
            Log::info('Google User Data:', [
                'id' => $googleUser->id,
                'email' => $googleUser->email,
                'name' => $googleUser->name
            ]);

            $user = User::where('google_id', $googleUser->id)->first();

            if (!$user) {
                $existingUser = User::where('email', $googleUser->email)->first();
                
                if ($existingUser) {
                    $existingUser->update([
                        'google_id' => $googleUser->id,
                        'avatar' => $googleUser->avatar
                    ]);
                    $user = $existingUser;
                } else {
                    $user = User::create([
                        'name' => $googleUser->name,
                        'email' => $googleUser->email,
                        'google_id' => $googleUser->id,
                        'avatar' => $googleUser->avatar,
                        'role_id' => 2,
                        'password' => bcrypt(str()->random(16))
                    ]);
                }
            }

            if ($user->role->name === 'admin') {
                return redirect()->route('login')
                    ->withErrors(['email' => 'Akun Google ini terdaftar sebagai admin. Silakan gunakan login admin.']);
            }

            Auth::login($user);
            return redirect()->route('halaman-utama');

        } catch (Exception $e) {
            Log::error('Google Login Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('login')
                ->withErrors(['email' => 'Terjadi kesalahan saat login dengan Google. Error: ' . $e->getMessage()]);
        }
    }
} 