<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Systeme de reservation -- UPVD</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
<div class="page">

    <div class="blocGauche">
        <a href="https://www.univ-perp.fr/" target="_blank"><img class="logo" src="{{ asset('images/upvd_logo.png') }}" alt="UPVD"></a>
        <div class="description">
            <h3>Système de réservation</h3><br>
            <p>  Cette plateforme permet aux étudiants, enseignants et administrateurs de gérer
                efficacement les réservations de salles et de ressources.
                Elle offre une interface simple et sécurisée pour consulter les disponibilités,
                effectuer des réservations et assurer une meilleure organisation
                des espaces au sein de l’établissement.
            </p>
        </div>

    </div>

    <!--<div class="divider" aria-hidden="true"></div>-->

    <div class="blocDroite">
            <h2 class="titre">CONNEXION</h2>
            <p class="sub">Accédez à votre espace de réservation</p>


            @if (session ('error'))
                <p class="msg error">{{ session('error') }}</p>
            @endif

            @if($errors->any())
                <ul class="msg error">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            @endif

            <form class="form" method="POST" action="/login">
                @csrf

                <div class="field">
                    <input class="input" id="identifiant" type="text" name="idUtilisateur" placeholder="identifiant" required>
                </div>

                <div class="field">
                    <input class="input" id="pass" type="password" name="password" placeholder="mot de passe" required>
                </div>

                <button class="btn" type="submit">Se connecter</button>
            </form>
    </div>

</div>
</body>
</html>
