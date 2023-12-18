<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SubmitEnrollment extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public $mapUserFYPolicyData;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user,  $mapUserFYPolicyData)
    {
        $this->user = $user;
        $this->mapUserFYPolicyData =  $mapUserFYPolicyData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        return $this->view('emails.submit_enrollment')
            ->with(['user' => $this->user, 'mapUserFYPolicyData' => $this->mapUserFYPolicyData])
            ->subject('Enrollment Submission');
    }
}
