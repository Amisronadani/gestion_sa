<?php
session_start();
require 'connexion.php'; // Connexion à la base de données

$id_echeance = $_GET['id'] ?? null;

if ($id_echeance) {
    try {
        // Suppression de l'échéance
        $stmt = $pdo->prepare("DELETE FROM echeance WHERE id_echeances = ?");
        if ($stmt->execute([$id_echeance])) {
            $_SESSION['success'] = "Échéance supprimée avec succès.";
        } else {
            $_SESSION['error'] = "Erreur lors de la suppression de l'échéance.";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Erreur : " . $e->getMessage();
    }
} else {
    $_SESSION['error'] = "ID d'échéance non spécifié.";
}

// Redirection vers afficheecheance.php
header("Location: afficheecheance.php");
exit;
?>