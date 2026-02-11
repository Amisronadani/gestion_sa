<?php
session_start();
require 'connexion.php';

// Redirection si déjà connecté
if (isset($_SESSION['username'])) {
    if ($_SESSION['role'] === 'decouvert') {
        header("Location: index1.php");
    } else {
        header("Location: accueil.php");
    }
    exit();
}

$erreur = '';

if (isset($_POST['Connex'])) {

    $username = $_POST['nom_utilisateur'];
    $password = $_POST['mot_de_passe'];

    $sql = "SELECT * FROM utilisateurs WHERE nom_utilisateur = :nom";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['nom' => $username]);

    if ($stmt->rowCount() == 1) {

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (password_verify($password, $user['mot_de_passe'])) {

            session_regenerate_id(true);

            $_SESSION['username'] = $user['nom_utilisateur'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] === 'decouvert') {
                header("Location: index1.php");
            } else {
                header("Location: accueil.php");
            }
            exit();

        } else {
            $erreur = "Mot de passe incorrect.";
        }

    } else {
        $erreur = "Nom d'utilisateur introuvable.";
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Connexion</title>
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
      position: relative;
    }
    h2 {
      text-align: center;
    }
    input, button {
      width: 100%;
      margin: 10px 0;
      padding: 10px;
      border-radius: 5px;
      border: 1px solid #aaa;
    }
    button {
      background: #007BFF;
      color: white;
      border: none;
    }
    .links {
      text-align: center;
      margin-top: 10px;
    }
    .links a {
      text-decoration: none;
      color: #007BFF;
    }
    .error {
      color: red;
      text-align: center;
    }
    .toggle-password {
      cursor: pointer;
      position: absolute;
      right: 10px;
      top: 50%;
      transform: translateY(-50%);
      color: #007BFF;
    }
	 .link {
      text-align: left;
      padding-right: 10px;
    }
    .link a {
      text-decoration: none;
      color: #FFFA50;
    }
  </style>
</head>
<body>
  <div class="form-container">
    <h2>Se connecter</h2>

    <?php if (!empty($erreur)) echo "<p class='error'>$erreur</p>"; ?>

    <form method="post" action="">
      <input type="email" name="nom_utilisateur" placeholder="Nom d'utilisateur" required>
      <div style="position: relative;">
        <input type="password" name="mot_de_passe" id="password" placeholder="Mot de passe" required>
        <span class="toggle-password" id="togglePassword">👁️</span>
      </div>
      <button type="submit" name="Connex">Connexion</button>
    </form>
	
     <div class="lin">
      <!--<a href="Régister.php">s'inscrire ?</a> -->

    </div>
	
    <div class="links">
     /<!--- <a href="forgot_password.php">Mot de passe oublié ?</a>-->
      
    </div>
  </div>

  <script>
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');

    togglePassword.addEventListener('click', function () {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.textContent = type === 'password' ? '👁️' : '🚫';
    });
  </script>
</body>
</html>