<h2>Votre réservation a été validée</h2>

<p>Bonjour {{ $data['nomComplet'] }}</p>

<p>
    Votre demande de réservation a été <strong>acceptée</strong>.
    Voici les détails :
</p>

<ul>
    <li><strong>N° réservation :</strong> {{ $data['idReservation'] }}</li>
    <li><strong>Salle :</strong> {{ $data['salle'] ?? 'Aucune' }}</li>
    <li><strong>Date début :</strong> {{ $data['dateDebut'] }}</li>
    <li><strong>Date fin :</strong> {{ $data['dateFin'] }}</li>
    <li><strong>Motif :</strong> {{ $data['motif'] }}</li>
</ul>

{{-- Message affiché seulement si matériel demandé --}}
@if(!empty($data['nbMateriels']) && $data['nbMateriels'] > 0)
    <p>
        <strong>Information importante :</strong><br>
        Veuillez vous présenter à l’administration au moins <strong>30 minutes avant le début</strong>
        de votre réservation, muni de votre <strong>numéro de réservation</strong>, afin de récupérer le matériel réservé.
    </p>
@endif

<p>
    Nous vous remercions de respecter les horaires et le matériel réservé.<br>
    Pour toute question, n’hésitez pas à nous contacter.
</p>

<p>
    Cordialement,<br>
    Système de Réservation - UPVD
</p>
