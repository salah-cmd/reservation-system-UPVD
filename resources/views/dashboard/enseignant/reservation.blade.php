@php use Carbon\Carbon; @endphp
@extends('layouts.etudiant')
@section('title', 'Reservation des salles et matériaux')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/etudiant/materiel.css') }}">
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

            <a href="/enseignant/reservation" class="nav-item active">
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
            <a href="/enseignant/parametre" class="nav-item">
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
    <div class="dashboard-container">

        <div class="page-header">
            <h1>Nouvelle Réservation</h1>
            <p>Remplissez les informations pour réserver un matériel ou une salle</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success flash-message">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger flash-message">
                {{ session('error') }}
            </div>
        @endif

        <div class="card">
            <form action="{{ route('reservation.store') }}" method="POST">
                @csrf

                <!-- ID utilisateur caché -->
                <input type="hidden" name="idUtilisateur" value="{{ $user['idUtilisateur'] }}">

                <div class="form-grid">

                    <!-- Nom -->
                    <div class="form-group">
                        <label>Nom</label>
                        <input type="text" value="{{ strtoupper($user['nomUtilisateur']) }}" readonly>
                    </div>

                    <!-- Prénom -->
                    <div class="form-group">
                        <label>Prénom</label>
                        <input type="text" value="{{ strtoupper($user['prenomUtilisateur']) }}" readonly>
                    </div>

                    <!-- Date début -->
                    <div class="form-group">
                        <label>Date début</label>
                        <input type="datetime-local" name="dateDebut">
                        @error('dateDebut')
                        <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Date fin -->
                    <div class="form-group">
                        <label>Date fin</label>
                        <input type="datetime-local" name="dateFin">
                        @error('dateFin')
                        <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Motif -->
                    <div class="form-group full-width">
                        <label>Motif</label>
                        <input type="text" name="motif" placeholder="Ex: Projet Java, TP Réseau...">
                        @error('motif')
                        <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Salle -->
                    <div class="form-group">
                        <label>Salle</label>
                        <select name="codeSalle">
                            <option value="">Sélectionnez une salle</option>
                            @foreach($etudiants['salles'] as $salle)
                                <option
                                    value="{{ $salle->codeSalle }}"
                                    {{ old('codeSalle') == $salle->codeSalle ? 'selected' : '' }}
                                >
                                    La salle {{ $salle->codeSalle }} ({{ $salle->capacite }} personnes)
                                </option>
                            @endforeach
                        </select>
                        @error('codeSalle')
                        <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Nombre de personnes -->
                    <div class="form-group">
                        <label>Nombre de personnes</label>
                        <input type="text" id= "nbPersonnes" name="nbPersonnes">
                        @error('nbPersonnes')
                        <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                </div>

                <!-- Materiels -->
                <div class="materiel-list improved-mat-list">
                    @foreach($etudiants['materiels'] as $m)
                        <div class="mat-card">

                            <label class="mat-header">
                                <input type="checkbox"
                                       name="codeMat[]"
                                       value="{{ $m->codeMat }}"
                                       onchange="toggleQuantity('{{ $m->codeMat }}')"
                                       class="mat-checkbox">

                                <span class="mat-name">{{ $m->nom }}</span>
                            </label>

                            <div class="mat-quantity">
                                <input type="number"
                                       id="qte_{{ $m->codeMat }}"
                                       name="qteDemande[{{ $m->codeMat }}]"
                                       min="1"
                                       placeholder="Quantité"
                                       disabled
                                       class="mat-input">
                            </div>

                        </div>
                    @endforeach
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">
                        Confirmer la Réservation
                    </button>
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
        }, 5000); // 5000ms = 5 secondes

        function toggleQuantity(codeMat) {
            const checkbox = document.querySelector(`input[value="${codeMat}"]`);
            const input = document.getElementById(`qte_${codeMat}`);

            input.disabled = !checkbox.checked;

            if (!checkbox.checked) {
                input.value = "";
            }
        }
    </script>
@endsection


