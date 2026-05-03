<?php

// Paramètres de connexion
$host = 'localhost';
$dbname = 'apexkart';
$username = 'apexkart';
$password = 'apexkart';
$charset = 'utf8mb4';

// Connexion 
try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=$charset",
        $username,
        $password,
        array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,//Si une erreur SQL se produit => exception
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            //Quand on fais : $stmt->fetch();
            //PHP te retourne 2 types d’index en même temps :
            /*[0 => "Ali",
            "nom" => "Ali",
            1 => 25,
            "age" => 25]
Problème :doublons, moins lisible, inutilement lourd
Avec PDO::FETCH_ASSOC:
["nom" => "Ali",
  "age" => 25]

=>Seulement les noms des colonnes, Plus clair, Plus propre

On accèdes aux données comme ça :echo $row["nom"];
au lieu de :echo $row[0]*/
        )
    );
} catch (PDOException $e) {
    // En cas d'erreur, afficher un message simple
    die("Erreur de connexion : " . $e->getMessage());//Arrête immédiatement le script (die) et Affiche le message d’erreur
}
?>
<!--Comme on fait dans les autres fichiers php :
require_once 'config.php';
=> la variable $pdo est disponible partout dans le projet-->