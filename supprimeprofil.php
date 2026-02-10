<?php
require 'connexion.php';

// Vérifier si l'ID est présent
if (isset($_GET['id_client'])) {
    $id_client = $_GET['id_client'];

    // Suppression du client
    $stmt = $pdo->prepare("DELETE FROM client WHERE id_client = ?");
    if ($stmt->execute([$id_client])) {
        header("Location: afficheprofil.php");
        exit();
    } else {
        echo "Erreur lors de la suppression du profil.";
    }
} else {
    echo "ID du profil non spécifié.";
}
?>