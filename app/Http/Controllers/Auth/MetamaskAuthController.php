<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class MetamaskAuthController extends Controller
{
    public function authenticate(Request $request): RedirectResponse {
        if(empty($request->eth_address) || (!$user = User::query()->where('eth_address', $request->eth_address)->first())){
            throw ValidationException::withMessages([
                'metamask' => trans('auth.failed'),
            ]);
        }
        Auth::login($user);
        $request->session()->regenerate();
        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /*public function signature(Request $request) {
        // Generate some random nonce
        $code = \Str::random(8);

        // Save in session
        session()->put('web3-nonce', $code);

        // Create message with nonce
        return $this->getSignatureMessage($code);
    }

    private function getSignatureMessage($code)
    {
        return __("I have read and accept the terms and conditions.\nPlease sign me in.\n\nSecurity code (you can ignore this): :nonce", [
            'nonce' => $code
        ]);
    }*/
}
