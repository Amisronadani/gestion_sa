<?php
require 'connexion.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $email = trim($_POST['email']);

    $token = bin2hex(random_bytes(50));
    $expire = date("Y-m-d H:i:s", strtotime('+1 hour'));

    $sql = "SELECT * FROM utilisateurs WHERE nom_utilisateur = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {

        $update = $pdo->prepare("
            UPDATE utilisateurs 
            SET reset_token = ?, 
                token_expiration = ?
            WHERE nom_utilisateur = ?
        ");

        $update->execute([$token, $expire, $email]);

        $link = "http://localhost/reset_password.php?token=$token";

        $message = "Lien envoyé : <a href='$link'>$link</a>";

    } else {
        $message = "Aucun compte trouvé.";
    }
}
?>

<form method="POST">
    <h3>Mot de passe oublié</h3>
    <input type="email" name="email" placeholder="Votre email" required>
    <button type="submit">Envoyer</button>
</form>

<p><?= $message ?></p>
