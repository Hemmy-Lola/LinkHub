<?php
 
    require './function.php';
    require_once './include/verify_connection.php';
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/social.css">
    <link rel="stylesheet" href="./css/header.css">
    <link rel="stylesheet" href="./css/footer.css">
    <title>Communauté</title>
    
    <!-- // Afficher un bouton
    echo '<button class="mon-bouton">Cliquez ici</button>'; -->
    
</head>

<body>
    <?php require_once './pages/header.php' ?>
<section class="create_story">
    <div class="titres"> 
        <h1>Créer votre propre histoire !</h1>
    </div>
    <div class="section_button">
        <a href="./create_group.php"><button>Créer un groupe</button></a>
        <a href="./Page.php"><button>Créer une page</button></a>
    </div>
    </section>

    <!-- <div class="titres"> 
        <h2>Groupes</h2>     
    </div> -->


<section class="profil_list">
    <div class="titres_section"> 
        <h2>Suggestion de groupe :</h2>
    </div>
    <div class="profil_display">
        <?php isPublicGroupListEmpty(); ?>
    </div>
</section>


<section class="profil_list">
    <div class="titres_section"> 
        <h2> Mes groupes :</h2>
    </div>
    <div class="profil_display">
        <?php isGroupListEmpty(); ?>
    </div>
</section>
    
    <?php require_once './pages/footer.php' ?>
    </body>
</html>
