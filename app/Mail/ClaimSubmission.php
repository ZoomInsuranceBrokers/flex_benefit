<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ClaimSubmission extends Mailable
{
    use Queueable, SerializesModels;

    public $ClaimReferenceNo;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($ClaimReferenceNo)
    {
        $this->ClaimReferenceNo = $ClaimReferenceNo;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.claim_submision')
        ->with(['ClaimReferenceNo' => $this->ClaimReferenceNo])
        ->subject('Claim Submission Acknowledgement');
    }
}