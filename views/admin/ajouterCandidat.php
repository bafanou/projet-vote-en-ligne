<?php
session_start();
require '../../config/config.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
    header('Location: ../auth/login.php');
    exit();
}

// Récupérer les élections ouvertes ou en préparation
$stmt = $pdo->prepare("SELECT id, nom FROM elections WHERE statut IN ('ouverte', 'preparation')");
$stmt->execute();
$elections = $stmt->fetchAll();

// Ajouter un nouveau candidat
if (isset($_POST['ajouterCandidat'])) {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $filiere = $_POST['filiere'];
    $annee_formation = $_POST['annee_formation'];
    $election_id = $_POST['election_id'];

    $valid_annees = ['L1', 'L2', 'L3', 'M1', 'M2'];
    if (!in_array($annee_formation, $valid_annees)) {
        $_SESSION['message'] = "❌ Erreur : Année de formation invalide !";
        $_SESSION['type'] = 'error';
    } else {
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $image = $_FILES['image']['name'];
            $image_tmp = $_FILES['image']['tmp_name'];

            $dossier_cible = '../../assets/images/candidats/';
            if (!file_exists($dossier_cible)) {
                mkdir($dossier_cible, 0775, true);
                chown($dossier_cible, 'www-data');
                chgrp($dossier_cible, 'www-data');
            }

            $image_nom_final = time() . '_' . basename($image);
            $chemin_image = $dossier_cible . $image_nom_final;

            if (move_uploaded_file($image_tmp, $chemin_image)) {
                $stmt = $pdo->prepare("INSERT INTO candidats (nom, prenom, filiere, annee_formation, image, election_id) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$nom, $prenom, $filiere, $annee_formation, $image_nom_final, $election_id]);

                $_SESSION['message'] = "Candidat ajouté avec succès !";
                $_SESSION['type'] = 'success';
            } else {
                $_SESSION['message'] = "❌ Erreur lors du déplacement de l'image.";
                $_SESSION['type'] = 'error';
            }
        } else {
            $_SESSION['message'] = "❌ Erreur lors du téléchargement de l'image.";
            $_SESSION['type'] = 'error';
        }
    }

    header("Location: ajouterCandidat.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ajouter un Nouveau Candidat</title>
    <link rel="stylesheet" href="../../assets/css/ajouterCandidat.css">
</head>
<body>
    <div class="container">
        <a href="dashboard.php" class="back-btn">🏠 Retour à la Gestion des Candidats</a>

        <h2>Ajouter un Nouveau Candidat</h2>

        <!-- Notification avec couleur d'arrière-plan -->
        <?php if (isset($_SESSION['message'])) : ?>
            <div class="notification <?php echo $_SESSION['type']; ?>">
                <?php 
                    echo $_SESSION['message']; 
                    unset($_SESSION['message']);
                    unset($_SESSION['type']);
                ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" enctype="multipart/form-data">
            <label for="nom">Nom :</label>
            <input type="text" id="nom" name="nom" placeholder="Nom" required>

            <label for="prenom">Prénom :</label>
            <input type="text" id="prenom" name="prenom" placeholder="Prénom" required>

            <label for="filiere">Filière :</label>
            <input type="text" id="filiere" name="filiere" placeholder="Filière académique" required>

            <label for="annee_formation">Année de Formation :</label>
            <select id="annee_formation" name="annee_formation" required>
                <option value="">Sélectionnez l'année de formation</option>
                <option value="L1">L1</option>
                <option value="L2">L2</option>
                <option value="L3">L3</option>
                <option value="M1">M1</option>
                <option value="M2">M2</option>
            </select>

            <label for="election_id">Élection Associée :</label>
            <select id="election_id" name="election_id" required>
                <option value="">Sélectionnez une élection</option>
                <?php foreach ($elections as $election) : ?>
                    <option value="<?php echo $election['id']; ?>"><?php echo $election['nom']; ?></option>
                <?php endforeach; ?>
            </select>

            <label for="image">Image du Candidat :</label>
            <input type="file" id="image" name="image" accept="image/*" required>

            <button type="submit" name="ajouterCandidat">Ajouter le Candidat</button>
        </form>
    </div>

    <!-- Script pour masquer automatiquement le message après 5 secondes -->
    <script>
        window.onload = function() {
            setTimeout(function() {
                const notification = document.querySelector('.notification');
                if (notification) {
                    notification.style.display = 'none';
                }
            }, 5000); // Disparaît après 5 secondes
        };
    </script>
</body>
</html>
