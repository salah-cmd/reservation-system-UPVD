@extends('layouts.etudiant')
@section('title', 'Historique des Réservations')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/etudiant/historique.css') }}">
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const searchInput = document.getElementById('searchInput');
            const filterType = document.getElementById('filterType');
            const filterStatus = document.getElementById('filterStatus');
            const filterDate = document.getElementById('filterDate');
            const cards = document.querySelectorAll('.history-card');

            function applyFilters() {
                const search = searchInput.value.toLowerCase();
                const type = filterType.value;
                const status = filterStatus.value;
                const date = filterDate.value;

                cards.forEach(card => {
                    const title = card.dataset.title;
                    const cardType = card.dataset.type;
                    const cardStatus = card.dataset.status;
                    const cardDate = card.dataset.date;

                    let visible = true;

                    if (search && !title.includes(search)) visible = false;
                    if (type && cardType !== type) visible = false;
                    if (status && cardStatus !== status) visible = false;
                    if (date && cardDate !== date) visible = false;

                    card.style.display = visible ? "flex" : "none";
                });
            }

            searchInput.addEventListener('input', applyFilters);
            filterType.addEventListener('change', applyFilters);
            filterStatus.addEventListener('change', applyFilters);
            filterDate.addEventListener('change', applyFilters);

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
            <a href="/enseignant/historique" class="nav-item active">
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

    <div class="history-container">

        <div class="history-header">
            <h1>Historique des Réservations</h1>
            <p class="subtitle">Toutes vos réservations passées et en cours</p>
        </div>

        <div class="history-filters">

            <!-- Barre de recherche -->
            <input type="text" id="searchInput" class="search-bar"
                   placeholder="Rechercher une réservation...">

            <!-- Filtres -->
            <select id="filterType" class="filter-select">
                <option value="">Type</option>
                <option value="salle">Salle</option>
                <option value="materiel">Matériel</option>
            </select>

            <select id="filterStatus" class="filter-select">
                <option value="">Statut</option>
                <option value="enattente">En attente</option>
                <option value="valide">Validée</option>
                <option value="annulee">Refusée</option>
            </select>

            <input type="date" id="filterDate" class="filter-select">

        </div>

        @if(isset($historiques['h']) && count($historiques['h']) > 0)

            <div class="history-grid">

                @foreach($historiques['h'] as $item)
                    <div class="history-card"
                         data-title="{{ strtolower($item->codeSalle ?? $item->nomMateriel) }}"
                         data-type="{{ $item->codeSalle ? 'salle' : 'materiel' }}"
                         data-status="{{ strtolower($item->statut) }}"
                         data-date="{{ \Carbon\Carbon::parse($item->dateDebut)->format('Y-m-d') }}">

                        <div class="history-icon">
                            {{ $item->codeSalle ? '🏫' : '💻' }}
                        </div>

                        <div class="history-content">
                            <h3 class="history-title">
                                {{ $item->codeSalle ?? $item->nomMateriel }}
                            </h3>

                            <div class="history-dates">
                                <p><strong>Début :</strong>
                                    {{ \Carbon\Carbon::parse($item->dateDebut)->format('d/m/Y H:i') }}
                                </p>

                                <p><strong>Fin :</strong>
                                    {{ \Carbon\Carbon::parse($item->dateFin)->format('d/m/Y H:i') }}
                                </p>
                            </div>

                            <span class="status-badge status-{{ strtolower($item->statut) }}">
                            {{ ucfirst($item->statut) }}
                        </span>
                        </div>

                    </div>
                @endforeach

            </div>

        @else

            <div class="empty-state">
                <div class="empty-icon">📭</div>
                <h3>Aucun historique</h3>
                <p>Vous n'avez encore effectué aucune réservation.</p>
            </div>

        @endif

    </div>

@endsection
