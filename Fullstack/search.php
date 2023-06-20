<?php

session_start();
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
    <title>Recherche</title>
    <link rel="stylesheet" href="searchbarstyle.css">
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/header.css">
    
   
</head>
<body>

    <?php include_once './pages/header.php' ?>
    <section>
    <div class="flexd">
        <h2>Recherche</h2>
        <form action="" method="GET">
        <input type="text" class="search-bar" name="searchbar" placeholder="Entrez votre recherche ici..." required>
        <button type="submit" id="submitbut">Rechercher</button>
        
        </form>

        <div id="searchresult">

            <?php 
                
            // Si la variable est en "GET" (Elle est passée en GET dans le if juste au dessus). 
            // Deuxieme partie du if, le searchbar passera en true apres le isset($_GET)
            // Troisieme partie du if, apres le submit le $searchbar ne sera plus vide
            if ($methode === "GET" && $searchbar !== false && $searchbar !== ""){

                // PREPARATION REQUETES SQL

                // Requetes par PERSONNES
                $requete1 = $pdo->prepare("
                SELECT name, first_name, id 
                FROM users 
                WHERE name LIKE CONCAT('%', :searchbar, '%') OR first_name LIKE CONCAT('%', :searchbar, '%')
                
                ");

                // Requetes pour les GROUPES
                $requete2 = $pdo->prepare("
                SELECT group_name, status, id
                FROM `group`
                WHERE group_name LIKE CONCAT('%', :searchbar, '%');
                ");  

                // Requete pour les PAGES 
                $requete3 = $pdo->prepare("
                SELECT page_name
                FROM page
                WHERE page_name LIKE CONCAT('%', :searchbar, '%');
                ");

                // EXECUTIONS DES REQUETES 

                // Execution requete1
                $requete1->execute([ 
                    ":searchbar" => $searchbar,
                ]);

                // Execution requete2
                $requete2->execute([
                    ":searchbar" => $searchbar,
                ]);

                // Execution requete3
                $requete3->execute([
                    ":searchbar" => $searchbar,
                ]);
                

                // Si les requete SQL renvoie plus de 0 ligne
                if ($requete1->rowCount() > 0){

                    $line_counter = $requete1->rowCount();


                    //Ici on echo ul pour demarrer une liste 

                    echo "<h3>Personnes : </h3>";
                    
                    echo "Nombre de personnne(s) trouvé(s) : " . $line_counter;

                    echo "<ul id='suggestions-list'>";
                    // On récupère tous les résultats sous forme de tableau associatif
                    $resultat1 = $requete1->fetchAll(PDO::FETCH_ASSOC);


               

                    // On parcourt chaque ligne de résultat avec foreach
                    foreach ($resultat1 as $nom) {
                        // On affiche les colonnes 'name' et 'first_name'
                        echo 
                        "<li>{$nom['name']} 
                            {$nom['first_name']} 
                            <a href='./profil.php?name={$nom['first_name']}&first_name={$nom['name']}&id={$nom['id']}'>
                                <button name='add_btn'>Voir</button>
                            </a>
                        </li>";

                        // Ici le counter sert a ajouter 1 a chaque ligne de name pour pouvoir selectionner les bouttons
                       
                    }
                    //Ici la fin du ul pour la liste, et on peut voir juste au dessus un <li> ... </li>
                    echo "</ul>";

                }
                
                if ($requete2->rowCount() > 0){

                    $line_counter = $requete2->rowCount();


                    //Ici on echo ul pour demarrer une liste 

                    echo "<h3>Groupes : </h3>";

                    echo "Nombre de groupe(s) trouvé(s) : " . $line_counter;


                    echo "<ul>";
                    // On récupère tous les résultats sous forme de tableau associatif
                    $resultat2 = $requete2->fetchAll(PDO::FETCH_ASSOC);

                


                    // On parcourt chaque ligne de résultat avec foreach
                    foreach ($resultat2 as $group) {
                        // On affiche les colonnes 'group_name'
                        echo "<li>{$group['group_name']} ({$group['status']})
                        <a href='group_page.php?group_name={$group['group_name']}&group_id={$group['id']}'>
                            <button name='join_btn'>Rejoindre</button>
                        </a>
                        </li>";
                        
                        
                    }
                    //Ici la fin du ul pour la liste, et on peut voir juste au dessus un <li> ... </li>
                    echo "</ul>";
                } 


                if ($requete3->rowCount() > 0){

                    $line_counter = $requete3->rowCount();


                    //Ici on echo ul pour demarrer une liste 

                    echo "<h3>Page : </h3>";

                    echo "Nombre de page(s) trouvé(s) : " . $line_counter;


                    echo "<ul>";
                    // On récupère tous les résultats sous forme de tableau associatif
                    $resultat3 = $requete3->fetchAll(PDO::FETCH_ASSOC);

               


                    // On parcourt chaque ligne de résultat avec foreach
                    foreach ($resultat3 as $page) {
                        // On affiche les colonnes 'page_name' 
                        echo "<li>" . $page['page_name'] . ' ' . "<button name='page_btn" . "'>Voir</button>" . "</li>" . "</li>";
                 

                        
                    }
                    //Ici la fin du ul pour la liste, et on peut voir juste au dessus un <li> ... </li>
                    echo "</ul>";
                } 

                if ($requete1->rowCount() == 0 && $requete2->rowCount() == 0 && $requete3->rowCount() == 0){
                    echo "Aucun résultat n'a été trouvé";
                }

                 
            } 


               
            ?>

        </div>
    </div>
    </section>
</body>
</html>


