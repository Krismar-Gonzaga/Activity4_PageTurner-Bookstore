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
use App\Notifications\TwoFactorEnabledNotification;
use App\Notifications\TwoFactorDisabledNotification;
use App\Services\AuditLogService;

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

            $user->notify(new TwoFactorEnabledNotification('app'));

            AuditLogService::logTwoFactorEnable($user, 'app');

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

            $user->notify(new TwoFactorEnabledNotification($setup['type']));

            AuditLogService::logTwoFactorEnable($user, $setup['type']);

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

        AuditLogService::logTwoFactorDisable($user);

        $user->notify(new TwoFactorDisabledNotification());

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

    public function ajaxLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'The provided credentials are incorrect.',
            ], 401);
        }

        // Check if user has 2FA enabled
        if ($user->two_factor_method !== 'none' && $user->two_factor_confirmed_at) {
            // Store user ID in session for 2FA verification
            $request->session()->put('two_factor:user:id', $user->id);
            $request->session()->put('two_factor:remember', $request->boolean('remember'));

            return response()->json([
                'two_factor_required' => true,
                'user_id' => $user->id,
                'two_factor_method' => $user->two_factor_method,
                'two_factor_methods' => $this->getAvailableMethods($user),
                'message' => '2FA verification required',
            ]);
        }

        // No 2FA required, log the user in
        Auth::login($user, $request->boolean('remember'));

        return response()->json([
            'success' => true,
            'redirect' => session()->pull('url.intended', route('dashboard')),
        ]);
    }

    public function verify(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $userId = $request->session()->get('two_factor:user:id');
        $remember = $request->session()->get('two_factor:remember', false);

        if (!$userId) {
            return response()->json([
                'message' => 'Session expired. Please login again.',
            ], 401);
        }

        $user = User::findOrFail($userId);

        // Check if using recovery code
        if (str_contains($request->code, '-')) {
            return $this->verifyRecoveryCode($request, $user);
        }

        $valid = false;

        // Verify based on method
        if ($user->two_factor_method === 'app') {
            $valid = $this->google2fa->verifyKey(
                decrypt($user->two_factor_secret),
                $request->code
            );
        } elseif ($user->two_factor_method === 'email') {
            $valid = $this->verifyEmailOTP($user, $request->code);
        }

        if (!$valid) {
            return response()->json([
                'message' => 'The provided two factor authentication code was invalid.',
            ], 422);
        }

        // If trust device is checked, set a cookie/long session
        if ($request->boolean('trust_device')) {
            // Store device trust token
            $this->trustDevice($user, $request);
        }

        // Log the user in
        Auth::login($user, $remember);

        // Clear 2FA session data
        $request->session()->forget(['two_factor:user:id', 'two_factor:remember']);

        return response()->json([
            'success' => true,
            'redirect' => session()->pull('url.intended', route('dashboard')),
        ]);
    }

    protected function verifyRecoveryCode(Request $request, User $user)
    {
        $recoveryCodes = decrypt($user->two_factor_recovery_codes);

        foreach ($recoveryCodes as $index => $code) {
            if (hash_equals($code, $request->code)) {
                // Remove used recovery code
                unset($recoveryCodes[$index]);
                $user->two_factor_recovery_codes = encrypt(array_values($recoveryCodes));
                $user->save();

                // Log the user in
                Auth::login($user, $request->session()->get('two_factor:remember', false));

                // Clear 2FA session data
                $request->session()->forget(['two_factor:user:id', 'two_factor:remember']);

                return response()->json([
                    'success' => true,
                    'redirect' => route('dashboard'),
                    'message' => 'Recovery code used. Please generate new codes.',
                ]);
            }
        }

        return response()->json([
            'message' => 'Invalid recovery code.',
        ], 422);
    }

    public function enable(Request $request)
    {
        $request->validate([
            'method' => ['required', 'in:app,email'],
            'code' => ['required_if:method,app', 'string', 'size:6'],
            'email' => ['required_if:method,email', 'email', 'nullable'],
        ]);

        $user = $request->user();

        if ($request->method === 'app') {
            // Verify the code to confirm setup
            $secret = decrypt(session('two_factor:secret'));
            
            if (!$this->google2fa->verifyKey($secret, $request->code)) {
                return back()->withErrors(['code' => 'Invalid verification code']);
            }

            $user->two_factor_secret = encrypt($secret);
            $user->two_factor_method = 'app';
        } else {
            $user->two_factor_method = 'email';
            $user->two_factor_email = $request->email;
        }

        // Generate recovery codes
        $recoveryCodes = collect(range(1, 8))->map(function () {
            return strtoupper(Str::random(5) . '-' . Str::random(5));
        })->toArray();

        $user->two_factor_recovery_codes = encrypt($recoveryCodes);
        $user->two_factor_confirmed_at = now();
        $user->save();

        // Send notification
        $user->notify(new \App\Notifications\TwoFactorEnabledNotification($request->method));

        return redirect()->route('profile.two-factor')->with([
            'status' => 'Two-factor authentication enabled successfully.',
            'recovery_codes' => $recoveryCodes,
        ]);
    }

    

    /**
     * Regenerate recovery codes
     */
    

    /**
     * Trust a device
     */
    protected function trustDevice(User $user, Request $request)
    {
        $token = Str::random(60);
        
        // Store in database or cache
        cache()->put(
            "trust_device:{$token}",
            ['user_id' => $user->id, 'ip' => $request->ip(), 'user_agent' => $request->userAgent()],
            now()->addDays(30)
        );

        // Set cookie
        cookie()->queue('trust_device', $token, 60 * 24 * 30);
    }

    /**
     * Get available 2FA methods for user
     */
    protected function getAvailableMethods(User $user)
    {
        $methods = [];
        
        if ($user->two_factor_method === 'app' || $user->two_factor_secret) {
            $methods[] = 'app';
        }
        
        if ($user->two_factor_method === 'email' || $user->email) {
            $methods[] = 'email';
        }
        
        return $methods;
    }
}