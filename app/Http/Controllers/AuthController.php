<?php

namespace App\Http\Controllers;

use App\Repositories\UtilisateurRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    private UtilisateurRepository $users;

    public function __construct(UtilisateurRepository $users){
        $this->users = $users;
    }

    //GET /login : affiche le formulaire de login
    public function showLogin(){
        return view('auth.login');
    }

    //POST /login : traite le formulaire
    public function login(Request $request){

        //1) Valider les champs
        $request->validate([
            'idUtilisateur' => 'required|string',
            'password' => 'required|string'
        ]);

        $idUtilisateur = $request->input('idUtilisateur');
        $password = $request->input('password');

        //2) Chercher user en DB (SQL via Repository)
        $user = $this->users->findByIdUtilisateur($idUtilisateur);

        if($user === null){
            return back()->with('error','Identifiant ou mot de passe incorrect');
        }


        //3) Vérifier le mot de passe
        if (!Hash::check($password, $user->mdp)) {
            return back()->with('error', 'Mot de passe incorrect');
        }
        //4) Mettre en session (comme "user connecté)
        $request->session()->put('user', [
            'idUtilisateur' => $user->idUtilisateur,
            'nomUtilisateur' => $user->nom,
            'prenomUtilisateur' => $user->prenom,
            'roleUtilisateur' => $user->role,
            'mailUtilisateur' => $user->adresseMail,
        ]);

        //5) rediriger vers dashboard
        return redirect('/dashboard');
    }

    //POST /logout: déconnecte l'utilisateur
    public function logout(Request $request){
        $request->session()->forget('user');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
