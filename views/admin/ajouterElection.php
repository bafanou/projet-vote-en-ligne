<?php
session_start();
require '../../config/config.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
    header('Location: ../auth/login.php');
    exit();
}

if (isset($_POST['creerElection'])) {
    $nom = $_POST['nom'];
    $date_debut = $_POST['date_debut'] . ' ' . $_POST['heure_debut'];
    $date_fin = $_POST['date_fin'] . ' ' . $_POST['heure_fin'];

    $stmt = $pdo->prepare("INSERT INTO elections (nom, date_debut, date_fin, statut) VALUES (?, ?, ?, 'ouverte')");
    $stmt->execute([$nom, $date_debut, $date_fin]);

    $success = "Élection créée avec succès !";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Créer une Élection</title>
    <link rel="stylesheet" href="../../assets/css/ajouterElection.css">
</head>
<body>
    <div class="container">
        <!-- Bouton pour retourner à l'accueil -->
        <a href="../admin/dashboard.php" class="back-btn">🏠 Retour à l'Accueil</a>

        <h2>Créer une Nouvelle Élection</h2>

        <?php if (isset($success)) : ?>
            <div class="success fade-in"><?php echo $success; ?></div>
        <?php endif; ?>

        <div class="error fade-in"></div>

        <form id="electionForm" method="POST" action="">
            <label for="nom">Nom de l'élection :</label>
            <input type="text" id="nom" name="nom" placeholder="Nom de l'élection" required>

            <label for="date_debut">Date et heure de début :</label>
            <input type="date" id="date_debut" name="date_debut" required>
            <input type="time" id="heure_debut" name="heure_debut" required>

            <label for="date_fin">Date et heure de fin :</label>
            <input type="date" id="date_fin" name="date_fin" required>
            <input type="time" id="heure_fin" name="heure_fin" required>

            <button type="submit" name="creerElection">Créer l'élection</button>
        </form>
    </div>

    <script src="../../assets/js/ajouterElection.js"></script>
</body>
</html>
