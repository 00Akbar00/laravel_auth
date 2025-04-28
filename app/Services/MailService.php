<?php

namespace App\Services;

use App\Models\User;
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Mail;

class MailService
{
    /**
     * Send welcome email to new user
     * @param User $user The newly registered user
     */
    public function sendWelcomeEmail(User $user): void
    {
        Mail::to($user->email)->send(new WelcomeMail($user));
    }

    /**
     * Send OTP email for password reset
     * @param User $user The user requesting reset
     * @param int $otp The OTP code
     */
    public function sendOtpEmail(User $user, int $otp): void
    {
        Mail::raw("Your OTP code is: $otp\n\nThis code will expire in 10 minutes.", function ($message) use ($user) {
            $message->to($user->email)
                   ->subject('Your Password Reset OTP');
        });
    }
}