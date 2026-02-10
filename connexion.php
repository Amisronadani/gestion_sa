<?php
$host = "localhost";
$db   = "recouvrement";   // ⚠️la BD existe
$user = "root";
$pass = "";

try {

   // $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8;",$user,$pass);
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8;port=3307", $user, $pass);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
     $error = "Échec de la connexion à la base de données : " . $e->getMessage();
}
?>
