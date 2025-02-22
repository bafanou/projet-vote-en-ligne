<?php
require __DIR__ . '/config/config.php';

$currentTime = date('Y-m-d H:i:s');

// Sélectionner les élections ouvertes dont l'heure de fin est dépassée
$stmt = $pdo->prepare("SELECT * FROM elections WHERE statut = 'ouverte' AND date_fin <= ?");
$stmt->execute([$currentTime]);
$elections = $stmt->fetchAll();

foreach ($elections as $election) {
    $updateStmt = $pdo->prepare("UPDATE elections SET statut = 'cloturee' WHERE id = ?");
    $updateStmt->execute([$election['id']]);
    echo "Élection ID " . $election['id'] . " clôturée automatiquement.\n";
}
