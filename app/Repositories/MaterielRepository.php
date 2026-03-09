<?php

namespace App\Repositories;
use DB;

class MaterielRepository
{
    public function getMateriels(): array
    {
        return DB::select("
        select * from materiels
        order by codeMat
        ");
    }

    public function getNewIdMat(): int{
        $lastID = DB::selectOne("
        select substring(codeMat, '4') as nvID from materiels
        order by nvID + 0 desc
        limit 1");
        if ($lastID){
            return $lastID->nvID+1;
        }
        return 1;
    }
    public function createMateriel(array $data): bool{

        $codeMat = 'MAT' . $this->getNewIdMat();

        try{
            DB::insert("
            insert into materiels (codeMat, nom, qteTotal, description)
            values (?, ?, ?, ?)
            ", [
                $codeMat,
                $data['nom'],
                $data['qteTotal'],
                $data['description'],
            ]);
            return true;
        }catch (\Throwable $e) {
            return false;
        }
    }

    public function updateMateriel(array $data): bool {
        try{
            db::update("
            update materiels
            set nom = ?, qteTotal = ?, description = ?
            where codeMat = ?", [
                $data['nom'],
                $data['qteTotal'],
                $data['description'],
                $data['codeMat']
            ]);

            return true;

        }catch (\Throwable $e) {
            return false;
        }

    }

    public function deleteMateriel(string $codeMat): bool{
        try{
            DB::delete("
            delete from materiels
            where codeMat = ?", [$codeMat]);
            return true;
        }catch(\Throwable $e){
            return false;
        }
    }
}
