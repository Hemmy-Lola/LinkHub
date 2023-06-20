<?php
session_start();

$connexion = new PDO("mysql:host=localhost:3306;dbname=linkhub", "root", "");

// Vérification de la connexion
if (!$connexion) {
    die("La connexion a échoué : " . $connexion->errorInfo()[2]);
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données saisies dans le formulaire
    $page_name = isset($_POST['page_name']) ? $_POST['page_name'] : '';
    $category_page = isset($_POST['category_page']) ? $_POST['category_page'] : '';
    $page_bio = isset($_POST['page_bio']) ? $_POST['page_bio'] : '';
    $page_logo = '';

    // Traitement du fichier image
   if (isset($_FILES['page_logo']) && $_FILES['page_logo']['error'] === UPLOAD_ERR_OK) {
      $uploadDir = 'uploads/'; // Répertoire de destination des fichiers uploadés
      $tempName = $_FILES['page_logo']['tmp_name'];
      $fileName = $_FILES['page_logo']['name'];
      $targetPath = $uploadDir . $fileName;
      move_uploaded_file($tempName, $targetPath);
      $page_logo = $fileName;
    };

    // Enregistrement des données dans la base de données
    
    $requeteSave = $connexion->prepare("

        INSERT INTO `page` (page_name, category_page, page_bio, page_logo) 
        VALUES (:page_name, :category_page, :page_bio, :page_logo)
    
    ");

    $requeteSave->execute([

        ":page_name" => $page_name,
        ":category_page" => $category_page,
        ":page_bio" => $page_bio,
        ":page_logo" => $page_logo,

    ]);

    // Recupérer les données dans la bases de données
    
    $requeteRecup = $connexion->prepare("
    SELECT id FROM page
    WHERE page_name = :page_name AND category_page = :category_page AND page_bio = :page_bio AND page_logo = :page_logo

    ");
    
    $requeteRecup->execute([
        
        ":page_name" => $page_name,
        ":category_page" => $category_page,
        ":page_bio" => $page_bio,
        ":page_logo" => $page_logo,
        
    ]);
    
    $pageID = $requeteRecup->fetch(PDO::FETCH_ASSOC);

    // Change le role de l'utilisateur en admin
    
    $requeteSaveAdmin = $connexion->prepare("

        INSERT INTO `page_members` (role, user_id, page_id) 
        VALUES (:role, :user_id, :page_id)
    
    ");

    $requeteSaveAdmin->execute([

        ":role" => "",
        ":user_id" => $_SESSION['id'],
        ":page_id" => $pageID['id'],

    ]);    
    
    $requeteAdmin = $connexion->prepare("
    UPDATE `page_members`
    SET role = 'admin'
    WHERE user_id = :user_id

    ");

    $requeteAdmin->execute([

        ":user_id" => $_SESSION['id'],
    ]);



    $id = $pageID['id'];

    if (!$requeteSave) {
        die('Erreur d\'enregistrement dans la base de données : ' . $connexion->errorInfo()[2]);
    }

    echo 'Page créée avec succès !';
    header("Location: showCreatepage.php?id=$id");

} 
?>

<!DOCTYPE html>
<html>
<head>
    <title>Créer une page publique</title>
</head>
<body>
    <h1>Créer une page publique</h1>
    <h4>Les internautes accèdent à votre Page pour en savoir plus sur vous.</h4>
    <h4>Veillez à y inclure toutes les informations dont ils pourraient avoir besoin.</h4>

    <form method="POST" enctype="multipart/form-data">

        <label for="page_name">Nom de la page :</label>
        <input type="text" id="page_name" name="page_name" required><br><br>

        <label for="category_page">Secteur :</label>
        <select id="category_page" name="category_page" required>
            <option value="">--Choisissez un secteur--</option>
            <option value="Entreprise">Entreprise</option>
            <option value="Page Vitrine">Page vitrine</option>
            <option value="Etablissement Educatif">Etablissement Educatif</option>
        </select><br><br>

        <label for="page_bio">Slogan :</label>
        <input type="text" id="page_bio" name="page_bio" required><br><br>

        <label for="page_logo">Logo :</label>
        <input type="file" id="page_logo" name="page_logo"><br><br>
       
        <input type="submit" value="Créer la page">.

        
    
    </form>

</body>
</html>



