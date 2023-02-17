<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Elliptic\EC;
use kornrunner\Keccak;

class MetamaskAuthController extends Controller
{
    public function authenticate(Request $request): RedirectResponse {
        $nonce = session()->get('metamask-nonce');
        $message = $this->getSignatureMessage($nonce);

        if(empty($request->eth_address) ||
            (!$this->verifySignature($message, $request->signature, $request->eth_address)) ||
            (!$user = User::query()->where('eth_address', $request->eth_address)->first())
        ){
            throw ValidationException::withMessages([
                'error' => trans('auth.failed'),
            ]);
        }
        Auth::login($user);
        $request->session()->regenerate();
        return redirect()->intended(RouteServiceProvider::HOME);
    }

    public function signature(Request $request) {
        $code = \Str::random(8);

        session()->put('metamask-nonce', $code);

        return $this->getSignatureMessage($code);
    }

    private function getSignatureMessage($code)
    {
        return __("I have read and accept the terms and conditions.\nPlease sign me in.\n\nSecurity code (you can ignore this): :nonce", [
            'nonce' => $code
        ]);
    }

    protected function verifySignature($message, $signature, $address): bool
    {
        $msglen = strlen($message);
        $hash   = Keccak::hash("\x19Ethereum Signed Message:\n{$msglen}{$message}", 256);
        $sign   = ["r" => substr($signature, 2, 64),
            "s" => substr($signature, 66, 64)];
        $recid  = ord(hex2bin(substr($signature, 130, 2))) - 27;
        if ($recid != ($recid & 1))
            return false;

        $ec = new EC('secp256k1');
        $pubkey = $ec->recoverPubKey($hash, $sign, $recid);
        $derived_address = "0x" . substr(Keccak::hash(substr(hex2bin($pubkey->encode("hex")), 1), 256), 24);

        return $address == $derived_address;
    }
}
