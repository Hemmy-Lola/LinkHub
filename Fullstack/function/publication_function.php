<?php
$pdo = new PDO("mysql:host=localhost:3306; dbname=linkhub", "root", "");
session_start();

function user_publish($pdo){

    // Vérification des données envoyées
    //if (!isset($_POST['content']) || empty($_POST['content'])) {
    //die("Le contenu de la publication est obligatoire");
    //S}

    
    $contenu = filter_input(INPUT_POST, "content");
    $titre = filter_input(INPUT_POST, "title");
    // Insertion de la nouvelle publication dans la base de données
    $sql = "INSERT INTO  
    publications (content, who_typed, who_typed_id, title)
    VALUES (:content, :who_typed, :who_typed_id, :title)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        
        ":title" => $titre,
        ':content' => $contenu,
        ':who_typed' => "user",
        ":who_typed_id" => $_SESSION['id'],

    ]);

    header("Location: dashboard.php");
    // Redirection vers la page d'accueil

}

function showAllPublication($pdo){

    $request_get_publication = $pdo->prepare("
        SELECT publications.title AS pub_title, 
        publications.content AS pub_content, 
        publications.who_typed_id AS who_typed_id, 
        users.name AS user_name, 
        users.first_name AS user_first_name,
        `group`.group_name AS group_name,
        publications.creation_date AS pub_creation_date,
        publications.who_typed AS who_typed,
        publications.id AS publication_id
        FROM publications
        LEFT JOIN `group` ON publications.who_typed = 'group' AND `group`.id = publications.who_typed_id
        LEFT JOIN `users` ON publications.who_typed = 'user' AND `users`.id = publications.who_typed_id
        ORDER BY publications.creation_date DESC
        
    ");

    $request_get_publication->execute();
    
    $showPublications = $request_get_publication->fetchAll(PDO::FETCH_ASSOC); 

    foreach($showPublications as $row){
        $pub_title = $row['pub_title'];
        $pub_content = $row['pub_content'];
        $who_typed_id = $row['who_typed_id'];
        $user_name = $row['user_name'];
        $user_first_name = $row['user_first_name'];
        $group_name = $row['group_name'];
        $pub_creation_date = $row['pub_creation_date'];
        $who_typed = $row['who_typed'];
        $publication_id = $row['publication_id'];

        echo "
    
        <div class='card'>
            <a href='./publications.php?id={$publication_id}'>
                <div class='card-body'>
                    <h5 class='card-title'>{$pub_title}</h5>
                    <p>{$user_name} {$user_first_name} {$group_name}</p>
                    <p>{$pub_creation_date}</p>
                    <p class='card-text'>{$pub_content}</p>
                    <form method='POST' action='./publications.php?id={$publication_id}'>
                        <button type='submit' class='btn btn-primary'>Commenter</button>
                    </form>
                </div>
            </a>
        </div>
        <br>
        
        ";

    }

}


function showProfilPublication($pdo, $getCurrentProfileId){

    $request_get_publication = $pdo->prepare("
        SELECT publications.title AS pub_title, 
        publications.content AS pub_content, 
        publications.who_typed_id AS who_typed_id, 
        users.name AS user_name, 
        users.first_name AS user_first_name,
        `group`.group_name AS group_name,
        publications.creation_date AS pub_creation_date,
        publications.who_typed AS who_typed,
        publications.id AS publication_id
        FROM publications
        LEFT JOIN `group` ON publications.who_typed = 'group' AND `group`.id = publications.who_typed_id
        LEFT JOIN `users` ON publications.who_typed = 'user' AND `users`.id = publications.who_typed_id
        WHERE who_typed_id = :currentProfilId
        ORDER BY publications.creation_date DESC
        
    ");

    $request_get_publication->execute([

        ":currentProfilId" => $getCurrentProfileId,


    ]);
    
    $showPublications = $request_get_publication->fetchAll(PDO::FETCH_ASSOC); 

    if(!empty($showPublications)){
        
        foreach($showPublications as $row){
            $pub_title = $row['pub_title'];
            $pub_content = $row['pub_content'];
            $who_typed_id = $row['who_typed_id'];
            $user_name = $row['user_name'];
            $user_first_name = $row['user_first_name'];
            $group_name = $row['group_name'];
            $pub_creation_date = $row['pub_creation_date'];
            $who_typed = $row['who_typed'];
            $publication_id = $row['publication_id'];

            echo "
        
            <div class='card'>
                <a href='./publications.php?id={$publication_id}'>
                    <div class='card-body'>
                        <h5 class='card-title'>{$pub_title}</h5>
                        <p>{$user_name} {$user_first_name} {$group_name}</p>
                        <p>{$pub_creation_date}</p>
                        <p class='card-text'>{$pub_content}</p>
                        <form method='POST' action='./publications.php?id={$publication_id}'>
                            <button type='submit' class='btn btn-primary'>Commenter</button>
                        </form>
                    </div>
                </a>
            </div>
            <br>
            
            ";

        }
    } else {

        echo "<h4>Vous n'avez pas de publication, il serait temps de publier...?</h4>";

    }
}


if(isset($_POST['publish_user'])){

    user_publish($pdo);
    exit();

}


?>