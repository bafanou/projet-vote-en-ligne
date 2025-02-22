<?php
session_start();
require '../../config/config.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
    header('Location: ../auth/login.php');
    exit();
}

if (isset($_GET['candidat_id'])) {
    $candidat_id = $_GET['candidat_id'];

    $stmt = $pdo->prepare("DELETE FROM candidats WHERE id = ?");
    $stmt->execute([$candidat_id]);

   // $success = "Candidat supprimé avec succès !";
    header("Location: gererCandidats.php?success=" . urlencode($success));
    exit();
}
?>
