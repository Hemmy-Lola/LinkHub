<?php

session_start();

// Affiche la page du groupe associé à son id
if (isset($_GET['group_name']) && isset($_GET['group_id'])) {
    $group_name = $_GET['group_name'];
    $group_id = $_GET['group_id'];

}

// Connexion à la base de données
$host = 'localhost'; // l'hôte où est hébergée la base de données
$dbname = 'linkhub'; // le nom de votre base de données
$username = 'root'; // votre nom d'utilisateur MySQL
$password = ''; // votre mot de passe MySQL
$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);

$methode = filter_input(INPUT_SERVER, "REQUEST_METHOD");

if ($methode == "POST") {
  
    $contenu = filter_input(INPUT_POST, "content");
    $titre = filter_input(INPUT_POST, "title");

    // Insertion de la nouvelle publication dans la base de données
    $sql = "INSERT INTO publications (title, content, who_typed, who_typed_id)
            VALUES (:title, :content, :who_typed, :who_typed_id)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':title' => $titre,
        ':content' => $contenu,
        ':who_typed' => "group",
        ":who_typed_id" => $group_id,
    ]);

    header("Location: group_page.php?group_name={$group_name}&group_id={$group_id}");


}



?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer une publication - <?= $group_name ?></title>
    <link rel="stylesheet" href="./css/footer_shortpage.css">
    <link rel="stylesheet" href="./css/header.css">
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/publications.css">
</head>
<body>

<?php require_once './pages/header.php' ?>
    
    <section>
        <h1>Création d'une publication pour le groupe <?= $group_name ?>

    </section>

    <section>
    <div class="publication-form">
	<form method="post" enctype="multipart/form-data">
        <input type="text" id="title" name="title" placeholder="Titre de la publication" required>
        <textarea id="content" name="content" placeholder="Exprimez-vous" required></textarea>
        <input type="file" id="image" name="image" accept="image/*">
        <button type="submit"><i class="fas fa-paper-plane"></i> Publier</button>
    </form>

	</div>

    

    </section>


    <?php require_once './pages/footer.php' ?>
</body>
</html>