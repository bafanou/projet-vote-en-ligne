<?php
session_start();
require '../../config/config.php';

// Vérification de la connexion utilisateur
if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['admin', 'electeur'])) {
    header('Location: ../auth/login.php');
    exit();
}

// Récupération de l'ID de l'élection
if (!isset($_GET['election_id'])) {
    header('Location: ../vote/listeElections.php');
    exit();
}

$election_id = $_GET['election_id'];

// Récupération des informations de l'élection
$stmt = $pdo->prepare("SELECT * FROM elections WHERE id = ?");
$stmt->execute([$election_id]);
$election = $stmt->fetch();

if (!$election) {
    echo "Élection introuvable.";
    exit();
}

// Récupération des candidats et des votes
$stmt = $pdo->prepare("
    SELECT c.*, 
           COUNT(v.id) AS votes 
    FROM candidats c 
    LEFT JOIN votes v ON v.candidat_id = c.id 
    WHERE c.election_id = ? 
    GROUP BY c.id
");
$stmt->execute([$election_id]);
$candidats = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Résultats de l'Élection : <?php echo htmlspecialchars($election['nom']); ?></title>
    <link rel="stylesheet" href="../../assets/css/resultats.css">
</head>
<body>
    <div class="container">
        <h1>Résultats de l'Élection : <?php echo htmlspecialchars($election['nom']); ?></h1>
        
        <?php if ($_SESSION['user_role'] == 'admin'): ?>
            <a href="../admin/dashboard.php" class="back-btn">🏠 Retour au Dashboard</a>
        <?php else: ?>
            <a href="../vote/listeElections.php" class="back-btn">Retour à la liste des élections</a>
        <?php endif; ?>

        <div class="candidats-grid">
            <?php foreach ($candidats as $candidat): ?>
                <div class="candidat-card <?php echo $candidat['votes'] == max(array_column($candidats, 'votes')) ? 'winner' : ''; ?>">
                    <?php if ($candidat['votes'] == max(array_column($candidats, 'votes'))): ?>
                        <div class="crown">👑</div>
                    <?php endif; ?>
                    <h2><?php echo htmlspecialchars($candidat['prenom'] . ' ' . $candidat['nom']); ?></h2>
                    <p>Filière : <?php echo htmlspecialchars($candidat['filiere']); ?></p>
                    <p>Année : <?php echo htmlspecialchars($candidat['annee_formation']); ?></p>
                    <p>Votes : <strong><?php echo $candidat['votes']; ?></strong></p>
                    <img src="../../assets/images/candidats/<?php echo htmlspecialchars($candidat['image']); ?>" alt="Photo du candidat">
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($_SESSION['user_role'] == 'admin'): ?>
            <div class="graph-container">
                <h2>Graphique des Votes par Candidat</h2>
                <canvas id="votesChart"></canvas>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($_SESSION['user_role'] == 'admin'): ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('votesChart').getContext('2d');
        const votesChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_column($candidats, 'prenom')); ?>,
                datasets: [{
                    label: 'Votes par Candidat',
                    data: <?php echo json_encode(array_column($candidats, 'votes')); ?>,
                    backgroundColor: 'rgba(255, 0, 0, 0.2)',
                    borderColor: 'rgb(114, 176, 235)',
                    borderWidth: 2
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
    <?php endif; ?>
</body>
</html>
