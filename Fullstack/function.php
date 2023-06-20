<?php

session_start();
$pdo = new PDO("mysql:host=localhost:3306; dbname=linkhub", "root", "");

$findOwnUser = $pdo->prepare("
    
        SELECT * FROM users
        WHERE mail = :login
    
    ");

$findOwnUser->execute([

    ":login" => $_SESSION["login"],

]);

$utilisateur = $findOwnUser->fetch(PDO::FETCH_ASSOC);

$_SESSION['id'] = $utilisateur['id'];

$methode = filter_input (INPUT_SERVER, "REQUEST_METHOD");
function isBlacklisted($event_name) {
    $blacklist = array(
        'connard',
        'pute',
        'salope',
        'bitch',
        'connasse',
        'pd',
        'nigga'
    );

    $str = strtolower($event_name);

    foreach ($blacklist as $word) {
        if (stripos($str, $word) !==false) {
            return true;
        }
    }
    return false;
}
// Social function
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

function showButtons($pdo, $userinfo, $friend_or_not, $if_added, $getCurrentProfileId){

  if (isset($_SESSION['id']) and $userinfo['id'] == $_SESSION['id']) {
            
    echo 
    "<a href='./edition.php'>Editer mon profil</a>
    <a href='./delete_account.php'>Suppression du compte</a>
    <a href='./dashboard.php'>Retour</a>
    <a href='./friend_list.php?name={$userinfo['first_name']}&first_name={$userinfo['name']}&id={$userinfo['id']}'>
                    <button name='add_btn'>Liste d'ami</button>
    </a>";

    } elseif (!empty($friend_or_not['status'])){

        if ($friend_or_not['status'] == "Friend") {

            echo "
            <a href='./friend_list.php?id={$userinfo['id']}'>
                <button name='add_btn'>Deja amis</button>
            </a>
            <form method='POST' action='./function.php?id={$getCurrentProfileId}'>
                <button type='submit' name='delete_friend' value='delete'>Supprimez ce contact</button>
            </form>
            ";

        } elseif($friend_or_not['status'] == "Waiting") {

            echo "
            
            <form method='POST' action='function.php?id={$getCurrentProfileId}'>
                <button type='submit' name='cancel_friend_request' value='cancel'>Annuler la demande d'ami</button>
            </form>
            
            
            ";

        }
    } elseif(!empty($if_added)){

        echo "
        <form method='POST' action='function.php?id={$getCurrentProfileId}'>
            <button type='submit' name='accept_friend_request' value='accept'>Accepter</button>             
            <button type='submit' name='reject_friend_request' value='refuse'>Refuser</button>  
        </form>           
        ";


    } else {


        echo "
        <form method='POST' action='function.php?id={$getCurrentProfileId}'>
            <button type='submit' name='add_friend' value='ajouter en ami'>Ajouter en ami</button>
        </form>
        ";

    } {

    }
}




// Publication function
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



// group_function
// Vérifier si les paramètres 'group_id' et 'group_name' sont définis dans la requête GET
if (isset($_GET['group_id'])) {
    $id_groupe = $_GET['group_id'];
    
}
if (isset($_GET['group_name'])){

    $nom_groupe = $_GET['group_name'];
}


if(!empty($group_id) && !empty($group_name)){


    $methode = filter_input(INPUT_SERVER, "REQUEST_METHOD");
    // Préparer une requête SQL pour récupérer les informations de l'utilisateur dans un groupe spécifique
    $requete = $pdo->prepare("
                SELECT members_group.user_id, members_group.role, members_group.group_id 
                FROM members_group
                INNER JOIN `group` 
                ON `group`.id = members_group.group_id
                INNER JOIN users
                ON users.id = members_group.user_id
                WHERE user_id = :user_id AND group_id = :group_id
            ");

    // Exécuter la requête en remplaçant les paramètres :user_id et :group_id par les valeurs correspondantes
    $requete->execute([
        ":user_id" => $_SESSION['id'],
        ":group_id" => $group_id,
    ]);

    // Récupérer le résultat de la requête dans un tableau associatif
    $joined_or_not = $requete->fetch(PDO::FETCH_ASSOC);

    // Préparer une requête SQL pour vérifier l'existence d'un groupe spécifique
    $requete2 = $pdo->prepare("
                SELECT id , status
                FROM `group`
                WHERE id = :group_id
            ");

    // Exécuter la requête en remplaçant le paramètre :group_id par la valeur correspondante
    $requete2->execute([
        ":group_id" => $group_id
    ]);

    // Récupérer le résultat de la requête dans un tableau associatif
    $group_check = $requete2->fetch(PDO::FETCH_ASSOC);

    $visibilityQ = $pdo->prepare("
            SELECT `status`
            FROM `group`
            WHERE id = :group_id
        ");

    $visibilityQ->execute([
        ":group_id" => $group_id
    ]);

    $visibility = $visibilityQ->fetch(PDO::FETCH_ASSOC);

}

function leave_group($pdo, $group_id, $group_name)
{

    
    $pdo = new PDO("mysql:host=localhost:3306; dbname=linkhub", "root", "");


    $requete = $pdo->prepare("
        DELETE FROM members_group
        WHERE group_id = :id_groupe AND user_id = :id_user
    
    ");

    $requete->execute([

        ":id_groupe" => $group_id,
        ":id_user" => $_SESSION['id']


    ]);


    header("Location: ./group_page.php?group_name={$nom_group}&group_id={$group_id}");
        
    
}

if(!empty($methode) && $methode == "POST"){

    if ($methode == "POST"){

        if (isset($_POST['group_left'])){

            leave_group($pdo, $group_id, $group_name);
           
        }

    } else {

        echo "degage";

    }
} 

function check_if_joined()
{
    global $joined_or_not;
    global $group_check;
    global $group_id;

    if (isset($joined_or_not) && !empty($joined_or_not)) {

        if ($joined_or_not['role'] == "Membre" or $joined_or_not['role'] == "Admin") {
            return FALSE;
        } else {

            return TRUE;
        }
    } else {

        return TRUE;
    }
}

function show_waiting_button()
{

    global $pdo;
    global $group_name;
    global $group_id;

    $search_user_waiting = $pdo->prepare("
            SELECT user_id, role, group_id FROM members_group
            WHERE user_id = :user_id AND group_id = :group_id
        
        ");

    $search_user_waiting->execute([

        ":user_id" => $_SESSION['id'],
        ":group_id" => $group_id


    ]);

    $waiting = $search_user_waiting->fetch(PDO::FETCH_ASSOC);

    if (!empty($waiting['role'])) {

        if ($waiting['role'] == "En attente") {

            echo "<button>En attente...</button>";
            echo "
                <form method='POST' action='button.php?group_name={$group_name}&group_id={$group_id}'>
                <button type=submit name='cancel_group'>Annuler</button>
                </form>";
        } else {

            echo '<form action="./button.php?group_name=' . $group_name . '&group_id=' . $group_id . '" method="post">
                <input type="hidden" name="group_id" value="' . $group_id . '">
                <input type="hidden" name="user_id" value="1">
                <button type="submit" name="request_group">S\'inscrire</button>
                </form>';
        }
    } else {

        echo '<form action="./button.php?group_name=' . $group_name . '&group_id=' . $group_id . '" method="post">
            <input type="hidden" name="group_id" value="' . $group_id . '">
            <input type="hidden" name="user_id" value="1">
            <button type="submit" name="request_group">S\'inscrire</button>
            </form>';
    }
}

function private_group_or_not()
{
    global $visibility;
    global $group_name;
    global $group_id;


    if (!empty($visibility)){
        
        if ($visibility['status'] === 'private') {
            show_waiting_button();
        } else {

            echo '<form action="./button.php?group_name=' . $group_name . '&group_id=' . $group_id . '" method="post">
                <input type="hidden" name="group_id" value="' . $group_id . '">
                <input type="hidden" name="user_id" value="1">
                <button type="submit" name="join_group">Rejoindre</button>

                </form>';
        }
    }
}

function if_joined()
{
    global $joined_or_not;
    global $group_id;
    global $group_name;
    global $visibility;

    if (check_if_joined()) { // Test si la personne est abonné
        private_group_or_not(); // Permet d'afficher le bouton S'inscrire ou Rejoindre selon si le groupe est privé
    } else {
        
        echo '
            
            <form action="./group_function.php?group_name=' . $group_name . '&group_id=' . $group_id . '" method="post">
                <input type="hidden" name="group_id" value="' . $group_id . '">
                <button type="submit" name="group_left">Quitter</button>
                <a href="invite_page.php"><button type="button" class="mon-bouton">Inviter</button></a>
            </form>';
    }
}

function group_user($pdo)
{

    global $group_name;
    global $group_id;


    // Prépare la requête SQL demandée
    $statement = $pdo->prepare("
            SELECT `role`, `user_id`, `group_id`, `name`
            FROM `members_group`
            WHERE group_id = :group_id AND (role = 'Admin' OR role = 'Membre')
        ");
    $statement->execute([

        ":group_id" => $group_id,

    ]);
    // On vérifie sur la valeur $statement n'est pas égale à "false", alors on continue
    // if (isset($statement) && !empty($statement)){

    if ($statement !== false) {
        echo '<div class="profile-container">';
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $members_name = $row['name'];
            $members_id = $row['user_id'];

            echo "
                        <div class='profil_display'>
                            <a href='./profil.php?id={$members_id}'>
                                <div class='profil'>
                                    <div class='profile_picture'></div>
                                    <p>{$members_name}</p>
                                </div>
                            </a>
                        </div>";
        }
        echo '</div>';
    } else {
        echo 'Aucun utilisateur trouvé.';
    }
}

function admin_group($pdo, $group_id)
{
    // Sélectionne le nom et le rôle des administrateurs dans la table 'members_group'
    // où l'id du groupe correspond à la valeur de ':group_id' et le rôle est 'admin'
    $select_admin = "
                SELECT `name`, `role`, `user_id`
                FROM `members_group`
                WHERE `group_id` = :group_id AND `role` = 'Admin'
            ";

    $verify = $pdo->prepare($select_admin);
    $verify->execute([
        ':group_id' => $group_id
    ]);

    // Vérifie si l'exécution de la requête a réussi
    if ($verify !== false) {
        echo '<div class="profile-container">';
        while ($row = $verify->fetch(PDO::FETCH_ASSOC)) {
            $admin_name = $row['name'];
            $admin_id = $row['user_id'];

            echo "
                    <div class='profil_admin'>
                        <a href='./profil.php?id={$admin_id}'>
                            <div class='profil'>
                                <div class='profil_picture'></div>
                                <p>{$admin_name}</p>
                            </div>
                        </a>
                    </div>";
        }
        echo '</div>';
    } else {
        echo '';
    }
}

function description_group($pdo, $group_id)
{
    // Sélectionne "l'id, la description" de "group" si "id" = "id"
    $description = "
                SELECT `id`, `description`
                FROM `group`
                WHERE `id` = :id
            ";

    $description_group = $pdo->prepare($description);
    $description_group->execute([
        ':id' => $group_id
    ]);

    $result = $description_group->fetch(PDO::FETCH_ASSOC);

    if ($result !== false) {
        // On retourne le result de description
        echo '<p>' . $result['description'] . '</p>';
    }
}

function permission_admin($pdo, $group_id, $group_name)
{
    $select_user = "
        SELECT `name`, `role`, `user_id`
        FROM `members_group`
        WHERE `group_id`= :group_id
    ";
    // Exécute la requête spécifique à "$select_user" -> query retourne l'objet demandé
    $statement = $pdo->prepare($select_user);
    $statement->execute([
        ':group_id' => $group_id
    ]);

    $find_self_user = $pdo->prepare("
        SELECT role 
        FROM members_group
        WHERE `group_id` = :group_id AND user_id = :user_id
    ");

    $find_self_user->execute([
        ":group_id" => $group_id,
        ":user_id" => $_SESSION['id'],


    ]);

    $admin_or_not = $find_self_user->fetch(PDO::FETCH_ASSOC);

    // On vérifie que la valeur $statement n'est pas égale à "false", alors on continue
    if ($statement !== false) {
        echo '<div class="message-container">';

        if ($admin_or_not['role'] === 'Admin') {
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                $members_name = $row['name'];
                $member_role = $row['role'];
                $user_id = $row['user_id'];

                echo "
                    <div class='profil_group'>
                        <a href='./profil.php?id={$user_id}'>
                            <div class='profile_picture'></div>
                            <p>{$members_name}</p>
                        </a>
                    </div>
                    <div class='role_members'>
                        <p>{$member_role}</p>
                    </div>
                ";

                echo '
                    <div class="private_message_chat">
                        <a href="message.php">
                            <button class="private_message" data-user-id="' . $user_id . '">Message</button>
                        </a>
                    </div>
        
                    <div class="select_role_group">
                        <form method="post">
                            <select name="value">
                                <option value="Membre" ' . ($member_role === 'Membre' ? 'selected' : '') . '>Membre</option>
                                <option value="Admin" ' . ($member_role === 'Admin' ? 'selected' : '') . '>Administrateur</option>
                                <option value="kick_member_G">Exclure le membre</option>
                            </select>
                    </div>
                ';
            }
            echo '<button type="submit" name="confirm">Enregistrer</button>
                    </form>';


            if (isset($_POST['confirm'])) {
                // Traitement de la mise à jour du rôle de l'utilisateur
                $roles = $_POST['value'];

                if ($roles === 'Membre') {
                    $select_role = $pdo->prepare("
                        UPDATE `members_group`
                        SET `role` = 'Membre'
                        WHERE `user_id` = :user_id
                    ");
                    $select_role->execute([
                        "user_id" => $user_id,
                    ]);
                } elseif ($roles === 'Admin') {
                    $select_role = $pdo->prepare("
                        UPDATE `members_group`
                        SET `role` = 'Admin'
                        WHERE `user_id` = :user_id
                    ");
                    $select_role->execute([
                        "user_id" => $user_id,
                    ]);
                } elseif ($roles === 'kick_member_G') {
                    $select_role = $pdo->prepare("
                        DELETE FROM `members_group`
                        WHERE `group_id` = :group_id AND `user_id` =:user_id
                    ");
                    $select_role->execute([
                        "group_id" => $group_id,
                        "user_id" => $user_id,
                    ]);
                }

                header("Refresh", 1);
                 // header("Location: ./members_group.php?group_name={$group_name}&group_id={$group_id}");
            }
        } elseif ($admin_or_not['role'] === 'Membre') {
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                $members_name = $row['name'];
                    $member_role = $row['role'];
                    $user_id = $row['user_id'];

                    echo "
                    <div class='profil_group'>
                        <a href='./profil.php?id={$user_id}'>
                            <div class='profile_picture'></div>
                            <p>{$members_name}</p>
                        </a>
                    </div>
                    <div class='role_members'>
                        <p>{$member_role}</p>
                    </div>
                    ";

                    echo "
                            <div class='private_message_chat'>
                                <form action='./message_hub.php' method='POST'>
                                
                                <button type='submit' name='create_discussion_with_user' value='{$user_id}'>Message</message> 
        
                                
                                </form>
                            </div>
                        ";
            }
        } else {
            echo " ";
        }
        echo '</div>';
    }
}

function adminEditionButton($pdo, $group_id, $group_name){

    $select_user = "
        SELECT `name`, `role`, `user_id`
        FROM `members_group`
        WHERE `group_id`= :group_id
    ";
    // Exécute la requête spécifique à "$select_user" -> query retourne l'objet demandé
    $statement = $pdo->prepare($select_user);
    $statement->execute([
        ':group_id' => $group_id
    ]);

    $find_self_user = $pdo->prepare("
        SELECT role 
        FROM members_group
        WHERE `group_id` = :group_id AND user_id = :user_id
    ");

    $find_self_user->execute([
        ":group_id" => $group_id,
        ":user_id" => $_SESSION['id'],


    ]);

    $admin_or_not = $find_self_user->fetch(PDO::FETCH_ASSOC);
    
    if(empty($admin_or_not)){

    } else {

        if ($admin_or_not['role'] == "Admin"){

            echo 
                
            "<form action='./edition_group.php?group_id={$group_id}' method='POST'>
                <input type='hidden' name='group_id' value={$group_id} />
                <input type='submit' name='send_group_id' value='Edition' />
            </form>

            ";


        } else {

            
            
        }
    }
}


// Friend function

if (!empty($_GET['id'])){

    $request_to_id = $_GET['id'];


}
if (!empty($_GET['user_request_id'])){


    $user_request_id = $_GET['user_request_id'];


}

if ($methode == "POST"){

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


    } 

}



// Notif function

function showNotif($pdo){

    
    $admin_group_request = $pdo->prepare("
        SELECT 
        users.name AS user_name, users.first_name AS user_first_name, `group`.group_name AS group_name, `group`.id AS group_id, users.id AS users_id
        FROM notifications
        INNER JOIN members_group
        ON notifications.notif_type_id = members_group.group_id
        INNER JOIN users
        ON notifications.from_type_id = users.id
        INNER JOIN `group`
        ON members_group.group_id = `group`.id
        WHERE members_group.user_id = :user_id AND members_group.role = :role AND notifications.notification_type = 'group_req'
    
    ");

    $admin_group_request->execute([
        
        ":user_id"=> $_SESSION['id'],
        ":role"=> "admin",

    ]);

    $notif = $admin_group_request->fetchAll(PDO::FETCH_ASSOC);

    if(!empty($notif)){

        
        foreach($notif as $row){
            $name = $row['user_name'];
            $first_name = $row['user_first_name'];
            $group_name = $row['group_name'];
            $group_id = $row['group_id'];
            $user_id = $row['users_id'];
            
            echo "<div>
                {$name} {$first_name} souhaite rejoindre votre groupe {$group_name} !
                </div>
                <form method='POST' action='button.php?group_id={$group_id}&user_request_id={$user_id}'>
                    <input type='submit' name='accept_group' value='Accepter'>
                    <input type='submit' name='abort_group' value='Refuser'>
                </form>
                ";
        }
    } else {
        echo "<h4>Vous n'avez pas de notif d'invitation de groupe</h4>";
    }

}

function showNotifFriend($pdo){

    $friend_request_user = $pdo->prepare("

        SELECT users.name AS user_name, users.first_name AS user_first_name, users.id AS user_id
        FROM users
        INNER JOIN notifications
        ON users.id = notifications.from_type_id
        WHERE notifications.notif_type_id = :own_id AND notifications.notification_type = :notif_type

    
    ");

    $friend_request_user->execute([

        ":own_id" => $_SESSION['id'],
        ":notif_type" => "friend_request_to"

    ]);

    $show_friend_request = $friend_request_user->fetchAll(PDO::FETCH_ASSOC);


    foreach ($show_friend_request as $row){
        $requester_name = $row['user_name'];
        $requester_first_name = $row['user_first_name'];
        $requester_id = $row['user_id'];


        echo "
            <p><a href='profil.php?id={$requester_id}'>{$requester_name} {$requester_first_name}
            </a>
            veut vous ajouter en ami !</p>
            <form method='POST' action='function.php?id={$requester_id}'>
            <button type='submit' name='accept_friend_request' value='accepter'>Accepter</button>
            <button type='submit' name='reject_friend_request' value='refuser'>Refuser</button>
            </form>
        
        
        ";



    }



}




?>