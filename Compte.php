
<?php
require'connexion.php';
?>
<?php
session_start(); // Démarrer la session
require 'connexion.php'; // Connexion à la base de données

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $type_compte = $_POST['type_compte'];
    $solde = $_POST['solde'];
    $decouvert = $_POST['decouvert'] ?? 0; // Valeur par défaut
    $statut = $_POST['statut'];
    $id_client = $_POST['id_client']; // Assurez-vous d'avoir un champ pour l'ID du client

    // Récupérer le dernier numéro de compte
    $stmt = $pdo->query("SELECT numero_compte FROM compte ORDER BY numero_compte DESC LIMIT 1");
    $last_numero = $stmt->fetchColumn();

    // Déterminer le nouveau numéro de compte
    if ($last_numero) {
        // Extraire le numéro et l'incrémenter
        $last_number = (int) substr($last_numero, 4); // Enlève le préfixe "BGF-"
        $new_number = $last_number + 1;
    } else {
        // Si c'est le premier compte
        $new_number = 1; 
    }

    // Formater le nouveau numéro
    $numero_compte = "BGF-" . str_pad($new_number, 4, '0', STR_PAD_LEFT); // "BGF-0001"

    try {
        // Préparation de la requête d'insertion
        $stmt = $pdo->prepare("INSERT INTO compte (numero_compte, type_compte, solde, decouvert, statut, id_client) VALUES (?, ?, ?, ?, ?, ?)");
        
        // Exécution de la requête
        if ($stmt->execute([$numero_compte, $type_compte, $solde, $decouvert, $statut, $id_client])) {
            $_SESSION['success'] = "Compte créé avec succès. Numéro de compte : $numero_compte";
            // Redirection vers la page de gestion des comptes
            header("Location: affichecompte.php");
        } else {
            $_SESSION['error'] = "Erreur lors de la création du compte.";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Erreur : " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Formulaire Compte Client</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      background-color: #f4f4f4;
    }

    /* Main content styles */
    .main-content {
      padding: 20px;
    }

    form {
      background: #ffffff;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      width:800px;
      margin-left:100px;
    }

    form h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #333;
    }

    .form-row {
      display: flex;
      gap: 20px;
      margin-bottom: 20px;
    }

    .form-group {
      flex: 1;
      display: flex;
      flex-direction: column;
    }

    .form-group label {
      margin-bottom: 5px;
      font-weight: bold;
      color: #555;
      font-size: 14px;
    }

    .form-group input {
      padding: 14px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 16px;
    }
    .form-group select {
      padding: 14px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 16px;
    }

    .form-actions {
      display: flex;
      justify-content: flex-end;
      gap: 15px;
      margin-top: 15px;
    }

    .form-actions button {
      padding: 8px 16px;
      border: none;
      border-radius: 4px;
      font-size: 14px;
      cursor: pointer;
    }

    .btn-save {
      background-color: #28a745;
      color: white;
    }

    .btn-cancel {
      background-color: #dc3545;
      color: white;
    }

    .btn-save:hover {
      background-color: #218838;
    }

    .btn-cancel:hover {
      background-color: #c82333;
    }
  </style>
</head>
<body>
<?php
include('index1.php');
?>
<main class="main-content">
  <form method="POST" action="">
    <h2>Créer un Compte Client</h2>

    <!-- Ligne Client ID -->
    <div class="form-row">
      <div class="form-group">
        <label for="id_client">Nom complet du Client (Clé Étrangère)</label>
         <select name="id_client" id="">
          <?php
          $stmt = $pdo->query("SELECT * FROM client");
          $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
          foreach ($clients as $client ):
          ?>
          <option value="<?= $client['id_client'] ?>"><?= $client['nom'] ?> <?= $client['prenom'] ?></option>
          <?php endforeach; ?>
         </select>
      </div>
       <!-- Ligne Numéro de Compte -->
      <div class="form-group">
        <label for="numero_compte">Numéro de Compte</label>
        <input type="text" id="numero_compte" name="numero_compte" placeholder="Entrez le numéro de compte">
      </div>
    </div>

   
    <div class="form-row">

    </div>

    <!-- Ligne Type de Compte -->
    <div class="form-row">
      <div class="form-group">
        <label for="type_compte">Type de Compte</label>
        <select name="type_compte" id="">
          <option value="Compte courant">Compte courant</option>
          <option value="Compte epargne">Compte Epargne</option>
          <option value="Compte bloque">Compte Bloque</option>
        </select>
      </div>
      <!-- Ligne Solde -->
      <div class="form-group">
        <label for="solde">Solde</label>
        <input type="number" id="solde" name="solde" placeholder="Entrez le solde" required>
      </div>
    </div>

    
    <div class="form-row">
      <div class="form-group">
        <label for="decouvert">Découvert</label>
        <input type="number" id="decouvert" name="decouvert" placeholder="Entrez le découvert autorisé" required>
      </div>
      <!-- Ligne Statut -->
      <div class="form-group">
        <label for="statut">Statut</label>
        <select name="statut" id="">
          <option value="bloque">Bloque</option>
          <option value="active">Active</option>
        </select>
      </div>
    </div>
    <div class="form-row">

    </div>
    <!-- Boutons -->
    <div class="form-actions">
      <button type="submit" class="btn-save">Enregistrer</button>
      <button type="reset" class="btn-cancel">Annuler</button>
    </div>
  </form>
</main>

</body>
</html>