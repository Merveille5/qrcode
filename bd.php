<?php
// Inclure la configuration PDO
require_once "config.php";


$sql = "SELECT * FROM vue_presence";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Document</title>
</head>
<body>
    <h1> ENREGISTREMENTS </h1>
   <table class="table table-striped table-hover">
  <thead class="table-dark">
    <tr>
      <th scope="col">Matricule</th>
      <th scope="col">Nom</th>
      <th scope="col">Postnom</th>
      <th scope="col">Prénom</th>
      <th scope="col">Sexe</th>
      <th scope="col">Fonction</th>
      <th scope="col">Statut</th>
      <th scope="col">date_presence</th>
      <th scope="col">Heure_arrivée</th>
      <th scope="col">Heure_départ</th>
      <th scope="col"> est_ouvet</th>
    </tr>
  </thead>
  <tbody class="table-group-divider">
    <?php
  foreach ($rows as $row) {
    echo "<tr>
            <td>".$row['matricule']."</td>
            <td>".$row['nom']."</td>
            <td>".$row['postnom']."</td>
            <td>".$row['prenom']."</td>
            <td>".$row['sexe']."</td>
            <td>".$row['fonction']."</td>
            <td>".$row['statut']."</td>
            <td>".$row['date_presence']."</td>
            <td>".$row['heure_arrivee']."</td>
            <td>".$row['heure_depart']."</td>
            <td>".$row['est_ouvert']."</td>
          </tr>";
}
?>
  </tbody>
</table>

</body>
</html>