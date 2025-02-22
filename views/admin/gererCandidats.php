<?php
session_start();
require '../../config/config.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
    header('Location: ../auth/login.php');
    exit();
}

// Récupérer toutes les élections disponibles
$stmt = $pdo->prepare("SELECT id, nom FROM elections");
$stmt->execute();
$elections = $stmt->fetchAll();

// Récupérer l'élection sélectionnée (si existante)
$election_id = isset($_GET['election_id']) ? $_GET['election_id'] : '';

// Récupérer les candidats en fonction de l'élection sélectionnée
if ($election_id) {
    $stmt = $pdo->prepare("SELECT candidats.*, elections.nom AS election_nom FROM candidats 
                           JOIN elections ON candidats.election_id = elections.id 
                           WHERE elections.id = ?");
    $stmt->execute([$election_id]);
} else {
    $stmt = $pdo->prepare("SELECT candidats.*, elections.nom AS election_nom FROM candidats 
                           JOIN elections ON candidats.election_id = elections.id");
    $stmt->execute();
}

$candidats = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gérer les Candidats</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-6">
    <div class="container mx-auto">
        <h2 class="text-3xl font-bold mb-6">Gérer les Candidats</h2>
        
        <a href="ajouterCandidat.php" class="bg-blue-500 text-white px-4 py-2 rounded shadow mb-4 inline-block">
            Créer un Nouveau Candidat
        </a>

        <!-- Filtrer par Élection -->
        <form method="GET" class="mb-4">
            <label for="election_id" class="text-lg font-medium">Filtrer par Élection :</label>
            <select id="election_id" name="election_id" class="border border-gray-300 p-2 rounded" onchange="this.form.submit()">
                <option value="">Toutes les Élections</option>
                <?php foreach ($elections as $election) : ?>
                    <option value="<?php echo $election['id']; ?>" <?php echo ($election['id'] == $election_id) ? 'selected' : ''; ?>>
                        <?php echo $election['nom']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>

        <!-- Tableau des candidats -->
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                <thead class="bg-blue-500 text-white">
                    <tr>
                        <th class="py-3 px-6 text-left">ID</th>
                        <th class="py-3 px-6 text-left">Nom du Candidat</th>
                        <th class="py-3 px-6 text-left">Filière</th>
                        <th class="py-3 px-6 text-left">Année de Formation</th>
                        <th class="py-3 px-6 text-left">Élection Associée</th>
                        <th class="py-3 px-6 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($candidats as $candidat) : ?>
                        <tr class="border-b">
                            <td class="py-3 px-6"><?php echo $candidat['id']; ?></td>
                            <td class="py-3 px-6"><?php echo $candidat['nom'] . ' ' . $candidat['prenom']; ?></td>
                            <td class="py-3 px-6"><?php echo $candidat['filiere']; ?></td>
                            <td class="py-3 px-6"><?php echo $candidat['annee_formation']; ?></td>
                            <td class="py-3 px-6"><?php echo $candidat['election_nom']; ?></td>
                            <td class="py-3 px-6 flex space-x-2">
                                <a href="modifierCandidat.php?candidat_id=<?php echo $candidat['id']; ?>" 
                                   class="bg-green-500 text-white px-3 py-1 rounded shadow">
                                    Modifier
                                </a>
                                <a href="supprimerCandidat.php?candidat_id=<?php echo $candidat['id']; ?>" 
                                   class="bg-red-500 text-white px-3 py-1 rounded shadow"
                                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce candidat ?');">
                                    Supprimer
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <a href="dashboard.php" class="text-blue-500 mt-4 inline-block">🏠 Retour au Dashboard</a>
    </div>
</body>
</html>
