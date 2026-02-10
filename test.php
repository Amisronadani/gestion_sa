<?php
// Supposons que vous avez déjà fait une connexion à la base de données
require 'connexion.php';
$id_client = $_GET['id'];

// Récupérer les informations du client par ID
$stmt = $pdo->prepare("SELECT * FROM client WHERE id_client = ?");
$stmt->execute([$id_client]);
$client = $stmt->fetch(PDO::FETCH_ASSOC);

// Vérifiez si le client existe
if (!$client) {
    // Gérer le cas où le client n'existe pas
    echo "Client non trouvé.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier le Profil</title>
</head>
<body>

<form method="POST" action="traitement_modifier.php" enctype="multipart/form-data">
    <!-- Ligne Photo -->
    <div class="form-row">
        <div class="form-group">
            <div class="photo-preview" style="position: relative; width: 100px; height: 100px;">
                <i id="icon" class="fas fa-user-circle"></i>
                <img id="preview" class="avatar" src="uploads/<?= htmlspecialchars($client['photo']) ?>" alt="Prévisualisation" style="display:block; position:absolute; top:0; left:0; width:100%; height:100%; object-fit:cover; border-radius:50%;">
                <div class="add-icon" onclick="document.getElementById('photo').click()">
                    <i class="fas fa-plus-circle"></i>
                </div>
                <input type="file" id="photo" name="photo" accept="image/*" onchange="previewImage(event)" style="display:none;">
            </div>
        </div>
    </div>

    <!-- Autres Champs du Formulaire -->
    <div class="form-group">
        <label for="nom">Nom:</label>
        <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($client['nom']) ?>" required>
    </div>

    <div class="form-group">
        <label for="prenom">Prénom:</label>
        <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($client['prenom']) ?>" required>
    </div>

    <div class="form-group">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($client['email']) ?>" required>
    </div>

    <button type="submit">Modifier</button>
</form>

<script>
// Fonction pour prévisualiser l'image
function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function () {
        const output = document.getElementById('preview');
        const icon = document.getElementById('icon');
        output.src = reader.result;
        output.style.display = "block"; // Affiche l'image
        icon.style.display = "none"; // Masque l'icône
    };
    reader.readAsDataURL(event.target.files[0]);
}
</script>

</body>
</html>