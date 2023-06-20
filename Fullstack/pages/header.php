<?php

if (!isset($_SESSION["validtoken"]) || $_SESSION["validtoken"] === true) {

    $pdo = new PDO("mysql:host=localhost:3306; dbname=linkhub", "root", "");
    $requete = $pdo->prepare("
        
            SELECT * FROM users
            WHERE mail = :login
        
        ");

    $requete->execute([

        ":login" => $_SESSION["login"],

    ]);

    $utilisateur = $requete->fetch(PDO::FETCH_ASSOC);

    $_SESSION['id'] = $utilisateur['id'];
}

$methode = $searchbar = false;
// Input bar de recherche = null


// Si la methode de l'input est en 'GET' 
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["searchbar"])) {
    $methode = "GET"; // Ici on passe la variable en "GET"
    $searchbar = filter_input(INPUT_GET, "searchbar"); //On récup la valeur de l'input apres avoir submit
}





function connected()
{

    echo '<div>
                <ul id="navbar">
                    <li><a href="./index.php"><img src="./assets/icone/home.svg"></a></li>
                    <li><a href="message_hub.php"><img src="./assets/icone/message-circle.svg"></a></li>
                    <li><a href="./notifications.php"><img src="./assets/icone/bell.svg"></a></li>
                    <li><a href="./social.php"><img src="./assets/icone/users.svg"></a></li>
                    <li><a href="./settings.php"><img src="./assets/icone/menu.svg" >
                    </a><li>
                   
                </ul>
                <script src="./button.js"></script>
            </div>';
}

?>

<section id="header">
    <div id="navbar-left">
        <a href="./index.php">
            <div class="logo">
                <img src="assets/logo/linkhub.png">
                <a href="search.php"><img src="assets/icone/search-outline.svg"></a>
            </div>
        </a>
        <form action="./search.php" method="get">
            <input type="text" class="search-bar" name="searchbar" placeholder="Entrez votre recherche ici..." required>
        </form>
    </div>
    <?php

    if ($_SESSION["validtoken"] === true) {

        connected();
    } else {

        header("Location: logout.php");


    }

    ?>
</section>
