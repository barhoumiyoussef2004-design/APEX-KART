<?php

require_once 'config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    try {
        $stmt = $pdo->prepare("UPDATE reservations SET statut = 'confirme' WHERE id = :id AND statut = 'en_attente'");
        $stmt->execute(array(':id' => $id));
        echo 'OK';
    } catch (PDOException $e) {
        echo 'ERREUR';
    }
}
?>