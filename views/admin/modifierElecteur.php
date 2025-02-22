<?php
session_start();
require '../../config/config.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
    header('Location: ../auth/login.php');
    exit();
}

// Récupérer l'ID du candidat à modifier
if (!isset($_GET['candidat_id'])) {
    header('Location: gererCandidats.php');
    exit();
}

$candidat_id = $_GET['candidat_id'];

// Récupérer les informations du candidat depuis la base de données
$stmt = $pdo->prepare("SELECT * FROM candidats WHERE id = ?");
$stmt->execute([$candidat_id]);
$candidat = $stmt->fetch();

if (!$candidat) {
    header('Location: gererCandidats.php');
    exit();
}

// Récupérer les élections disponibles
$stmt = $pdo->prepare("SELECT id, nom FROM elections WHERE statut IN ('ouverte', 'preparation')");
$stmt->execute();
$elections = $stmt->fetchAll();

// Récupérer les années de formation disponibles
$annees_formation = ['L1', 'L2', 'L3', 'M1', 'M2'];

// Traitement du formulaire de modification du candidat
if (isset($_POST['modifierCandidat'])) {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $filiere = $_POST['filiere'];
    $annee_formation = $_POST['annee_formation'];
    $election_id = $_POST['election_id'];
    
    // Gestion de l'image du candidat
    $image = $candidat['image']; // Image par défaut
    if (isset($_FILES['image']['name']) && !empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        $image_tmp = $_FILES['image']['tmp_name'];
        $dossier_cible = '../../assets/images/candidats/';
        move_uploaded_file($image_tmp, $dossier_cible . $image);
    }

    // Mise à jour du candidat dans la base de données
    $stmt = $pdo->prepare("UPDATE candidats SET nom = ?, prenom = ?, filiere = ?, annee_formation = ?, image = ?, election_id = ? WHERE id = ?");
    $stmt->execute([$nom, $prenom, $filiere, $annee_formation, $image, $election_id, $candidat_id]);

    $success = "Candidat modifié avec succès !";
    header("Location: gererCandidats.php?success=" . urlencode($success));
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Modifier le Candidat</title>
    <link rel="stylesheet" href="../../assets/css/ajouterCandidat.css">
</head>
<body>
    <div class="container">
        <a href="gererCandidats.php" class="back-btn">🏠 Retour à la Gestion des Candidats</a>

        <h2>Modifier le Candidat : <?php echo htmlspecialchars($candidat['nom'] . ' ' . $candidat['prenom']); ?></h2>

        <form method="POST" action="" enctype="multipart/form-data">
            <label for="nom">Nom :</label>
            <input type="text" id="nom" name="nom" placeholder="Nom" value="<?php echo htmlspecialchars($candidat['nom']); ?>" required>

            <label for="prenom">Prénom :</label>
            <input type="text" id="prenom" name="prenom" placeholder="Prénom" value="<?php echo htmlspecialchars($candidat['prenom']); ?>" required>

            <label for="filiere">Filière :</label>
            <input type="text" id="filiere" name="filiere" placeholder="Filière académique" value="<?php echo htmlspecialchars($candidat['filiere']); ?>" required>

            <label for="annee_formation">Année de Formation :</label>
            <select id="annee_formation" name="annee_formation" required>
                <?php foreach ($annees_formation as $annee) : ?>
                    <option value="<?php echo $annee; ?>" <?php if ($candidat['annee_formation'] == $annee) echo 'selected'; ?>>
                        <?php echo $annee; ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="election_id">Élection Associée :</label>
            <select id="election_id" name="election_id" required>
                <?php foreach ($elections as $election) : ?>
                    <option value="<?php echo $election['id']; ?>" <?php if ($candidat['election_id'] == $election['id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($election['nom']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="image">Image du Candidat :</label>
            <input type="file" id="image" name="image" accept="image/*">
            <p>Image actuelle : <?php echo htmlspecialchars($candidat['image']); ?></p>

            <button type="submit" name="modifierCandidat">Modifier le Candidat</button>
        </form>
    </div>
</body>
</html>
