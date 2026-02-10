<?php
session_start();
require 'connexion.php'; // Connexion à la base de données

// Vérifiez si l'ID du dossier à modifier est passé dans l'URL
$id_dossier = $_GET['id'] ?? null;
    // Récupérer les informations du dossier existant
    $stmt = $pdo->prepare("SELECT * FROM dossier_de_recouvrement WHERE id_dossier = ?");
    $stmt->execute([$id_dossier]);
    $dossier = $stmt->fetch(PDO::FETCH_ASSOC);
    //print_r($dossier)

    // Si le formulaire a été soumis
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Récupérer les données du formulaire
        $date_ouverture = $_POST['date_ouverture'] ?? null;
        $montant = $_POST['montant'] ?? null;
        $id_client = $_POST['id_client'] ?? null;
        $statut_dossier = $_POST['statut_dossier'] ?? 'ouvert';
        $date_cloture = $_POST['date_cloture'] ?? null;
        $create_at = date('Y-m-d H:i:s');

        $error = '';
        $photoName = $dossier['photo']; // Garder l'ancienne photo par défaut

        // Gestion de la photo
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            // Définir le dossier de téléchargement
            $uploadDir = 'uploads/';
            $photoName = basename($_FILES['photo']['name']);
            $uploadFile = $uploadDir . $photoName;

            // Vérification des types de fichiers autorisés
            $imageFileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));
            if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
                $_SESSION['error'] = "Désolé, seuls les fichiers JPG, JPEG, PNG & GIF sont autorisés.";
                header("Location: affichedossier.php?id_dossier=$id_dossier");
                exit;
            }

            // Déplacer le fichier téléchargé
            if (!move_uploaded_file($_FILES['photo']['tmp_name'], $uploadFile)) {
                $error = "Erreur lors de l'upload de la photo.";
            }
        }

        // Si tout est en ordre et pas d'erreurs
        if (empty($error)) {
            try {
                // Préparation de la requête de mise à jour
                $stmt = $pdo->prepare(
                    "UPDATE dossier_de_recouvrement 
                    SET date_ouverture = ?, montant = ?, photo = ?, date_de_cloture = ?, statut_dossier = ?, create_at = ?, id_client = ? 
                    WHERE id_dossier = ?"
                );

                // Exécution de la requête
                if ($stmt->execute([$date_ouverture, $montant, $photoName, $date_cloture, $statut_dossier, $create_at, $id_client, $id_dossier])) {
                    $_SESSION['success'] = "Dossier de recouvrement modifié avec succès.";
                    header("Location: affichedossier.php");
                    exit;
                } else {
                    $_SESSION['error'] = "Erreur lors de la mise à jour du dossier.";
                }
            } catch (PDOException $e) {
                $_SESSION['error'] = "Erreur : " . $e->getMessage();
                header("Location: affichedossier.php?id_dossier=$id_dossier");
                exit;
            }
        } else {
            $_SESSION['error'] = $error;
            header("Location: affichedossier.php?id_dossier=$id_dossier");
            exit;
        }
    }
?>


<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Formulaire du dossier</title>
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
      margin-left:150px;
      margin-top:opx
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
      padding:20px;
    }

    .btn-cancel {
      background-color: #dc3545;
      color: white;
      padding:20px;
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
  <form action="" enctype="multipart/form-data" method="POST">
    <h2>Créer un Dossier</h2>

    <div class="form-row">
      <div class="form-group">
          <label for="id_dossier">ID Dossier:</label>
          <input type="varchar" name="id_dossier" value="<?= htmlspecialchars($dossier['id_dossier']) ?>" readonly>
      </div>
      <div class="form-group">
        <label for="date_ouverture">Date d'Ouverture:</label>
        <input type="date" name="date_ouverture" id="date_ouverture" value="<?= $dossier['date_ouverture'] ?>" required>
      </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label for="montant_du">Montant:</label>
        <input type="number" name="montant" id="montant_du" value="<?= $dossier['montant'] ?>" step="0.01" required>
      </div>
    
      <div class="form-group">
            <label for="photo_contrat">Photo Contrat:</label>
            <input type="file" name="photo" id="photo_contrat" accept="image/*" value="<?= htmlspecialchars($dossier['photo']) ?>" required>
      </div>
    </div>

    
    <div class="form-row">
      <div class="form-group">
        <label for="date_cloture">Date de Clôture:</label>
        <input type="date" name="date_cloture"  value="<?= $dossier['date_de_cloture'] ?>" id="date_cloture">        
      </div>
      <!-- Ligne Statut -->
      <div class="form-group">
      <label for="statut_dossier">Statut du Dossier:</label>
      <select name="statut_dossier" id="statut_dossier">
        <option value="actif" <?= isset($dossier['statut_dossier']) && $dossier['statut_dossier'] === 'actif' ? 'selected' : '' ?>>Actif</option>
        <option value="cloture" <?= isset($dossier['statut_dossier']) && $dossier['statut_dossier'] === 'cloture' ? 'selected' : '' ?>>Clôturé</option>
        <option value="encours" <?= isset($dossier['statut_dossier']) && $dossier['statut_dossier'] === 'encours' ? 'selected' : '' ?>>En cours</option>
        <option value="suspendu" <?= isset($dossier['statut_dossier']) && $dossier['statut_dossier'] === 'suspendu' ? 'selected' : '' ?>>Suspendu</option>
    </select>
      </div>
    </div>
    
    <div class="form-row">
        <div class="form-group">
        <label for="client">Nom et prenom du Client:</label>
        <select name="id_client" id="">
    <?php
    $stmt = $pdo->query("SELECT * FROM client");
    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($clients as $client): ?>
        <option value="<?= $client['id_client'] ?>" <?= $dossier['id_client'] == $client['id_client'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($client['nom']) ?> <?= htmlspecialchars($client['prenom']) ?>
        </option>
    <?php endforeach; ?>
</select>
      </div>
        </div>

    </div>

    <!-- Boutons -->
    <div class="form-actions">
      <button type="submit" class="btn-save">Mettre a jour</button>
      <button type="reset" class="btn-cancel">Annuler</button>
    </div>
  </form>
</main>

</body>
</html>