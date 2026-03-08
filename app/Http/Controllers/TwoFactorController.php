<?php
// app/Http/Controllers/TwoFactorController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\TwoFactorAuthentication;
use App\Mail\TwoFactorOTP;
use Illuminate\Support\Str;

class TwoFactorController extends Controller
{
    protected $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    public function index()
    {
        $user = Auth::user();
        $twoFactor = $user->twoFactorAuthentication;
        
        return view('profile.two-factor', compact('user', 'twoFactor'));
    }

    public function setup(Request $request)
    {
        $user = Auth::user();
        $type = $request->type; // 'app' or 'email'

        if ($type === 'app') {
            // Generate secret key
            $secret = $this->google2fa->generateSecretKey();
            
            // Generate QR code URL
            $qrCodeUrl = $this->google2fa->getQRCodeUrl(
                config('app.name'),
                $user->email,
                $secret
            );

            // Store temporarily in session
            session(['2fa_setup' => [
                'type' => 'app',
                'secret' => $secret,
                'qr_code' => $qrCodeUrl
            ]]);

            return view('profile.two-factor-setup-app', compact('secret', 'qrCodeUrl'));
        } 
        elseif ($type === 'email') {
            // Generate and send OTP
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            
            session(['2fa_setup' => [
                'type' => 'email',
                'otp' => Hash::make($otp),
                'expires_at' => now()->addMinutes(10)
            ]]);

            // Send OTP via email
            Mail::to($user->email)->send(new TwoFactorOTP($otp));

            return view('profile.two-factor-setup-email');
        }

        return redirect()->back()->with('error', 'Invalid 2FA type selected.');
    }

    public function verifySetup(Request $request)
    {
        $user = Auth::user();
        $setup = session('2fa_setup');

        if (!$setup) {
            return redirect()->route('profile.two-factor')->with('error', 'Setup session expired.');
        }

        if ($setup['type'] === 'app') {
            $request->validate([
                'code' => 'required|string|size:6'
            ]);

            $valid = $this->google2fa->verifyKey($setup['secret'], $request->code);

            if (!$valid) {
                return back()->with('error', 'Invalid verification code.');
            }

            // Generate recovery codes
            $recoveryCodes = $this->generateRecoveryCodes();

            // Save to database
            $twoFactor = TwoFactorAuthentication::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'type' => 'app',
                    'secret' => $setup['secret'],
                    'recovery_codes' => $recoveryCodes,
                    'verified_at' => now(),
                    'enabled' => true
                ]
            );

            $user->update([
                'two_factor_enabled' => true,
                'two_factor_type' => 'app'
            ]);

            session()->forget('2fa_setup');

            return view('profile.two-factor-recovery-codes', compact('recoveryCodes'));
        } 
        elseif ($setup['type'] === 'email') {
            $request->validate([
                'code' => 'required|string|size:6'
            ]);

            if (now()->gt($setup['expires_at'])) {
                return back()->with('error', 'OTP has expired. Please request a new one.');
            }

            if (!Hash::check($request->code, $setup['otp'])) {
                return back()->with('error', 'Invalid OTP code.');
            }

            // Generate recovery codes
            $recoveryCodes = $this->generateRecoveryCodes();

            // Save to database
            $twoFactor = TwoFactorAuthentication::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'type' => 'email',
                    'secret' => null,
                    'recovery_codes' => $recoveryCodes,
                    'verified_at' => now(),
                    'enabled' => true
                ]
            );

            $user->update([
                'two_factor_enabled' => true,
                'two_factor_type' => 'email'
            ]);

            session()->forget('2fa_setup');

            return view('profile.two-factor-recovery-codes', compact('recoveryCodes'));
        }

        return redirect()->back()->with('error', 'Invalid setup type.');
    }

    public function disable(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'password' => 'required|current_password'
        ]);

        // Delete 2FA record
        $user->twoFactorAuthentication()->delete();

        // Update user
        $user->update([
            'two_factor_enabled' => false,
            'two_factor_type' => null
        ]);

        return redirect()->route('profile.two-factor')->with('success', 'Two-factor authentication disabled successfully.');
    }

    public function showRecoveryCodes()
    {
        $user = Auth::user();
        $twoFactor = $user->twoFactorAuthentication;

        if (!$twoFactor || !$twoFactor->enabled) {
            return redirect()->route('profile.two-factor')->with('error', '2FA is not enabled.');
        }

        $recoveryCodes = $twoFactor->recovery_codes;

        return view('profile.two-factor-recovery-codes', compact('recoveryCodes'));
    }

    public function regenerateRecoveryCodes()
    {
        $user = Auth::user();
        $twoFactor = $user->twoFactorAuthentication;

        if (!$twoFactor || !$twoFactor->enabled) {
            return redirect()->route('profile.two-factor')->with('error', '2FA is not enabled.');
        }

        $recoveryCodes = $this->generateRecoveryCodes();
        $twoFactor->update(['recovery_codes' => $recoveryCodes]);

        return view('profile.two-factor-recovery-codes', compact('recoveryCodes'));
    }

    private function generateRecoveryCodes()
    {
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = Str::random(10);
        }
        return $codes;
    }
}