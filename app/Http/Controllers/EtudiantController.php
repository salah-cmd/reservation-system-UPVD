<?php

namespace App\Http\Controllers;

use App\Repositories\DashboardRepository;
use App\Repositories\UtilisateurRepository;
use Illuminate\Http\Request;

class EtudiantController extends Controller
{
    /**
     * Afficher le dashboard de l'étudiant
     */
    public function info(DashboardRepository $dashboardRepo)
    {
        $user = session('user');
        $userId = $user['idUtilisateur'];

        $stats = $dashboardRepo->getAdminStats($userId);

        $etudiants = $dashboardRepo->getEtudiantDash($userId);

        return view('dashboard.etudiant.home', compact('user', 'stats', 'etudiants'));
    }
    public function profile(DashboardRepository $dashboardRepo)
    {
        $user = session('user');
        $userId = $user['idUtilisateur'];

        $stats = $dashboardRepo->getEtudiantMateriel($userId);
        $etudiants = $dashboardRepo->getEtudiantDash($userId);


        return view('dashboard.etudiant.materiel', compact('user', 'etudiants', 'stats'));
    }

    public function store(DashboardRepository $dashboardRepo, Request $request)
    {
        $user = session('user');

        $insertion = $dashboardRepo->createReservation($request, $user);

        // ✅ Cas 1 : salle seulement
        if ($insertion['SalleNonVide'] && !$insertion['MatNonVide']) {
            if ($insertion['reservSalle']) {
                return redirect()->route('materiels.page')
                    ->with('success', 'Réservation salle créée avec succès ✅');
            }
        }

        // ✅ Cas 2 : matériel seulement
        if ($insertion['MatNonVide'] && !$insertion['SalleNonVide']) {
            if ($insertion['reservSalle'] && $insertion['reservMat']) {
                return redirect()->route('materiels.page')
                    ->with('success', 'Réservation matériel créée avec succès ✅');
            }
        }

        // ✅ Cas 3 : salle + matériel
        if ($insertion['SalleNonVide'] && $insertion['MatNonVide']) {
            if ($insertion['reservSalle'] && $insertion['reservMat']) {
                return redirect()->route('materiels.page')
                    ->with('success', 'Réservation complète (salle + matériel) créée avec succès ✅');
            }
        }

        // ❌ Erreur
        return redirect()->route('materiels.page')
            ->with('error', 'Erreur lors de la création ❌');
    }

    public function annulationReservation(DashboardRepository $dashboardRepo, Request $request)
    {


        if ($request->has('idReservation')) {
            $idReservation = $request->idReservation;
        } else {
            $idReservation = null;
        }

        $result = $dashboardRepo->modifierStatutReservation($idReservation);

        if(!$result['success']) {
            return redirect()->back()->with('error', $result['message']);
        }

        return redirect()->back()->with('success', $result['message']);
    }

    public function parametre(UtilisateurRepository $utilisateurRepo)
    {
        $user = session('user');
        $userId = $user['idUtilisateur'];

        $utilisateur = $utilisateurRepo->findByIdUtilisateur($userId);

        return view('dashboard.etudiant.parametre', compact('user', 'utilisateur'));
    }

    public function modifParametre(DashboardRepository $dashboardRepo, Request $request)
    {
        $user = session('user');
        $userId = $user['idUtilisateur'];


        $data = [
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'adresseMail' => $request->adresseMail,
            'mdp' => $request->mdp,
            'mdp_confirmation' => $request->mdp_confirmation,
            'telephone' => $request->telephone,
        ];

        session([
            'user.nomUtilisateur' => $data['nom'],
            'user.prenomUtilisateur' => $data['prenom'],
            'user.mailUtilisateur' => $data['adresseMail'],
        ]);

        if ($data['mdp'] == $data['mdp_confirmation']) {
            $dashboardRepo->modifierParametre($data, $userId);
        }else {
            return redirect()->route('parametre.home')->with('error', 'Erreur l\'ancien mot de passe');
        }
        return redirect()->route('parametre.home')->with('success', 'Paramètres mis à jour !');
    }

    public function historique(DashboardRepository $dashboardRepo)
    {
        $user = session('user');
        $userId = $user['idUtilisateur'];

        $historiques = $dashboardRepo->historique($userId);

        return view('dashboard.etudiant.historique', compact('user', 'historiques'));
    }

}
