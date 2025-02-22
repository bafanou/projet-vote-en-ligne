<?php
session_start();
require '../../config/config.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
    header('Location: ../auth/login.php');
    exit();
}

// Vérifier si l'ID de l'élection est fourni dans l'URL
if (!isset($_GET['election_id'])) {
    die("ID de l'élection non fourni.");
}

$election_id = $_GET['election_id'];

// Récupérer les informations actuelles de l'élection
$stmt = $pdo->prepare("SELECT * FROM elections WHERE id = ?");
$stmt->execute([$election_id]);
$election = $stmt->fetch();

if (!$election) {
    die("Élection introuvable.");
}

// Mise à jour de l'élection
if (isset($_POST['modifierElection'])) {
    $nom = $_POST['nom'];
    $date_debut = $_POST['date_debut'] . ' ' . $_POST['heure_debut'];
    $date_fin = $_POST['date_fin'] . ' ' . $_POST['heure_fin'];
    $statut = $_POST['statut'];

    $stmt = $pdo->prepare("UPDATE elections SET nom = ?, date_debut = ?, date_fin = ?, statut = ? WHERE id = ?");
    $stmt->execute([$nom, $date_debut, $date_fin, $statut, $election_id]);

    $success = "Élection modifiée avec succès !";
    // Rafraîchir les données après la mise à jour
    header("Location: gererElections.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Modifier l'Élection : <?php echo $election['nom']; ?></title>
    <link rel="stylesheet" href="../../assets/css/ajouterElection.css">
</head>
<body>
    <div class="container">
        <a href="gererElections.php" class="back-btn">🏠 Retour à la Gestion des Élections</a>

        <h2>Modifier l'Élection : <?php echo $election['nom']; ?></h2>

        <?php if (isset($success)) : ?>
            <div class="success fade-in"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="nom">Nom de l'élection :</label>
            <input type="text" id="nom" name="nom" value="<?php echo $election['nom']; ?>" required>

            <label for="date_debut">Date et heure de début :</label>
            <input type="date" id="date_debut" name="date_debut" value="<?php echo date('Y-m-d', strtotime($election['date_debut'])); ?>" required>
            <input type="time" id="heure_debut" name="heure_debut" value="<?php echo date('H:i', strtotime($election['date_debut'])); ?>" required>

            <label for="date_fin">Date et heure de fin :</label>
            <input type="date" id="date_fin" name="date_fin" value="<?php echo date('Y-m-d', strtotime($election['date_fin'])); ?>" required>
            <input type="time" id="heure_fin" name="heure_fin" value="<?php echo date('H:i', strtotime($election['date_fin'])); ?>" required>

            <label for="statut">Statut de l'élection :</label>
            <select id="statut" name="statut" required>
                <option value="ouverte" <?php echo ($election['statut'] == 'ouverte') ? 'selected' : ''; ?>>Ouverte</option>
                <option value="cloturee" <?php echo ($election['statut'] == 'cloturee') ? 'selected' : ''; ?>>Clôturée</option>
            </select>

            <button type="submit" name="modifierElection">Enregistrer les Modifications</button>
        </form>
    </div>
</body>
</html>
