<?php
require_once './include/session_start.php';
require_once './include/verify_connection.php';

// Vérifier si les paramètres 'group_id' et 'group_name' sont définis dans la requête GET
if (isset($_GET['group_id'])) {
    $group_id = $_GET['group_id'];
    $group_name = $_GET['group_name'];
}

// Créer une connexion PDO à la base de données
$pdo = new PDO("mysql:host=localhost:3306; dbname=linkhub", "root", "");

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