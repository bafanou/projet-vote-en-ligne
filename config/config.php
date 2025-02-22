<?php
// Configuration de la base de données
$host = 'localhost';
$dbname = 'db_vote_en_ligne';
$username = 'root'; // Par défaut sur XAMPP
$password = ''; // Laisser vide si tu n'as pas configuré de mot de passe MySQL

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("SET NAMES utf8");
    //echo "Connexion réussie à la base de données !";
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>
