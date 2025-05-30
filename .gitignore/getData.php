<?php
header('Content-Type: application/json');

// Verbindungsdaten – ANPASSEN!
$host = 'mysql01.infomaniak.com';
$db   = 'zi7vz6_IM4_PlantGrowth_CV';
$user = 'zi7vz6_IM4';
$pass = 'Leila-11';

// Verbindung herstellen mit Fehlerbehandlung
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Datenbankverbindung fehlgeschlagen: ' . $e->getMessage()]);
    exit;
}

// Den Chart-Parameter auslesen
$chart = isset($_GET['chart']) ? $_GET['chart'] : '';

// Abhängig vom Chart unterschiedliche SQL-Queries
switch ($chart) {
    case 'proTag':
        $sql = "SELECT datum AS label, anzahl AS value FROM bewasserung ORDER BY datum ASC";
        break;

    case 'inML':
        $sql = "SELECT datum AS label, ml AS value FROM wassermenge ORDER BY datum ASC";
        break;

    case 'fuellstand':
        $sql = "SELECT zeitpunkt AS label, fuellstand AS value FROM reservoir ORDER BY zeitpunkt ASC";
        break;

    default:
        echo json_encode(['error' => 'Ungültiger Chart-Parameter']);
        exit;
}

// Query ausführen und Daten holen
try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($data);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Abfrage fehlgeschlagen: ' . $e->getMessage()]);
}