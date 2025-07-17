<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    protected $prodiOptions = [
        'D3 - Teknik Informatika',
        'D3 - Teknik Pendingin dan Tata Udara',
        'D4 - Rekayasa Perangkat Lunak',
    ];

    protected $prodiAbbreviations = [
        'D3 - Teknik Informatika' => 'D3-TI',
        'D3 - Teknik Pendingin dan Tata Udara' => 'D3-TPTU',
        'D4 - Rekayasa Perangkat Lunak' => 'D4-RPL',
    ];

    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
            'prodiOptions' => $this->prodiOptions,
            'prodiAbbreviations' => $this->prodiAbbreviations,
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validatedData = $request->validated();

        $updateData = [
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
        ];

        if ($user->hasRole('mahasiswa')) {
            $prodiPrefix = $this->prodiAbbreviations[$validatedData['prodi']] ?? 'PRODI';
            $kelasDetailUpper = strtoupper($validatedData['kelas_detail']);
            $kelasString = "{$prodiPrefix}-{$kelasDetailUpper}";
            
            $updateData['nim'] = $validatedData['nim'];
            $updateData['prodi'] = $validatedData['prodi'];
            $updateData['angkatan'] = $validatedData['angkatan'];
            $updateData['kelas'] = $kelasString;
        }

        $user->fill($updateData);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        Auth::logout();
        $user->delete();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}