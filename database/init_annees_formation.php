<?php
// Activer l'affichage des erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../config/config.php';

// Liste des années de formation par défaut à initialiser
$annees_par_defaut = ['L1', 'L2', 'L3', 'M1', 'M2'];

// Utiliser un ID d'élection valide (remplace 1 par un ID existant)
$election_id_valide = 4;

try {
    // Vérifier les années de formation déjà présentes dans la base de données
    $stmt = $pdo->prepare("SELECT DISTINCT annee_formation FROM candidats WHERE annee_formation IS NOT NULL AND annee_formation != ''");
    $stmt->execute();
    $annees_existantes = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Insérer les années manquantes
    foreach ($annees_par_defaut as $annee) {
        if (!in_array($annee, $annees_existantes)) {
            // Insertion en tant que candidat factice avec un ID d'élection valide
            $stmt = $pdo->prepare("INSERT INTO candidats (nom, prenom, filiere, annee_formation, image, election_id) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute(['Année', 'Formation', 'Générale', $annee, 'default.png', $election_id_valide]);
            echo "Année de formation ajoutée : $annee<br>";
        }
    }

    echo "Initialisation des années de formation terminée.";
} catch (Exception $e) {
    echo "Erreur lors de l'initialisation : " . $e->getMessage();
}
