<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PasswordResetService
{
    protected $mailService;

    public function __construct(MailService $mailService)
    {
        $this->mailService = $mailService;
    }

    /**
     * Send OTP to user for password reset
     * @param User $user The user requesting reset
     * @throws \Exception If OTP was recently requested
     */
    public function sendOtp(User $user): void
    {
        // Prevent OTP spam (2 minute cooldown)
        if ($user->otp_requested_at && now()->diffInSeconds($user->otp_requested_at) < 120) {
            throw new \Exception('Please wait before requesting a new OTP');
        }

        $otp = random_int(1000, 9999); // Generate 4-digit OTP

        $user->update([
            'otp_code' => $otp,
            'otp_expires_at' => now()->addMinutes(10), // OTP valid for 10 mins
            'otp_requested_at' => now() // Track when OTP was sent
        ]);

        $this->mailService->sendOtpEmail($user, $otp);
    }

    /**
     * Reset user password with OTP verification
     * @param User $user The user
     * @param string $otp The OTP code
     * @param string $newPassword The new password
     * @throws \Exception If OTP is invalid or expired
     */
    public function resetPassword(User $user, string $otp, string $newPassword): void
    {
        if ($user->otp_code !== $otp) {
            throw new \Exception('Invalid OTP');
        }

        if (now()->gt($user->otp_expires_at)) {
            throw new \Exception('OTP has expired');
        }

        $user->update([
            'password' => Hash::make($newPassword),
            'otp_code' => null, // Clear OTP after use
            'otp_expires_at' => null
        ]);
    }
}