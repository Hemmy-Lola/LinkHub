<?php

session_start();
$pdo = new PDO("mysql:host=localhost:3306; dbname=linkhub", "root", "");
if (!empty($_GET['id'])){

    $request_to_id = $_GET['id'];


} else {

    echo "Can't get ID";
}

$methode = filter_input(INPUT_SERVER, "REQUEST_METHOD");

print_r($_POST);

if ($methode == "POST"){

    echo "La methode marche";
    if (!empty($_POST['add_friend'])){
        
        if(isset($_POST['add_friend'])){
            echo "<br>J'ai cliqué sur add friend";
            
            $add_friend_request = $pdo->prepare("
                INSERT INTO friend_list(user_id1, user_id2, status)
                VALUES (:own_id, :requested_id, :status)
            
            ");

            $add_friend_request->execute([
                ":own_id" => $_SESSION['id'],
                ":requested_id" => $request_to_id,
                ":status" => "Waiting"

            ]);


            $add_friend_request_notification = $pdo->prepare("
                INSERT INTO notifications (from_type, from_type_id, notification_type, notif_type_id)
                VALUES(:from_type, :own_id, :notif_type, :notif_type_id)
            ");

            $add_friend_request_notification->execute([

                ":from_type" => "user",
                ":own_id" => $_SESSION['id'],
                ":notif_type" => "friend_request_to",
                ":notif_type_id" => $request_to_id

            ]);

            header("Location: profil.php?id={$request_to_id}");
        } else {

            echo "<br>J'ai pas cliqué sur le add friend";

        }
    } else {

        echo "<br>La methode POST add_friend n'est pas crée ou appelée";

    }

    if (!empty($_POST['accept_friend_request'])){
        if(isset($_POST['accept_friend_request'])){

            echo "<br>J'ai cliqué sur le bouton Accepter en ami";
            
            $accept_friend_request = $pdo->prepare("
            
                UPDATE friend_list
                SET status = 'Friend'
                WHERE user_id1 = :requested_id AND user_id2 = :own_id AND status = :status

            ");

            $accept_friend_request->execute([
                ":requested_id" => $request_to_id,
                ":own_id" => $_SESSION['id'],
                ":status" => "Waiting" 


            ]);

        
            $mutual_friend_creation = $pdo->prepare("
            
                INSERT INTO friend_list(user_id1, user_id2, status)
                VALUES (:own_id, :requested_id, :status)

            ");

            $mutual_friend_creation->execute([
                ":own_id" => $_SESSION['id'],
                ":requested_id" => $request_to_id,
                ":status" => "Friend"


            ]);

            $delete_notification = $pdo->prepare("
                DELETE FROM notifications
                WHERE from_type = :from_type 
                AND from_type_id = :requester_id
                AND notification_type = :notif_type
                AND notif_type_id = :notif_type_id
            ");

            $delete_notification->execute([

                ":from_type" => "user",
                ":requester_id" => $request_to_id,
                ":notif_type" => "friend_request_to",
                ":notif_type_id" => $_SESSION['id']

            ]);

            header("Location: profil.php?id={$request_to_id}");

        } else {

            echo "<br>J'ai pas cliqué sur le bouton accepter l'ami";

        }
    } else {

        echo "<br>La methode POST accept_friend_request n'est pas crée ou appelée";
    }

    if (!empty($_POST['reject_friend_request'])){

        if(isset($_POST['reject_friend_request'])){

            $reject_friend_request = $pdo->prepare("

                DELETE FROM friend_list
                WHERE user_id1 = :requested_id AND user_id2 = :own_id AND status = :status
            
            ");

            $reject_friend_request->execute([
                ":requested_id" => $request_to_id,
                "own_id" => $_SESSION['id'],
                ":status" => "Waiting"
                
            ]);

            $delete_notification = $pdo->prepare("
            DELETE FROM notifications
            WHERE from_type = :from_type 
            AND from_type_id = :requester_id
            AND notification_type = :notif_type
            AND notif_type_id = :notif_type_id
            ");

            $delete_notification->execute([

                ":from_type" => "user",
                ":requester_id" => $request_to_id,
                ":notif_type" => "friend_request_to",
                ":notif_type_id" => $_SESSION['id']

            ]);

            
            header("Location: notifications.php");
        } else {
            echo "<br>Je n'ai pas cliqué sur le bouton rejeter l'ami";

        }


    } else {

        echo "<br>La methode POST reject_friend_request n'est pas crée ou appelée";
    }

    if(isset($_POST['cancel_friend_request'])){

        $delete_friend_request = $pdo->prepare("
        
            DELETE FROM friend_list
            WHERE user_id1 = :own_id AND user_id2 = :currentProfileId AND status = :status
        
        ");

        $delete_friend_request->execute([
        
            ":own_id" => $_SESSION['id'],
            ":currentProfileId" => $request_to_id,
            ":status" => "Waiting"
        
        ]);

        $url = "profil.php?id={$request_to_id}";
        header("Location: {$url}");

    } else {


        echo "<br>La methode POST cancel_friend_request n'est pas crée ou appelée";

    }

    if(isset($_POST['delete_friend'])){

        $delete_mutual_friend1 = $pdo->prepare("
        
            DELETE FROM friend_list
            WHERE user_id1 = :own_id 
            AND user_id2 = :request_to_id
            AND status = :status
        
        ");

        $delete_mutual_friend1->execute([
            ":own_id"=> $_SESSION['id'],
            ":request_to_id"=> $request_to_id,
            ":status"=> "Friend"

        ]);

        $delete_mutual_friend2 = $pdo->prepare("
        
            DELETE FROM friend_list
            WHERE user_id1 = :own_id 
            AND user_id2 = :request_to_id
            AND status = :status
        
        ");

        $delete_mutual_friend2->execute([
            ":own_id"=> $request_to_id,
            ":request_to_id"=> $_SESSION['id'],
            ":status"=> "Friend"

        ]);

        header("Location: profil.php?id={$request_to_id}");


    } else {

        echo "<br>La methode POST delete_friend n'est pas crée ou appelée";
    }



  

} else {


echo "La methode n'est pas bonne";

}

?>
