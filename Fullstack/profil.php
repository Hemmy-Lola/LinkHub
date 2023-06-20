<?php
require_once './function.php';

//Connexion à la base de donnée :
$pdo = new PDO("mysql:host=localhost:3306; dbname=linkhub", "root", "");

$methode = filter_input(INPUT_SERVER, "REQUEST_METHOD");

//Identification de l'id de l'utilisateur et récupération des informations de l'utilisateur en fonction de son id: 
if (isset($_GET['id']) and $_GET['id'] > 0) {
    $getid = intval($_GET['id']);
    $requser = $pdo->prepare('SELECT * FROM users WHERE id = ?');
    $requser->execute(array($getid));
    $userinfo = $requser->fetch(PDO::FETCH_ASSOC);

}

// Fermeture de la connexion à la base de données
$pdo = null;


?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="./css/header.css">
        <link rel="stylesheet" href="./css/footer.css">
        <link rel="stylesheet" href="./css/profil_user.css">

        <title>Mon Profil</title>

    </head>

    <body>
    <?php require_once './pages/header.php' ?>

    <div class="user_header">
        <div class="user_banner">
            <img src="./assets/uploads/<?php echo $userinfo['banner'];?>" />
        </div>
        <div class="user_avatar">
            <img src="./assets/uploads/<?php echo $userinfo['avatar'];?>" />
        </div>
    </div>
    <div class="profil">
        
        <?= $_SESSION['status_error'] ?>
        <div class="user_info">
            <div class="name_user">
                    <h2>Profil de <?php echo $userinfo['first_name']; ?></h2>
            </div>
                <h3>Nom :<?php echo $userinfo['name']; ?></h3>
                <h3>Prénom :<?php echo $userinfo['first_name']; ?></h3>
                <h3>Téléphone : <?php echo $userinfo['phone']; ?></h3>
                <h3>Genre : <?php echo $userinfo['gender']; ?></h3>
                <h3>Anniversaire : <?php echo $userinfo['birthday']; ?></h3>
        </div>

            <?php
            if (isset($_SESSION['id']) and $userinfo['id'] == $_SESSION['id']){
               echo' <div class="option_user">
               <a href="./edition.php">
               <button type="button" class="editeur_btn">Editer mon profil</button>
               </a>
               <a href="./delete_account.php">
               <button type="button" class="editeur_btn">Suppression du compte</button></a>
               </div>
            ';
            }
            ?>
            
        <div class="user_group">
                <h2>Mes groupes</h2>
                <div class="my_group">
                    <?php isGroupListEmpty(); ?>
        </div>
    </div>
        <?php require_once './pages/footer.php' ?> 
    </body>

    </html>