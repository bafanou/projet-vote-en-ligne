<?php
session_start();
require '../../config/config.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
    header('Location: ../auth/login.php');
    exit();
}

// Suppression d'un électeur
if (isset($_GET['supprimer_id'])) {
    $supprimer_id = $_GET['supprimer_id'];
    $stmt = $pdo->prepare("DELETE FROM utilisateurs WHERE id = ? AND role = 'electeur'");
    $stmt->execute([$supprimer_id]);
    $message = "Électeur supprimé avec succès !";
}

// Activation/désactivation d'un électeur
if (isset($_GET['changer_statut_id'])) {
    $changer_statut_id = $_GET['changer_statut_id'];
    $stmt = $pdo->prepare("SELECT statut FROM utilisateurs WHERE id = ?");
    $stmt->execute([$changer_statut_id]);
    $utilisateur = $stmt->fetch();

    if ($utilisateur) {
        $nouveau_statut = ($utilisateur['statut'] == 'actif') ? 'inactif' : 'actif';
        $stmt = $pdo->prepare("UPDATE utilisateurs SET statut = ? WHERE id = ?");
        $stmt->execute([$nouveau_statut, $changer_statut_id]);
        $message = "Statut de l'électeur mis à jour avec succès !";
    }
}

// Récupérer la liste des électeurs
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE role = 'electeur' ORDER BY date_creation DESC");
$stmt->execute();
$electeurs = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gérer les Électeurs</title>
    <link rel="stylesheet" href="../../assets/css/gererElecteurs.css">
</head>
<body>
<a href="dashboard.php" class="action-btn back-btn">🏠 Retour au Dashboard</a>
    <div class="main-content">
    
        <h1>Gérer les Électeurs</h1>
        <a href="ajouterElecteur.php" class="action-btn">Ajouter un Nouvel Électeur</a>

        <?php if (isset($message)) : ?>
            <div class="success fade-in"><?php echo $message; ?></div>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Statut</th>
                    <th>Date de Création</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($electeurs as $electeur) : ?>
                    <tr>
                        <td><?php echo $electeur['id']; ?></td>
                        <td><?php echo $electeur['prenom'] . ' ' . $electeur['nom']; ?></td>
                        <td><?php echo $electeur['email']; ?></td>
                        <td><?php echo $electeur['telephone']; ?></td>
                        <td><?php echo ucfirst($electeur['statut']); ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($electeur['date_creation'])); ?></td>
                        <td>
                            <a href="modifierElecteur.php?electeur_id=<?php echo $electeur['id']; ?>" class="btn edit">Modifier</a>
                            <a href="gererElecteurs.php?changer_statut_id=<?php echo $electeur['id']; ?>" class="btn status">
                                <?php echo ($electeur['statut'] == 'actif') ? 'Désactiver' : 'Activer'; ?>
                            </a>
                            <a href="gererElecteurs.php?supprimer_id=<?php echo $electeur['id']; ?>" class="btn delete" onclick="return confirm('Voulez-vous vraiment supprimer cet électeur ?');">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

       
    </div>
</body>
</html>
