<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdatePasswordRequest;
use App\Models\SiteProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function edit(Request $request): View
    {
        return view('admin.account.edit', [
            'account' => $request->user(),
            'profile' => SiteProfile::current(),
            'hasDefaultCv' => is_file(public_path('Tian-Maysa-CV.pdf')),
        ]);
    }

    public function update(UpdatePasswordRequest $request): RedirectResponse
    {
        $request->user()->forceFill([
            'password' => $request->validated('password'),
            'remember_token' => null,
        ])->save();

        $request->session()->regenerate(true);

        return redirect()->route('admin.account.edit')->with('status', 'Kata sandi berhasil diubah.');
    }
}
