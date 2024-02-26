<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LifeEventAcknowledgement extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $relation;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $relation)
    {
        $this->user = $user;
        $this->relation = $relation;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.life_event')
        ->with(['user' => $this->user, 'relation' => $this->relation])
        ->subject('Life Event Acknowledgement');
    }
}
