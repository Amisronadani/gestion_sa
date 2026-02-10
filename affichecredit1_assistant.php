<?php
session_start();
require 'connexion.php'; // Assurez-vous de vous connecter à votre base de données

//Configuration de la pagination
$limit = 3;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

if ($page < 1) {
    $page = 1;
}

$offset = ($page - 1) * $limit;
// Récupérer les crédits
$stmt = $pdo->query("SELECT * FROM credit LIMIT $limit OFFSET $offset");
$credits = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer le nombre total d'éléments
$total_query = "SELECT COUNT(*) FROM credit";
$total_stmt = $pdo->query($total_query);
$total_items = $total_stmt->fetchColumn();
$total_pages = ceil($total_items / $limit);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Crédits</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color:white;
            border-radius:10px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 20px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
          /*background-color: #0000FF; /* Bleu standard */

        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        #search {
           padding: 10px;
           font-size: 16px;
           float: right; /* Aligne à droite */
           margin-left: 20px; /* Pour ajouter un espace à gauche si nécessaire */
           border-radius:5px;
            }
     h1{
          text-align:center;
     }
     .image{
          max-width: 100px;
           max-height: 100px;
     }
    .ajouter{
    background-color: #FFA500; /* Orange */
    border-radius:5px;
    padding:15px;
    text-decoration:none;
     }
     .modifier{   
          background-color: #0000FF; /* Bleu standard */
          border-radius:5px;
          padding:15px;
          text-decoration:none;
          color:white;
     }
     .modifier{   
          background-color: #0000FF; /* Bleu standard */
          border-radius:5px;
          padding:15px;
          text-decoration:none;
          color:white;
     }
     .delete{   
          background-color:red; /* Bleu standard */
          border-radius:5px;
          padding:15px;
          text-decoration:none;
          color:white;
     }
     .success { color: green; font-weight: bold; }
     .error { color: red; font-weight: bold; }
    
    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border: 1px solid transparent;
        border-radius: 0.25rem;
        position: relative;
        width: 100%;
    }

    .alert-success {
        color: #155724;
        background-color: #d4edda;
        border-color: #c3e6cb;
    }

    .alert-danger {
        color: #721c24;
        background-color: #f8d7da;
        border-color: #f5c6cb;
    }

    .alert-success::before {
        content: "✔️ "; /* Emoji ou symbole pour indiquer le succès */
    }

    .alert-danger::before {
        content: "❌ "; /* Emoji ou symbole pour indiquer l'erreur */
    }

    .alert button {
        position: absolute;
        top: 10px;
        right: 10px;
        border: none;
        background: none;
        font-size: 1.5rem;
        color: inherit;
        cursor: pointer;
    }
    span{
     color:darkorange"
    }
#st{
    background-color:#721c24;
    color: white;
}
</style>
</head>
<body>

<?php
include('index_assistant.php');
?>
<main>
<div class="container mt-3">
<?php
if (isset($_SESSION['success'])) {
    echo "<div class='alert alert-success' role='alert'>{$_SESSION['success']}<button onclick=\"this.parentElement.style.display='none';\">✖️</button></div>";
    unset($_SESSION['success']);
}

if (isset($_SESSION['error'])) {
    echo "<div class='alert alert-danger' role='alert'>{$_SESSION['error']}<button onclick=\"this.parentElement.style.display='none';\">✖️</button></div>";
    unset($_SESSION['error']);
}
?>
</div>

     <h1>Gestion des <span>Credit</span></></h1>
    <a href="addcredit_assistant.php" class="ajouter">➕ Ajout Quelque qui demande un credit</a>
    <input type="text" id="search" placeholder="Rechercher...">

<table>
    <thead>
        <tr>
            <th>Numéro</th>
            <th>Type</th>
            <th>Montant</th>
            <th>Montant restant</th>
            <th>Taux</th>
            <th>Durée</th>
            <th>Numéro de Compte</th>
            <th>ID Dossier</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody id="clientList">
        <?php foreach ($credits as $credit): ?>
        <tr>
            <td><?= htmlspecialchars($credit['numero_credit']) ?></td>
            <td><?= htmlspecialchars($credit['type_credit']) ?></td>
            <td><?= htmlspecialchars($credit['montant']) ?> Fbu</td>
            <td  id="st"><?= htmlspecialchars($credit['montant_restant']) ?>Fbu</td>
            <td><?= htmlspecialchars($credit['taux_de_remboursement']) ?> %</td>
            <td><?= htmlspecialchars($credit['duree_de_remboursement']) ?> mois</td>
            <td><?= htmlspecialchars($credit['numero_compte']) ?></td>
            <td><?= htmlspecialchars($credit['id_dossier']) ?></td>
            <td>
                <a href="modifiercredit.php?id=<?= $credit['numero_credit'] ?>" class="delete">✏️</a>
                <a href="supprimercredit.php?id=<?= $credit['numero_credit'] ?>" class="modifier">🗑️</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<div class="pagination">
    <?php if ($page > 1): ?>
        <a href="?page=<?php echo $page - 1; ?>">Précédent</a>
    <?php endif; ?>
    
    <span>Page <?php echo $page; ?> sur <?php echo $total_pages; ?></span>

    <?php if ($page < $total_pages): ?>
        <a href="?page=<?php echo $page + 1; ?>">Suivant</a>
    <?php endif; ?>
</div>
        </main>

<script>
    const searchInput = document.getElementById('search');
    searchInput.addEventListener('keyup', function() {
        const filter = searchInput.value.toLowerCase();
        const rows = document.querySelectorAll('#clientList tr');
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    });
</script>
</body>
</html>