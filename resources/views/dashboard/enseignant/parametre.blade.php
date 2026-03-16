@php use Carbon\Carbon; @endphp
@extends('layouts.etudiant')
@section('title', 'Paramètres')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/etudiant/parametre.css') }}">
@endsection

@section('sideBar')
    <div class="brand">
        <div class="avatar">🎓</div>
        <div class="brand-text">
            <div class="brand-title">{{strtoupper($user['nomUtilisateur'])}}</div>
            <div class="brand-sub">UPVD</div>
        </div>
    </div>
    <nav class="nav-menu">
        <div class="nav-section">
            <div class="nav-section-title">Menu Principal</div>
            <a href="/enseignant/dashboard" class="nav-item">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Tableau de bord
            </a>

            <a href="/enseignant/reservation" class="nav-item">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Réservation des Salles et Matériaux
            </a>

            <a href="/enseignant/historique" class="nav-item">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4l3 3m9-3a9 9 0 11-3-6.708" />
                </svg>
                Historique des Réservations
            </a>

        </div>

        <div class="nav-section">
            <div class="nav-section-title">Autres</div>
            <a href="/enseignant/parametre" class="nav-item active">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Paramètres
            </a>
        </div>
    </nav>
@endsection

@section('content')
    @if(session('success'))
        <div class="alert alert-success flash-message">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error flash-message">
            {{ session('error') }}
        </div>
    @endif
    <div class="card">
        <div class="card-header">
            <h2>Paramètres du Compte</h2>
            <p>Gérez vos informations personnelles et votre mot de passe</p>
        </div>

        <div class="card-body">
            <form method="POST" action="{{route('parametre.update-enseignant')}}">
                @csrf
                <!-- Nom -->
                <div class="form-group">
                    <label for="nomUtilisateur">Nom</label>
                    <input type="text" id="nomUtilisateur" name="nom" class="form-control"
                           value="{{ $user['nomUtilisateur'] ?? '' }}" required>
                </div>

                <!-- Prénom -->
                <div class="form-group">
                    <label for="prenomUtilisateur">Prénom</label>
                    <input type="text" id="prenomUtilisateur" name="prenom" class="form-control"
                           value="{{ $user['prenomUtilisateur'] ?? '' }}" required>
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="mailUtilisateur">Email</label>
                    <input type="email" id="mailUtilisateur" name="adresseMail" class="form-control"
                           value="{{ $user['mailUtilisateur'] ?? '' }}" required>
                </div>

                <!-- Téléphone -->
                <div class="form-group">
                    <label for="telephoneUtilisateur">Téléphone</label>
                    <input type="text" id="telephoneUtilisateur" name="telephone" class="form-control"
                           value="{{ $utilisateur->telephone ?? '' }}" required>
                </div>

                <!-- Nouveau mot de passe -->
                <div class="form-group">
                    <label for="password">Nouveau mot de passe</label>
                    <input type="password" id="password" name="mdp" class="form-control" placeholder="Laissez vide pour ne pas changer">
                </div>

                <!-- Confirmation mot de passe -->
                <div class="form-group">
                    <label for="password_confirmation">Confirmer mot de passe</label>
                    <input type="password" id="password_confirmation" name="mdp_confirmation" class="form-control" placeholder="Confirmez le mot de passe">
                </div>

                <!-- Bouton de sauvegarde -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        setTimeout(function() {
            let messages = document.querySelectorAll('.flash-message');
            messages.forEach(function(message) {
                message.classList.add('fade-out');
                setTimeout(() => message.remove(), 500);
            });
        }, 5000); // 3000ms = 3 secondes
    </script>
@endsection
