@extends('layouts.admin')
@section('title', 'Gestion Utilisateurs')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/inventaire.css') }}">
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const select = document.getElementById('roleFilter');
            const rows = document.querySelectorAll('.user-row');

            const searchInput = document.getElementById('searchInput');

            function applyRoleFilter() {
                const selectedRole = select.value; // "" / admin / etudiant / enseignant

                rows.forEach(row => {
                    const rowRole = row.dataset.role;

                    if (selectedRole === "" || rowRole === selectedRole) {
                        row.style.display = "";
                    } else {
                        row.style.display = "none";
                    }
                });
            }

            // 1) Filtre rôle (inchangé, juste appelle la fonction)
            select.addEventListener('change', () => {
                applyRoleFilter();

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
                // si vide => retour à l’état "filtre rôle"
                if (query === "") {
                    applyRoleFilter();
                    return;
                }
                // sinon : on repart de l’état rôle, puis on filtre par texte
                applyRoleFilter();
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
            <h1 class="page-title">Gestion des utilisateurs</h1>

            <div class="toolbar">
                <button type="button" class="btn-primary"
                        data-bs-toggle="modal"
                        data-bs-target="#addUserModal">
                    <span class="icon">+</span>
                    Ajouter un utilisateur
                </button>

                <div class="filters">
                    <div class="input-search">
                        <span class="search-ico">🔍</span>
                        <input id="searchInput" type="text" placeholder="Recherche...">
                    </div>

                    <select id="roleFilter">
                        <option value="">Filtrer par rôle</option>
                        <option value="admin">Admin</option>
                        <option value="etudiant">Étudiant</option>
                        <option value="enseignant">Enseignant</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="table-wrap">
                <table class="users-table">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom Prénom</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Téléphone</th>
                        <th>Statut</th>
                        <th>Action</th>
                    </tr>
                    </thead>

                    <tbody>
                    @forelse($users as $u)
                        @php
                            $nomComplet = trim($u->nom.' '.$u->prenom);
                            $statutTxt = $u->actif ? 'actif' : 'inactif';

                            // Texte "indexé" pour la recherche (tout en minuscule)
                            $searchText = strtolower(
                                $u->idUtilisateur.' '.$nomComplet.' '.$u->adresseMail.' '.$u->role.' '.$u->telephone
                                .' '.$statutTxt
                            );
                        @endphp

                        <tr class="user-row" data-role="{{ $u->role }}" data-search="{{ $searchText }}">
                            <td class="muted">{{ $u->idUtilisateur }}</td>
                            <td class="name">{{$u->nom }} {{ $u->prenom }}</td>
                            <td class="muted">{{ $u->adresseMail }}</td>
                            <td>{{ ucfirst($u->role) }}</td>
                            <td class="muted">{{$u->telephone}}</td>
                            <td>
                                @if($u->actif)
                                    <span class="badge badge-green">Actif</span>
                                @else
                                    <span class="badge badge-gray">Inactif</span>
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn-edit"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editUserModal"
                                        data-id="{{ $u->idUtilisateur }}"
                                        data-nom="{{ $u->nom }}"
                                        data-prenom="{{ $u->prenom }}"
                                        data-email="{{ $u->adresseMail }}"
                                        data-telephone="{{ $u->telephone }}"
                                        data-statut="{{ $u->actif ? 'actif' : 'inactif' }}">
                                    Éditer
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty">Aucun utilisateur trouvé</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">Ajouter un utilisateur</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                    </div>

                    <form method="POST" action="{{ route('admin.utilisateurs.store') }}">
                        @csrf

                        <div class="modal-body">

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nom *</label>
                                    <input name="nom" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Prénom *</label>
                                    <input name="prenom" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Email *</label>
                                    <input name="email" type="email" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Téléphone</label>
                                    <input name="telephone" class="form-control">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Rôle *</label>
                                    <select name="role" class="form-select" required>
                                        <option value="etudiant">Étudiant</option>
                                        <option value="enseignant">Enseignant</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Statut *</label>
                                    <select name="statut" class="form-select" required>
                                        <option value="actif">Actif</option>
                                        <option value="inactif">Inactif</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Mot de passe *</label>
                                    <input name="password" type="password" class="form-control" required minlength="8">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Confirmer *</label>
                                    <input name="password_confirmation" type="password" class="form-control" required
                                           minlength="8">
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

        <div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">Modifier utilisateur</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <form id="editUserForm" method="POST" action="{{ route('admin.utilisateurs.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="modal-body">
                            <input type="hidden" name="idUtilisateur" id="edit_idUtilisateur">

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nom</label>
                                    <input name="nom" id="edit_nom" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Prénom</label>
                                    <input name="prenom" id="edit_prenom" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input name="email" id="edit_email" type="email" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Téléphone</label>
                                    <input name="telephone" id="edit_telephone" class="form-control">
                                </div>


                                <div class="col-md-6">
                                    <label class="form-label">Statut</label>
                                    <select name="statut" id="edit_statut" class="form-select" required>
                                        <option value="actif">Actif</option>
                                        <option value="inactif">Inactif</option>
                                    </select>
                                </div>

                                {{-- Optionnel: changer mot de passe --}}
                                <div class="col-md-6">
                                    <label class="form-label">Nouveau mot de passe (optionnel)</label>
                                    <input name="password" id="edit_password" type="password" class="form-control"
                                           minlength="8">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Confirmation</label>
                                    <input name="password_confirmation" id="edit_password_confirmation" type="password"
                                           class="form-control" minlength="8">
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
                            const editModal = document.getElementById('editUserModal');

                            editModal.addEventListener('show.bs.modal', (event) => {
                                const btn = event.relatedTarget; // le bouton "Éditer" cliqué

                                document.getElementById('edit_idUtilisateur').value = btn.dataset.id;
                                document.getElementById('edit_nom').value = btn.dataset.nom ?? '';
                                document.getElementById('edit_prenom').value = btn.dataset.prenom ?? '';
                                document.getElementById('edit_email').value = btn.dataset.email ?? '';
                                document.getElementById('edit_telephone').value = btn.dataset.telephone ?? '';
                                document.getElementById('edit_role').value = btn.dataset.role ?? '';
                                document.getElementById('edit_statut').value = btn.dataset.statut ?? 'actif';

                                // vider les champs mot de passe à chaque ouverture
                                document.getElementById('edit_password').value = '';
                                document.getElementById('edit_password_confirmation').value = '';
                            });
                        });


                    </script>


                </div>
            </div>
        </div>


    </div>
@endsection
