@extends('layouts.dashboard')
@section('title', 'Gestion salles')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/inventaire.css') }}">
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const select = document.getElementById('typeFilter');
            const rows = document.querySelectorAll('.salle-row');

            const searchInput = document.getElementById('searchInput');

            function applyTypeFilter() {
                const selectedType = select.value;

                rows.forEach(row => {
                    const rowType = row.dataset.statut;
                    if (selectedType === "" || rowType === selectedType) {
                        row.style.display = "";
                    } else {
                        row.style.display = "none";
                    }
                });
            }

            // 1) Filtre rôle (inchangé, juste appelle la fonction)
            select.addEventListener('change', () => {
                applyTypeFilter();

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
                    applyTypeFilter();
                    return;
                }
                // sinon : on repart de l’état type, puis on filtre par texte
                applyTypeFilter();
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
    <div class="users-page">

        <div class="page-head">
            <h1 class="page-title">Gestion des salles</h1>

            <div class="toolbar">
                <button type="button" class="btn-primary"
                        data-bs-toggle="modal"
                        data-bs-target="#addSalleModal">
                    <span class="icon">+</span>
                    Ajouter une salle
                </button>

                <div class="filters">
                    <div class="input-search">
                        <span class="search-ico">🔍</span>
                        <input id="searchInput" type="text" placeholder="Recherche...">
                    </div>

                    <select id="typeFilter">
                        <option value="">Filtrer par type</option>
                        <option value="amphi">Amphi</option>
                        <option value="groupe">Groupe</option>
                        <option value="tp">TP</option>
                        <option value="reunion">Réunion</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="table-wrap">
                <table class="users-table">
                    <thead>
                    <tr>
                        <th>Code</th>
                        <th>Capacité</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Disponibilité</th>
                        <th>Action</th>
                    </tr>
                    </thead>

                    <tbody>
                    @forelse($salles as $s)
                        @php
                            $statutTxt = $s->statut ? 'disponible ' : 'indisponible ';

                            // Texte "indexé" pour la recherche (tout en minuscule)
                            $searchText = strtolower(
                                $s->codeSalle.' '.$s->capacite.' '.$s->typeSalle.' '.$s->description.' '.$statutTxt
                            );
                        @endphp

                        <tr class="salle-row" data-statut="{{$s->typeSalle}}" data-search="{{ $searchText }}">
                            <td class="muted">{{ $s->codeSalle }}</td>
                            <td class="name">{{$s->capacite }}</td>
                            <td class="muted">{{ $s->typeSalle }}</td>
                            <td>{{ ucfirst($s->description) }}</td>
                            <td>
                                @if($s->statut)
                                    <span class="badge badge-green">Disponible</span>
                                @else
                                    <span class="badge badge-gray">Indisponible</span>
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn-edit"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editSalleModal"
                                        data-codesalle="{{ $s->codeSalle }}"
                                        data-capacite="{{ $s->capacite }}"
                                        data-description="{{ $s->description }}"
                                        data-type="{{ $s->typeSalle }}"
                                        data-statut="{{ $s->statut ? 'disponible' : 'indisponible' }}">
                                    Éditer
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

        <div class="modal fade" id="addSalleModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">Ajouter une salle</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                    </div>

                    <form method="POST" action="{{ route('admin.salles.store') }}">
                        @csrf

                        <div class="modal-body">

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Capacité *</label>
                                    <input name="capacite" class="form-control" type="number" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Type *</label>
                                    <select name="type" class="form-select" required>
                                        <option value="amphi">Amphi</option>
                                        <option value="groupe">Groupe</option>
                                        <option value="tp">TP</option>
                                        <option value="reunion">Réunion</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Description </label>
                                    <textarea name ="description" class ="form-control"></textarea>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Statut *</label>
                                    <select name="statut" class="form-select" required>
                                        <option value="disponible">Disponible</option>
                                        <option value="indisponible">Indisponible</option>
                                    </select>
                                </div>
                            </div>

                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                        </div>
                    </form>


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

        <div class="modal fade" id="editSalleModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">Modifier salle</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <form id="editUserForm" method="POST" action="{{ route('admin.salles.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="modal-body">
                            <input type="hidden" name="codeSalle" id="edit_codeSalle">

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Capacité</label>
                                    <input name="capacite" id="edit_capacite" class="form-control" type="number" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Type</label>
                                    <select name="type" id="edit_type" class="form-select" required>
                                        <option value="amphi">Amphi</option>
                                        <option value="groupe">Groupe</option>
                                        <option value="tp">TP</option>
                                        <option value="reunion">Réunion</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Description </label>
                                    <textarea name ="description" id="edit_description" class ="form-control"></textarea>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Statut *</label>
                                    <select name="statut" id="edit_statut" class="form-select" required>
                                        <option value="disponible">Disponible</option>
                                        <option value="indisponible">Indisponible</option>
                                    </select>
                                </div>

                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                        </div>
                    </form>

                    <script>
                        document.addEventListener('DOMContentLoaded', () => {
                            const editModal = document.getElementById('editSalleModal');

                            editModal.addEventListener('show.bs.modal', (event) => {
                                const btn = event.relatedTarget; // le bouton "Éditer" cliqué

                                document.getElementById('edit_codeSalle').value = btn.dataset.codesalle;
                                document.getElementById('edit_capacite').value = btn.dataset.capacite ?? '';
                                document.getElementById('edit_description').value = btn.dataset.description ?? '';
                                document.getElementById('edit_type').value = btn.dataset.type ?? '';
                                document.getElementById('edit_statut').value = btn.dataset.statut ?? 'disponible';

                            });
                        });


                    </script>


                </div>
            </div>
        </div>


    </div>
@endsection


