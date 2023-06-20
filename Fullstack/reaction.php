<?php
// Connexion à la base de données
$pdo = new PDO("mysql:host=localhost:3306;dbname=linkhub;charset=utf8mb4", 'root', '');

// Vérification de la méthode HTTP utilisée
$methode = filter_input(INPUT_SERVER, "REQUEST_METHOD");

if ($methode === 'POST') {
  // Récupération des données envoyées
  $type = filter_input(INPUT_POST, "type");
  $emoji = filter_input(INPUT_POST, "emoji", FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
  $utilisateur_id = filter_input(INPUT_POST, "user_id", FILTER_VALIDATE_INT);
  $commentaire_id = filter_input(INPUT_POST, "comment_id", FILTER_VALIDATE_INT);

  if ($type && $emoji && $utilisateur_id && $commentaire_id) {
    
    // Insertion de la nouvelle réaction dans la base de données
    $sql = "INSERT INTO `reactions` (`user_id`, `publication_id`, `comment_id`, `type`, `emoji`)
    VALUES (:user_id, NULL, :comment_id, :type, :emoji)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
      ':user_id' => $utilisateur_id,
      ':comment_id' => $commentaire_id,
      ':type' => $type,
      ':emoji' => $emoji,
    ]);

    // Redirection vers la page du commentaire
    // header("Location: commentaire.php?id=$commentaire_id");
    // exit();
if (!$type || !$emoji || !$utilisateur_id || !$commentaire_id) {
  die("Erreur : Les données envoyées sont invalides.");
}
  } 
}


// Redirection vers la page du commentaire
//header("Location: commentaire.php?id=$commentaire_id");
//exit();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>
<body>
  <h1>Réaction ajoutée !</h1>
  <p>Type : <?=$type?></p>
  <p>Emoji : <?=$emoji?></p>
  <p>Commentaire : <?=$commentaire_id?></p>
</body>
</html>
