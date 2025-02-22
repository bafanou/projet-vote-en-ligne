<?php
session_start();
require '../../config/config.php';

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = md5($_POST['password']);

    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ? AND mot_de_passe = ?");
    $stmt->execute([$email, $password]);
    $user = $stmt->fetch();
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['prenom'] . ' ' . $user['nom'];
        $_SESSION['user_role'] = $user['role'];
    
        // Enregistrer la dernière connexion
        $stmt = $pdo->prepare("UPDATE utilisateurs SET derniere_connexion = NOW() WHERE id = ?");
        $stmt->execute([$user['id']]);
    
        if ($user['role'] == 'admin') {
            header('Location: ../admin/dashboard.php');
        } else {
            header('Location: ../vote/listeElections.php');
        }
        exit();
    }
    
}


?>

<!DOCTYPE html>
<html>
<head>
    <title>Connexion</title>
    <link rel="stylesheet" href="../../assets/css/login.css">
</head>
<body>
    <div class="container">
        <h2>Connexion</h2>

        <?php if (isset($error)) : ?>
            <div class="error fade-in"><?php echo $error; ?></div>
        <?php endif; ?>

        <form id="loginForm" method="POST" action="">
            <input type="email" id="email" name="email" placeholder="Email" required><br>
            <input type="password" id="password" name="password" placeholder="Mot de passe" required><br>
            <button type="submit" name="login">Se connecter</button>
        </form>
        
        <a href="#">Mot de passe oublié ?</a>
    </div>

    <script src="../../assets/js/login.js"></script>
</body>
</html>
