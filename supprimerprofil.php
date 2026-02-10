<?php
session_start(); // Démarrer la session
require 'connexion.php';
$id = $_GET['id_client'];
echo"".$id;

if (isset($_GET['id_client'])) {
    $id = $_GET['id_client']; // Récupérer l'ID du client à supprimer
    try {
        // Préparation de la requête
        $stmt = $pdo->prepare("DELETE FROM client WHERE id_client = ?");
        
        // Exécution de la requête
        if ($stmt->execute([$id])) {
            // Si la suppression est réussie
            $_SESSION['success'] = "Client supprimé avec succès.";
        } else {
            // Si la suppression échoue
            $_SESSION['error'] = "Erreur lors de la suppression du client.";
        }
    } catch (PDOException $e) {
        // Gérer les erreurs d'exécution
        $_SESSION['error'] = "Erreur : " . $e->getMessage();
    }
} else {
    $_SESSION['error'] = "Aucun client à supprimer.";
}
// Redirection vers la page d'affichage du profil
header("Location: afficherprofil.php");
exit();
?>