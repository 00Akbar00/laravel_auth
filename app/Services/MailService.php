<?php

namespace App\Services;

use App\Models\User;
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class MailService
{
    /**
     * Send welcome email to new user
     * @param User $user The newly registered user
     * @return array Returns status and optional error message
     */
    public function sendWelcomeEmail(User $user): array
    {
        try {
            $sent = Mail::to($user->email)->send(new WelcomeMail($user));

            if (!$sent) {
                throw new \Exception('Email failed to send');
            }

            return ['status' => true, 'message' => 'Welcome email sent successfully'];
        } catch (\Exception $e) {
            Log::error('Welcome email failed: ' . $e->getMessage());
            return ['status' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Send OTP email for password reset
     * @param User $user The user requesting reset
     * @param int $otp The OTP code
     * @return array Returns status and optional error message
     */
    public function sendOtpEmail(User $user, int $otp): array
    {
        try {
            Mail::raw("Your OTP code is: $otp\n\nThis code will expire in 10 minutes.", function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Your Password Reset OTP');
            });

            if (count(Mail::failures()) > 0) {
                throw new \Exception('Failed to send OTP email to: ' . implode(', ', Mail::failures()));
            }

            return ['status' => true, 'message' => 'OTP email sent successfully'];
        } catch (\Exception $e) {
            Log::error('OTP email failed: ' . $e->getMessage());
            return ['status' => false, 'error' => $e->getMessage()];
        }
    }
}
