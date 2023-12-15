<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable
{
   use Queueable, SerializesModels;

    public $token;
    public $user;

    public function __construct($token, $user)
    {
        $this->token = $token;
        $this->user = $user;
    }

    public function build()
    {
        return $this->view('emails.password-reset')
                    ->with(['token' => $this->token, 'user' => $this->user])
                    ->subject('Password Reset');
    }
}
