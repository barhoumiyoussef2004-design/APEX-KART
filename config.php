<?php
// =============================================
// CONFIG.PHP - Connexion à la base de données
// =============================================

// Paramètres de connexion
$host = 'localhost';
$dbname = 'apexkart';
$username = 'apexkart';
$password = 'apexkart';
$charset = 'utf8mb4';

// Connexion PDO avec try/catch
try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=$charset",
        $username,
        $password,
        array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        )
    );
} catch (PDOException $e) {
    // En cas d'erreur, afficher un message simple
    die("Erreur de connexion : " . $e->getMessage());
}
?>
