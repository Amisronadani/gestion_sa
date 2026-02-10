<?php
require 'connexion.php';

//Configuration de la pagination
$limit = 3;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

if ($page < 1) {
    $page = 1;
}

$offset = ($page - 1) * $limit;
// Récupérer la liste des clients
$stmt = $pdo->query("SELECT * FROM client LIMIT $limit OFFSET $offset");
$clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer le nombre total d'éléments
$total_query = "SELECT COUNT(*) FROM dossier_de_recouvrement";
$total_stmt = $pdo->query($total_query);
$total_items = $total_stmt->fetchColumn();
$total_pages = ceil($total_items / $limit);
?>
<?php
session_start();
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
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #007bff;
            /*background-color: #f2f2f2;*/
            color:white;
            font-size:20px;
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
</style>
</head>
<body>

<?php
include('index1.php');
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

     <h1>Liste des Clients</h1>
    <a href="profil.php" class="ajouter">➕ un nouveau client</a>
    <input type="text" id="search" placeholder="Rechercher...">
    <table>
        <thead>
            <tr>
                <th>Picture</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Email</th>
                <th>Téléphone</th>
                <th>Pays</th>
                <th>Province</th>
                <th>Commune</th>
                <th>Collines</th>
                <th colspan="2">Actions</th>
            </tr>
        </thead>
        <tbody id="clientList">
            <?php foreach ($clients as $client): ?>
                <tr>
                    <td><image class="image" src="uploads/<?= $client['photo'] ?>"></image></td>
                    <td><?= $client['nom'] ?></td>
                    <td><?= $client['prenom'] ?></td>
                    <td><?= $client['email'] ?></td>
                    <td><?= $client['telephone'] ?></td>
                    <td><?= $client['pays'] ?></td>
                    <td><?= $client['province'] ?></td>
                    <td><?= $client['commune'] ?></td>
                    <td><?= $client['collines'] ?></td>
                    <td>
                        <a href="modifierprofil.php?id=<?= $client['id_client'] ?>" class="modifier"> ✏️</a>
                        <a href="supprimerprofil.php?id=<?= $client['id_client'] ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce profil ?');" class="delete"> 🗑️</a>
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