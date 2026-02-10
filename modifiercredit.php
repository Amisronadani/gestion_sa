<?php
session_start();
require 'connexion.php'; // Assurez-vous de vous connecter à votre base de données

// Vérifier si l'ID du crédit est passé dans l'URL
$id_credit = $_GET['id'] ?? null;

if ($id_credit) {
    // Récupérer les détails existants du crédit
    $stmt = $pdo->prepare("SELECT * FROM credit WHERE numero_credit = ?");
    $stmt->execute([$id_credit]);
    $credit = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérifier si le crédit a été trouvé
    if (!$credit) {
        $_SESSION['error'] = "Crédit introuvable.";
        header("Location: affichecredit1.php");
        exit;
    }
} else {
    $_SESSION['error'] = "ID de crédit non spécifié.";
    header("Location: affichecredit1.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données mises à jour autorisées
    $montant = $_POST['montant'] ?? null;
    $taux_de_remboursement = $_POST['taux_remboursement'] ?? null;
    $duree_de_remboursement = $_POST['duree_remboursement'] ?? null;
    $numero_compte = $_POST['numero_compte'] ?? null;
    $id_dossier = $_POST['id_dossier'] ?? null;

    // Exemple de validation simple pour le montant
    if ($montant < 0) {
        $_SESSION['error'] = "Le montant doit être positif.";
    } else {
        try {
            // Préparation de la requête de mise à jour
            $stmt = $pdo->prepare("UPDATE credit SET montant = ?, taux_de_remboursement = ?, duree_de_remboursement = ?, numero_compte = ?, id_dossier = ? WHERE numero_credit = ?");
            
            // Exécution de la mise à jour
            if ($stmt->execute([$montant, $taux_de_remboursement, $duree_de_remboursement, $numero_compte, $id_dossier, $id_credit])) {
                $_SESSION['success'] = "Crédit modifié avec succès.";
                
                // Vérification de la mise à jour
                $stmtCheck = $pdo->prepare("SELECT montant FROM credit WHERE numero_credit = ?");
                $stmtCheck->execute([$id_credit]);
                $newMontant = $stmtCheck->fetchColumn();

                if ($newMontant == $montant) {
                    header("Location: affichecredit1.php");
                    exit;
                }
            } else {
                $_SESSION['error'] = "Erreur lors de la modification du crédit.";
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Erreur : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier le Crédit</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .form-container {
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 20px;
            max-width: 800px;
            margin: auto;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
        }
        .form-row {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            margin-bottom: 15px;
        }
        .form-group {
            flex: 0 0 48%;
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"], input[type="number"], select {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            background-color: #5cb85c;
            color: white;
            border: none;
            cursor: pointer;
            padding: 10px 15px;
            float: right;
        }
        input[type="submit"]:hover {
            background-color: #4cae4c;
        }
    </style>
</head>
<body>

<?php include('index1.php'); ?>
<main>
    <div class="form-container">
        <h2>Modifier le Crédit</h2>
        <form action="" method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label for="numero_credit">Numéro de Crédit</label>
                    <input type="text" id="numero_credit" name="numero_credit" value="<?= htmlspecialchars($credit['numero_credit']) ?>" readonly>
                </div>
                
                <div class="form-group">
                    <label for="type_credit">Type de Crédit</label>
                    <select id="type_credit" name="type_credit" required>
                        <option value="Crédits aux PME" <?= ($credit['type_credit'] == 'Crédits aux PME') ? 'selected' : '' ?>>Crédits aux PME</option>
                        <option value="Crédits BGF 24" <?= ($credit['type_credit'] == 'Crédits BGF 24') ? 'selected' : '' ?>>Crédits BGF 24</option>
                        <option value="Crédits de consommation" <?= ($credit['type_credit'] == 'Crédits de consommation') ? 'selected' : '' ?>>Crédits de consommation</option>
                        <option value="Crédits immobiliers" <?= ($credit['type_credit'] == 'Crédit sur salaire') ? 'selected' : '' ?>>Crédit sur salaire</option>
                        <option value="Crédits à court terme" <?= ($credit['type_credit'] == 'Crédits à court terme') ? 'selected' : '' ?>>Crédits à court terme</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="montant">Montant du Crédit</label>
                    <input type="number" id="montant" name="montant" value="<?= htmlspecialchars($credit['montant']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="taux_remboursement">Taux de Remboursement (%)</label>
                    <input type="number" id="taux_remboursement" name="taux_remboursement" value="<?= htmlspecialchars($credit['taux_de_remboursement']) ?>" step="0.01" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="duree_remboursement">Durée de Remboursement (en mois)</label>
                    <input type="number" id="duree_remboursement" name="duree_remboursement" value="<?= htmlspecialchars($credit['duree_de_remboursement']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="numero_compte">Numéro de Compte</label>
                    <select id="numero_compte" name="numero_compte" required>
                        <?php 
                        $stmt = $pdo->query("SELECT * FROM compte c JOIN client cl ON c.id_client = cl.id_client");
                        $comptes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($comptes as $compte):
                        ?>
                        <option value="<?= htmlspecialchars($compte['numero_compte']) ?>" <?= ($credit['numero_compte'] == $compte['numero_compte']) ? 'selected' : '' ?>><?= htmlspecialchars($compte['numero_compte']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="id_dossier">ID Dossier</label>
                    <select id="id_dossier" name="id_dossier" required>
                        <?php
                        $stmt = $pdo->query("SELECT * FROM dossier_de_recouvrement");
                        $dossiers = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($dossiers as $dossier):
                        ?>
                        <option value="<?= htmlspecialchars($dossier['id_dossier']) ?>" <?= ($credit['id_dossier'] == $dossier['id_dossier']) ? 'selected' : '' ?>><?= htmlspecialchars($dossier['id_dossier']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div>
                <input type="submit" value="Modifier">
            </div>
        </form>
    </div>
</main>

</body>
</html>