<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewJoiningCredentials extends Mailable
{
    use Queueable, SerializesModels;

    public $users;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($users)
    {
        $this->users = $users;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.new_joining_credentials')
        ->with(['users' => $this->users])
        ->subject('Your MyBenefits@Zoom Login Credentials');
    }
}
