<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class ReservationRefuserMail extends Mailable
{
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    {
        return $this->subject('Réservation refusée')
            ->view('emails.reservation_refuser_mail');
    }
}
