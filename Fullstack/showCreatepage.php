<?php
session_start();

$id =$_GET['id'];

$connexion = new PDO("mysql:host=localhost:3306;dbname=linkhub", "root", "");

$requeterecup = $connexion->prepare("
    
    SELECT * 
    FROM `page`
    WHERE id = :id
    
");

$requeterecup->execute([

    ":id" => $id

]);

$data_page = $requeterecup->fetch(PDO::FETCH_ASSOC);

$page_name = $data_page['page_name'];
$category_page = $data_page['category_page'];
$page_bio = $data_page['page_bio'];
$page_logo = $data_page['page_logo'];
 

?>
    
<!DOCTYPE html>
<html>
<head>
    <title>Résultat du formulaire</title>
</head>
<body>
    <h1>Résultat du formulaire</h1>


    <p>Nom de la page : <?= $page_name ?> </p>
    <p>Secteur : <?= $category_page ?> </p>
    <p>Slogan : <?= $page_bio ?> </p>
    <p>Logo : <?= $page_logo ?> </p>

</body>
</html>
