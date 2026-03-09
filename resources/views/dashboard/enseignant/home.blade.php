@extends('layouts.dashboard')
@section('title', 'Paramètres')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/enseignant/reservation.css') }}">
@endsection

@section('content')
    <div class="page-wrapper">

        <div class="page-header">
            <h1>Nouvelle Réservation</h1>
            <p>Formulaire destiné aux enseignants pour réserver une salle ou du matériel</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="card">
            <form action="{{ route('materiels.store') }}" method="POST">
                @csrf

                <input type="hidden" name="idUtilisateur" value="{{ $user['idUtilisateur'] }}">

                <div class="form-grid">

                    <div class="form-group">
                        <label>Nom</label>
                        <input type="text" value="{{ strtoupper($user['nomUtilisateur']) }}" readonly>
                    </div>

                    <div class="form-group">
                        <label>Prénom</label>
                        <input type="text" value="{{ strtoupper($user['prenomUtilisateur']) }}" readonly>
                    </div>

                    <div class="form-group">
                        <label>Date début</label>
                        <input type="datetime-local" name="dateDebut">
                    </div>

                    <div class="form-group">
                        <label>Date fin</label>
                        <input type="datetime-local" name="dateFin">
                    </div>

                    <div class="form-group full-width">
                        <label>Motif</label>
                        <input type="text" name="motif" placeholder="Ex : Cours, TP, Projet encadré…">
                    </div>

                    <div class="form-group">
                        <label>Salle</label>
                        <select name="codeSalle">
                            <option value="">Sélectionnez une salle</option>
                            @foreach($etudiants['salles'] as $salle)
                                <option value="{{ $salle->codeSalle }}">
                                    Salle {{ $salle->codeSalle }} ({{ $salle->capacite }} places)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Nombre de personnes</label>
                        <input type="number" name="nbPersonnes" min="1">
                    </div>

                </div>

                <h3 style="margin-top: 25px; margin-bottom: 10px;">Matériel disponible</h3>

                <div class="materiel-list">
                    @foreach($etudiants['materiels'] as $m)
                        <div class="mat-card">
                            <label class="mat-header">
                                <input type="checkbox"
                                       name="codeMat[]"
                                       value="{{ $m->codeMat }}"
                                       onchange="toggleQuantity('{{ $m->codeMat }}')">
                                {{ $m->nom }}
                            </label>

                            <input type="number"
                                   id="qte_{{ $m->codeMat }}"
                                   name="qteDemande[{{ $m->codeMat }}]"
                                   min="1"
                                   placeholder="Quantité"
                                   disabled
                                   class="mat-input">
                        </div>
                    @endforeach
                </div>

                <div style="margin-top: 25px; text-align: right;">
                    <button type="submit" class="btn-primary">Confirmer la Réservation</button>
                </div>

            </form>
        </div>
    </div>

    <script>
        function toggleQuantity(codeMat) {
            const checkbox = document.querySelector(`input[value="${codeMat}"]`);
            const input = document.getElementById(`qte_${codeMat}`);
            input.disabled = !checkbox.checked;
            if (!checkbox.checked) input.value = "";
        }
    </script>
@endsection
