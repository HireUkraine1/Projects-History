<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AcceptMember extends Mailable
{
    use Queueable, SerializesModels;

    protected $credentials;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($credentials)
    {
        $this->credentials = $credentials;
        $this->subject('CM Membership Invitation');
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.accept_member')->with([
            'password' => $this->credentials['password'],
            'email' => $this->credentials['email'],
            'name' => $this->credentials['name'],
            'link' => asset('/'),
        ]);
    }
}
