<?php
session_start();

$pdo = new PDO("mysql:host=localhost:3306; dbname=linkhub", "root", "");

if(isset($_GET['id'])){

    $currentProfileId = $_GET['id'];

}


//Dans cette requete, on récup dans l'url l'id en _GET. puis on """"convertis"""" l'id 
//En bind param :id = $id pour pouvoir l'utiliser en SQL sans avoir a mettre des variables PHP dans la requete
$requete3251 = $pdo->prepare("

    SELECT *
    FROM friend_list
    INNER JOIN users
    ON friend_list.user_id2 = users.id
    WHERE user_id1 = :id AND friend_list.status = 'Friend'

");


$requete3251->bindParam(':id', $currentProfileId);
$requete3251->execute();


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/header.css">
    <link rel="stylesheet" href="./css/footer_shortpage.css">
    <link rel="stylesheet" href="./css/style.css">
    <title>Friend list</title>
</head>
<body>
    
    <?php include_once './pages/header.php' ?>

    <section>
    <h3>Liste d'amis</h3> 

    <a href="./profil.php?id=<?= $currentProfileId ?>">Retour sur le profil</a>
    <?php
        
        if ($requete3251->rowCount() > 0 ){
            $friendlist = $requete3251->fetchAll(PDO::FETCH_ASSOC);
        
            foreach ($friendlist as $friend) {
               
                echo "
                <li> 
                <a href='profil.php?id={$friend['id']}'>
                {$friend['first_name']} {$friend['name']}
                </a>
                </li>";
            }
        } else {
            echo "
            <br>
            <p>Cette personne n'a actuellement personne en ami :( </p>
            <br>Peut-être que vous seriez le premier ? :)</br>
            ";
        }
    ?>
    </section>
    <?php include_once './pages/footer.php' ?>
</body>
</html>
