<?php
$conn = new mysqli("localhost", "root", "", "car");

// Vérifiez la connexion
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $conn->real_escape_string(trim($_POST['email']));
    $token = bin2hex(random_bytes(50));
    $expire = date("Y-m-d H:i:s", strtotime('+1 hour'));

    $check = $conn->query("SELECT * FROM utilisateurs WHERE nom_utilisateur='$email'");
    if ($check && $check->num_rows > 0) {
        $conn->query("UPDATE users SET reset_token='$token', reset_expire='$expire' WHERE nom_utilisateur='$email'");

        $resetLink = "http://localhost/reset_password.php?token=$token";

        // Simulation d'envoi email (ici juste affichage)
        echo "Un lien de réinitialisation a été envoyé : <a href='$resetLink'>$resetLink</a>";
    } else {
        echo "Aucun compte trouvé avec cet email.";
    }
}
?>

<form method="POST">
    <input type="email" name="email" placeholder="Votre email" required>
    <button type="submit">Envoyer</button>
</form>