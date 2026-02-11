<?php
include('connexion.php');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f2f5; /* Couleur de fond douce */
        }
        header {
            background-color: #007bff;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
        }
        .logout {
            background-color: darkorange;
            color: white;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .logout:hover {
            background-color: #c82333;
        }
        nav {
            display: flex;
            flex-direction: column;
            background-color: #ffffff;
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            width: 120px; /* Largeur réduite pour le menu */
            height: 100vh; /* Hauteur pleine */
            position: fixed; /* Fixe le menu à gauche */
          /*  border-radius: 5px; /* Coins arrondis */
        }
        .menu a {
            margin: 15px 0; /* Espacement vertical */
            text-decoration: none;
            color: #333;
            text-align: center;
            display: flex; /* Utiliser flex pour aligner l'icône et le texte */
            flex-direction: column; /* Alignement vertical */
            align-items: center; /* Centrer les éléments */
            padding: 10px; /* Espace autour des liens */
            border-radius: 5px; /* Coins arrondis pour les liens */
            transition: background-color 0.3s ease; /* Transition pour survol */
        }
        .menu a:hover {
            background-color: #e9ecef; /* Couleur de survol douce */
            color: #007bff; /* Changer couleur du texte au survol */
        }
        .menu a i {
            font-size: 25px;
            margin-bottom: 5px; /* Espacement sous l'icône */
        }
        main {
            margin-left: 200px; /* Décalage pour le menu à gauche */
            padding:5px;
        }
        img{
            width: 100px;
            height: 80px;

        }
        .reg {
            background-color: #55522a;
            color: white;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .reg:hover {
            background-color: #c82002;
        }
    </style>
</head>
<body>

<header>
    <img src="images/logow_bgf.ico" alt="">

    <a href="Régister.php"><i class="fas fa-registered" style="color:dark;"><button class="reg">S'inscrire </button></i>
    <a href="Logout.php"><button class="logout">Déconnexion</button></a>
            
        </a>
</header>

<nav>
    <div class="menu">
        <a href="afficheprofil.php">
            <i class="fas fa-user"></i>
            Clients
        </a>
        <a href="affichecompte.php">
           <i class="fas fa-piggy-bank" style="color:purple; font-size:22px;"></i>
            Compte
        </a>
        <a href="affichedossier.php">
            <i class="fas fa-folder-open"></i>
            Dossier de decouvrement
        </a>
        <a href="affichecredit1.php">
            <i class="fas fa-credit-card" style="color:darkgreen; font-size:22px;"></i>
            Credit
        </a>
         <a href="afficheecheance.php">
            <i class="fas fa-calendar-alt" style="color:darkred; font-size:22px;"></i>
            Echeances 
        </a>
        
         <a href="historique.php">
             <i class="fas fa-history" style="color:darkorange; font-size:22px;"></i>
            Historique 
        </a>
        
        <a href="Régister.php">
             <i class="fas fa-registered" style="color:dark;"></i>
            S'inscrire 
        </a>
        
    </div>
</nav>
<main>

</main>
</body>
</html>