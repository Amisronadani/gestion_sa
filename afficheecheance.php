<?php
require 'connexion.php'; // Connexion à la base de données


//Configuration de la pagination
$limit = 3;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

if ($page < 1) {
    $page = 1;
} 
$offset = ($page - 1) * $limit;

// Récupérer toutes les échéances
$stmt = $pdo->query("
    SELECT 
        e.id_echeances, 
        e.date_echeances, 
        e.statut_echeances, 
        e.retard, 
        e.numero_credit, 
        c.nom, c.prenom 
    FROM echeance e 
    LEFT JOIN dossier_de_recouvrement d ON e.numero_credit = d.id_dossier 
    LEFT JOIN client c ON d.id_client = c.id_client
");
$echeances = $stmt->fetchAll(PDO::FETCH_ASSOC);
$echeances = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt = $pdo->query("SELECT * FROM echeance LIMIT $limit OFFSET $offset");
$echeances = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer le nombre total d'éléments
$total_query = "SELECT COUNT(*) FROM echeance";
$total_stmt = $pdo->query($total_query);
$total_items = $total_stmt->fetchColumn();
$total_pages = ceil($total_items / $limit);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Échéances</title>
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
            padding: 15px;
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
          padding:12px;
          text-decoration:none;
          color:white;
     }
     .modifier{   
          background-color: #0000FF; /* Bleu standard */
          border-radius:5px;
          padding:12px;
          text-decoration:none;
          color:white;
     }
     .delete{   
          background-color:red; /* Bleu standard */
          border-radius:5px;
          padding:12px;
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
</style>

</head>
<body>
<?php
require"index1.php";
?>
<main>
<div class="container">
   

   <?php if (isset($_SESSION['success'])): ?>
       <div class="alert alert-success">
           <?= $_SESSION['success']; unset($_SESSION['success']); ?>
       </div>
   <?php endif; ?>

   <?php if (isset($_SESSION['error'])): ?>
       <div class="alert alert-danger">
           <?= $_SESSION['error']; unset($_SESSION['error']); ?>
       </div>
   <?php endif; ?>
   <h1>Liste des Échéances</h1>
   <a href="echeances.php" class="ajouter">Ajouter une Échéance</a><br>
   <table class="table">
       <thead>
           <tr>
               <th>ID Échéance</th>
               <th>Date d'Échéance</th>
               <th>Montan paye</th>
               <th>Statut</th>
               <th>Retard</th>
               <th>Numéro de Crédit</th>
               <th>Actions</th>
           </tr>
       </thead>
          <tbody id="clientList">
               <?php if (count($echeances) > 0): ?>
                   <?php foreach ($echeances as $echeance): ?>
                   <tr>
                       <td><?= htmlspecialchars($echeance['id_echeances']) ?></td>
                       <td><?= htmlspecialchars($echeance['date_echeances']) ?></td>
                       <td><?= htmlspecialchars($echeance['montant_du']) ?></td>
                       <td><?= htmlspecialchars($echeance['statut_echeances']) ?></td>
                       <td><?= htmlspecialchars($echeance['retard']) ?></td>
                       <td><?= htmlspecialchars($echeance['numero_credit']) ?></td>
                      
                       <td>
                           <a href="modifierecheance.php?id=<?= $echeance['id_echeances'] ?>" class="modifier">Modifier</a>
                           <a href="supprimerecheance.php?id=<?= $echeance['id_echeances'] ?>" class="delete">Supprimer</a>
                       </td>
                   </tr>
                   <?php endforeach; ?>
               <?php else: ?>
                   <tr>
                       <td colspan="6" class="text-center">Aucune échéance trouvée.</td>
                   </tr>
               <?php endif; ?>
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
</div>
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
</main>
</body>
</html>