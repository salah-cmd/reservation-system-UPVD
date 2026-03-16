<?php

namespace App\Http\Controllers;
use App\Repositories\EnseignantRepository;
use App\Repositories\UtilisateurRepository;
use Illuminate\Http\Request;


class EnseignantController extends Controller
{
    public function info(EnseignantRepository $enseignantRepo)
    {
        $user = session('user');
        $userId = $user['idUtilisateur'];

        $stats = $enseignantRepo->getEtudiantStats($userId);

        $etudiants = $enseignantRepo->getEtudiantDash($userId);

        return view('dashboard.enseignant.home', compact('user', 'stats', 'etudiants'));
    }
    public function profile(EnseignantRepository $enseignantRepo)
    {
        $user = session('user');
        $userId = $user['idUtilisateur'];

        $etudiants = $enseignantRepo->getEtudiantDash($userId);


        return view('dashboard.enseignant.reservation', compact('user', 'etudiants'));
    }

    public function store(EnseignantRepository $enseignantRepo, Request $request)
    {
        $user = session('user');

        $insertion = $enseignantRepo->createReservation($request, $user);

        // ✅ Cas 1 : salle seulement
        if ($insertion['SalleNonVide'] && !$insertion['MatNonVide']) {
            if ($insertion['reservSalle']) {
                return redirect()->route('reservation.page')
                    ->with('success', 'Réservation salle créée avec succès ✅');
            }
        }

        // ✅ Cas 2 : matériel seulement
        if ($insertion['MatNonVide'] && !$insertion['SalleNonVide']) {
            if ($insertion['reservSalle'] && $insertion['reservMat']) {
                return redirect()->route('reservation.page')
                    ->with('success', 'Réservation matériel créée avec succès ✅');
            }
        }

        // ✅ Cas 3 : salle + matériel
        if ($insertion['SalleNonVide'] && $insertion['MatNonVide']) {
            if ($insertion['reservSalle'] && $insertion['reservMat']) {
                return redirect()->route('reservation.page')
                    ->with('success', 'Réservation complète (salle + matériel) créée avec succès ✅');
            }
        }

        // ❌ Erreur
        return redirect()->route('materiels.page')
            ->with('error', $insertion['message']);
    }

    public function annulationReservation(EnseignantRepository $enseignantRepo, Request $request)
    {
        $user = session('user');
        $userId = $user['idUtilisateur'];

        if ($request->has('idReservation')) {
            $idReservation = $request->idReservation;
        } else {
            $idReservation = null;
        }

        $result = $enseignantRepo->modifierStatutReservation($idReservation,$userId);

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

        return view('dashboard.enseignant.parametre', compact('user', 'utilisateur'));
    }

    public function modifParametre(EnseignantRepository $enseignantRepo, Request $request)
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
            $enseignantRepo->modifierParametre($data, $userId);
        }else {
            return redirect()->route('parametre.enseignant')->with('error', 'Erreur l\'ancien mot de passe');
        }
        return redirect()->route('parametre.enseignant')->with('success', 'Paramètres mis à jour !');
    }

    public function historique(EnseignantRepository $enseignantRepo)
    {
        $user = session('user');
        $userId = $user['idUtilisateur'];

        $historiques = $enseignantRepo->historique($userId);

        return view('dashboard.enseignant.historique', compact('user', 'historiques'));
    }
}
