@php use Carbon\Carbon;
use \Illuminate\Support\Str;
@endphp
@extends('layouts.admin')
@section('title', 'Dashboard Admin')

@section('content')
    <div class="topcard">
        <h1>Bienvenue {{strtoupper($user['nomUtilisateur']) . ' ' . $user['prenomUtilisateur']}}.</h1>
        <p>Administrateur: <u>#{{$user['idUtilisateur']}}</u></p>
    </div>

    <section class="cards">
        <div class="card stat">
            <div class="stat-title">Utilisateurs</div>
            <div class="stat-value">{{$stats['utilisateurs']}}</div>
            <div class="stat-sub">Total utilisateurs</div>
        </div>

        <div class="card stat">
            <div class="stat-title">Salles</div>
            <div class="stat-value">{{$stats['salles']}}</div>
            <div class="stat-sub">Total salles</div>
        </div>

        <div class="card stat">
            <div class="stat-title">Réservations</div>
            <div class="stat-value">{{$stats['reservationsEnAttente']}}</div>
            <div class="stat-sub">en attente</div>
        </div>

        <div class="card stat">
            <div class="stat-title">Matériels</div>
            <div class="stat-value">{{$stats['materiels']}}</div>
            <div class="stat-sub">Total Matériels</div>
        </div>
    </section>

    <section class="grid">
        <div class="card">
            <div class="card-head">
                <h2>Réservation récente</h2>
            </div>

            <table class="table">
                <thead>
                <tr>
                    <th>Utilisateur</th>
                    <th>Rôle</th>
                    <th>Réservation</th>
                    <th>Date</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($recentReservations as $res)
                    <tr>
                        <td>{{ $res->nomComplet }}</td>
                        <td>{{ $res->role }}</td>
                        <td>
                            @if($res->codeSalle !== '—')
                                Salle {{ $res->codeSalle }}
                            @endif
                            @if($res->nbMateriels > 0)
                                – {{ $res->nbMateriels }} matériel(s)
                            @endif
                        </td>
                        <td>{{Carbon::parse($res->dateReservation)
                            ->translatedformat('l, d M Y H:i')}}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="text-align:center; font-weight: bold;">Aucune activité récente</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
