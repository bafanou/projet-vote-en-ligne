<?php
session_start();
require '../../config/config.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
    header('Location: ../auth/login.php');
    exit();
}

// Récupérer toutes les élections depuis la base de données
$stmt = $pdo->prepare("SELECT * FROM elections ORDER BY date_creation DESC");
$stmt->execute();
$elections = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Administrateur</title>
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Tableau de Bord</h2>
        <a href="dashboard.php" class="active">Accueil</a>
        <a href="ajouterElection.php">Créer une Élection</a>
        <a href="gererElecteurs.php">Gérer les Électeurs</a>
        <a href="ajouterCandidat.php">Gérer les Candidats</a>
        <a href="../auth/logout.php">Déconnexion</a>
    </div>

    <!-- Contenu principal -->
<div class="main-content">
        <h1>Bienvenue, <?php echo $_SESSION['user_name']; ?> !</h1>

 <div class="card-container">
    <!-- Affichage dynamique des élections avec bouton pour voir les résultats -->
      <?php foreach ($elections as $election) : ?>
        <div class="card">
    <div class="icon">📊</div>
    <h3><?php echo $election['nom']; ?></h3>
    <p>Voir les résultats de cette élection.</p>
    
    <!-- Affichage du compte à rebours -->
    <?php if ($election['statut'] == 'ouverte') : ?>
        <p class="countdown" data-date="<?php echo $election['date_fin']; ?>"></p>
    <?php else : ?>
        <p class="countdown closed">Élection clôturée</p>
    <?php endif; ?>

    <button class="action-btn" 
        onclick="location.href='../resultats/resultats.php?election_id=<?php echo $election['id']; ?>'">
        Voir les Résultats
    </button>
  
    </div>
    <?php endforeach; ?>
</div>

     

    
  


   <div class="card-container">
    <!-- Carte pour gérer les élections existantes -->
    <div class="card">
        <div class="icon">📋</div>
        <h3>Gérer les Élections</h3>
        <p>Consultez, modifiez ou supprimez les élections en cours.</p>
        <button class="action-btn" onclick="location.href='gererElections.php'">Voir la Liste des Élections</button>
    </div>
    <!-- Carte pour gérer les candidats -->
    <div class="card">
        <div class="icon">👥</div>
        <h3>Gérer les Candidats</h3>
        <p>Voir la liste modifiez ou supprimez les candidats inscrits.</p>
        <button class="action-btn" onclick="location.href='gererCandidats.php'">Accéder</button>
    </div>

    
    
</div>



</div>

    <script src="../../assets/js/dashboard.js"></script>
</body>
</html>
