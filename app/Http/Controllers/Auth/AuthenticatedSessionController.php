<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Mail\TwoFactorOTP;
use Illuminate\Support\Facades\Mail;

use App\Notifications\NewDeviceLoginNotification;
use App\Services\AuditLogService;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        

        if (Auth::validate($credentials)) {
            $user = Auth::getProvider()->retrieveByCredentials($credentials);

            // Check if user has 2FA enabled
            if ($user->two_factor_enabled && $user->two_factor_type) {
                // Store user ID in session for 2FA verification
                Session::put('two_factor:user:id', $user->id);
                Session::put('two_factor:remember', $remember);
                Session::put('two_factor:credentials', encrypt($credentials));

                // If email 2FA, send OTP
                if ($user->two_factor_type === 'email') {
                    $this->sendEmailOTP($user);
                }

                // Redirect to 2FA verification page
                return redirect()->route('two-factor.verify');
            }

            // No 2FA, log them in directly
            Auth::login($user, $remember);
            $request->session()->regenerate();
            
            AuditLogService::logLogin($user, true);

            // Send new device notification (optional for trusted devices)
            $this->sendNewDeviceNotification($user, $request);

            return redirect()->route('home');
        }

        AuditLogService::logLogin(null, false);
        
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Send new device login notification
     */
    private function sendNewDeviceNotification($user, Request $request)
    {
        $ip = $request->ip();
        $userAgent = $request->userAgent();
        
        // Method 1: Simple - notify for every login (you can enable/disable in user settings)
        if ($user->notify_on_new_device ?? true) {
            $user->notify(new NewDeviceLoginNotification($ip, $userAgent));
        }
        
        // Method 2: Advanced - Track known devices in database
        // You'll need to create a user_devices table for this
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
            
            // Send notification for new device
            $user->notify(new NewDeviceLoginNotification($ip, $userAgent));
        } else {
            // Update last login time for known device
            $knownDevice->update(['last_login_at' => now()]);
        }
        */
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        AuditLogService::logLogout($user);

        return redirect('/');
    }

    /**
     * Send OTP for email-based 2FA
     */
    private function sendEmailOTP($user)
    {
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        Session::put('two_factor:email_otp', [
            'code' => bcrypt($otp),
            'expires_at' => now()->addMinutes(10)
        ]);

        // Send email
        Mail::to($user->email)->send(new TwoFactorOTP($otp));
    }
}