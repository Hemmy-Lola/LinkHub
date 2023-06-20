<?php
session_start();

$pdo = new PDO("mysql:host=localhost:3306;dbname=linkhub", "root", "");
$methode = filter_input(INPUT_SERVER, "REQUEST_METHOD");

$name_request = $pdo->prepare("SELECT first_name FROM users WHERE id = :pseudo"); 
$name_request->bindParam(':pseudo', $_SESSION['id']);
$name_request->execute();

$resultat = $name_request->fetch(PDO::FETCH_ASSOC);

if(isset($_GET['session_id']) && isset($_GET['user_id'])){

    $session_id = $_GET['session_id'];
    $user_id = $_GET['user_id'];


} else {

    echo "WHy";
    exit();
}


$whoAreWeTalkingTo = $pdo->prepare("

    SELECT * FROM users
    WHERE id = :talking_to_id

");

$whoAreWeTalkingTo->execute([

    ":talking_to_id" => $user_id,

]);

$dataUser = $whoAreWeTalkingTo->fetch(PDO::FETCH_ASSOC);



//envoyé le msg
if (isset($_POST["submitmsg"])) {
    if (empty($_POST['usermsg'])){


    } else {
    $content = filter_input(INPUT_POST, "usermsg");
    
        $requete_insert_msg = $pdo->prepare("
            INSERT INTO messages (expediteur_name, content, expediteur_id, session_id) 
            VALUES(:name, :content, :expediteur_id, :session_id)
        "); 
        $requete_insert_msg->execute([

            ":name" => $resultat['first_name'],
            ":content" => $content,
            ":expediteur_id" => $_SESSION['id'],
            ":session_id" => $session_id,

        ]);    
        $delai=1; 
        $url="message.php?session_id={$session_id}&user_id={$user_id}";
        header("Location: {$url}");
       
        exit(); // Terminer le script pour éviter l'affichage supplémentaire des messages
    }
}


function get_message_data($pdo, $session_id){
    global $resultats;

    $requete_afficher = $pdo ->prepare("

        SELECT * FROM messages
        WHERE session_id = :session_id 

    ");
    
    // Exécution de la requête
    $requete_afficher -> execute([

        ":session_id" => $session_id

    ]); 
    
    // Récupération des résultats
    $resultats = $requete_afficher->fetchAll(PDO::FETCH_ASSOC);

}


function affichage_message($pdo, $session_id, $user_id, $resultats){

   
    // Affichage des résultats
    foreach ($resultats as $row) {
            
        if(empty($row['modify_or_not'])){

            $modify = "";

        } else {

            $modify = $row['modify_or_not'];


        }

        echo "
        <div class='single_message'>
        ";
        if($user_id != $_SESSION['id'] && $row['expediteur_id'] == $_SESSION['id']){

            echo "
            <form action='' method='POST'>
                <button type='submit' name='modify_message' value='{$row['content']}'>Modifier</button>
                <button type='submit' name='delete_message' value='{$row['id']}'>Effacer</button>
                <input type='hidden' name='id' value='{$row['id']}' /> 

                
            </form>
            ";
        
        } 
   
        echo "
        <p>{$row['date_envoi']} {$row['expediteur_name']} : {$row['content']} {$modify}<p> <br>
        </div>
        ";
    }
    



}

if(isset($_POST['delete_message'])){

    $request_delete_msg = $pdo->prepare("
        DELETE FROM messages
        WHERE id = :id
    
    ");

    $request_delete_msg->execute([

        ":id" => $_POST['delete_message']
    
    ]);

    $delai=1; 
    $url="message.php?session_id={$session_id}&user_id={$user_id}";
    header("Location: {$url}");
    

}

if(isset($_POST['cancel_modify_msg'])){

    $_POST['modify_message'] = null;


}


if(isset($_POST['modify_new_msg'])){    
    
    if(empty($_POST['new_message'])){
        echo "le message est vide";
        
    } else {

        $new_message = filter_input(INPUT_POST, "new_message");
        $message_id = $_POST['modify_new_msg'];

        $request_change_msg = $pdo->prepare("

            UPDATE messages
            SET content = :new_msg, modify_or_not = :modify_or_not
            WHERE id = :msg_id

        "); 

        $request_change_msg->execute([
            ":new_msg" => $new_message,
            ":msg_id" => $message_id,
            ":modify_or_not" => "(modifié)",


        ]);

    }

}


get_message_data($pdo, $session_id);





?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta charset="UTF-8">
        <title>linkhub</title>
        <link rel="stylesheet" href="./css/style.css">
        <link rel="stylesheet" href="./css/header.css">
        <link rel="stylesheet" href="./css/message_hub.css">
        <link rel="stylesheet" href="./css/footer.css">
    </head>
    <body>
        
        <?php require_once './pages/header.php' ?>
        
        <section>
            <a href="./message_hub.php">Retour</a>
            <h1 class="text-align"><?= $dataUser['first_name'] ?></h1>    
            <div id="wrapper">
                <div id="chatbox">
                <?php 
                
                affichage_message($pdo, $session_id, $user_id, $resultats);

                ?>

               
                <?php 
                
                if(!empty($_POST['modify_message'])) {
                    
                    $current_modify_message = $_POST['modify_message'];
                    $test = $_POST['id'];

                    echo "
                    <form action='' method='POST'>
                        <input name='new_message' type='text' value='{$current_modify_message}' />
                        <button type='submit' name='modify_new_msg' value='{$_POST['id']}'>Modifier votre message</button>
                        <button type='submit' name='cancel_modify_msg'>Annuler</button>
                    </form>
                    ";

                }else {   
                    
                    echo '<form name="message" action="" method="post">
                        <input name="usermsg" type="text"  />
                        <button name="submitmsg" type="submit">Send</button>
                    </form>';
                }
                ?>
            </div>
            
    
            <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
            <script type="text/javascript" src="message.js"></script>
        </section>

    </body>
</html>