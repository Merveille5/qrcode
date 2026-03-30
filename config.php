<?php
try {
    // Connexion à la base
    $pdo = new PDO("mysql:host=localhost;dbname=presence;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Requête SQL (vue déjà créée dans ta base)
    $sql = "SELECT * FROM vue_presence";

    // Préparation et exécution
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    // Récupération des résultats
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Ici tu peux traiter $rows (affichage, etc.)
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
