<?php
session_start();
require '../../config/config.php';

// Vérification de l'accès utilisateur (électeur ou administrateur)
if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['electeur', 'admin'])) {
    header('Location: ../auth/login.php');
    exit();
}

// Récupérer toutes les élections ouvertes ou clôturées
$stmt = $pdo->prepare("SELECT * FROM elections WHERE statut IN ('ouverte', 'cloturee')");
$stmt->execute();
$elections = $stmt->fetchAll();

// Vérifier si des élections doivent être clôturées automatiquement
$currentTime = date('Y-m-d H:i:s');
foreach ($elections as $election) {
    if ($currentTime >= $election['date_fin'] && $election['statut'] == 'ouverte') {
        // Mettre à jour le statut de l'élection à "cloturee"
        $updateStmt = $pdo->prepare("UPDATE elections SET statut = 'cloturee' WHERE id = ?");
        $updateStmt->execute([$election['id']]);
    }
}

// Récupérer les élections après mise à jour pour afficher uniquement les valides
$stmt = $pdo->prepare("SELECT * FROM elections WHERE statut IN ('ouverte', 'cloturee')");
$stmt->execute();
$elections = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Élections Disponibles</title>
    <link rel="stylesheet" href="../../assets/css/listeElections.css">
</head>
<body>
    <div class="container">
        <h1>Élections Disponibles</h1>

        <?php if (empty($elections)) : ?>
            <p>Aucune élection disponible pour le moment.</p>
        <?php else : ?>
            <div class="election-grid">
                <?php foreach ($elections as $election) : ?>
                    <div class="election-card">
                        <h2><?php echo $election['nom']; ?></h2>
                        <p>Date de fin : <?php echo date('d/m/Y H:i', strtotime($election['date_fin'])); ?></p>

                        <!-- Compte à rebours -->
                        <?php if ($election['statut'] == 'ouverte') : ?>
                            <p id="countdown-<?php echo $election['id']; ?>" class="countdown"></p>
                            <a href="voter.php?election_id=<?php echo $election['id']; ?>" class="btn-vote">Voter</a>
                        <?php elseif ($election['statut'] == 'cloturee') : ?>
                            <a href="../resultats/resultats.php?election_id=<?php echo $election['id']; ?>" class="btn-results">Voir les Résultats</a>
                        <?php endif; ?>
                    </div>

                    <!-- Script du compte à rebours -->
                    <script>
                        const countdownElement<?php echo $election['id']; ?> = document.getElementById('countdown-<?php echo $election['id']; ?>');
                        const endTime<?php echo $election['id']; ?> = new Date("<?php echo $election['date_fin']; ?>").getTime();

                        function updateCountdown<?php echo $election['id']; ?>() {
                            const now = new Date().getTime();
                            const distance = endTime<?php echo $election['id']; ?> - now;

                            if (distance < 0) {
                                countdownElement<?php echo $election['id']; ?>.innerHTML = "Élection clôturée !";
                                countdownElement<?php echo $election['id']; ?>.classList.add('closed');
                                return;
                            }

                            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                            countdownElement<?php echo $election['id']; ?>.innerHTML = 
                                `Temps restant : ${days}j ${hours}h ${minutes}m ${seconds}s`;
                        }

                        setInterval(updateCountdown<?php echo $election['id']; ?>, 1000);
                    </script>

                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <a href="../auth/logout.php" class="btn-logout">Déconnexion</a>
    </div>
</body>
</html>
