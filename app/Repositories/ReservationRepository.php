<?php

namespace App\Repositories;
use App\Mail\ReservationValiderMail;
use DB;
use Mail;

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


    public function validerReservation($id, $idAdmin, array $data): bool
    {
        try{
            DB::update("call validerReservation(?,?)",
            [$id, $idAdmin]);

            Mail::to($data['email'])->send(new ReservationValiderMail($data));
            return true;
        }catch (\Throwable $e){
            dd($e->getMessage());
        }
    }
}
