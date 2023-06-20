<?php 
    require_once './include/session_start.php';
    require_once './include/verify_connection.php';

    $pdo = new PDO("mysql:host=localhost:3306;dbname=linkhub", "root", "");
    
    $requete = $pdo->prepare("
        SELECT id, name, first_name FROM users
        WHERE mail = :login
    
    ");

    $requete->execute([

        ":login" => $_SESSION['login']

    ]);

    $utilisateur = $requete->fetch(PDO::FETCH_ASSOC);

    $_SESSION['id'] = $utilisateur['id'];
    $_SESSION['name'] = $utilisateur['name'];
    $_SESSION['first_name'] = $utilisateur['first_name'];
    

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/settings.css">
    <link rel="stylesheet" href="./css/header.css">
    <link rel="stylesheet" href="./css/publications.css">
    <link rel="stylesheet" href="./css/footer.css">
    <title>Paramètre</title>
</head>
<body>
    
    <?php require_once './pages/header.php' ?>
    <div class="container">
        <div class="header_parametre">
            <a href="profil.php?id=<?= $_SESSION['id'] ?>">
                <h3><?= $_SESSION['first_name'] . ' ' . $_SESSION['name'] ?></h3>
            </a>
        </div>
    
        <div class="logout">
            <a href='logout.php'>
                <button type='logout' class='logout'>Déconnexion</button>
            </a>
        </div>
    </div>
    <?php require_once './pages/footer.php' ?>
</body>
</html>


