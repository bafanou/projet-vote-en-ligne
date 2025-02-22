<?php
session_start();
require '../../config/config.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
    header('Location: ../auth/login.php');
    exit();
}

// Suppression d'une élection
if (isset($_GET['supprimer_id'])) {
    $supprimer_id = $_GET['supprimer_id'];
    $stmt = $pdo->prepare("DELETE FROM elections WHERE id = ?");
    $stmt->execute([$supprimer_id]);
    $message = "Élection supprimée avec succès !";
}

// Récupérer la liste des élections
$stmt = $pdo->prepare("SELECT * FROM elections ORDER BY date_creation DESC");
$stmt->execute();
$elections = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gérer les Élections</title>
    <link rel="stylesheet" href="../../assets/css/gererElections.css">
</head>
<body>
    <div class="main-content">
        <h1>Gérer les Élections</h1>
        <a href="ajouterElection.php" class="action-btn">Créer une Nouvelle Élection</a>

        <?php if (isset($message)) : ?>
            <div class="success fade-in"><?php echo $message; ?></div>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom de l'Élection</th>
                    <th>Date de Début</th>
                    <th>Date de Fin</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($elections as $election) : ?>
                    <tr>
                        <td><?php echo $election['id']; ?></td>
                        <td><?php echo $election['nom']; ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($election['date_debut'])); ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($election['date_fin'])); ?></td>
                        <td><?php echo ucfirst($election['statut']); ?></td>
                        <td>
                            <a href="modifierElection.php?election_id=<?php echo $election['id']; ?>" class="btn edit">Modifier</a>
                            <a href="gererElections.php?supprimer_id=<?php echo $election['id']; ?>" class="btn delete" onclick="return confirm('Voulez-vous vraiment supprimer cette élection ?');">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <a href="dashboard.php" class="action-btn back-btn">🏠 Retour au Dashboard</a>
    </div>
</body>
</html>
