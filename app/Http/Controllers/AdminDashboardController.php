<?php

namespace App\Http\Controllers;

use App\Repositories\DashboardRepository;
use App\Repositories\MaterielRepository;
use App\Repositories\ReservationRepository;
use App\Repositories\UtilisateurRepository;
use Illuminate\Http\Request;
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

    public function materiels(MaterielRepository $materielRepo){
        $user = session('user');

        $materiels = $materielRepo->getMateriels();
        return view('dashboard.admin.materiels', compact('user', 'materiels'));
    }

    public function reservations(ReservationRepository $reservationRepo){
        $user = session('user');
        $reservations = $reservationRepo->getReservations();


        return view('dashboard.admin.reservations', compact('user', 'reservations'));
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

    public function storeSalle(Request $request, SalleRepository $repo){

        $data = $request->validate([
            'capacite'        => 'required',
            'type'            => 'required|in:amphi,groupe,tp,reunion',
            'description'     => 'nullable|string|max:500',
            'disponibilite'          => 'required|in:disponible,indisponible',
        ]);

        $ok=$repo->createSalle($data);

        if (!$ok) {
            return back()->withErrors(['Erreur' => "Une erreur s'est produite lors de l'insertion de la salle"])
                ->withInput();
        }

        return back()->with('success', "Salle ajoutée avec succès.");

    }

    public function storeMateriel(Request $request, MaterielRepository $repo){

        $data = $request->validate([
            'nom'          => 'required|string|max:60',
            'qteTotal'      => 'required|numeric|min:1',
            'description'   => 'nullable|string|max:500',
        ],
        [
            'qteTotal.min' => 'La quantité totale doit être au moins de 1.',
        ]);

        $ok=$repo->createMateriel($data);

        if (!$ok) {
            return back()->withErrors(['Erreur' => "Une erreur s'est produite lors de l'insertion du matériel"])
                ->withInput();
        }

        return back()->with('success', "Matériel ajoutée avec succès.");
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
            return back()->withErrors(['email' => "Une erreur s'est produite lors de la modification de l'utilisateur"])
                ->withInput();
        }

        return back()->with('success', "Utilisateur modifié avec succès.");

    }


    public function updateSalle(Request $request, SalleRepository $repo){
        $data = $request->validate([
            'codeSalle'       => 'required',
            'capacite'        => 'required',
            'type'            => 'required|in:amphi,groupe,tp,reunion',
            'description'     => 'nullable|string|max:500',
            'disponibilite'          => 'required|in:disponible,indisponible',
        ]);

        $ok=$repo->updateSalle($data);

        if (!$ok) {
            return back()->withErrors(['Erreur' => "Une erreur s'est produite lors de la modification de la salle"])
                ->withInput();
        }

        return back()->with('success', "Salle modifiée avec succès.");
    }

    public function updateMateriel(Request $request, MaterielRepository $repo){
        $data = $request->validate([
            'codeMat'       => 'required|string',
            'nom'           => 'required|string|max:60',
            'qteTotal'      => 'required|numeric|min:1',
            'description'   => 'nullable|string|max:500',
        ],
            [
                'qteTotal.min' => 'La quantité totale doit être au moins de 1.',
            ]);

        $ok=$repo->updateMateriel($data);

        if (!$ok) {
            return back()->withErrors(['Erreur' => "Une erreur s'est produite lors de la modification du matériel"])
                ->withInput();
        }

        return back()->with('success', "Matériel modifié avec succès.");
    }

    public function destroyMateriel($codeMat, MaterielRepository $repo)
    {
        $ok=$repo->deleteMateriel($codeMat);

        if(!$ok){
            return back()->withErrors(['Erreur'=>"Une erreur s'est produite lors de la suppression du matériel"]);
        }
        return back()->with('success', 'Matériel supprimé avec succès');
    }

    public function getReservationDetails($id, ReservationRepository $repo)
    {
        // Procédure 1 : détails réservation + utilisateur + salle
        $details = $repo->getReservationDetails($id);

        // Procédure 2 : matériels
        $materiels = $repo->getReservationMateriels($id);

        // Si aucune réservation trouvée
        if (empty($details)) {
            return response()->json([
                'success' => false
            ]);
        }

        return response()->json([
            'success' => true,
            'details' => $details[0], // une seule ligne
            'materiels' => $materiels // tableau (0, 1 ou plusieurs)
        ]);
    }

    public function validerReservation($id, Request $request, ReservationRepository $repo){
        $data = [
            "email"=>$request->input('emailUtilisateur'),
            "nomComplet"=>$request->input('nomComplet'),
            "idReservation"=>$request->input('idReservation'),
            "dateDebut"=>$request->input('dateDebut'),
            "dateFin"=>$request->input('dateFin'),
            "salle"=>$request->input('salle'),
            "motif"=>$request->input('motif'),
            "nbMateriels" =>$request->input('nbMateriels'),
        ];
        $idAdmin = session('user')['idUtilisateur'];
        $ok = $repo->validerReservation($id, $idAdmin, $data);

        if(!$ok){
            return back()->withErrors(['Erreur'=> 'Seules les réservations en attente peuvent être validées']);
        }

        return back()->with('success', 'Réservation N°: ' . $id.', validée avec succes');

    }

    public function refuserReservation($id, ReservationRepository $repo, Request $request){
        $data = [
            "email"=>$request->input('emailUtilisateur'),
            "nomComplet"=>$request->input('nomCompletrefus'),
            "idReservation"=>$request->input('idReservationrefus'),
            "dateDebut"=>$request->input('dateDebutrefus'),
            "dateFin"=>$request->input('dateFinrefus'),
            "salle"=>$request->input('sallerefus'),
            "motif"=>$request->input('motifrefus'),
            "nbMateriels" =>$request->input('nbMaterielsrefus'),
            "motifRefus" =>$request->input('motifRefus'),
        ];
        $idAdmin = session('user')['idUtilisateur'];

        $ok = $repo->refuserReservation($id, $idAdmin, $data);
        if(!$ok){
            return back()->withErrors(['Erreur'=> 'Seules les réservations en attente peuvent être refusées']);
        }

        return back()->with('success', 'Réservation N°: ' . $id.', refusée avec succes');
    }

}
