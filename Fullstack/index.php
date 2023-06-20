<?php

session_start();


if (isset($_SESSION["validtoken"]) && $_SESSION["validtoken"] === true) {
    header('Location: dashboard.php');
    exit();
} else {


    header('Location: login.php');
    exit();

    
}


?>

