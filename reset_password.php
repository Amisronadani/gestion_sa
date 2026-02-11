<?php
require 'connexion.php';

$token = $_GET['token'] ?? '';

if (empty($token)) {
    die("Token manquant");
}

// Vérifier validité
$sql = "
    SELECT * FROM utilisateurs
    WHERE reset_token = ?
    AND token_expiration > NOW()
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$token]);

if ($stmt->rowCount() == 0) {
    die("Token invalide ou expiré");
}

// Traitement nouveau mot de passe
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $newpass = password_hash($_POST['newpass'], PASSWORD_DEFAULT);

    $update = $pdo->prepare("
        UPDATE utilisateurs 
        SET mot_de_passe = ?,
            reset_token = NULL,
            token_expiration = NULL
        WHERE reset_token = ?
    ");

    $update->execute([$newpass, $token]);

    echo "Mot de passe réinitialisé avec succès.";
}
?>

<form method="POST">
    <h3>Nouveau mot de passe</h3>
    <input type="password" name="newpass" required>
    <button type="submit">Réinitialiser</button>
</form>
