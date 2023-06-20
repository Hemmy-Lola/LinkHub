<?php

$pdo = new PDO("mysql:host=localhost:3306; dbname=linkhub", "root", "");

$methode = $searchbar = false;
// Input bar de recherche = null


// Si la methode de l'input est en 'GET' 
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["searchbar"])) {
    $methode = "GET"; // Ici on passe la variable en "GET"
    $searchbar = filter_input(INPUT_GET, "searchbar"); //On récup la valeur de l'input apres avoir submit
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recherche par personne</title>
    <link rel="stylesheet" href="searchbarstyle.css">
    <script src="script.js"></script>
</head>
<body>
    
    <div class="flexd">
        <h2>Recherche par personne :</h2>
        <form action="" method="get">
        <input type="text" id="searchinput" name="searchbar" placeholder="Entrez votre recherche ici..." required>
        <button type="submit" id="submitbut">Rechercher</button>
        </form>
        <a href="group_search.php"><button id="group_direction">Groupe</button></a>
        <a href="person_search.php"><button id="person_direction">Personne</button></a>
        <a href="page_search.php"><button id="person_direction">Page</button></a>

        <div id="searchresult">

            <?php // Si la variable est en "GET" (Elle est passée en GET dans le if juste au dessus). 
            // Deuxieme partie du if, le searchbar passera en true apres le isset($_GET)
            // Troisieme partie du if, apres le submit le $searchbar ne sera plus vide
            if ($methode === "GET" && $searchbar !== false && $searchbar !== "") {
                $requete = $pdo->prepare("
                    SELECT name, first_name
                    FROM users 
                    WHERE name LIKE CONCAT('%', :searchbar, '%')
                    
                ");

                $requete->execute([ 
                    ":searchbar" => $searchbar,
                ]);

                //Compte le nombre de ligne
                $line_counter = $requete->rowCount();

                // Si la requete SQL renvoie plus de 0 ligne
                if ($requete->rowCount() > 0){


                    echo "Nombre de résultat(s) trouvé(s) : " . $line_counter;

                    //Ici on echo ul pour demarrer une liste 
                    echo "<ul id='suggestions-list'>";
                    // On récupère tous les résultats sous forme de tableau associatif
                    $resultats = $requete->fetchAll(PDO::FETCH_ASSOC);

                    // On parcourt chaque ligne de résultat avec foreach
                    foreach ($resultats as $nom) {
                        // On affiche les colonnes 'name' et 'first_name'
                        echo "<li>" . $nom['name'] . ' ' . $nom['first_name'] . "<button class='delete-btn'>Ajouter</button>" . "</li>";
                        
                    }
                    //Ici la fin du ul pour la liste, et on peut voir juste au dessus un <li> ... </li>
                    echo "</ul>";
                }    
                else {
                    echo "Aucun résultat n'a été trouvé";   
                }
                }
            ?>

        </div>
    </div>

</body>
</html>
