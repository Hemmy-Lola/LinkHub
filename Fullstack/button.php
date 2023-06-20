<?php
require_once './include/session_start.php';
require_once './include/verify_connection.php';

$pdo = new PDO("mysql:host=localhost:3306;dbname=linkhub", "root", "");
$methode = filter_input(INPUT_SERVER, "REQUEST_METHOD");

if (isset($_GET['group_id'])) {
    $id_groupe = $_GET['group_id'];
    
}
if (isset($_GET['group_name'])){

    $nom_groupe = $_GET['group_name'];
}

if (isset($_GET['user_request_id'])){


    $user_request_id = $_GET['user_request_id'];


}

// function public_button()
// {
// Version publique
if ($methode == "POST") {
    if (isset($_POST['join_group'])) {
        $user_id = $_SESSION['id'];

        // Récupérer le nom de l'utilisateur via la base de données
        $get_name = $pdo->prepare("
                    SELECT `name`
                    FROM `users`
                    WHERE `id` = :user_id
                ");
        $get_name->execute([
            ":user_id" => $user_id,
        ]);
        // Récupère les résultats du SQL exécutée avec $get_name
        $result = $get_name->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $user_name = $result['name'];
            // Ajout des informations du nouvel recrue
            $add_member = $pdo->prepare("
                        INSERT INTO `members_group` (`name`, `role`, `user_id`, group_id)
                        VALUES (:user_name, 'Membre', :user_id, :group_id)
                    ");

            $add_member->execute([
                ":user_id" => $user_id,
                ":user_name" => $user_name,
                ":group_id" => $id_groupe,
            ]);

         

            header("Location: ./group_page.php?group_name=" . urlencode($nom_groupe) . "&group_id=" . urlencode($id_groupe));
            exit;
        }
    }
    if (isset($_POST['request_group'])){

        $user_id = $_SESSION['id'];

        // Récupérer le nom de l'utilisateur via la base de données
        $get_name = $pdo->prepare("
                    SELECT `name`
                    FROM `users`
                    WHERE `id` = :user_id
                ");
        $get_name->execute([
            ":user_id" => $user_id,
        ]);
        // Récupère les résultats du SQL exécutée avec $get_name
        $result = $get_name->fetch(PDO::FETCH_ASSOC);

        $user_name = $result['name'];

        $create_request = $pdo->prepare("
            INSERT INTO members_group (name, role, user_id, group_id)
            VALUES (:name, 'En attente', :user_id, :group_id)
        
        ");

        $create_request->execute([
            ":name" => $user_name,
            ":user_id" => $user_id,
            ":group_id" => $id_groupe,

        ]);
        
        $send_notification = $pdo->prepare("
            INSERT INTO notifications (from_type, from_type_id, notification_type, notif_type_id)
            VALUES (:from_type, :from_type_id, :notif_type, :notif_type_id)        
        
        ");

        $send_notification->execute([

            ":from_type" => "user",
            ":from_type_id" => $_SESSION['id'],
            ":notif_type" => "group_req",
            ":notif_type_id" => $id_groupe,

        ]);

        header("Location: ./group_page.php?group_name=" . urlencode($nom_groupe) . "&group_id=" . urlencode($id_groupe));
        exit();
    }

    if (isset($_POST['accept_group'])){

        $user_id = $user_request_id;

        $accept_invitation = $pdo->prepare("
        
            UPDATE members_group
            SET role = 'Membre'
            WHERE role = 'En attente' AND user_id = :user_id and group_id = :group_id
        
        ");

        $accept_invitation->execute([
            ":user_id" => $user_id,
            ":group_id" => $id_groupe

        ]);

        $remove_notification = $pdo->prepare("
        DELETE FROM notifications
        WHERE from_type = :from_type AND from_type_id = :from_type_id AND notification_type = :notif_type AND notif_type_id = :notif_type_id        
    
        ");

        $remove_notification->execute([

            ":from_type" => "user",
            ":from_type_id" => $user_id,
            ":notif_type" => "group_req",
            ":notif_type_id" => $id_groupe,

        ]);

        header("Location: notifications.php");
        exit();


    }

    if(isset($_POST['abort_group'])){

        $user_id = $user_request_id;

        $delete_invitation = $pdo->prepare("
            DELETE FROM members_group
            WHERE role = 'En attente' AND user_id = :user_id and group_id = :group_id
        
        ");    

        $delete_invitation->execute([
            ":user_id" => $user_id,
            ":group_id" => $id_groupe

        ]);

        $remove_notification = $pdo->prepare("
        DELETE FROM notifications
        WHERE from_type = :from_type AND from_type_id = :from_type_id AND notification_type = :notif_type AND notif_type_id = :notif_type_id        
    
        ");

        $remove_notification->execute([

            ":from_type" => "user",
            ":from_type_id" => $user_id,
            ":notif_type" => "group_req",
            ":notif_type_id" => $id_groupe,

        ]);
       
        header("Location: notifications.php");
        exit();
    }

    if(isset($_POST['cancel_group'])){

        $user_id = $_SESSION['id'];
        
        $delete_invitation = $pdo->prepare("
        DELETE FROM members_group
        WHERE role = 'En attente' AND user_id = :user_id and group_id = :group_id
    
        
        ");    

        $delete_invitation->execute([
            ":user_id" => $user_id,
            ":group_id" => $id_groupe

        ]);

        $remove_notification = $pdo->prepare("
        DELETE FROM notifications
        WHERE from_type = :from_type AND from_type_id = :from_type_id AND notification_type = :notif_type AND notif_type_id = :notif_type_id        
    
        ");

        $remove_notification->execute([

            ":from_type" => "user",
            ":from_type_id" => $user_id,
            ":notif_type" => "group_req",
            ":notif_type_id" => $id_groupe,

        ]);

        
        header("Location: ./group_page.php?group_name={$nom_groupe}&group_id={$id_groupe}");
        exit();


    }

}
// }
?>