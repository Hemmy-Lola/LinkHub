<?php
session_start();
 
if (isset($_GET['publication_id'])){

  $publication_id = $_GET['publication_id'];

}

// Connexion à la base de données
$pdo = new PDO("mysql:host=localhost:3306;dbname=linkhub;charset=utf8mb4", 'root', '');

// Vérification de la méthode HTTP utilisée
$methode = filter_input(INPUT_SERVER, "REQUEST_METHOD");

// Vérification des données envoyées
//if (!isset($_POST['content']) || empty($_POST['content'])) {
 //die("Le contenu du commentaire est obligatoire");

//}

//if (!isset($_POST['publication_id']) || empty($_POST['publication_id'])) {
 // die("L'identifiant de la publication est obligatoire");
//}

if ($methode == "POST"){

  if(!empty($_POST['publish_commentary'])){
    if($_POST['publish_commentary']){
    // Récupération des données envoyées
      $id_de_l_utilisateur = $_SESSION['id'];
      $contenu = filter_input(INPUT_POST, "content");

      // Insertion du nouveau commentaire dans la base de données
      $sql = "INSERT INTO comments (user_id, publication_id, content) 
              VALUES (:user_id, :publication_id, :content)"
              ;
      $stmt = $pdo->prepare($sql);
      $stmt->bindParam(':user_id', $id_de_l_utilisateur, PDO::PARAM_INT);
      $stmt->bindParam(':publication_id', $publication_id, PDO::PARAM_INT);
      $stmt->bindParam(':content', $contenu, PDO::PARAM_STR);

      $stmt->execute();
      }
      header("Refresh:0");
  }
}
// Récupération du contenu du commentaire inséré
//$commentaire_id = $pdo->lastInsertId();
//$sql = "SELECT content FROM comments WHERE id = :comment_id";
//$stmt = $pdo->prepare($sql);
//$stmt->bindParam(':comment_id', $commentaire_id, PDO::PARAM_INT);
//$stmt->execute();
//$commentaire = $stmt->fetchColumn();
  

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Commenter une publication</title>
    <link rel="stylesheet" href="./css/footer_shortpage.css">
    <link rel="stylesheet" href="./css/header.css">
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/publications.css">
</head>
<body>
  
  <?php require_once './pages/header.php' ?>
  <section>

  <h1> Commenter une publication</h1>
     <div class="comment-form">
        <form method="POST" action="">
            <textarea id="content" name="content" placeholder="Ajouter un commentaire..."></textarea>
            <button type="submit" name="publish_commentary" value="Publier"><i class="fas fa-comment"></i>Publier </button>
        </form>
    </div>
  </section>

  <?php require_once './pages/footer.php' ?>
</body>
</html>
