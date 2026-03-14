<?php
// app/Http/Controllers/TwoFactorVerifyController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use App\Mail\TwoFactorOTP;
use App\Notifications\NewDeviceLoginNotification;

class TwoFactorVerifyController extends Controller
{
    protected $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    /**
     * Show the 2FA verification form
     */
    public function showVerifyForm()
    {
        $userId = Session::get('two_factor:user:id');
        
        if (!$userId) {
            return redirect()->route('login');
        }

        $user = User::find($userId);
        
        if (!$user || !$user->two_factor_enabled) {
            return redirect()->route('login');
        }

        // Store method in session for view
        Session::put('two_factor_method', $user->two_factor_type);

        return view('auth.two-factor-verify');
    }

    /**
     * Verify the 2FA code
     */
    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string'
        ]);

        $userId = Session::get('two_factor:user:id');
        $remember = Session::get('two_factor:remember', false);
        
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Session expired. Please login again.');
        }

        $user = User::find($userId);
        
        if (!$user || !$user->two_factor_enabled) {
            return redirect()->route('login');
        }

        $twoFactor = $user->twoFactorAuthentication;

        // Check if it's a recovery code
        if ($twoFactor && $this->verifyRecoveryCode($user, $request->code)) {
            return $this->completeLogin($user, $remember, $request);
        }

        // Verify based on type
        $valid = false;
        
        if ($user->two_factor_type === 'app') {
            $valid = $this->verifyAppCode($user, $request->code);
        } elseif ($user->two_factor_type === 'email') {
            $valid = $this->verifyEmailCode($request->code);
        }

        if ($valid) {
            // If trust device is checked, set a cookie
            if ($request->boolean('trust_device')) {
                $this->trustDevice($user, $request);
            }

            

            return $this->completeLogin($user, $remember, $request);
        }

        return back()->with('error', 'Invalid verification code.');
    }

    /**
     * Resend email OTP
     */
    public function resend()
    {
        $userId = Session::get('two_factor:user:id');
        
        if (!$userId) {
            return redirect()->route('login');
        }

        $user = User::find($userId);
        
        if ($user && $user->two_factor_type === 'email') {
            $this->sendEmailOTP($user);
            return back()->with('success', 'New verification code sent to your email.');
        }

        return back()->with('error', 'Unable to resend code.');
    }

    /**
     * Complete the login process
     */
    private function completeLogin($user, $remember, Request $request)
    {
        // Log the user in
        Auth::login($user, $remember);

        $this->checkNewDeviceAndNotify($user, $request);

        // Clear 2FA session data
        Session::forget([
            'two_factor:user:id', 
            'two_factor:remember', 
            'two_factor:credentials',
            'two_factor:email_otp',
            'two_factor_method'
        ]);
        
        // Regenerate session
        $request = request();
        $request->session()->regenerate();

        return redirect()->route('home');
    }

    /**
     * Check if this is a new device and send notification
     */
    private function checkNewDeviceAndNotify($user, $request)
    {
        $ip = $request->ip();
        $userAgent = $request->userAgent();
        
        // Method 1: Simple - notify for every login (you can enable/disable in user settings)
        if ($user->notify_on_new_device ?? true) {
            $user->notify(new NewDeviceLoginNotification($ip, $userAgent));
        }
        
        // Method 2: Advanced - Track known devices in database
        // You would need to create a user_devices table for this
        /*
        $knownDevice = $user->devices()
            ->where('ip', $ip)
            ->where('user_agent', $userAgent)
            ->first();
        
        if (!$knownDevice) {
            // Save this device as known
            $user->devices()->create([
                'ip' => $ip,
                'user_agent' => $userAgent,
                'last_login_at' => now()
            ]);
            
            // Send notification
            $user->notify(new NewDeviceLoginNotification($ip, $userAgent));
        } else {
            // Update last login time
            $knownDevice->update(['last_login_at' => now()]);
        }
        */
    }

    /**
     * Verify app-based 2FA code
     */
    private function verifyAppCode($user, $code)
    {
        $twoFactor = $user->twoFactorAuthentication;
        
        if (!$twoFactor || !$twoFactor->secret) {
            return false;
        }

        return $this->google2fa->verifyKey($twoFactor->secret, $code);
    }

    /**
     * Verify email-based OTP
     */
    private function verifyEmailCode($code)
    {
        $otpData = Session::get('two_factor:email_otp');
        
        if (!$otpData || now()->gt($otpData['expires_at'])) {
            return false;
        }

        return Hash::check($code, $otpData['code']);
    }

    /**
     * Verify recovery code
     */
    private function verifyRecoveryCode($user, $code)
    {
        $twoFactor = $user->twoFactorAuthentication;
        
        if (!$twoFactor || !$twoFactor->recovery_codes) {
            return false;
        }

        $recoveryCodes = $twoFactor->recovery_codes;
        
        foreach ($recoveryCodes as $index => $recoveryCode) {
            if (hash_equals($recoveryCode, $code)) {
                // Remove used recovery code
                unset($recoveryCodes[$index]);
                $twoFactor->update(['recovery_codes' => array_values($recoveryCodes)]);
                return true;
            }
        }

        return false;
    }

    /**
     * Send email OTP
     */
    private function sendEmailOTP($user)
    {
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        Session::put('two_factor:email_otp', [
            'code' => Hash::make($otp),
            'expires_at' => now()->addMinutes(10)
        ]);

        Mail::to($user->email)->send(new TwoFactorOTP($otp));
    }

    /**
     * Trust a device for 30 days
     */
    private function trustDevice($user, $request)
    {
        $token = bin2hex(random_bytes(32));
        
        // Store in database or cache
        cache()->put(
            "2fa_trust:{$token}",
            [
                'user_id' => $user->id,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ],
            now()->addDays(30)
        );

        // Set cookie
        cookie()->queue(
            '2fa_trust',
            $token,
            60 * 24 * 30, // 30 days in minutes
            '/',
            null,
            config('app.env') === 'production', // secure in production
            true, // httpOnly
            false,
            'lax'
        );
    }
}