<?php
session_start();

$message = $_SESSION['success']
           ?? "Opération réussie.";

unset($_SESSION['success']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Succès</title>

<style>
body{
    font-family:Arial;
    background:#d4edda;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
}

.box{
    background:white;
    padding:40px;
    border-radius:10px;
    text-align:center;
    width:400px;
}

h2{color:green;}

a{
    display:inline-block;
    margin-top:20px;
    padding:10px 18px;
    background:#007bff;
    color:white;
    text-decoration:none;
    border-radius:5px;
}
</style>
</head>

<body>

<div class="box">
    <h2>Succès</h2>
    <p><?= htmlspecialchars($message) ?></p>

    <a href="index1.php">Retour accueil</a>
</div>

</body>
</html>
