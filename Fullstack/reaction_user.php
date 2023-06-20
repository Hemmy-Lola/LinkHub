<?php
include 'config.php';
$pdo = new PDO("mysql:host=localhost:3306;dbname=linkhub;charset=utf8mb4", 'root', '');

// Vérification de la méthode HTTP utilisée
$methode = filter_input(INPUT_SERVER, "REQUEST_METHOD");

if ($methode === 'POST') {
  // Récupération des données envoyées
  $emoji = filter_input(INPUT_POST, "emoji", FILTER_SANITIZE_STRING);
  $user_id = 1;
  $publication_id = 1;
  $comment_id = 1;
  $type = "like";

  if ($emoji && $user_id && $publication_id && $comment_id && $type) {
    // Insertion de la nouvelle réaction dans la base de données
    $sql = "INSERT INTO reactions (user_id, publication_id, comment_id, type, emoji, creation_date)
            VALUES (:user_id, :publication_id, :comment_id, :type, :emoji, NOW())";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
      ':user_id' => $user_id,
      ':publication_id' => $publication_id,
      ':comment_id' => $comment_id,
      ':type' => $type,
      ':emoji' => $emoji
    ]);

    // Redirection vers la page de la publication ou du commentaire
    //header("Location: publication.php?id=$comment_id");
    //exit();
  } //else {
    //die("Erreur : Les données envoyées sont invalides.");
  //}
} else {
  // Récupération des données pour affichage
  $type = $_GET['type'] ?? '';
  $emoji = $_GET['emoji'] ?? '';
  $comment_id = $_GET['comment_id'] ?? '';
}
header("Location: dashboard.php");
?>

