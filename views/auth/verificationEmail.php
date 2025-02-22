<?php
session_start();
require '../../config/config.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE token_verification = ?");
    $stmt->execute([$token]);
    $utilisateur = $stmt->fetch();

    if ($utilisateur) {
        $stmt = $pdo->prepare("UPDATE utilisateurs SET email_verifie = 1, token_verification = NULL WHERE id = ?");
        $stmt->execute([$utilisateur['id']]);
        echo "Votre adresse email a été vérifiée avec succès ! Vous pouvez maintenant vous connecter.";
    } else {
        echo "Token de vérification invalide ou expiré.";
    }
} else {
    echo "Aucun token de vérification fourni.";
}
?>
