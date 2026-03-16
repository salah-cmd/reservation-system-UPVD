@extends('layouts.etudiant')
@section('title', 'Dashboard Étudiant - Système de Réservation')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/etudiant/home.css') }}">
@endsection
@section('scripts')
    <script>
        function openMaterialsModal() {
            document.getElementById('materialsModal').style.display = 'flex';
        }

        function closeMaterialsModal() {
            document.getElementById('materialsModal').style.display = 'none';
        }

        // fermer si on clique en dehors du contenu
        document.getElementById('materialsModal').addEventListener('click', function(e){
            if(e.target === this) closeMaterialsModal();
        });

        function openReservationsModal() {
            document.getElementById('reservationsModal').style.display = 'flex';
        }

        function closeReservationsModal() {
            document.getElementById('reservationsModal').style.display = 'none';
        }

        // fermer si on clique en dehors du contenu
        document.getElementById('reservationsModal').addEventListener('click', function(e){
            if(e.target === this) closeReservationsModal();
        });

        function openSallesModal() {
            document.getElementById('sallesModal').style.display = 'flex';
        }

        function closeSallesModal() {
            document.getElementById('sallesModal').style.display = 'none';
        }

        // fermer si on clique en dehors du contenu
        document.getElementById('sallesModal').addEventListener('click', function(e) {
            if (e.target === this) closeSallesModal();
        });
    </script>
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
            <a href="/enseignant/dashboard" class="nav-item active">
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


    <!-- Main Content -->
    <div class="main-content">
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
        <div class="page-header">
            <h1>Bienvenue, {{ $user['prenomUtilisateur'] ?? 'Enseignant' }} !</h1>
            <p>Gérez vos réservations de salles et matériels universitaires</p>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card blue">
                <div class="stat-header">
                    <div>
                        <div class="stat-value">{{ $stats['reservationsActive'] ?? 0 }}</div>
                        <div class="stat-label">Réservations Actives</div>
                    </div>
                    <div class="stat-icon">📅</div>
                </div>
            </div>

            <div class="stat-card orange">
                <div class="stat-header">
                    <div>
                        <div class="stat-value">{{ $stats['reservationsTotales'] ?? 0 }}</div>
                        <div class="stat-label">Reservations Totales</div>
                    </div>
                    <div class="stat-icon">📝</div>
                </div>
            </div>

            <div class="stat-card green">
                <div class="stat-header">
                    <div>
                        <div class="stat-value">{{ $stats['salles'] ?? 0 }}</div>
                        <div class="stat-label">Salles Disponibles</div>
                    </div>
                    <div class="stat-icon">🏫</div>
                </div>
            </div>

            <div class="stat-card purple">
                <div class="stat-header">
                    <div>
                        <div class="stat-value">{{ $stats['materiels'] ?? 0 }}</div>
                        <div class="stat-label">Matériels Disponibles</div>
                    </div>
                    <div class="stat-icon">💻</div>
                </div>
            </div>
        </div>

        <!-- Content Grid -->
        <div class="content-grid">
            <!-- Les Reservations -->
            <div class="card">
                <div class="card-header">
                    <h2>Mes Réservations Récentes</h2>
                    <button onclick="openReservationsModal()" class="btn btn-secondary">
                        Voir plus
                    </button>
                </div>

                <div class="reservation-list">
                    @if(isset($etudiants['reservation']) && count($etudiants['reservation']) > 0)
                        @foreach($etudiants['reservation'] as $reservation)
                            <div class="reservation-item">
                                <h4>{{ $reservation->codeSalle === null ? $reservation->nomMateriel : $reservation->codeSalle }}</h4>
                                <div class="reservation-icon {{ $reservation->codeSalle }}">
                                    {{ $reservation->codeSalle === null ? '💻' : '🏫' }}
                                </div>
                                <div class="reservation-info">
                                    <p><strong>Début :</strong>
                                        {{ \Carbon\Carbon::parse($reservation->dateDebut)->translatedFormat('l d F Y à H:i') }}
                                    </p>
                                    <p><strong>Fin :</strong>
                                        {{ \Carbon\Carbon::parse($reservation->dateFin)->translatedFormat('l d F Y à H:i') }}
                                    </p>
                                </div>
                                <form method="post" action="{{route('reservation.update')}}">
                                    @csrf
                                    <button type="submit" name="idReservation" value="{{ $reservation->idReservation }}" class="btn-annuler">
                                        Annuler
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-state">
                            <div class="empty-state-icon">📭</div>
                            <h3>Aucune réservation</h3>
                            <p>Commencez par réserver une salle ou du matériel</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Les Salles -->
            <div class="card">
                <div class="card-header">
                    <h2>Salles Disponibles</h2>
                    <div class="card-header">
                        <button onclick="openSallesModal()" class="btn btn-secondary">
                            Voir plus
                        </button>
                    </div>
                </div>

                <div class="rooms-grid">
                    @if(isset($etudiants['salle']) && count($etudiants['salle']) > 0)
                        @foreach($etudiants['salle'] as $salle)
                            <div class="room-item">
                                <div class="room-info">
                                    <h4>{{ $salle->codeSalle }} ({{$salle->typeSalle}})</h4>
                                    <p>Capacité: {{ $salle->capacite }} personnes</p>
                                </div>
                                <div class="availability">
                                    @if($salle->disponibilite == 1)
                                        <p>Disponible</p>
                                    @else <p>Non Disponible</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-state">
                            <div class="empty-state-icon">🚫</div>
                            <h3>Aucune salle disponible</h3>
                            <p>Toutes les salles sont actuellement réservées</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- La section du materiel -->
        <div class="card">
            <div class="card-header">
                <h2>Matériels Disponibles</h2>
                <div class="card-header">
                    <button onclick="openMaterialsModal()" class="btn btn-secondary">
                        Voir plus
                    </button>
                </div>
            </div>

            <div class="materials-list">
                @if(isset($etudiants['materiel']) && count($etudiants['materiel']) > 0)
                    @foreach($etudiants['materiel'] as $materiel)
                        <div class="material-item">
                            <div class="material-info">
                                <div class="material-icon">{{ $materiel->icon ?? '💻' }}</div>
                                <div class="material-details">
                                    <h4>{{ $materiel->nom }}</h4>
                                    <p>{{ $materiel->description ?? 'Matériel universitaire' }}</p>
                                </div>
                            </div>
                            <div class="quantity">{{ $materiel->qteTotal ?? 0 }} disponible(s)</div>
                        </div>
                    @endforeach
                @else
                    <div class="empty-state">
                        <div class="empty-state-icon">🔌</div>
                        <h3>Aucun matériel disponible</h3>
                        <p>Tous les matériels sont actuellement réservés</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Listes du Materiels -->
        <div id="materialsModal" class="modal" style="display: none;">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Tous les Matériels</h2>
                    <button class="close-modal" onclick="closeMaterialsModal()">✕</button>
                </div>

                <div class="modal-body" style="max-height: 400px; overflow-y: auto;">
                    @if(!empty($etudiants['materiels']))
                        @foreach($etudiants['materiels'] as $materiel)
                            <div class="material-item" style="margin-top: 15px;">
                                <div class="material-info">
                                    <div class="material-icon">{{ $materiel->icon ?? '💻' }}</div>
                                    <div class="material-details">
                                        <h4>{{ $materiel->nom }}</h4>
                                        <p>{{ $materiel->description ?? 'Matériel universitaire' }}</p>
                                    </div>
                                </div>
                                <div class="quantity">{{ $materiel->qteTotal ?? 0 }} disponible(s)</div>
                                <hr>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-state">
                            <div class="empty-state-icon">🔌</div>
                            <h3>Aucun matériel disponible</h3>
                            <p>Tous les matériels sont actuellement réservés</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Listes des Reservations -->
        <div id="reservationsModal" class="modal">
            <div class="modal-content">

                <div class="modal-header">
                    <h2>Toutes les Réservations</h2>
                    <button class="close-modal" onclick="closeReservationsModal()">✕</button>
                </div>

                <div class="modal-body">
                    @if(isset($etudiants['reservations']) && count($etudiants['reservations']) > 0)
                        @foreach($etudiants['reservations'] as $reservation)
                            <div class="reservation-item" style="margin-top: 15px;">
                                <h4>{{ $reservation->codeSalle === null ? $reservation->nomMateriel : $reservation->codeSalle  }}</h4>

                                <div class="reservation-icon {{ $reservation->codeSalle }}">
                                    {{ $reservation->codeSalle === null ? '💻' : '🏫' }}
                                </div>

                                <div class="reservation-info">
                                    <p>Date Début: {{ $reservation->dateDebut }}</p>
                                    <p>Date Fin: {{ $reservation->dateFin }}</p>
                                </div>

                                <form method="post" action="{{route('reservation.update-enseignant')}}">
                                    @csrf
                                    <button type="submit"
                                            name="idReservation"
                                            value="{{ $reservation->idReservation }}"
                                            class="btn-annuler">
                                        Annuler
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-state">
                            <div class="empty-state-icon">📭</div>
                            <h3>Aucune réservation</h3>
                            <p>Commencez par réserver une salle ou du matériel</p>
                        </div>
                    @endif
                </div>

            </div>
        </div>

        <!-- Listes du Salles , ajoute code salle -->
        <div id="sallesModal" class="modal" style="display: none;">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Tous les Salles</h2>
                    <button class="close-modal" onclick="closeSallesModal()">✕</button>
                </div>

                <div class="modal-body" style="max-height: 400px; overflow-y: auto;">
                    @if(isset($etudiants['salles']) && count($etudiants['salles']) > 0)
                        @foreach($etudiants['salles'] as $salle)
                            <div class="room-item" style="margin-top: 15px;">
                                <div class="room-info">

                                    <h4>{{ $salle->codeSalle }} ({{$salle->typeSalle}})</h4>
                                    <p>Capacité: {{ $salle->capacite }} personnes</p>
                                </div>
                                <div class="availability">
                                    <p>Disponible</p>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-state">
                            <div class="empty-state-icon">🚫</div>
                            <h3>Aucune salle disponible</h3>
                            <p>Toutes les salles sont actuellement réservées</p>
                        </div>
                    @endif
                </div>
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
