<?php

namespace App\Http\Controllers;

use App\Repositories\DashboardRepository;
use App\Repositories\UtilisateurRepository;
use App\Mail\CompteCreeMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Repositories\SalleRepository;

class AdminDashboardController extends Controller
{
    public function index(DashboardRepository $dashboardRepo)
    {
        $user = session('user');

        $stats = $dashboardRepo->getAdminStats();
        $recentReservations = $dashboardRepo->getRecentReservations();

        return view('dashboard.admin.index', compact('user', 'stats', 'recentReservations'));
    }


    public function utilisateurs(UtilisateurRepository $userRepo){
        $user = session('user');

        $users = $userRepo->getUtilisateurs();

        return view('dashboard.admin.utilisateurs', compact('user', 'users'));
    }

    public function salles(SalleRepository $salleRepo){
        $user = session('user');

        $salles = $salleRepo->getSalles();
        return view('dashboard.admin.salles', compact('user', 'salles'));
    }


    public function storeUtilisateur(Request $request, UtilisateurRepository $repo)
    {
        // Validation (adapte les champs)
        $data = $request->validate([
            'nom'          => 'required|string|max:60',
            'prenom'       => 'required|string|max:60',
            'email'        => 'required|email|max:120',
            'telephone'    => 'nullable|digits:10',
            'role'         => 'required|in:admin,enseignant,etudiant',
            'statut'       => 'required|in:actif,inactif',
            'password'     => 'required|string|min:8|confirmed',
        ],
        [ 'password.confirmed' => 'Les mots de passe ne correspondent pas.',
            'telephone.digits' => 'Le numéro de téléphone doit contenir exactement 10 chiffres.',]
        );

        // Appel SQL pur via Repository
        $ok = $repo->createUtilisateur($data);

        if (!$ok) {
            return back()->withErrors(['email' => "Impossible de créer l'utilisateur (email déjà utilisé)."])
                ->withInput();
        }

        return back()->with('success', "Utilisateur ".$data['nom'] . " " . $data['prenom']." ajouté avec succès.");

    }

    public function updateUtilisateur(Request $request, UtilisateurRepository $repo){
        $data = $request->validate([
            'idUtilisateur' => 'required|string',
            'nom'          => 'required|string|max:60',
            'prenom'       => 'required|string|max:60',
            'email'        => 'required|email|max:120',
            'telephone'    => 'nullable|digits:10',
            'statut'       => 'required|in:actif,inactif',
            'password'     => 'nullable|string|min:8|confirmed',
        ],
            [ 'password.confirmed' => 'Les mots de passe ne correspondent pas.',
                'telephone.digits' => 'Le numéro de téléphone doit contenir exactement 10 chiffres.',]
        );
        $ok=$repo->updateUtilisateur($data);

        if (!$ok) {
            return back()->withErrors(['email' => "Impossible de créer l'utilisateur (email déjà utilisé)."])
                ->withInput();
        }

        return back()->with('success', "Utilisateur modifié avec succès.");

    }

    public function storeSalle(Request $request, SalleRepository $repo){

        $data = $request->validate([
            'capacite'        => 'required',
            'type'            => 'required|in:amphi,groupe,tp,reunion',
            'description'     => 'nullable|string|max:500',
            'statut'          => 'required|in:disponible,indisponible',
        ]);

        $ok=$repo->createSalle($data);

        if (!$ok) {
            return back()->withErrors(['Erreur' => "Une erreur s'est produite lors de l'insertion de la salle"])
                ->withInput();
        }

        return back()->with('success', "Salle ajoutée avec succès.");

    }

    public function updateSalle(Request $request, SalleRepository $repo){
        $data = $request->validate([
            'codeSalle'       => 'required',
            'capacite'        => 'required',
            'type'            => 'required|in:amphi,groupe,tp,reunion',
            'description'     => 'nullable|string|max:500',
            'statut'          => 'required|in:disponible,indisponible',
        ]);

        $ok=$repo->updateSalle($data);

        if (!$ok) {
            return back()->withErrors(['Erreur' => "Une erreur s'est produite lors de l'insertion de la salle"])
                ->withInput();
        }

        return back()->with('success', "Salle modifiée avec succès.");
    }
}
