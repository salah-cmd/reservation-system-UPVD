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

    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const modal = document.getElementById('detailsReservationModal');

            modal.addEventListener('show.bs.modal', function (event) {

                const button = event.relatedTarget;
                const id = button.dataset.id;

                fetch(`/dashboard/admin/reservations/${id}`)
                    .then(response => response.json())
                    .then(data => {

                        if (!data.success) {
                            alert("Réservation introuvable");
                            return;
                        }


                        const d = data.details;
                        document.getElementById('detailsTitle').innerText = 'Détails réservation N°: ' +d.idReservation;
                        // ================= UTILISATEUR =================
                        document.getElementById('res_user_id').innerText = d.idUtilisateur;
                        document.getElementById('res_user_nom').innerText = d.nomComplet;
                        document.getElementById('res_user_email').innerText = d.adresseMail;
                        document.getElementById('res_user_tel').innerText = d.telephone;
                        document.getElementById('res_user_role').innerText = d.role;

                        // ================= SALLE =================
                        if (d.codeSalle) {
                            document.getElementById('res_salle_nom').innerText = d.codeSalle;
                            document.getElementById('res_salle_capacite').innerText = d.capacite;
                            document.getElementById('res_salle_type').innerText = d.typeSalle;
                            document.getElementById('res_salle_description').innerText = d.description;
                        } else {
                            document.getElementById('res_salle_nom').innerText = "Aucune salle";
                            document.getElementById('res_salle_capacite').innerText = "-";
                            document.getElementById('res_salle_type').innerText = "-";
                            document.getElementById('res_salle_description').innerText = "-";
                        }

                        // ================= RESERVATION =================

                        if(d.statut === "valider"){
                            document.getElementById('text_traiteLe').innerText = "Validée le: ";
                            document.getElementById('text_traitePar').innerText = "Validée par: ";
                            document.getElementById('res_traiteLe').innerText = d.traiteLe;
                            document.getElementById('res_traitePar').innerText = d.traitePar;
                            console.log("Valider par: " + d.traitePar);
                        }else if(d.statut === "refuser"){
                            document.getElementById('text_traiteLe').innerText = "Refusée le: ";
                            document.getElementById('text_traitePar').innerText = "Refusée par: ";
                            document.getElementById('res_traiteLe').innerText = d.traiteLe;
                            document.getElementById('res_traitePar').innerText = d.traitePar;
                        }else if(d.statut === "annulee"){
                            document.getElementById('text_traiteLe').innerText = "Annulée le: ";
                            document.getElementById('text_traitePar').innerText = "Annulée par: ";
                            document.getElementById('res_traiteLe').innerText = d.traiteLe;
                            document.getElementById('res_traitePar').innerText = d.traitePar;
                        }else {
                            document.getElementById('text_traiteLe').innerText = "";
                            document.getElementById('text_traitePar').innerText = "";
                            document.getElementById('res_traiteLe').innerText = "";
                            document.getElementById('res_traitePar').innerText = "";
                        }
                        document.getElementById('res_debut').innerText = d.dateDebut;
                        document.getElementById('res_fin').innerText = d.dateFin;
                        document.getElementById('res_motif').innerText = d.motif;
                        document.getElementById('res_nbPersonnes').innerText = d.nbPersonnes;
                        document.getElementById('res_dateReservation').innerText = d.dateReservation;

                        // ================= MATERIELS =================
                        const tbody = document.getElementById('res_materiels');
                        tbody.innerHTML = "";

                        if (data.materiels.length === 0) {
                            tbody.innerHTML = `
                            <tr>
                                <td colspan="6" class="text-center text-muted">
                                    Aucun matériel demandé
                                </td>
                            </tr>
                        `;
                        } else {
                            data.materiels.forEach(mat => {
                                tbody.innerHTML += `
                                <tr>
                                    <td>${mat.codeMat}</td>
                                    <td>${mat.nom}</td>
                                    <td>${mat.qteDemande}</td>
                                    <td>${mat.statut}</td>
                                    <td>${mat.dateRetour ?? '-'}</td>
                                </tr>
                                `;
                            });
                        }


                        // ================= BOUTONS ACTION =================
                        document.getElementById('validateForm').action =
                            `/dashboard/admin/reservations/${id}/valider`;

                        document.getElementById('refuseForm').action =
                            `/dashboard/admin/reservations/${id}/refuser`;

                        document.getElementById('emailValidate').value = d.adresseMail;
                        document.getElementById('emailRefuse').value = d.adresseMail;
                        document.getElementById('idReservation').value = d.idReservation;
                        document.getElementById('dateDebut').value = d.dateDebut;
                        document.getElementById('dateFin').value = d.dateFin;
                        document.getElementById('salle').value = d.codeSalle;
                        document.getElementById('motif').value = d.motif;
                        document.getElementById('nomComplet').value = d.nomComplet;
                        document.getElementById('nbMateriels').value = d.nbMateriels;
                    })
                    .catch(error => {
                        console.error(error);
                        alert("Erreur lors du chargement");
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
                            <button class="btn-edit"
                                    data-id="{{ $r->idReservation }}"
                                    data-bs-toggle="modal"
                                    data-bs-target="#detailsReservationModal">
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

        <div class="modal fade" id="detailsReservationModal" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title" id="detailsTitle"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <div class="row g-4">

                            <!-- ================= UTILISATEUR ================= -->
                            <div class="col-md-6">
                                <div class="card shadow-sm h-100">
                                    <div class="card-header bg-light fw-bold">
                                        👤 Informations utilisateur
                                    </div>
                                    <div class="card-body">
                                        <p><strong>ID Utilisateur :</strong> <span id="res_user_id"></span></p>
                                        <p><strong>Nom Complet:</strong> <span id="res_user_nom"></span></p>
                                        <p><strong>Email :</strong> <span id="res_user_email"></span></p>
                                        <p><strong>Téléphone :</strong> <span id="res_user_tel"></span></p>
                                        <p><strong>Rôle :</strong> <span id="res_user_role"></span></p>
                                    </div>
                                </div>
                            </div>

                            <!-- ================= SALLE ================= -->
                            <div class="col-md-6">
                                <div class="card shadow-sm h-100">
                                    <div class="card-header bg-light fw-bold">
                                        🏢 Informations salle
                                    </div>
                                    <div class="card-body">
                                        <p><strong>Code :</strong> <span id="res_salle_nom"></span></p>
                                        <p><strong>Capacité :</strong> <span id="res_salle_capacite"></span></p>
                                        <p><strong>Type de la salle :</strong> <span id="res_salle_type"></span></p>
                                        <p><strong>Description :</strong> <span id="res_salle_description"></span></p>
                                    </div>
                                </div>
                            </div>

                            <!-- ================= MATERIEL ================= -->
                            <div class="col-md-6">
                                <div class="card shadow-sm h-100">
                                    <div class="card-header bg-light fw-bold">
                                        🖥️ Matériels demandés
                                    </div>
                                    <div class="card-body">

                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered align-middle mb-0">
                                                <thead class="table-light">
                                                <tr>
                                                    <th>Code</th>
                                                    <th>Nom</th>
                                                    <th>Qté Demandée</th>
                                                    <th>Statut</th>
                                                    <th>Date Retour</th>
                                                </tr>
                                                </thead>
                                                <tbody id="res_materiels">
                                                <!-- rempli dynamiquement -->
                                                </tbody>
                                            </table>
                                        </div>

                                    </div>

                                </div>
                            </div>

                            <!-- ================= RESERVATION ================= -->
                            <div class="col-md-6">
                                <div class="card shadow-sm h-100">
                                    <div class="card-header bg-light fw-bold">
                                        📅 Détails réservation
                                    </div>
                                    <div class="card-body">
                                        <p><strong>Du :</strong> <span id="res_debut"></span></p>
                                        <p><strong>Au :</strong> <span id="res_fin"></span></p>
                                        <p><strong>Motif :</strong> <span id="res_motif"></span></p>
                                        <p><strong>Nombre de personnes :</strong> <span id="res_nbPersonnes"></span></p>
                                        <p><strong id="text_traiteLe"></strong> <span id="res_traiteLe"></span></p>
                                        <p><strong id="text_traitePar"></strong> <span id="res_traitePar"></span></p>
                                        <p><strong>Date Réservation :</strong> <span id="res_dateReservation"></span></p>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>


                    <div class="modal-footer">

                        <form id="validateForm" method="POST">
                            @csrf
                            <input type="hidden" name="emailUtilisateur" id="emailValidate">
                            <input type="hidden" name ="idReservation" id="idReservation">
                            <input type="hidden" name ="dateDebut" id="dateDebut">
                            <input type="hidden" name ="dateFin" id="dateFin">
                            <input type="hidden" name ="salle" id="salle">
                            <input type="hidden" name ="motif" id="motif">
                            <input type="hidden" name="nomComplet" id="nomComplet">
                            <input type="hidden" name="nbMateriels" id="nbMateriels">
                            <button type="submit" class="btn btn-success">
                                Valider
                            </button>
                        </form>

                        <form id="refuseForm" method="POST">
                            @csrf
                            <input type="hidden" name="emailUtilisateur" id="emailRefuse">
                            <button type="submit" class="btn btn-danger">
                                Refuser
                            </button>
                        </form>

                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Annuler
                        </button>

                        @if ($errors->any())
                            <script>
                                alert(@json($errors->first()));
                            </script>
                        @endif

                        @if (session('success'))
                            <script>
                                alert(@json(session('success')));
                            </script>
                        @endif

                    </div>

                </div>
            </div>
        </div>

    </div>
@endsection
