@extends('layouts.admin')
@section('title', 'Gestion reservations')

@php
    use Carbon\Carbon;
@endphp
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/reservation.css') }}">
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const select = document.getElementById('statutfilter');
            const rows = document.querySelectorAll('.reservation-row');

            const searchInput = document.getElementById('searchInput');

            function applyStatutFilter() {
                const selectedStatut = select.value;

                rows.forEach(row => {
                    const rowType = row.dataset.statut;
                    if (selectedStatut === "" || rowType === selectedStatut) {
                        row.style.display = "";
                    } else {
                        row.style.display = "none";
                    }
                });
            }

            // 1) Filtre rôle (inchangé, juste appelle la fonction)
            select.addEventListener('change', () => {
                applyStatutFilter();

                // si une recherche est en cours, on la ré-applique après le rôle
                const query = searchInput.value.toLowerCase().trim();
                if (query !== "") {
                    rows.forEach(row => {
                        if (row.style.display !== "none") { // ne toucher que les lignes visibles par rôle
                            const text = row.innerText.toLowerCase();
                            if (!text.includes(query)) row.style.display = "none";
                        }
                    });
                }
            });

            // 2) Recherche
            searchInput.addEventListener('input', () => {
                const query = searchInput.value.toLowerCase();
                // si vide => retour à l’état "filtre type"
                if (query === "") {
                    applyStatutFilter();
                    return;
                }
                // sinon : on repart de l’état type, puis on filtre par texte
                applyStatutFilter();
                rows.forEach(row => {
                    if (row.style.display !== "none") {
                        const text = (row.dataset.search || "");
                        if (!text.includes(query)) row.style.display = "none";
                    }
                });
            });

        });
    </script>
@endsection

@section('content')
    <div class="page-head">
        <h1 class="page-title">Gestion des reservations</h1>

        <div class="toolbar">
            <div class="filters">
                <div class="input-search">
                    <span class="search-ico">🔍</span>
                    <input id="searchInput" type="text" placeholder="Recherche...">
                </div>

                <select id="statutfilter">
                    <option value="">Filtrer par Statut</option>
                    <option value="enAttente">En attente</option>
                    <option value="valider">Valider</option>
                    <option value="annulee">Annuler</option>
                    <option value="refuser">Refuser</option>
                    <option value="terminee">Terminer</option>
                    <option value="expiree">Expirer</option>
                </select>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="table-wrap">
            <table class="users-table">
                <thead>
                <tr>
                    <th>N°</th>
                    <th>Utilisateur</th>
                    <th>Salle</th>
                    <th>Matériel</th>
                    <th>Du</th>
                    <th>À</th>
                    <th>Statut</th>
                    <th>Action</th>
                </tr>
                </thead>

                <tbody>
                @forelse($reservations as $r)
                    @php
                        $dateDebut = carbon::parse($r->dateDebut)
                        ->translatedFormat('l, d M Y H:i');
                        $dateFin = carbon::parse($r->dateFin)
                        ->translatedFormat('l, d M Y H:i');
                        //Texte "indexé" pour la recherche (tout en minuscule)
                        $searchText = strtolower(
                            $r->idReservation.' '.$r->utilisateur.' '.$r->salle.' '.$r->nbMateriels.' '.
                            $dateDebut . ' ' . $dateFin . ' ' . $r->statut
                        );
                    @endphp

                    <tr class="reservation-row" data-statut="{{$r->statut}}" data-search="{{ $searchText }}">
                        <td class="muted">{{ $r->idReservation }}</td>
                        <td class="name">{{  $r->utilisateur }}</td>
                        <td class="muted">{{ $r->salle }}</td>
                        <td class="muted">{{ $r->nbMateriels }}</td>
                        <td class="date">{{ Carbon::parse($r->dateDebut)
                            ->translatedformat('d M à H:i') }}</td>
                        <td class="date">{{ Carbon::parse($r->dateFin)
                            ->translatedformat(' d M à H:i') }}</td>
                        <td>
                            @if($r->statut == 'valider')
                                <span class="badge badge-green">Valider</span>
                            @elseif($r->statut == 'enAttente')
                                <span class="badge badge-yellow">En attente</span>
                            @elseif($r->statut == 'refuser')
                                <span class="badge badge-red">Refuser</span>
                            @elseif($r->statut == 'annulee')
                                <span class="badge badge-darkGray">Annuler</span>
                            @elseif($r->statut == 'terminee')
                                <span class="badge badge-blue">Terminer</span>
                            @else
                                <span class="badge badge-gray">Expirer</span>
                            @endif
                        </td>
                        <td>
                            <button type="button" class="btn-edit"
                                    data-bs-toggle="modal">
                                Détails
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty">Aucune salle trouvée</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
