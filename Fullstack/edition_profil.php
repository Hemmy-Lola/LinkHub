<?php

require_once './include/session_start.php';

$pdo = new PDO("mysql:host=localhost:3306; dbname=linkhub", "root", "");

$methode = filter_input(INPUT_SERVER, "REQUEST_METHOD");

if (isset($_SESSION['id'])) {
    $requser = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $requser->execute(array($_SESSION['id']));
    $user = $requser->fetch(PDO::FETCH_ASSOC);


}
    if (isset($_POST['newprenom']) and !empty($_POST['newprenom']) and $_POST['newprenom'] != $user['first_name']) {
        $newprenom = htmlspecialchars(($_POST['newprenom']));
        $insertprenom = $pdo->prepare("UPDATE users SET first_name = ? WHERE id = ?");
        $insertprenom->execute(array($newprenom, $_SESSION['id']));
        header('Location: profil.php?id=' . $_SESSION['id']);
    }

    if (isset($_POST['newnom']) and !empty($_POST['newnom']) and $_POST['newnom'] != $user['name']) {
        $newnom = htmlspecialchars(($_POST['newnom']));
        $insertnom = $pdo->prepare("

                UPDATE users 
                SET name = ? 
                WHERE id = ?

            ");
        $insertnom->execute(array($newnom, $_SESSION['id']));
        header('Location: profil.php?id=' . $_SESSION['id']);
    }

    if (isset($_POST['newmail']) and !empty($_POST['newmail']) and $_POST['newmail'] != $user['mail']) {
        $newmail = htmlspecialchars(($_POST['newmail']));
        $insertmail = $pdo->prepare("UPDATE users SET mail = ? WHERE id = ?");
        $insertmail->execute(array($newmail, $_SESSION['id']));
        header('Location: profil.php?id=' . $_SESSION['id']);
    }

    if (isset($_POST['newbirthday']) and !empty($_POST['newbirthday']) and $_POST['newbirthday'] != $user['birthday']) {
        $newmail = htmlspecialchars(($_POST['newbirthday']));
        $insertmail = $pdo->prepare("UPDATE users SET birthday = ? WHERE id = ?");
        $insertmail->execute(array($newmail, $_SESSION['id']));
        header('Location: profil.php?id=' . $_SESSION['id']);
    }

    if (isset($_POST['newmdp1']) and !empty($_POST['newmdp1']) and isset($_POST['newmdp2']) and !empty($_POST['newmdp2'])) {
        $mdp1 = sha1($_POST['newmdp1']);
        $mdp2 = sha1($_POST['newmdp2']);

        if ($mdp1 == $mdp2) {
            $insertmdp = $pdo->prepare("UPDATE users SET mdp = ? WHERE id = ?");
            $insertmdp->execute(array($mdp1, $_SESSION['id']));
            header('Location: profil.php?id=' . $_SESSION['id']);
        } else {
            $msg = "Vos deux mots de passes ne correspondent pas !";
        }
    }

    if (isset($_POST['submit'])) {

        $file = $_FILES['file'];

        $fileName = $_FILES['file']['name'];

        $fileTempName = $_FILES['file']['tmp_name'];

        $fileSize = $_FILES['file']['size'];

        $fileError = $_FILES['file']['error'];

        $fileType = $_FILES['file']['type'];


        $fileExt = explode('.', $fileName);
        $fileActualExt = strtolower(end($fileExt));

        $allowedExt = array("jpg", "jpeg", "png", "pdf");


        if (in_array($fileActualExt, $allowedExt)) {

            if ($fileError == 0) {

                if ($fileSize < 10000000) {

                    $fileNemeNew = uniqid('', true) . "." . $fileActualExt;

                    $fileDestination = 'assets/uploads/' . $fileNemeNew;

                    move_uploaded_file($fileTempName, $fileDestination);

                    echo "File Uploaded successfully";
                } else {

                    //Message,If file size greater than allowed size
                    //echo "File Size Limit beyond acceptance";

                }
            } else {

                //Message, If there is some error
                //echo "Something Went Wrong Please try again!";

            }
        } else {

            //Message,If this is not a valid file type
            //echo "You can't upload this extention of file";
        }



        $requete = $pdo->prepare("
                UPDATE users 
                SET avatar = :image
                WHERE id = :id
            ");


        $requete->execute([

            ":image" => $fileNemeNew,
            ":id" => $_SESSION['id']

        ]);
    }

    // if(isset($_FILES['avatar']) AND !empty($_FILES['avatar']['name']))
    // {
    //     $tailleMax = 2097152;
    //     $extensionsValides = array('jpg','jpeg','gif','png');
    //     if($_FILES['avatar']['size'] <= $tailleMax)
    //     {
    //         $extensionUpload = strtolower(substr(strrchr($_FILES['avatar']['name'], '.'), 1));
    //        if(in_array($extensionUpload, $extensionsValides))
    //        {
    //             $chemin = "members/avatars/".$_SESSION['id'].".".$extensionUpload;
    //             $resultat = move_uploaded_file($_FILES['avatar']['tmp_name'], $chemin);
    //             if($resultat)
    //             {
    //                 $updateavatar = $pdo->prepare('UPDATE users SET avatar = :avatar WHERE id = :id');
    //                 $updateavatar->execute(array(
    //                     'avatar' => $_SESSION['id'].".".$extensionUpload,
    //                     'id' => $_SESSION['id']
    //                 ));
    //                 header('Location: profil.php?id='.$_SESSION['id']);


    //             }

    //             else
    //             {
    //                 $msg = "Erreur durant l'importation de votre profil";
    //             }
    //        }
    //        else
    //        {
    //             $msg = "Votre photo de profil doit être au format jpg, jpeg, gif ou png.";
    //        }
    //     }
    //     else
    //     {
    //         $msg = "Votre photo de profil ne doit pas dépasser 2Mo";
    //     }
    // }

    if (isset($_POST['newprenom']) and !empty($_POST['newprenom'] == $user['name'])) {
        header('Location: profil.php?id=' . $_SESSION['id']);
    }

// Condition pour désactiver les compte buger pour le moment marche mes pas comme il le faut 
if (isset($_POST['disable_Account'])) {
    $stmt = $pdo->prepare("UPDATE users SET status = 'inactive' WHERE id = :id");
    $stmt->execute([
        ":id" => $_SESSION['id']
    ]);

    // Rediriger l'utilisateur
    header("Location: login.php");
    exit(); // Terminer l'exécution du script

} else {
    // Vérifier si le compte est déjà désactivé
    $stmt = $pdo->prepare("SELECT status FROM users WHERE id = :id");
    $stmt->execute([
        ":id" => $_SESSION['id']
    ]);
    $status = $stmt->fetchColumn();

if ($status === 'inactive') {
        // Activer le compte
        $stmt = $pdo->prepare("UPDATE users SET status = 'active' WHERE id = :id");
        $stmt->execute([
            ":id" => $_SESSION['id']
        ]);

        // Rediriger l'utilisateur
        header("Location: profil.php?id=" . $_SESSION['id']);
        exit(); // Terminer l'exécution du script
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
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/footer_shortpage.css">
    <link rel="stylesheet" href="./css/edition_profil.css">

    <title>Editer mon profil</title>

</head>

<body>
    <?php require_once './pages/header.php' ?>
    
    <section id="header">
        <!-- <div id="navbar-left">
                <a href="./index.php"><div class="logo"></div></a>
            </div> -->
    </section>
    <?= $_SESSION['status_error'] ?>
    <section>
        <h2>Editer mon profil</h2>
        <form method="POST" action="" enctype="multipart/form-data">
            <label>Prénom :</label>
            <input type="text" name="newprenom" placeholder="Prenom" value="<?= $user['first_name'] ?>" />

            <label>Nom :</label>
            <input type="text" name="newnom" placeholder="Nom" value="<?php echo $user['name']; ?>" />

            <label>Mail :</label>
            <input type="text" name="newmail" placeholder="Mail" value="<?php echo $user['mail']; ?>" />

            <label>Genre :</label>
            <input type="text" name="newgender" placeholder="Genre" value="<?php echo $user['gender']; ?>" />

            <label>Date Anniversaire :</label>
            <input type="date" name="newbirthday" placeholder="Birthday" value="<?= $user_birthday; ?>" />

            <label>Mot de passe :</label>
            <input type="password" name="newpassword" placeholder="Mot de passe" />

            <label>Confirmation mot de passe :</label>
            <input type="password" name="newcpassword" placeholder="Confirmer le mot de passe">

            <label>Avatar: </label>
            <input type="file" name="file" id="file" /><br><br>

            <input type="submit" name="submit" value="Mettre à jour" />
        </form>
        <br>
        <a href="profil.php?id=<?= $_SESSION['id'] ?>"> <button>Retour</button></a>
        <?php if (isset($msg)) {
            echo $msg;
        } ?>

    </section>
    <section>
        <form method="POST" action="">
            <label>Êtes-vous sûr de vouloir désactiver votre compte ? Cette action est irréversible.</label>
            <input type="submit" name="disable_Account" value="Désactiver mon compte" />
        </form>
    </section>
    

    <?php require_once './pages/footer.php' ?>
</body>

</html>