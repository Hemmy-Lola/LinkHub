<?php
    require_once './function.php';

    $pdo = new PDO("mysql:host=localhost:3306;dbname=linkhub", "root", "");


?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/header.css">
    <link rel="stylesheet" href="./css/footer.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
   
    <title>Document</title>
</head>
<body> 

    <?php require_once './pages/header.php' ?>

    <section class="notification">
        <h1> Notifications </h1>
    
        <?php showNotifFriend($pdo) ?>


        <?php showNotif($pdo) ?>
    </section>

    <?php require_once './pages/footer.php' ?>

</body>
</html>