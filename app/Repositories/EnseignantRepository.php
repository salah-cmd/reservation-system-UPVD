<?php

namespace App\Repositories;

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EnseignantRepository
{
    public function getEtudiantStats($idUtilisateur): array
    {
        return [
            'salles' => (int)DB::selectOne("SELECT COUNT(codeSalle) as nbSalles FROM SALLE")->nbSalles,
            'reservationsTotales' => (int)DB::selectOne("SELECT COUNT(idReservation) as reservations FROM reservation where idUtilisateur=?", [$idUtilisateur])->reservations,
            'reservationsActive' => (int)DB::selectOne("SELECT COUNT(idReservation) as reservationV FROM reservation where idUtilisateur=? and now() between dateDebut and dateFin", [$idUtilisateur])->reservationV,
            'materiels' => (int)DB::selectOne("SELECT COUNT(codeMat) as materiels FROM materiels")->materiels,
        ];
    }
    public function getEtudiantDash(string $idUtilisateur): array
    {
        $salle = DB::select("SELECT * FROM salle where typeSalle IN ('tp','amphi','reunion') and disponibilite=1 LIMIT 3");
        $materiel = DB::select("SELECT * FROM materiels LIMIT 3");
        $reservation = DB::select("SELECT r.*, m.nom AS nomMateriel
        FROM reservation r
        LEFT JOIN reservationMateriels rm ON r.idReservation = rm.idReservation
        LEFT JOIN materiels m ON rm.codeMat = m.codeMat
        WHERE r.idUtilisateur = ?
        and r.statut ='valider'
        ORDER BY r.dateDebut DESC
        LIMIT 3
    ", [$idUtilisateur]);

        $salles = DB::select("SELECT * FROM salle where typeSalle IN ('tp','amphi','reunion') and disponibilite=1");
        $materiels = DB::select("SELECT * FROM materiels");
        $reservations = DB::select("SELECT r.*, m.nom AS nomMateriel
        FROM reservation r
        LEFT JOIN reservationMateriels rm ON r.idReservation = rm.idReservation
        LEFT JOIN materiels m ON rm.codeMat = m.codeMat
        WHERE r.idUtilisateur = ?
        and r.statut ='valider'
        ORDER BY r.dateDebut DESC
    ", [$idUtilisateur]);

        return [
            'salles' => $salles,
            'materiels' => $materiels,
            'reservations' => $reservations,
            'salle' => $salle,
            'materiel' => $materiel,
            'reservation' => $reservation,
        ];
    }

    public function createReservation(Request $request, $user): array
    {
        $validated = $request->validate([
            'dateDebut' => 'required|date',
            'dateFin' => 'required|date|after:dateDebut',
            'motif' => 'required|string|max:500',
            'codeSalle' => 'nullable|string',
            'nbPersonnes' => 'nullable|integer|min:1',
            'codeMat' => 'nullable|array',
            'codeMat.*' => 'string',
            'qteDemande' => 'nullable|array',
            'qteDemande.*' => 'integer|min:1',
        ]);

        $SalleNonVide = !empty($validated['codeSalle']);
        $MatNonVide = !empty($validated['codeMat']);

        $reservSalle = false;
        $reservMat = false;

        $dateRetour = \Carbon\Carbon::parse($validated['dateFin'])->addWeek()->format('Y-m-d H:i:s');

        // Cas 1 : salle seulement
        if ($SalleNonVide && !$MatNonVide) {

            try {
                $reservSalle = DB::insert("
            INSERT INTO reservation
                (idUtilisateur, dateDebut, dateFin, motif, statut, traiteLe, validePar, codeSalle, nbPersonnes)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ", [
                    $user['idUtilisateur'],
                    $validated['dateDebut'],
                    $validated['dateFin'],
                    $validated['motif'],
                    'valider',
                    now(), null,
                    $validated['codeSalle'],
                    $validated['nbPersonnes'] ?? null,
                ]);
            }catch (QueryException $e){
                $message = $e->errorInfo[2];
            }

        }

        // Cas 2 : matériel seulement
        if ($MatNonVide && !$SalleNonVide) {
            // On insère d'abord dans reservation sans salle
            $reservSalle = DB::insert("
            INSERT INTO reservation
                (idUtilisateur, dateDebut, dateFin, motif, statut, traiteLe, validePar, codeSalle, nbPersonnes)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ", [
                $user['idUtilisateur'],
                $validated['dateDebut'],
                $validated['dateFin'],
                $validated['motif'],
                'valider',
                now(), null, null, null,
            ]);

            // On récupère l'id généré pour lier reservationMateriels
            $idReservation = DB::getPdo()->lastInsertId();

            foreach ($validated['codeMat'] as $codeMat) {

                $quantite = $validated['qteDemande'][$codeMat] ?? 1;

                try {
                    $reservMat = DB::insert("
                INSERT INTO reservationMateriels (idReservation, codeMat, qteDemande, statut, dateRetour)
                VALUES (?, ?, ?, ?, ?)
            ", [
                        $idReservation,
                        $codeMat,
                        $quantite,
                        'reserve',
                        $dateRetour,
                    ]);
                }catch (QueryException $e){
                    $message = $e->errorInfo[2];
                }
            }
        }

        // Cas 3 : salle + matériel
        if ($SalleNonVide && $MatNonVide) {
            // On insère dans reservation avec la salle
            try{
                $reservSalle = DB::insert("
            INSERT INTO reservation
                (idUtilisateur, dateDebut, dateFin, motif, statut, traiteLe, validePar, codeSalle, nbPersonnes)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ", [
                    $user['idUtilisateur'],
                    $validated['dateDebut'],
                    $validated['dateFin'],
                    $validated['motif'],
                    'valider',
                    now(), null,
                    $validated['codeSalle'],
                    $validated['nbPersonnes'] ?? null,
                ]);
            }catch (QueryException $e){
                $message = $e->errorInfo[2];
            }


            // On récupère l'id généré pour lier reservationMateriels
            $idReservation = DB::getPdo()->lastInsertId();

            foreach ($validated['codeMat'] as $codeMat) {
                $quantite = $validated['qteDemande'][$codeMat] ?? 1;

                try {
                    $reservMat = DB::insert("
                INSERT INTO reservationMateriels (idReservation, codeMat, qteDemande, statut, dateRetour)
                VALUES (?, ?, ?, ?, ?)
            ", [
                        $idReservation,
                        $codeMat,
                        $quantite,
                        'reserve',
                        $dateRetour,
                    ]);
                }catch (QueryException $e){
                    $message = $e->errorInfo[2];
                }

            }
        }

        return [
            'SalleNonVide' => $SalleNonVide,
            'MatNonVide' => $MatNonVide,
            'reservSalle' => $reservSalle,
            'reservMat' => $reservMat,
            'message' => $message,
        ];
    }

        public function modifierStatutReservation($idReservation,$idUtilisateur)
    {
        try {

            DB::statement('CALL annulerReservation(?,?)', [$idReservation,$idUtilisateur]);

            return [
                'success' => true,
                'message' => 'Réservation annulée avec succès'
            ];

        } catch (\Exception $e) {

            return [
                'success' => false,
                'message' => 'Impossible d\'annuler cette réservation'
            ];
        }
    }

    public function modifierParametre(array $data, $idUtilisateur)
    {
        if (empty($data['mdp'])) {
            $parametre = DB::update("UPDATE utilisateur SET nom=?, prenom=?, adresseMail=?, telephone=? WHERE idUtilisateur = ?",
                [
                    $data['nom'],
                    $data['prenom'],
                    $data['adresseMail'],
                    $data['telephone'],
                    $idUtilisateur,
                ]);
        }else{
            $parametre = DB::update("UPDATE utilisateur SET nom=?, prenom=?, adresseMail=?, mdp=?, telephone=? WHERE idUtilisateur = ?",
                [
                    $data['nom'],
                    $data['prenom'],
                    $data['adresseMail'],
                    $data['mdp'],
                    $data['telephone'],
                    $idUtilisateur,
                ]);
        }

        return ['parametre' => $parametre];
    }

    public function historique($idUtilisateur)
    {

        $h = DB::select("
        SELECT r.*, m.nom AS nomMateriel
        FROM reservation r
        LEFT JOIN reservationMateriels rm ON r.idReservation = rm.idReservation
        LEFT JOIN materiels m ON rm.codeMat = m.codeMat
        WHERE r.idUtilisateur = ?
        ORDER BY r.dateDebut DESC
    ", [$idUtilisateur]);

        return ['h' => $h];
    }

}
