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

    public function getReservationDetails($id): array{
        return DB::select("CALL getReservationDetails(?)", [$id]);
    }

    public function getReservationMateriels($id): array{
        return DB::select("CALL getReservationMateriels(?)", [$id]);
    }
}
