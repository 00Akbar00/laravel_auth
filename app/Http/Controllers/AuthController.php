<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserVerification;
use App\Models\Session;
use App\Models\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeEmail;
use Illuminate\Support\Facades\Cookie;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function showRegisterForm()
    {
        return view('register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'passwordHash' => Hash::make($request->password),
            'status' => 'unverified',
        ]);

        // Create verification token
        $verificationToken = Str::uuid();
        UserVerification::create([
            'userId' => $user->id,
            'verificationToken' => $verificationToken,
            'expiresAt' => Carbon::now()->addDays(1),
        ]);

        // Send welcome email
        Mail::to($user->email)->send(new WelcomeEmail($user, $verificationToken));

        return redirect()->route('login')->with('success', 'Registration successful! Please check your email for verification.');
    }

    public function showLoginForm()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->passwordHash)) {
            return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
        }

        if ($user->status === 'unverified') {
            return back()->withErrors(['email' => 'Please verify your email first'])->withInput();
        }

        // Create session
        $sessionId = Str::random(64);
        $session = Session::create([
            'userId' => $user->id,
            'sessionId' => $sessionId,
            'ipAddress' => $request->ip(),
            'userAgent' => $request->userAgent(),
            'metadata' => [],
            'expiresAt' => Carbon::now()->addDays(30),
        ]);

        // Set session cookie
        $cookie = Cookie::make('session_token', $sessionId, 60 * 24 * 30); // 30 days

        return redirect()->intended('/')->cookie($cookie);
    }

    public function logout(Request $request)
    {
        $sessionId = $request->cookie('session_token');
        
        if ($sessionId) {
            Session::where('sessionId', $sessionId)->delete();
        }

        return redirect('/')->cookie(Cookie::forget('session_token'));
    }

    public function showForgotPasswordForm()
    {
        return view('forgot-password');
    }

    public function sendPasswordResetOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Email not found'])->withInput();
        }

        // Generate 6-digit OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Store hashed OTP
        PasswordReset::create([
            'userId' => $user->id,
            'otpHash' => Hash::make($otp),
            'ipAddress' => $request->ip(),
            'expiresAt' => Carbon::now()->addMinutes(30),
        ]);

        // In a real app, you would send the OTP via email/SMS
        // For this example, we'll just return it (don't do this in production!)
        return redirect()->route('password.reset.form', ['email' => $user->email])
                         ->with('otp', $otp)
                         ->with('email', $user->email);
    }

    public function showResetPasswordForm(Request $request)
    {
        return view('reset-password', [
            'email' => $request->email,
            'otp' => $request->session()->get('otp'),
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Email not found'])->withInput();
        }

        $resetRecord = PasswordReset::where('userId', $user->id)
            ->where('expiresAt', '>', Carbon::now())
            ->latest()
            ->first();

        if (!$resetRecord || !Hash::check($request->otp, $resetRecord->otpHash)) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP'])->withInput();
        }

        // Update password
        $user->update(['passwordHash' => Hash::make($request->password)]);

        // Delete all OTPs for this user
        PasswordReset::where('userId', $user->id)->delete();

        return redirect()->route('login')->with('success', 'Password reset successfully! Please login with your new password.');
    }

    public function verifyEmail($token)
    {
        $verification = UserVerification::where('verificationToken', $token)
            ->where('expiresAt', '>', Carbon::now())
            ->first();

        if (!$verification) {
            return redirect()->route('login')->with('error', 'Invalid or expired verification token.');
        }

        $user = $verification->user;
        $user->update(['status' => 'verified']);

        // Delete all verification tokens for this user
        UserVerification::where('userId', $user->id)->delete();

        return redirect()->route('login')->with('success', 'Email verified successfully! You can now login.');
    }
}