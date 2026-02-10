<?php
session_start();

// Récupérer le message d'erreur
$message = $_SESSION['error'] ?? "Une erreur inconnue s'est produite.";

// Supprimer le message après affichage
unset($_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Page d'erreur</title>

    <!-- FontAwesome -->
    <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8d7da;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .error-box {
            background: white;
            padding: 40px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            width: 450px;
        }

        .error-box i {
            font-size: 60px;
            color: #dc3545;
            margin-bottom: 20px;
        }

        .error-box h2 {
            color: #dc3545;
            margin-bottom: 15px;
        }

        .error-box p {
            color: #333;
            font-size: 16px;
            margin-bottom: 25px;
        }

        .btn-retour {
            background-color: #007bff;
            color: white;
            padding: 10px 18px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
            transition: 0.3s;
        }

        .btn-retour:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="error-box">
    <i class="fas fa-exclamation-triangle"></i>

    <h2>Erreur</h2>

    <p><?php echo htmlspecialchars($message); ?></p>

    <!-- Bouton retour -->
    <a href="index1.php" class="btn-retour">
        <i class="fas fa-arrow-left"></i> Retour à l'accueil
    </a>
</div>

</body>
</html>
