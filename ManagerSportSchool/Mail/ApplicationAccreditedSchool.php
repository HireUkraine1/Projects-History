<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApplicationAccreditedSchool extends Mailable
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
        $this->subject('sport Application Accredited School');
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.application_accredited_school')->with([
            'password' => $this->credentials['password'],
            'email' => $this->credentials['email'],
            'link' => asset('/school/login'),
        ]);
    }
}
