<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'decouvert') {
    header("Location: index.php");
    exit();
}
?>
<h2>Bienvenue decouvert : <?= htmlspecialchars($_SESSION['username']) ?></h2>
<p><a href="index1.php">Gérer le site</a></p>
<a href="Logout.php">Déconnexion</a>