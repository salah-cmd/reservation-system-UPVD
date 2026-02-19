<?php

namespace App\Repositories;
use DB;

class SalleRepository
{
    public function getSalles(): array
    {
        return DB::select("
        select * from salle
        order by codeSalle
        ");
    }

    public function getNewIdSalle(string $typeSalle): int{
        $lastID = DB::selectOne("
        select substring(codeSalle, '2') as nvID from salle
        where typeSalle=?
        order by nvID + 0 desc
        limit 1", [$typeSalle]);
        if ($lastID){
            return $lastID->nvID+1;
        }
        return 1;
    }
    public function createSalle(array $data): bool{

        //TODO: Expliquer ca lors de la soutenance
        $codeSalle = ucfirst($data['type'][0]) . $this->getNewIdSalle($data['type']);

        try{
            DB::insert("
            insert into salle (codeSalle, capacite, typeSalle, description, disponibilite)
            values (?, ?, ?, ?, ?)
            ", [
                $codeSalle,
                $data['capacite'],
                $data['type'],
                $data['description'],
                $data['disponibilite'] == "disponible" ? 1 : 0,
            ]);
            return true;
        }catch (\Throwable $e) {
            return false;
        }
    }

    public function updateSalle(array $data): bool {
        try{
            db::update("
            update salle
            set capacite = ?, typeSalle = ?, description = ?, disponibilite = ?
            where codeSalle = ?", [
                $data['capacite'],
                $data['type'],
                $data['description'],
                $data['disponibilite'] == "disponible" ? 1 : 0,
                $data['codeSalle']
            ]);

            return true;

        }catch (\Throwable $e) {
            return false;
        }

    }
}
