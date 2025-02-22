<?php
session_start();
require '../../config/config.php';
require '../../config/mailer.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
    header('Location: ../auth/login.php');
    exit();
}

// Fonction pour générer un mot de passe temporaire aléatoire
function genererMotDePasseTemporaire($longueur = 8) {
    $caracteres = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
    return substr(str_shuffle($caracteres), 0, $longueur);
}

// Ajouter un nouvel électeur
if (isset($_POST['ajouterElecteur'])) {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $telephone = $_POST['telephone'];
    $mot_de_passe_temp = genererMotDePasseTemporaire();
    $mot_de_passe_hache = md5($mot_de_passe_temp);
    $statut = 'actif';
    $role = 'electeur';

    // Vérifier si l'email est déjà utilisé
    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
    $stmt->execute([$email]);
    $existe = $stmt->fetch();

    if ($existe) {
        $error = "Cet email est déjà utilisé par un autre utilisateur !";
    } else {
        // Insérer le nouvel électeur dans la base de données
        $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, prenom, email, telephone, mot_de_passe, role, statut) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nom, $prenom, $email, $telephone, $mot_de_passe_hache, $role, $statut]);
        $success = "Électeur ajouté avec succès !";

        // Préparer le contenu de l'email
        $sujet = "Vos identifiants de connexion pour le vote en ligne";
        $messageHtml = "
            <h1>Bienvenue sur notre plateforme de vote en ligne</h1>
            <p>Bonjour <strong>$prenom $nom</strong>,</p>
            <p>Vous avez été inscrit en tant qu'électeur sur notre plateforme de vote en ligne.</p>
            <p>Voici vos identifiants de connexion :</p>
            <ul>
                <li>Email : <strong>$email</strong></li>
                <li>Mot de passe temporaire : <strong>$mot_de_passe_temp</strong></li>
            </ul>
            <p><a href='http://localhost/projet-vote-en-ligne/views/auth/login.php'>Cliquez ici pour vous connecter</a></p>
            <p>Cordialement,<br>L'équipe de gestion des élections</p>
        ";

        // Envoyer l'email via PHPMailer
        if (envoyerEmail($email, $sujet, $messageHtml)) {
            $success .= "<br>Les identifiants ont été envoyés par email.";
        } else {
            $error = "Électeur ajouté, mais l'envoi de l'email a échoué.";
        }
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Ajouter un Nouvel Électeur</title>
    <link rel="stylesheet" href="../../assets/css/ajouterElecteur.css">
</head>
<body>
    <div class="container">
        <a href="gererElecteurs.php" class="back-btn">🏠 Retour à la Gestion des Électeurs</a>

        <h2>Ajouter un Nouvel Électeur</h2>

        <?php if (isset($success)) : ?>
            <div class="success fade-in"><?php echo $success; ?></div>
        <?php endif; ?>

        <?php if (isset($error)) : ?>
            <div class="error fade-in"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="nom">Nom :</label>
            <input type="text" id="nom" name="nom" placeholder="Nom" required>

            <label for="prenom">Prénom :</label>
            <input type="text" id="prenom" name="prenom" placeholder="Prénom" required>

            <label for="email">Email :</label>
            <input type="email" id="email" name="email" placeholder="Adresse email" required>

            <label for="telephone">Téléphone :</label>
            <input type="text" id="telephone" name="telephone" placeholder="Numéro de téléphone" required>

            <button type="submit" name="ajouterElecteur">Ajouter l'Électeur</button>
        </form>
    </div>
</body>
</html>
