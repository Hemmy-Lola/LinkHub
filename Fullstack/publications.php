<?php

    session_start();

    $pdo = new PDO("mysql:host=localhost:3306; dbname=linkhub", "root", "");
    $methode = filter_input(INPUT_SERVER, "REQUEST_METHOD");
    if(isset($_GET['id'])){

        $publication_id = $_GET['id'];


    } else {

        echo "Non";
    

    }


    function showPublication($publication_id, $pdo){    

        $request_publication_finder = $pdo->prepare("
            SELECT 
            publications.content, 
            publications.creation_date, 
            `group`.group_name, 
            users.first_name, 
            users.name, 
            publications.who_typed_id,
            publications.title 
            FROM publications
            LEFT JOIN `group` ON publications.who_typed = 'group' AND `group`.id = publications.who_typed_id
            LEFT JOIN `users` ON publications.who_typed = 'user' AND `users`.id = publications.who_typed_id
            WHERE publications.id = :publication_id
        
        ");

        $request_publication_finder->execute([

            ":publication_id" => $publication_id

        ]);

        $publication = $request_publication_finder->fetch(PDO::FETCH_ASSOC);
        $publisher_id = $publication['who_typed_id'];
        $publisher_name = $publication['name'];
        $publisher_first_name = $publication['first_name'];
        $publisher_group_name = $publication['group_name'];
        $publisher_creation_date = $publication['creation_date'];
        $publisher_content = $publication['content'];






        echo "<h1> Publication </h1>
        <h2>{$publication['title']}</h2>
        <img src='#'>
        ";
        

        if(empty($publisher_first_name) && empty($publisher_name)){

            echo "<h2><a href='./group_page.php?group_name={$publisher_group_name}&group_id={$publisher_id}'>{$publisher_group_name}</a></h2>";

        } else {

            echo "<h2><a href='./profil.php?id={$publisher_id}'>{$publisher_name} {$publisher_first_name}</a></h2>";
            
        }

        
        echo "<p>{$publisher_creation_date}</p>
        <p>{$publisher_content}</p>
        <p>Image</p>

        ";



    }

    if ($methode == "POST"){

        if(!empty($_POST['publish_commentary'])){
            if($_POST['publish_commentary']){
            // Récupération des données envoyées
            $id_de_l_utilisateur = $_SESSION['id'];
            $contenu = filter_input(INPUT_POST, "content");

            // Insertion du nouveau commentaire dans la base de données
            $sql = "INSERT INTO comments (user_id, publication_id, content) 
                    VALUES (:user_id, :publication_id, :content)"
                    ;
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':user_id', $id_de_l_utilisateur, PDO::PARAM_INT);
            $stmt->bindParam(':publication_id', $publication_id, PDO::PARAM_INT);
            $stmt->bindParam(':content', $contenu, PDO::PARAM_STR);

            $stmt->execute();
            }
            header("Refresh:0");
        }
    }

    
    function showComments($pdo, $publication_id){
        $request_publication_comments = $pdo->prepare("
        
            SELECT comments.publication_id,
            comments.content,
            comments.creation_date,
            users.id AS user_id,
            users.name AS user_name,
            users.first_name AS user_first_name
            FROM comments
            INNER JOIN users
            ON comments.user_id = users.id
            WHERE publication_id = :publication_id
            ORDER BY comments.creation_date DESC
        
        ");

        $request_publication_comments->execute([


            ":publication_id" => $publication_id

        ]);

        $showComments = $request_publication_comments->fetchAll(PDO::FETCH_ASSOC);

   
        foreach ($showComments as $row){

            $comment_content = $row['content'];
            $comment_date = $row['creation_date'];
            $comment_fullName = "{$row['user_name']} {$row['user_first_name']}";
     
            $comment_userId = $row['user_id'];

            echo "

                <a href='./profil.php?id={$comment_userId}'><h4>{$comment_fullName}</h4></a>
                <h5>{$comment_date}</h5>
                <p>{$comment_content}</p>
        
                
            ";

            

        }

    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/header.css">
    <link rel="stylesheet" href="./css/footer.css">
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/publications.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <title>Publication</title>
</head>
<body>

    <?php require_once './pages/header.php' ?>
    <section>
        <?php showPublication($publication_id, $pdo) ?>

    <h2>Commentaire</h2>
     <div class="comment-form">
        <form method="POST" action="">
            <textarea id="content" name="content" placeholder="Ajouter un commentaire..."></textarea>
            <button type="submit" name="publish_commentary" value="Publier"><i class="fas fa-comment"></i>Publier </button>
        </form>
    </div>

    <?php showComments($pdo, $publication_id) ?>
    </section>

    <?php require_once './pages/footer.php' ?>
</body>
</html>

