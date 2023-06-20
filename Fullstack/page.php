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
    }

    // Enregistrement des données dans la base de données
    
    $requete = $connexion->prepare("

        INSERT INTO `page` (page_name, category_page, page_bio, page_logo) 
        VALUES (:page_name, :category_page, :page_bio, :page_logo)
    
    ");
    $requete->execute([

        ":page_name" => $page_name,
        ":category_page" => $category_page,
        ":page_bio" => $page_bio,
        ":page_logo" => $page_logo,

    ]);

    // Recupérer les données dans la bases de données
    
    $requete2 = $connexion->prepare("
        SELECT id FROM page
        WHERE page_name = :page_name AND category_page = :category_page AND page_bio = :page_bio AND page_logo = :page_logo

    ");

    $requete2->execute([

        ":page_name" => $page_name,
        ":category_page" => $category_page,
        ":page_bio" => $page_bio,
        ":page_logo" => $page_logo,

    ]);

    $pageID = $requete2->fetch(PDO::FETCH_ASSOC);

    $id = $pageID['id'];

    if (!$requete) {
        die('Erreur d\'enregistrement dans la base de données : ' . $connexion->errorInfo()[2]);
    }

    echo 'Page créée avec succès !';
    header("Location: showCreatepage.php?id=$id");

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/footer.css">
    <link rel="stylesheet" href="./css/header.css">
    <link rel="stylesheet" href="./css/create_page.css">
    <title>Créer une page publique</title>
</head>
<body>
<?php require_once './pages/header.php' ?>

<div class="form_create_page">
    <h1>Créer une page publique</h1>

    <div class="info_create_page">
        <form method="POST" enctype="multipart/form-data">
            <label for="page_name">Nom de la page :</label>
            <input type="text" id="page_name" name="page_name" required>
            <label for="category_page">Secteur :</label>
            <select id="category_page" name="category_page" required>
                <option value="">--Choisissez un secteur--</option>
                <option value="Entreprise">Entreprise</option>
                <option value="Page Vitrine">Page vitrine</option>
                <option value="Etablissement Educatif">Etablissement Educatif</option>
            </select>

            <label for="page_bio">Slogan :</label>
            <input type="text" id="page_bio" name="page_bio" required>

            <label for="page_logo">Logo :</label>
            <input type="file" id="page_logo" name="page_logo">

            <input type="submit" value="Créer la page">.
        </form>
    </div>
</div>

    <?php require_once './pages/footer.php' ?>

</body>
</html>



