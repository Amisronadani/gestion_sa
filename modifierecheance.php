<?php
session_start();
require 'connexion.php'; // Connexion à la base de données

$id_echeance = $_GET['id'] ?? null;

if ($id_echeance) {
    // Récupérer l'échéance à modifier
    $stmt = $pdo->prepare("SELECT * FROM echeance WHERE id_echeances = ?");
    $stmt->execute([$id_echeance]);
    $echeance = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Traitement du formulaire de modification
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $date_echeance = $_POST['date_echeance'];
        $statut_echeance = $_POST['statut_echeance'];
        $jour_retard = $_POST['jour_retard'];
        $numero_credit = $_POST['credit'];

        try {
            // Mettre à jour l'échéance dans la base de données
            $stmt = $pdo->prepare("UPDATE echeance SET date_echeances = ?, statut_echeances = ?, retard = ?, numero_credit = ? WHERE id_echeances = ?");
            $stmt->execute([$date_echeance, $statut_echeance, $jour_retard, $numero_credit, $id_echeance]);
            $_SESSION['success'] = "Échéance modifiée avec succès.";
            header("Location: afficheecheance.php");
            exit;
        } catch (PDOException $e) {
            $_SESSION['error'] = "Erreur lors de la modification : " . $e->getMessage();
        }
    }
}

// Récupérer les crédits pour le sélecteur
$dossiers = $pdo->query("
    SELECT d.id_dossier, c.nom, c.prenom, cr.numero_credit 
    FROM dossier_de_recouvrement d 
    JOIN client c ON d.id_client = c.id_client 
    JOIN credit cr ON d.id_dossier = cr.id_dossier
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier une Échéance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #ecf0f1;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        h1, h2 {
            text-align: center;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 0.9rem;
        }

        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }

        .input-container {
            margin-bottom: 10px;
        }

        .input-container label {
            display: block;
            margin-bottom: 4px;
            font-size: 0.9rem;
        }

        .input-container input,
        .input-container select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 0.9rem;
        }

        button[type="submit"] {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 8px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
            width: 100%; /* Bouton full-width */
        }

        button[type="submit"]:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Modifier une Échéance</h2>
    <form method="POST" action="">
        <div class="row">
            <div class="col-6 input-container">
                <label for="date_echeance">Date d'Échéance:</label>
                <input type="date" name="date_echeance" id="date_echeance" value="<?= htmlspecialchars($echeance['date_echeances']) ?>" required>
            </div>
            <div class="col-6 input-container">
                <label for="statut_echeance">Statut d'Échéance:</label>
                <input type="number" name="statut_echeance" id="statut_echeance" value="<?= htmlspecialchars($echeance['statut_echeances']) ?>" required>
            </div>
        </div>
        <div class="row">
            <div class="col-6 input-container">
                <label for="jour_retard">Jours de Retard:</label>
                <input type="text" name="jour_retard" id="jour_retard" value="<?= htmlspecialchars($echeance['retard']) ?>" required>
            </div>
            <div class="col-6 input-container">
                <label for="client">Numéro de Crédit :</label>
                <select name="credit" id="client" required>
                    <option value="" disabled>Choisir un crédit</option>
                    <?php foreach ($dossiers as $dossier): ?>
                        <option value="<?= htmlspecialchars($dossier['numero_credit']) ?>" <?= $echeance['numero_credit'] == $dossier['numero_credit'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($dossier['prenom'] . ' ' . $dossier['nom'] . ' - ' . $dossier['numero_credit']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="button-container">
            <button type="submit">Modifier</button>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>