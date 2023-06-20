<?php
session_start();

// Connexion à la base de données
$pdo = new PDO("mysql:host=localhost:3306;dbname=linkhub;charset=utf8mb4", 'root', '');

// Vérification de la méthode HTTP utilisée
$methode = filter_input(INPUT_SERVER, "REQUEST_METHOD");

// Vérification des données envoyées
//if (!isset($_POST['content']) || empty($_POST['content'])) {
 //die("Le contenu du commentaire est obligatoire");

//}

//if (!isset($_POST['publication_id']) || empty($_POST['publication_id'])) {
  //die("L'identifiant de la publication est obligatoire");
//}

// Récupération des données envoyées
$id_de_l_utilisateur = 1; // exemple, à remplacer par le véritable identifiant de l'utilisateur
$contenu = filter_input(INPUT_POST, "content");
$publication_id = filter_input(INPUT_POST, "publication_id");

// Insertion du nouveau commentaire dans la base de données
$sql = "INSERT INTO comments (user_id, publication_id, parent_id, content,who_comment,who_comment_id) 
        VALUES (:user_id, :publication_id, :parent_id, :content,:who_comment,:who_comment_id)"
        ;
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user_id', $id_de_l_utilisateur, PDO::PARAM_INT);
$stmt->bindParam(':publication_id', $publication_id, PDO::PARAM_INT);
$stmt->bindValue(':parent_id', null, PDO::PARAM_NULL);
$stmt->bindParam(':content', $contenu, PDO::PARAM_STR);
$stmt->bindValue(':who_comment', "group", PDO::PARAM_STR);
$stmt->bindValue(':who_comment_id', $_SESSION['id'], PDO::PARAM_INT);
$stmt->execute();

// Récupération du contenu du commentaire inséré
$commentaire_id = $pdo->lastInsertId();
$sql = "SELECT content FROM comments WHERE id = :comment_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':comment_id', $commentaire_id, PDO::PARAM_INT);
$stmt->execute();
$commentaire = $stmt->fetchColumn();


// Redirection vers la page de la publication
//header("Location: publication.php?id=$publication_id");
//exit();
header("Location: dashboard.php");
?>