<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Services\TokenService;
use App\Services\MailService;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    protected $tokenService;
    protected $mailService;

    public function __construct(TokenService $tokenService, MailService $mailService)
    {
        $this->tokenService = $tokenService;
        $this->mailService = $mailService;
    }

    // User Signup
    public function signup(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
            Log::info('User created', ['email' => $user->email]);
            $emailResult = $this->mailService->sendWelcomeEmail($user);

            if (!$emailResult['status']) {
                Log::error('Welcome email failed for user ' . $user->email, $emailResult);
                // Continue anyway since user is created, but log the error
            }

            return response()->json([
                'message' => 'User registered successfully.',
                'email_sent' => $emailResult['status'],
                'email_message' => $emailResult['message'] ?? null,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Signup error: ' . $e->getMessage());
            return response()->json([
                'error' => 'User registration failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // User Login
    public function login(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $user = User::where('email', $validated['email'])->first();

            if(!$user){
                return response()->json(['message'=>'User dose not exists'], 404);
            }

            if (!Hash::check($validated['password'], $user->password)) {
                return response()->json(['message' => 'Invalid email or password'], 401);
            }

            $token = $this->tokenService->generateToken($user);

            return response()->json([
                'message' => 'Login successful',
                'token' => $token,
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred during login',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Send OTP
    public function sendOtp(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email',
            ]);

            $user = User::where('email', $validated['email'])->first();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found',
                    'data' => null,
                ], 404);
            }

            if ($user->otp_requested_at && now()->diffInSeconds($user->otp_requested_at) < 120) {
                $secondsLeft = 120 - now()->diffInSeconds($user->otp_requested_at);

                return response()->json([
                    'status' => 'error',
                    'message' => 'Too many requests. Please wait before requesting a new OTP.',
                    'data' => [
                        'retry_after_seconds' => $secondsLeft,
                    ],
                ], 429);
            }

            $otp = random_int(1000, 9999);

            $user->otp_code = $otp;
            $user->otp_expires_at = now()->addMinutes(10);
            $user->otp_requested_at = now();
            $user->save();

            $this->mailService->sendOtpEmail($user, $otp);

            return response()->json([
                'status' => 'success',
                'message' => 'OTP sent successfully.',
                'data' => [
                    'email' => $user->email,
                ],
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An internal error occurred while sending the OTP.',
                'error' => [
                    'message' => $e->getMessage(),
                ],
            ], 500);
        }
    }


    // Verify OTP and Reset Password
    public function resetPassword(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email',
                'otp' => 'required|digits:4',
                'new_password' => 'required|min:6|confirmed',
            ]);

            $user = User::where('email', $validated['email'])->first();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found.',
                    'data' => null,
                ], 404);
            }

            if ($user->otp_code !== $validated['otp']) {
                return response()->json([
                    'status' => 'fail',
                    'message' => 'Invalid OTP provided.',
                    'data' => null,
                ], 400);
            }

            if (now()->gt($user->otp_expires_at)) {
                return response()->json([
                    'status' => 'fail',
                    'message' => 'OTP has expired.',
                    'data' => null,
                ], 400);
            }

            $user->password = Hash::make($validated['new_password']);
            $user->otp_code = null;
            $user->otp_expires_at = null;
            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Password has been reset successfully.',
                'data' => [
                    'email' => $user->email,
                ],
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An internal error occurred while resetting the password.',
                'error' => [
                    'message' => $e->getMessage(),
                ],
            ], 500);
        }
    }
}
