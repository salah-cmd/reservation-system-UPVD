<h2>Votre réservation a été refusée</h2>

<p>Bonjour {{ $data['nomComplet'] }}</p>

<p>
    Votre demande de réservation n’a pas été acceptée par l’administration.
    Voici les détails de votre demande :
</p>

<ul>
    <li><strong>N° réservation :</strong> {{ $data['idReservation'] }}</li>
    <li><strong>Salle :</strong> {{ $data['salle'] ?? 'Aucune' }}</li>
    <li><strong>Nombre de matériels :</strong> {{ $data['nbMateriels'] ?? 0 }}</li>
    <li><strong>Date début :</strong> {{ $data['dateDebut'] }}</li>
    <li><strong>Date fin :</strong> {{ $data['dateFin'] }}</li>
    <li><strong>Motif :</strong> {{ $data['motif'] }}</li>
</ul>

<p>
    <strong>Motif du refus par l’administrateur :</strong><br>
    {{ $data['motifRefus'] ?? 'Aucun motif précisé.' }}
</p>

<p>
    Vous pouvez effectuer une nouvelle demande en tenant compte des remarques indiquées.<br>
    Pour toute question, n’hésitez pas à nous contacter.
</p>

<p>
    Cordialement,<br>
    Système de Réservation - UPVD
</p>
