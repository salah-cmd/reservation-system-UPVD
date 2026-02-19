<?php

namespace App\Repositories;
use DB;

class ReservationRepository
{
    public function getReservations(): array
    {
        return DB::select("
        CALL getReservations()");
    }
}
