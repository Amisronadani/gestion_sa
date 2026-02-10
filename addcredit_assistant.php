<?php
session_start();
require 'connexion.php'; // La connexion à la base de données

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $type_credit = $_POST['type_credit'] ?? null;
    $montant = $_POST['montant'] ?? null;
    $taux_de_remboursement = $_POST['taux_remboursement'] ?? null;
    $duree_de_remboursement = $_POST['duree_de_remboursement'] ?? null;
    $numero_compte = $_POST['numero_compte'] ?? null;
    $id_dossier = $_POST['id_dossier'] ?? null;

    $montant_total=0;

    try {
        // Récupérer le dernier numéro de crédit pour l'auto-incrémentation
        $stmt = $pdo->query("SELECT COUNT(*) FROM credit");
        $count = $stmt->fetchColumn();
        $numero_credit = str_pad($count + 1, 4, '0', STR_PAD_LEFT); // Générer le numéro de crédit

        // Préparation de la requête d'insertion
        $stmt = $pdo->prepare("INSERT INTO credit (numero_credit, type_credit, montant, montant_restant, taux_de_remboursement, duree_de_remboursement, numero_compte, id_dossier)
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
         //calucl du montant total a payer par rapport au taux
         $mont_total=$montant+(($montant*$taux_de_remboursement)/100);
 
        // Exécution de la requête
        if ($stmt->execute([$numero_credit, $type_credit, $montant, $mont_total, $taux_de_remboursement, $duree_de_remboursement, $numero_compte, $id_dossier])) {
            $_SESSION['success'] = "Crédit ajouté avec succès. Numéro : $numero_credit";
            header("Location: affichecredit1.php"); // Redirigez vers une page de succès
            exit;
        } else {
            $_SESSION['error'] = "Erreur lors de l'ajout du crédit.";
            header("Location: affichecredit1.php"); // Redirigez vers le formulaire
            exit;
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Erreur : " . $e->getMessage();
        header("Location: affichecredit1.php"); // Redirigez vers le formulaire
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Formulaire de Crédit</title>
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
            width: 1000px; /* Augmenter la largeur du formulaire */
            margin: auto; /* Centre le formulaire */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
        }
        .form-group {
            margin-bottom: 15px;
            width: 48%; /* Ajuste la largeur des groupes pour les mettre côte à côte */
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"], input[type="number"], select {
            width: 100%; /* Réduit la largeur pour le padding */
            padding: 8px;
            box-sizing: border-box;
        }
        .button-container {
            text-align: right; /* Aligne le bouton à droite */
        }
        input[type="submit"] {
            background-color: #5cb85c;
            color: white;
            border: none;
            cursor: pointer;
            padding: 10px 15px;
        }
        input[type="submit"]:hover {
            background-color: #4cae4c;
        }
        .form-row {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<?php
include('index_assistant.php');
?>
<main>
    <div class="form-container">
        <h2>Formulaire de Crédit</h2>
        <form action="" method="POST">
            <div class="form-row">
                <div class="form-group">
                <label for="id_dossier">ID Dossier</label>
                    <select id="id_dossier" name="id_dossier" required>
                     <?php
                     // Récupérer la liste des clients
                   $stmt = $pdo->query("SELECT * FROM dossier_de_recouvrement d join client cl on d.id_client=cl.id_client ");
                    $dossier= $stmt->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($dossier as $dossier):
                     ?>
                     <option value="<?= $dossier['id_dossier'] ?>"><?= $dossier['id_dossier'] ?></option>
                      <?php endforeach;?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="type_credit">Type de Crédit</label>
                     <select name="type_credit" id="">
                        <option value="Crédit consommation">Crédits aux PME</option>
                        <option value="Crédit BGF 24">Crédits BGF 24</option>
                        <option value="Crédit sur salaire">Crédit sur salaire</option>
                        <option value="Crédit sur salaire">Crédits de consommation</option>
                        <option value="Crédit sur salaire">Crédits à court terme</option>
                       
                     </select>
                </div>

            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="montant_credit">Montant du Crédit</label>
                    <input type="number" id="montant" name="montant" required>
                </div>
                <div class="form-group">
                    <label for="taux_remboursement">Taux de Remboursement (%)</label>
                    <input type="number" id="taux_remboursement" name="taux_remboursement" step="0.01" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="duree_remboursement">Durée de Remboursement (en mois)</label>
                    <input type="number" id="duree_remboursement" name="duree_de_remboursement" required>
                </div>
                <div class="form-group">
                    <label for="numero_compte">Numéro de Compte</label>
                    <select id="numero_compte" name="numero_compte" required>
                        <?php 
                        // Récupérer la liste des clients
                    $stmt = $pdo->query("SELECT * FROM compte c join client cl on c.id_client=cl.id_client ");
                    $compte = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($compte as $compte):
                        ?>
                        <option value="<?= $compte['numero_compte'] ?>"><?= $compte['numero_compte'] ?></option>
                    <?php endforeach;?>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                </div>
                <div class="form-group">
                </div>
            </div>
            <div class="button-container">
                <input type="submit" value="Soumettre">
            </div>
        </form>
    </div>
</main>

<!-- Section pour inclure le tableau de bord -->
<div id="dashboard-placeholder">
    <!-- Ici, vous pouvez inclure votre tableau de bord -->
</div>

</body>
</html>