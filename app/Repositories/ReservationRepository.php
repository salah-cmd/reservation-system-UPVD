<?php

namespace App\Repositories;
use App\Mail\ReservationRefuserMail;
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
            return false;
        }
    }

    public function refuserReservation($id, $idAdmin, array $data): bool{
        try{
            db::update("call refuserReservation(?,?)",
            [$id, $idAdmin]);

            Mail::to($data['email'])->send(new ReservationRefuserMail($data));
            return true;
        }catch(\Throwable $e){
            return false;
        }
    }
}
