<?php
require 'connexion.php';

// Vérifier si l'ID du compte est passé et si la méthode est POST pour la suppression
if (isset($_GET['id']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $numero = $_GET['id'];

    try {
        // Préparation de la requête de suppression
        $stmt = $pdo->prepare("DELETE FROM historique WHERE idhisto = ?");

        // Exécution de la requête
        if ($stmt->execute([$numero])) {
            $_SESSION['success'] = "historique supprimé avec succès.";
        } else {
            $_SESSION['error'] = "Erreur lors de la suppression.";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Erreur : " . $e->getMessage();
    }
}

// Configuration de la pagination
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

if ($page < 1) {
    $page = 1;
}

$offset = ($page - 1) * $limit;


// Récupérer la liste des clients
$stmt = $pdo->query("SELECT * FROM historique LIMIT $limit OFFSET $offset");
$historique = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer le nombre total d'éléments
$total_query = "SELECT COUNT(*) FROM historique";
$total_stmt = $pdo->query($total_query);
$total_items = $total_stmt->fetchColumn();
$total_pages = ceil($total_items / $limit);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Historique</title>
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
     border-radius:50px;
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
    </style>
    <style>
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
include('index1.php');
?>
<main>
</div>

     <h1>Historique</></h1>
    <input type="text" id="search" placeholder="Rechercher...">
    <table>
        <thead>
            <tr>
                <th>CODE</th>
                <th>Nom complet du client</th>
                <th>Date historique</th>
                <th>Montant paye</th>
                <th>Montant restant</th>
                <th>Montant tetal deja paye</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="clientList">
            <?php foreach ($historique as $historique): ?>
                <tr>
                    <td><?= $historique['idhisto'] ?></td>
                    <td><?= $historique['client'] ?> </td>
                    <td><?= $historique['date'] ?></td>
                    <td><?= $historique['montant_paye'] ?> Fbu</td>
                    <td><?= $historique['montant_restant'] ?></td>
                    <td><?= $historique['montant_total_p'] ?></td>
                    <td>
                    <form action="?id=<?= $historique['idhisto'] ?>" method="POST" style="display:inline;">
                            <button type="submit" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce compte ?');" class="delete">🗑️ Supprimer</button>
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
    
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</script>
</body>
</html>