<?php
require 'connexion.php';

session_start();

// Vérifier si l'ID du compte est passé et si la méthode est POST pour la suppression
if (isset($_GET['id']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_GET['id'];

    try {
        // Préparation de la requête de suppression
        $stmt = $pdo->prepare("DELETE FROM dossier_de_recouvrement WHERE id_dossier = ?");

        // Exécution de la requête
        if ($stmt->execute([$id])) {
            $_SESSION['success'] = "Dossier supprimé avec succès.";
        } else {
            $_SESSION['error'] = "Erreur lors de la suppression du compte.";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Erreur : " . $e->getMessage();
    }
}
//Configuration de la pagination
$limit = 3;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

if ($page < 1) {
    $page = 1;
}

$offset = ($page - 1) * $limit;


// Récupérer la liste des clients
$stmt = $pdo->query("SELECT * FROM dossier_de_recouvrement d join client cl on d.id_client=cl.id_client LIMIT $limit OFFSET $offset ");
$dossier= $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer le nombre total d'éléments
$total_query = "SELECT COUNT(*) FROM dossier_de_recouvrement";
$total_stmt = $pdo->query($total_query);
$total_items = $total_stmt->fetchColumn();
$total_pages = ceil($total_items / $limit);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Profils Clients</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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
     color:darkorange;
    }
    .pagination {
            margin: 10px 0;
        }
        .pagination a {
            margin: 0 5px;
            text-decoration: none;
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

     <h1>Gestion des <span>dossiers</span></></h1>
    <a href="dossier_assistant.php" class="ajouter">➕ Creer une compte</a>
    <input type="text" id="search" placeholder="Rechercher...">
    <table>
        <thead>
            <tr>
                <th>ID Dossier</th>
                <th>Titulaire</th>
                <th>Montant</th>
                <th>Date d'ouverture</th>
                <th>Photo</th>
                <th>Date de cloture</th>
                <th>Statut</th>
                <th colspan="2">Actions</th>
            </tr>
        </thead>
        <tbody id="clientList">
            <?php foreach ($dossier as $dossier): ?>
                <tr>
                    <td><?= $dossier['id_dossier'] ?></td>
                    <td><?= $dossier['nom'] ?> <?= $dossier['prenom'] ?></td>
                    <td><?= $dossier['montant'] ?> Fbu</td>
                    <td><?= $dossier['date_ouverture'] ?></td>
                    <td>
                      <img class="image" src="uploads/<?= htmlspecialchars($dossier['photo']) ?>" alt="Photo" style="max-width: 100px; max-height: 100px;">
                      <a href="uploads/<?= htmlspecialchars($dossier['photo']) ?>" class="btn btn-primary" download> Télécharger </a>
                    </td>
                    <td><?= $dossier['date_de_cloture'] ?></td>
                    <td><?= $dossier['statut_dossier'] ?></td>
                    <td>
                        <a href="modifierdossier.php?id=<?= $dossier['id_dossier'] ?>" class="modifier"> ✏️ </a>
                    </td>
                    <td>
                    <form action="?id=<?= $dossier['id_dossier'] ?>" method="POST" style="display:inline;">
                            <button type="submit" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce compte ?');" class="delete">🗑️</button>
                        </form>
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