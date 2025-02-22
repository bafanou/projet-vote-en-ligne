<?php
session_start();
require '../../config/config.php';

// Vérification de la connexion de l'électeur
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'electeur') {
    header('Location: ../auth/login.php');
    exit();
}

// Récupération des identifiants de l'électeur et de l'élection
$utilisateur_id = $_SESSION['user_id'];
$election_id = $_GET['election_id'] ?? null;
$candidat_id = $_GET['candidat_id'] ?? null;

if ($election_id && $candidat_id) {
    try {
        // Vérification si l'utilisateur a déjà voté pour cette élection
        $stmt = $pdo->prepare("SELECT * FROM votes WHERE utilisateur_id = ? AND election_id = ?");
        $stmt->execute([$utilisateur_id, $election_id]);
        $vote_existant = $stmt->fetch();

        if ($vote_existant) {
            $message = "Vous avez déjà voté pour cette élection !";
            $message_type = "error";
        } else {
            // Enregistrement du vote dans la base de données
            $stmt = $pdo->prepare("INSERT INTO votes (election_id, utilisateur_id, candidat_id) VALUES (?, ?, ?)");
            $stmt->execute([$election_id, $utilisateur_id, $candidat_id]);

            $message = "Votre vote a été enregistré avec succès !";
            $message_type = "success";
        }
    } catch (Exception $e) {
        $message = "Erreur lors de l'enregistrement du vote : " . $e->getMessage();
        $message_type = "error";
    }
}

// Récupération des candidats pour l'élection en cours
$stmt = $pdo->prepare("SELECT * FROM candidats WHERE election_id = ?");
$stmt->execute([$election_id]);
$candidats = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Voter pour une Élection</title>
    <link rel="stylesheet" href="../../assets/css/voter.css">
</head>
<body>
    <div class="container">
        <h2>Choisissez un Candidat pour l'Élection</h2>

        <?php if (isset($message)) : ?>
            <div class="notification <?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="candidates-grid">
            <?php foreach ($candidats as $candidat) : ?>
                <div class="candidate-card">
                    <img src="../../assets/images/candidats/<?php echo $candidat['image']; ?>" alt="<?php echo $candidat['nom']; ?>">
                    <h3><?php echo $candidat['prenom'] . ' ' . $candidat['nom']; ?></h3>
                    <p>Filière : <?php echo $candidat['filiere']; ?></p>
                    <button class="btn-vote" onclick="location.href='voter.php?candidat_id=<?php echo $candidat['id']; ?>&election_id=<?php echo $election_id; ?>'">
                        Voter pour <?php echo $candidat['prenom']; ?>
                    </button>
                </div>
            <?php endforeach; ?>
        </div>

        <a href="listeElections.php" class="back-btn">Retour à la Liste des Élections</a>
    </div>
</body>
</html>
