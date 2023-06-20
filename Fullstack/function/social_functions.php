<?php
    require_once './include/session_start.php';

    $pdo = new PDO("mysql:host=localhost:3306; dbname=linkhub", "root", "");

    $requete = $pdo->prepare("

        SELECT * FROM `group`
        INNER JOIN members_group
        ON `group`.id = members_group.group_id
        INNER JOIN users
        ON users.id = members_group.user_id
        WHERE users.id = :user_id AND (members_group.role = 'Admin' OR members_group.role = 'Membre')

    ");

    $requete->execute([

        ":user_id" => $_SESSION['id']

    ]);

    $group_data = $requete->fetchAll(PDO::FETCH_ASSOC);

    $requete2 = $pdo->prepare("

        SELECT * FROM `group`
        WHERE status = 'public'
        
    ");

    $requete2->execute();

    $publicGroup_data = $requete2->fetchAll(PDO::FETCH_ASSOC);


    function check_isGroupListEmpty(){
        global $group_data;

        if (isset($group_data) && !empty($group_data)){
            return false;
            
        } else {
            return true;
        }
        
    }

    function isGroupListEmpty(){
        global $group_data;

        if(check_isGroupListEmpty()){

            echo "<h4> Vous n'avez pas de groupe...</h4>";

        } else { 

            foreach($group_data as $row) {
                $nom_groupe = $row['group_name'];
                $id_groupe = $row['group_id'];
                
                echo '
                
                <div class="profil">
                <a href="./group_page.php?group_name=' . $nom_groupe . '&group_id=' . $id_groupe . '">
                    <div class="profile_picture"></div>
                    <h4>', $nom_groupe , '</h4>
                </a>
                </div> 
                
                ';
                
            };
        }
    }

    function check_isPublicGroupListEmpty(){
        global $publicGroup_data;

        if (isset($publicGroup_data) && !empty($publicGroup_data)){
            return false;
            
        } else {
            return true;
        }
        
    }

    function isPublicGroupListEmpty(){
        global $publicGroup_data;

        if(check_isPublicGroupListEmpty()){

            echo "<h1>Il n'y a pas de groupe public disponible ...</h1>";

        } else { 

            foreach($publicGroup_data as $row) {
                $nom_Publicgroupe = $row['group_name'];
                $id_Publicgroupe = $row['id'];
                
                echo '<a href="./group_page.php?group_name=' . $nom_Publicgroupe . '&group_id=' . $id_Publicgroupe . '">
                        <div class="profil">
                            <div class="profile_picture"></div>
                            <h4>', $nom_Publicgroupe , '</h4>
                        </div> 
                    </a>';
                
            };
        }
    }

    function showFriendList($pdo){
        $requete3251 = $pdo->prepare("

        SELECT *
        FROM friend_list
        INNER JOIN users
        ON friend_list.user_id2 = users.id
        WHERE user_id1 = :id AND friend_list.status = 'friend'
        
        ");
            
    
        $requete3251->bindParam(':id', $_SESSION['id']);
        $requete3251->execute();

        $friendListData = $requete3251->fetchAll(PDO::FETCH_ASSOC);

        if(!empty($friendListData)){

        
            foreach($friendListData as $row){

                echo "<a href=./profil.php?id={$row['id']}>
                            <div class='profil'>
                                <div class='profile_picture'></div>
                                <h4>{$row['first_name']}</h4>
                            </div> 
                        </a>

                ";

            } 
        } else {

            echo "<p>Vous n'avez pas d'ami.... Il serait temps d'en ajouter !</p>";
        }
    }











?>