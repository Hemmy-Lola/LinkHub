<?php

    if(isset($_SESSION['validtoken']) && $_SESSION['validtoken'] == false && !empty($_SESSION['validtoken'])) {


        header('Location: login.php');
        session_destroy();
        exit();


    }
    
?>