<?php
session_start();
require 'connexion.php'; // Connexion à la base de données
//


// Traitement du formulaire d'ajout d'échéance
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date_echeance = $_POST['date_echeance'];
    $statut_echeance = $_POST['statut_echeance'];
    $montant_du = $_POST['montant_du'];
    $jour_retard = $_POST['jour_retard'];
    $numero_credit = $_POST['credit']; // Utilisation de numero_credit

    // Récupérer les crédits avec les informations du client
$info_client = $pdo->query("
SELECT  c.nom, c.prenom
FROM dossier_de_recouvrement d 
JOIN client c ON d.id_client = c.id_client 
JOIN credit cr ON d.id_dossier = cr.id_dossier WHERE cr.numero_credit=$numero_credit
")->fetch(PDO::FETCH_ASSOC);
$client=$info_client['nom']." ".$info_client['prenom'];

    try {
                // Récupérer montant total
                $stmt = $pdo->prepare("SELECT SUM(montant_du) FROM echeance WHERE numero_credit = ?");
                $stmt->execute([$numero_credit]);
                $compte_info = $stmt->fetch(PDO::FETCH_ASSOC);

        // Insérer la nouvelle échéance dans la base de données
        $stmt = $pdo->prepare("INSERT INTO echeance (date_echeances,montant_du,statut_echeances, retard, numero_credit) VALUES (?,?,?, ?, ?)");
        $stmt->execute([$date_echeance,$montant_du, $statut_echeance, $jour_retard, $numero_credit]);

        // Récupérer le numéro de compte associé au crédit
        $stmt = $pdo->prepare("SELECT montant FROM credit WHERE numero_credit = ?");
        $stmt->execute([$numero_credit]);
        $compte_info = $stmt->fetch(PDO::FETCH_ASSOC);
        $nouveau_montant_restant=0;
        if ($compte_info) {
            // Calculer le nouveau montant restant
            $nouveau_montant_restant = $compte_info['montant'] - $montant_du;

            // Mettre à jour la colonne montant_restant dans la table compte
            $stmt = $pdo->prepare("UPDATE credit SET montant_restant = ? WHERE numero_credit = ?");
            $stmt->execute([$nouveau_montant_restant, $numero_credit]);
        }
         // creation de historique
         $montant_total_paye =$compte_info['montant_du']+$montant_du;
         $stmt = $pdo->prepare("INSERT INTO historique( date, montant_paye, montant_restant,montant_total_p,client) VALUES (?,?,?,?,?)");
         $stmt->execute([$date_echeance,$montant_du,$nouveau_montant_restant,$montant_total_paye,$client]);
 

        $_SESSION['success'] = "Échéance enregistrée avec succès et montant restant mis à jour.";
        header("Location: afficheecheance.php");
        exit;
    } catch (PDOException $e) {
        $_SESSION['error'] = "Erreur d'enregistrement : " . $e->getMessage();
        header("Location: afficheecheance.php");
        exit;
    }
}
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
    <title>Ajouter une Échéance</title>
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
<?php
require"index1.php";
?>
<div class="container">
    <h2>Ajouter une Échéance</h2>
    <form method="POST" action="">
        <div class="row">
            <div class="col-6 input-container">
                <label for="date_echeance">Date d'Échéance:</label>
                <input type="date" name="date_echeance" id="date_echeance" required>
            </div>
            <div class="col-6 input-container">
                <label for="statut_echeance">Statut d'Échéance:</label>
                <select name="statut_echeance" id="">
                    <option value="paye">paye</option>
                    <option value="impaye">impaye</option>   
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-6 input-container">
                <label for="jour_retard">Jours de Retard:</label>
                <input type="text" name="jour_retard" id="jour_retard" required>
            </div>
            <div class="col-6 input-container">
                <label for="client">Numéro de Crédit :</label>
                <select name="credit" id="client" required>
                    <option value="" disabled selected>Choisir un client et leur crédit</option>
                    <?php foreach ($dossiers as $dossier): ?>
                        <option value="<?= htmlspecialchars($dossier['numero_credit']) ?>">
                            <?= htmlspecialchars($dossier['prenom'] . ' ' . $dossier['nom'] . ' - ' . $dossier['numero_credit']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 input-container">
                <label for="statut_echeance">montant du</label>
                <input type="number" name="montant_du" id="montant_du" placeholder="Entre le montant " required>
            </div>
        </div>
        <div class="button-container">
            <button type="submit">Enregistrer</button>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>