<?php
session_start();
include 'connexion.php';

$erreur = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['nom_utilisateur']);
    $password = $_POST['mot_de_passe'];
    $confirm = $_POST['confirmer'];
    $role = $_POST['role'];

    if ($password !== $confirm) {
        $erreur = "Les mots de passe ne correspondent pas.";
    } else {
        // Vérifie si l'utilisateur existe déjà
        $sql = "SELECT * FROM utilisateurs WHERE nom_utilisateur = :nom_utilisateur";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nom_utilisateur', $username, PDO::PARAM_STR);
        $stmt->execute();
        $res = $stmt->fetchAll();

        if (count($res) > 0) {
            $erreur = "Nom d'utilisateur déjà utilisé.";
        } else {
            // Insérer utilisateur
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO utilisateurs (nom_utilisateur, mot_de_passe, role) VALUES (:nom_utilisateur, :mot_de_passe, :role)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nom_utilisateur', $username, PDO::PARAM_STR);
            $stmt->bindParam(':mot_de_passe', $hash, PDO::PARAM_STR);
            $stmt->bindParam(':role', $role, PDO::PARAM_STR);
            if ($stmt->execute()) {
                $_SESSION['message'] = "Nouvel utilisateur ajouté avec succès.";
                header("Location: index.php");
                exit();
            } else {
                $erreur = "Erreur lors de l'ajout de l'utilisateur.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Inscription</title>
  <style>
    body {
      font-family: Arial;
      background-color: #f5f5f5;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .form-container {
      background: white;
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0px 0px 10px #ccc;
      width: 300px;
    }
    h2 {
      text-align: center;
    }
    input, select, button {
      width: 100%;
      margin: 10px 0;
      padding: 10px;
      border-radius: 5px;
      border: 1px solid #aaa;
    }
    button {
      background: #28a745;
      color: white;
      border: none;
    }
    .error {
      color: red;
      text-align: center;
    }
    .message {
      color: green;
      text-align: center;
    }
    .link {
      text-align: center;
      margin-top: 10px;
    }
  </style>
</head>
<body>
  <div class="form-container">
    <h2>Créer un compte</h2>

    <?php if ($erreur) echo "<p class='error'>$erreur</p>"; ?>
    <?php if (isset($_SESSION['message'])): ?>
        <p class='message'><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></p>
    <?php endif; ?>

    <form method="post" action="">
      <input type="email" name="nom_utilisateur" placeholder="Nom d'utilisateur" required>
      <input type="password" name="mot_de_passe" placeholder="Mot de passe" required>
      <input type="password" name="confirmer" placeholder="Confirmer mot de passe" required>

      <select name="role" required>
        <option value="">-- Sélectionner un rôle --</option>
        <option value="assistant">assistant</option>       
        <option value="decouvert">Decouvert</option>       
	 </select>

      <button type="submit">S'inscrire</button>
    </form>

    <div class="link">
      <a href="index1.php">← Retour à la connexion</a>
    </div>
  </div>
</body>
</html>