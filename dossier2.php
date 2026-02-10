<?php
session_start();
require 'connexion.php'; // Connexion à la base de données

// Vérifiez si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $date_ouverture = $_POST['date_ouverture'] ?? null;
    $montant = $_POST['montant'] ?? null; // Montant
    $id_client = $_POST['id_client'] ?? null; // ID client
    $statut_dossier = $_POST['statut_dossier'] ?? 'ouvert'; // Statut par défaut
    $date_cloture = $_POST['date_cloture'] ?? null; // Date de clôture
    $create_at = date('Y-m-d H:i:s');

    $error = ''; // Initialisation de la variable d'erreur
    $photoName = ''; // Initialisation du nom de la photo

    // Gestion de la photo
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        // Définir le dossier de téléchargement
        $uploadDir = 'uploads/';
        $photoName = basename($_FILES['photo']['name']); 
        $uploadFile = $uploadDir . $photoName;

        // Créer le dossier si nécessaire
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Vérification des types de fichiers autorisés
        $imageFileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));
        if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            $_SESSION['error'] = "Désolé, seuls les fichiers JPG, JPEG, PNG & GIF sont autorisés.";
            header("Location: affichedossier.php");
            exit;
        }

        // Déplacer le fichier téléchargé
        if (!move_uploaded_file($_FILES['photo']['tmp_name'], $uploadFile)) {
            $error = "Erreur lors de l'upload de la photo.";
        }
    } else {
        $error = "Aucune photo téléchargée ou erreur lors du téléchargement.";
    }

    // Si tout est en ordre et pas d'erreurs, procédez à l'insertion
    if (empty($error)) {
        try {
            // Récupérer l'année en cours pour la création de l'ID dossier
            $year = date('Y');

            // Récupération du dernier ID existant pour le même année
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM dossier_de_recouvrement WHERE id_dossier LIKE ?");
            $stmt->execute(["D-$year/%"]);
            $count = $stmt->fetchColumn();

            // Générer un nouvel ID dossier unique
            do {
                $id_dossier = "D-$year/" . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM dossier_de_recouvrement WHERE id_dossier = ?");
                $stmt->execute([$id_dossier]);
                $exists = $stmt->fetchColumn();
                $count++;
            } while ($exists > 0);

            // Préparation de la requête d'insertion
            $stmt = $pdo->prepare(
                "INSERT INTO dossier_de_recouvrement 
                (id_dossier, date_ouverture, montant, photo, date_de_cloture, statut_dossier, create_at, id_client) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
            );

            // Exécution de la requête
            if ($stmt->execute([$id_dossier, $date_ouverture, $montant, $photoName, $date_cloture, $statut_dossier, $create_at, $id_client])) {
                $_SESSION['success'] = "Dossier de recouvrement ajouté avec succès.";
                header("Location: affichedossier.php");
                exit;
            } else {
                $_SESSION['error'] = "Erreur lors de l'ajout du dossier.";
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Erreur : " . $e->getMessage();
            header("Location: affichedossier.php");
            exit;
        }
    } else {
        $_SESSION['error'] = $error;
        header("Location: affichedossier.php");
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
        <label for="date_ouverture">Date d'Ouverture:</label>
        <input type="date" name="date_ouverture" id="date_ouverture" required>
      </div>
      <div class="form-group">
        <label for="montant_du">Montant:</label>
        <input type="number" name="montant" id="montant_du" step="0.01" required>
      </div>
    </div>

    <div class="form-row">
     
    
      <div class="form-group">
            <label for="photo_contrat">Photo Contrat:</label>
            <input type="file" name="photo" id="photo_contrat" accept="image/*" required>
      </div>

      <div class="form-group">
        <label for="date_cloture">Date de Clôture:</label>
        <input type="date" name="date_cloture" id="date_cloture">        
      </div>
    </div>

    
    <div class="form-row">
      
      <!-- Ligne Statut -->
      <div class="form-group">
      <label for="statut_dossier">Statut du Dossier:</label>
      <select name="statut_dossier" id="">
        <option value="actif">actif</option>
        <option value="cloture">cloture</option>
        <option value="encours">Encours</option>
        <option value="suspendu">suspendu</option>
      </select>
      </div>
      <div class="form-group">
        <label for="client">Nom et prenom du Client:</label>
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