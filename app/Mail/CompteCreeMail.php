<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class CompteCreeMail extends Mailable
{
    public $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function build()
    {
        return $this->subject('Votre compte a été créé')
            ->view('emails.compte_cree_mail');
    }
}
