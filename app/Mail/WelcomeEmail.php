<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $verificationToken;

    public function __construct($user, $verificationToken)
    {
        $this->user = $user;
        $this->verificationToken = $verificationToken;
    }

    public function build()
    {
        return $this->subject('Welcome to Our Application')
            ->view('emails.welcome')
            ->with([
                'name' => $this->user->name,
                'verificationUrl' => route('verify.email', $this->verificationToken),
            ]);
    }
}
