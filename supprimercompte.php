<?php
session_start();
require 'connexion.php'; // Assurez-vous d'avoir un fichier de connexion à la DB

// Vérifiez que l'identifiant du compte est passé via GET
if (isset($_GET['id'])) {
    $numero = $_GET['id'];

    // Récupérer les informations du compte pour affichage
    $stmt = $pdo->prepare("SELECT * FROM compte WHERE numero_compte = ?");
    $stmt->execute([$numero]);
    $compte = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$compte) {
        $_SESSION['error'] = "Compte non trouvé.";
        header("Location: affichecompte.php");
        exit;
    }
} else {
    $_SESSION['error'] = "ID de compte non spécifié.";
    header("Location: affichecompte.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supprimer un compte</title>
</head>
<body>
    <h1>Confirmer la suppression</h1>

    <?php if (isset($_SESSION['error'])): ?>
        <p><?php echo $_SESSION['error']; ?></p>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <p>Voulez-vous vraiment supprimer le compte numéro : <?php echo htmlspecialchars($compte['numero_compte']); ?> ?</p>

    <form action="supprimer.php?id=<?php echo $numero; ?>" method="POST">
        <button type="submit">Confirmer la suppression</button>
        <a href="affichecompte.php">Annuler</a>
    </form>
</body>
</html>