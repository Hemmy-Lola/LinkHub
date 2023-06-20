<?php
session_start();

$pdo = new PDO("mysql:host=localhost:3306;dbname=linkhub;charset=utf8mb4", 'root', '');

// Affiche la page du groupe associé à son id
if (isset($_GET['group_name']) && isset($_GET['group_id'])) {
    $group_name = $_GET['group_name'];
    $group_id = $_GET['group_id'];
    
}

// Vérification de la méthode HTTP utilisée
$methode = filter_input(INPUT_SERVER, "REQUEST_METHOD");

if ($methode === 'POST') {
    // Récupération des données envoyées
    $emoji = isset($_POST['emoji']) ? $_POST['emoji'] : '';
    $user_id = 1; // Remplacez par l'ID de l'utilisateur actuel
    $publication_id = 1; // Remplacez par l'ID de la publication actuelle
    $comment_id = 1; // Remplacez par l'ID du commentaire actuel
    $type = "like"; // Remplacez par le type de réaction approprié

    if ($emoji && $user_id && $publication_id && $comment_id && $type) {

        // Insertion de la nouvelle réaction dans la base de données
        $sql = "INSERT INTO reactions (user_id, publication_id, comment_id, type, emoji, creation_date, who_react, who_react_id)
            VALUES (:user_id, :publication_id, :comment_id, :type, :emoji, NOW(), :who_react, :who_react_id)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':user_id' => $user_id,
            ':publication_id' => $publication_id,
            ':comment_id' => $comment_id,
            ':who_react' => "group",
            ':who_react_id' => $group_id,
            ':type' => $type,
            ':emoji' => $emoji
        ]);

        // Redirection vers la page de la publication ou du commentaire
        // header("Location: publication.php?id=$comment_id");
        // exit();
    }
} else {
    // Récupération des données pour affichage
    $type = isset($_GET['type']) ? $_GET['type'] : '';
    $emoji = isset($_GET['emoji']) ? $_GET['emoji'] : '';
    $comment_id = isset($_GET['comment_id']) ? $_GET['comment_id'] : '';
    $who_react = isset($_GET['who_react']) ? $_GET['who_react'] : '';
    $who_react_id = isset($_GET['who_react_id']) ? $_GET['who_react_id'] : '';
}

// header("Location: group_page.php?group_name=$group_name&group_id=$group_id");
// exit();
// ...

$sql = "SELECT * FROM reactions WHERE who_react = 'group' AND who_react_id = :group_id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':group_id' => $group_id]);
$reactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Réaction ajoutée !</title>
  <link rel="stylesheet" href="./css/footer_shortpage.css">
  <link rel="stylesheet" href="./css/header.css">
  <link rel="stylesheet" href="./css/style.css">
  <link rel="stylesheet" href="./css/publications.css">
  <link rel="stylesheet" href="./js/publications.js">

</head>
<body>
  <?php require_once './pages/header.php' ?>

  <div class="reaction-form">
    <form method="POST" action="">
      <input type="hidden" name="comment_id" value="1">
      <input type="hidden" name="user_id" value="1">
      <input type="hidden" name="emoji" id="emoji-input" value="">
      <input type="hidden" name="type" id="reaction-type-input">
      <div class="reactions">
        <button class="reaction-btn" data-reaction-type="like" data-emoji="👍">👍</button>
        <button class="reaction-btn" data-reaction-type="love" data-emoji="❤️">❤️</button>
        <button class="reaction-btn" data-reaction-type="haha" data-emoji="😂">😂</button>
        <button class="reaction-btn" data-reaction-type="wow" data-emoji="😮">😮</button>
        <button class="reaction-btn" data-reaction-type="sad" data-emoji="😢">😢</button>
        <button class="reaction-btn" data-reaction-type="angry" data-emoji="😠">😠</button>
      </div>
      <button type="submit"><i class="far fa-thumbs-up"></i> J'aime</button>
    </form>
  </div>
  <section>
    <?php
    foreach ($reactions as $reaction) {
        // Afficher les données de réaction
        // echo "ID de réaction : " . $reaction['reaction_id'] . "<br>";
        echo "ID de l'utilisateur : " . $reaction['user_id'] . "<br>";
        echo "ID de la publication : " . $reaction['publication_id'] . "<br>";
        echo "ID du commentaire : " . $reaction['comment_id'] . "<br>";
        echo "Type de réaction : " . $reaction['type'] . "<br>";
        echo "Emoji : " . $reaction['emoji'] . "<br>";
        echo "Date de création : " . $reaction['creation_date'] . "<br>";
        echo "Qui a réagi : " . $reaction['who_react'] . "<br>";
        echo "ID de celui qui a réagi : " . $reaction['who_react_id'] . "<br>";
        echo "<br>";
    }
    ?>
  </section>
  
  <?php require_once './pages/footer.php' ?>
</body>
</html>
