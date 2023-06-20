<?php
    session_start();

    $pdo = new PDO("mysql:host=localhost:3306; dbname=linkhub", "root", "");

    $verify_own_connection = $pdo->prepare("

        SELECT * FROM message_session
        WHERE type_id = :own_id OR type_id2 = :own_id

    
    ");

    $verify_own_connection->execute([

        "own_id" => $_SESSION['id']

    ]);
    
    $own_connection = $verify_own_connection->fetch(PDO::FETCH_ASSOC);

    // J'ai besoin de changer la préparation SQL en personne ami à qui je peux message
    $request_get_all_friend_user = $pdo->prepare("

        SELECT * FROM users
        WHERE NOT users.id = :own_id
        
    ");

    $request_get_all_friend_user->execute([

        ":own_id" => $_SESSION['id']

    ]);

    $users_data = $request_get_all_friend_user->fetchAll(PDO::FETCH_ASSOC);

   
    function show_existing_session_users($pdo){

        $verify_own_connection = $pdo->prepare("

        SELECT type_id FROM message_session
        WHERE (type_id = :own_id OR type_id2 = :own_id) 

    
        ");

        $verify_own_connection->execute([

            "own_id" => $_SESSION['id']

        ]);

        $own_connection = $verify_own_connection->fetch(PDO::FETCH_ASSOC);

        if(!empty($own_connection)){
            
            if ($own_connection['type_id'] == $_SESSION['id']){

                $request_get_existing_session = $pdo->prepare("
            
                    SELECT message_session.id AS message_session_id, message_session.type_id, message_session.type_id2, users.first_name, users.name, users.id AS users_id
                    FROM message_session
                    INNER JOIN users
                    ON message_session.type_id = users.id
                    WHERE type_discussion = :type_discussion AND (type_id = :own_id OR type_id2 = :own_id)
                        
                
                ");

                $request_get_existing_session->execute([

                    ":type_discussion" => "private",
                    ":own_id" => $_SESSION['id'],
                    
                ]);

            } else {

                $request_get_existing_session = $pdo->prepare("
            
                SELECT message_session.id AS message_session_id, message_session.type_id, message_session.type_id2, users.first_name, users.name, users.id AS users_id
                FROM message_session
                INNER JOIN users
                ON message_session.type_id2 = users.id
                WHERE type_discussion = :type_discussion AND (type_id = :own_id OR type_id2 = :own_id)
                
                
                ");

                $request_get_existing_session->execute([

                    ":type_discussion" => "private",
                    ":own_id" => $_SESSION['id'],
                    
                ]);

            }
        

            $discussion_data = $request_get_existing_session->fetchAll(PDO::FETCH_ASSOC);
        
                if(!empty($discussion_data)){
                    foreach($discussion_data as $row){
                        
                        if($row['type_id'] == $_SESSION['id']) {
                            echo "
                            <div class='conversation'>
                            <a href='./message.php?session_id={$row['message_session_id']}&user_id={$row['type_id2']}'>
                            <h4>{$row['name']} {$row['first_name']}</h4>
                            {$row['name']}
                            </a>
                            <form action='' method='POST'>
                            <button type='submit' name='delete_conversation' value='{$row['message_session_id']}'>Supprimer la conversation</button>
                            </form>
                            </div>
                            
                            ";
                        } else {

                            echo "
                            <div class='conversation'>
                            <a href='./message.php?session_id={$row['message_session_id']}&user_id={$row['type_id']}'>
                            <h4>{$row['name']} {$row['first_name']}</h4>
                            </a>
                            <form action='' method='POST'>
                            <button type='submit' name='delete_conversation' value='{$row['message_session_id']}'>Supprimer la conversation</button>
                            </form>
                            </div>
                            
                            ";

                        }

                    }
                 
                } else {

                    echo "Vous n'avez pas de discussion";

                }


            }        
            if(isset($_POST['delete_conversation'])){

                $request_delete_conv = $pdo->prepare("
                    DELETE FROM message_session
                    WHERE id = :message_session
                
                ");

                $request_delete_conv->execute([

                    ":message_session" => $_POST['delete_conversation']

                ]);
            
                    $url="message_hub.php";
                    header("Location: {$url}");
                
            } 
        


    }
 
    function create_discussion_with_user($pdo, $own_connection){

        if(!empty($_POST['create_discussion_with_user'])){

            if(isset($_POST['create_discussion_with_user'])){

                $request_create_discussion = $pdo->prepare("

                    INSERT INTO message_session (type_discussion, type_id, type_id2)
                    VALUES (:type_discussion, :own_id, :requested_id)

                ");

                $request_create_discussion->execute([

                    ":type_discussion"=> "private",
                    ":own_id"=> $_SESSION['id'],
                    ":requested_id"=> $_POST['create_discussion_with_user']

                ]);
        

                header("Refresh:0");
                exit();

            } else {

                echo "C'est pas bon {$_POST['create_discussion_with_user']}";
                
                
            }
        }
  
        
    }
    
    if(isset($_POST['create_discussion_with_user'])){

        create_discussion_with_user($pdo, $own_connection);
       
    }

    function create_discussion($users_data, $pdo){
        if (!empty($users_data)){

            foreach($users_data as $row){
            
                echo "
                <br>
                <form action='./message_hub.php' method='POST'>
                <label>{$row['name']} {$row['first_name']} {$row['id']}</label>
               
                <input type='submit' name='create_discussion_with_user' value='Parlez avec lui !' />
                <input type='hidden' name='create_discussion_with_user' value='{$row['id']}' />
                
                </form>
                
                
                ";


            }

        } else {

            echo "Il n'y a personne !";


        }

    }

    // function show_add_list_discussion($users_data, $pdo){
    //     if (!empty($_POST['start_discussion'])){
    //         if(isset($_POST['start_discussion'])){
    //             search_bar_user($pdo);
    //         } else {
    //             echo "j'ai pas pu afficher la liste";

    //         }
        
    //     }
    // }

    function search_bar_user($pdo){


        echo "
        
        <h1>Discutez avec quelqu'un !</h1>
        <form action='' method='GET'>
            <input type='text' class='search-bar' name='searchbar' placeholder='Entrez votre recherche ici...'>
            <button type='submit' id='submitbut'>Rechercher</button>
        </form>
        ";
        
        $methode = filter_input(INPUT_SERVER, "REQUEST_METHOD");
        $searchbar = null;
        // Input bar de recherche = null
        $searchbar = filter_input(INPUT_GET, "searchbar"); //On récup la valeur de l'input apres avoir submit

        if ($methode == "GET" && $searchbar !== null && $searchbar !== ""){
        
            $requete1 = $pdo->prepare("
                SELECT name, first_name, id 
                FROM users 
                WHERE (name LIKE CONCAT('%', :searchbar, '%') OR first_name LIKE CONCAT('%', :searchbar, '%'))
                AND NOT id = :own_id
                
                ");

            $requete1->execute([ 
                ":searchbar" => $searchbar,
                ":own_id" => $_SESSION['id'],
            ]);

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
                    "<li> 
                        <form action='./message_hub.php' method='POST'>
                        <label>{$nom['name']} {$nom['first_name']}</label>
                        <button type='submit' name='create_discussion_with_user' value='{$nom['id']}'>Message</message> 
  
                        
                        </form>
                    </li>";

                    // Ici le counter sert a ajouter 1 a chaque ligne de name pour pouvoir selectionner les bouttons
                
                }
                //Ici la fin du ul pour la liste, et on peut voir juste au dessus un <li> ... </li>
                echo "</ul>";
                
    
            }
            if ($requete1->rowCount() == 0){
            echo "Aucun résultat n'a été trouvé...";
            }
        }
  

    }   
    
   


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/message_hub.css">
    <link rel="stylesheet" href="./css/header.css">
    <link rel="stylesheet" href="./css/footer.css">
    <title>Messagerie</title>
</head>
<body>


    <?php require_once './pages/header.php' ?>

    <section>
        
        <div class="messaging">
            <div class="title">
                <div class="search_bar_chat">
                    <?php 
                        search_bar_user($pdo);
                    ?>
                <div>
            </div>

            <div class="chat_box">
                <?php show_existing_session_users($pdo) ?>
            </div>

         
        </div>
        
    </section>
    <?php include_once './pages/footer.php' ?>
</body>
</html>