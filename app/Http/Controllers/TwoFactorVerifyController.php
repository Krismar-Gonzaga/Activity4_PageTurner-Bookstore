<?php
// app/Http/Controllers/TwoFactorVerifyController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\TwoFactorOTP;

class TwoFactorVerifyController extends Controller
{
    protected $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    public function showVerifyForm()
    {
        $user = Auth::user();
        
        if (!$user || !$user->two_factor_enabled) {
            return redirect()->route('dashboard');
        }

        // If using email 2FA, send OTP
        if ($user->two_factor_type === 'email') {
            $this->sendEmailOTP($user);
        }

        return view('auth.two-factor-verify');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string'
        ]);

        $user = Auth::user();
        $twoFactor = $user->twoFactorAuthentication;

        if (!$twoFactor || !$twoFactor->enabled) {
            return redirect()->route('dashboard');
        }

        // Check if it's a recovery code
        if ($twoFactor->recovery_codes && in_array($request->code, $twoFactor->recovery_codes)) {
            // Remove used recovery code
            $codes = array_diff($twoFactor->recovery_codes, [$request->code]);
            $twoFactor->update(['recovery_codes' => array_values($codes)]);
            
            session(['two_factor_authenticated' => true]);
            return redirect()->intended('dashboard');
        }

        // Verify based on type
        $valid = false;
        
        if ($twoFactor->type === 'app') {
            $valid = $this->google2fa->verifyKey($twoFactor->secret, $request->code);
        } elseif ($twoFactor->type === 'email') {
            $valid = $this->verifyEmailOTP($request->code, $user);
        }

        if ($valid) {
            session(['two_factor_authenticated' => true]);
            return redirect()->intended('dashboard');
        }

        return back()->with('error', 'Invalid verification code.');
    }

    public function resend()
    {
        $user = Auth::user();
        
        if ($user && $user->two_factor_type === 'email') {
            $this->sendEmailOTP($user);
            return back()->with('success', 'New OTP code sent to your email.');
        }

        return back()->with('error', 'Unable to resend code.');
    }

    private function sendEmailOTP($user)
    {
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        session(['2fa_email_otp' => [
            'code' => Hash::make($otp),
            'expires_at' => now()->addMinutes(10)
        ]]);

        Mail::to($user->email)->send(new TwoFactorOTP($otp));
    }

    private function verifyEmailOTP($code, $user)
    {
        $sessionOtp = session('2fa_email_otp');
        
        if (!$sessionOtp || now()->gt($sessionOtp['expires_at'])) {
            return false;
        }

        return Hash::check($code, $sessionOtp['code']);
    }
}