@extends('layouts.dashboard')
@section('title', 'Gestion materiels')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/inventaire.css') }}">
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const rows = document.querySelectorAll('.materiel-row');
            const searchInput = document.getElementById('searchInput');

            searchInput.addEventListener('input', () => {
                const query = searchInput.value.toLowerCase();

                rows.forEach(row => {
                    row.style.display ="";
                    const text = (row.dataset.search || "");
                    if (!text.includes(query)) row.style.display = "none";
                });
            });

        });
    </script>
@endsection

@section('content')
    <div class="users-page">

        <div class="page-head">
            <h1 class="page-title">Gestion des matériels</h1>

            <div class="toolbar">
                <button type="button" class="btn-primary"
                        data-bs-toggle="modal"
                        data-bs-target="#addMaterielModal">
                    <span class="icon">+</span>
                    Ajouter un matériel
                </button>

                <div class="filters">
                    <div class="input-search">
                        <span class="search-ico">🔍</span>
                        <input id="searchInput" type="text" placeholder="Recherche...">
                    </div>

                </div>
            </div>
        </div>

        <div class="card">
            <div class="table-wrap">
                <table class="users-table">
                    <thead>
                    <tr>
                        <th>Code</th>
                        <th>Nom</th>
                        <th>Quantité</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                    </thead>

                    <tbody>
                    @forelse($materiels as $m)
                        @php
                            // Texte "indexé" pour la recherche (tout en minuscule)
                            $searchText = strtolower(
                                $m->codeMat.' '.$m->nom.' '.$m->qteTotal.' '.$m->description
                            );
                        @endphp

                        <tr class="materiel-row"  data-search="{{ $searchText }}">
                            <td class="muted">{{ $m->codeMat }}</td>
                            <td class="name">{{ $m->nom }}</td>
                            <td class="muted">{{ $m->qteTotal }}</td>
                            <td>{{ ucfirst($m->description) }}</td>
                            <td>
                                <button type="button" class="btn-edit" title="éditer"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editMaterielModal"
                                        data-codemat="{{ $m->codeMat }}"
                                        data-nom="{{ $m->nom }}"
                                        data-description="{{ $m->description }}"
                                        data-qtetotal="{{ $m->qteTotal }}">
                                    📝
                                </button>

                                <form method="POST" action="{{ route('admin.materiels.destroy', $m->codeMat) }}" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-delete"
                                            title="supprimer"
                                            onclick="return confirm('Voulez-vous vraiment supprimer ce matériel ?');">
                                        🗑
                                    </button>
                                </form>

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty">Aucun matériel trouvé</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="modal fade" id="addMaterielModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">Ajouter une salle</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                    </div>

                    <form method="POST" action="{{ route('admin.materiels.store') }}">
                        @csrf

                        <div class="modal-body">

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nom *</label>
                                    <input name="nom" class="form-control" type="text" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Quantité total *</label>
                                    <input name="qteTotal" class="form-control" type="number" required>
                                </div>


                                <div class="col-md-6">
                                    <label class="form-label">Description </label>
                                    <textarea name ="description" class ="form-control"></textarea>
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

        <div class="modal fade" id="editMaterielModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">Modifier matériel</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <form id="editUserForm" method="POST" action="{{ route('admin.materiels.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="modal-body">
                            <input type="hidden" name="codeMat" id="edit_codeMat">

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nom</label>
                                    <input name="nom" id="edit_nom" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Quantité total</label>
                                    <input name="qteTotal" id="edit_qteTotal" class="form-control" type="number" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Description </label>
                                    <textarea name ="description" id="edit_description" class ="form-control"></textarea>
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
                            const editModal = document.getElementById('editMaterielModal');

                            editModal.addEventListener('show.bs.modal', (event) => {
                                const btn = event.relatedTarget; // le bouton "Éditer" cliqué

                                document.getElementById('edit_codeMat').value = btn.dataset.codemat;
                                document.getElementById('edit_nom').value = btn.dataset.nom ?? '';
                                document.getElementById('edit_qteTotal').value = btn.dataset.qtetotal ?? '';
                                document.getElementById('edit_description').value = btn.dataset.description ?? '';
                            });
                        });


                    </script>


                </div>
            </div>
        </div>


    </div>
@endsection
