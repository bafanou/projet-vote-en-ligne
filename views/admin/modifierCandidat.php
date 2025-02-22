<?php
session_start();
require '../../config/config.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
    header('Location: ../auth/login.php');
    exit();
}

// Récupérer les données du candidat à modifier
if (isset($_GET['candidat_id'])) {
    $candidat_id = $_GET['candidat_id'];

    $stmt = $pdo->prepare("SELECT * FROM candidats WHERE id = ?");
    $stmt->execute([$candidat_id]);
    $candidat = $stmt->fetch();

    if (!$candidat) {
        die("Candidat non trouvé.");
    }
}

// Récupérer les élections ouvertes ou en préparation
$stmt = $pdo->prepare("SELECT id, nom FROM elections WHERE statut IN ('ouverte', 'preparation')");
$stmt->execute();
$elections = $stmt->fetchAll();

// Gestion de la modification du candidat
if (isset($_POST['modifierCandidat'])) {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $filiere = $_POST['filiere'];
    $annee_formation = $_POST['annee_formation'];
    $election_id = $_POST['election_id'];

    // Gestion de l'image du candidat
    $image = $candidat['image']; // Conserver l'image existante par défaut
    if (!empty($_FILES['image']['name'])) {
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
    <title>Modifier un Candidat</title>
    <link rel="stylesheet" href="../../assets/css/ajouterCandidat.css">
</head>
<body>
    <div class="container">
        <a href="gererCandidats.php" class="back-btn">🏠 Retour à la Gestion des Candidats</a>

        <h2>Modifier le Candidat : <?php echo $candidat['nom'] . ' ' . $candidat['prenom']; ?></h2>

        <?php if (isset($success)) : ?>
            <div class="success fade-in"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST" action="" enctype="multipart/form-data">
            <label for="nom">Nom :</label>
            <input type="text" id="nom" name="nom" value="<?php echo $candidat['nom']; ?>" required>

            <label for="prenom">Prénom :</label>
            <input type="text" id="prenom" name="prenom" value="<?php echo $candidat['prenom']; ?>" required>

            <label for="filiere">Filière :</label>
            <input type="text" id="filiere" name="filiere" value="<?php echo $candidat['filiere']; ?>" required>

            <label for="annee_formation">Année de Formation :</label>
            <select id="annee_formation" name="annee_formation" required>
                <option value="">Sélectionnez l'année de formation</option>
                <?php foreach (['L1', 'L2', 'L3', 'M1', 'M2'] as $annee) : ?>
                    <option value="<?php echo $annee; ?>" <?php echo ($candidat['annee_formation'] == $annee) ? 'selected' : ''; ?>>
                        <?php echo $annee; ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="election_id">Élection Associée :</label>
            <select id="election_id" name="election_id" required>
                <option value="">Sélectionnez une élection</option>
                <?php foreach ($elections as $election) : ?>
                    <option value="<?php echo $election['id']; ?>" <?php echo ($candidat['election_id'] == $election['id']) ? 'selected' : ''; ?>>
                        <?php echo $election['nom']; ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="image">Image du Candidat :</label>
            <input type="file" id="image" name="image" accept="image/*">
            <p>Image actuelle : <?php echo $candidat['image']; ?></p>

            <button type="submit" name="modifierCandidat">Enregistrer les Modifications</button>
        </form>
    </div>
</body>
</html>
