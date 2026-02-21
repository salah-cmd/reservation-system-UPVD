<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class ReservationValiderMail extends Mailable
{
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    {
        return $this->subject('Réservation validée')
            ->view('emails.reservation_valider_mail');
    }
}
