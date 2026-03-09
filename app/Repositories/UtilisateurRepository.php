<?php

namespace App\Repositories;

use App\Mail\CompteCreeMail;
use App\Mail\MotDePasseChangeMail;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UtilisateurRepository
{

    public function findByIdUtilisateur(string $idUtilisateur): ?object
    {
        //retourne un seul resultat (objet ou NULL)
        return DB::selectOne(
            "SELECT * FROM utilisateur WHERE idUtilisateur = ? and actif = true LIMIT 1",
            [$idUtilisateur]
        );
    }

    public function getUtilisateurs(): array{
        return DB::select("
        SELECT idUtilisateur, nom, prenom, adresseMail, role, actif, telephone
            FROM utilisateur
            ORDER BY idUtilisateur");
    }

    public function getNewIdUser(string $role): int{
        $lastID = DB::selectOne("
        select substring(idUtilisateur, '4') as nvID from utilisateur
        where role=?
        order by nvID + 0 desc
        limit 1;", [$role]);
        if ($lastID){
            return $lastID->nvID+1;
        }
        return 1;
    }

    public function createUtilisateur(array $data): bool
    {
        // Hash du mot de passe
        $hashedPassword = Hash::make($data['password']);


        $idUser = strtoupper(substr($data['role'], 0, 3)) . $this->getNewIdUser($data['role']);

        try {
            //(paramétré => évite SQL injection)
            DB::insert("
                INSERT INTO utilisateur
                (idUtilisateur, nom, prenom, adresseMail, telephone, role, actif, mdp)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ", [
                $idUser,
                $data['nom'],
                $data['prenom'],
                $data['email'],
                $data['telephone'] ?? null,
                $data['role'],
                $data['statut'] == 'actif' ? '1' : '0',
                $hashedPassword,
            ]);

            // Envoyé un email Ssi le compte crée est actif
            if ($data['statut'] == 'actif'){
                $data['idUtilisateur'] = $idUser;
                Mail::to($data['email'])->send(new CompteCreeMail($data));
            }

            return true;

        } catch (\Throwable $e) {
            return false;
        }
    }

    public function updateUtilisateur(array $data): bool
    {

        try {
            DB::update("
          UPDATE utilisateur
          SET nom=?, prenom=?, adresseMail=?, telephone=?, actif=?
          WHERE idUtilisateur=?
        ", [
                $data['nom'],
                $data['prenom'],
                $data['email'],
                $data['telephone'] ?? null,
                $data['statut'] == 'actif' ? '1' : '0',
                $data['idUtilisateur'],
            ]);

            // mot de passe optionnel
            if (!empty($data['password'])) {
                DB::update("UPDATE utilisateur SET mdp=? WHERE idUtilisateur=?", [
                    Hash::make($data['password']),
                    $data['idUtilisateur']
                ]);
                Mail::to($data['email'])->send(new MotDePasseChangeMail($data));
            }

            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

}
