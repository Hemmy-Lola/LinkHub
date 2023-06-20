<?php

require_once './include/session_start.php';

$pdo = new PDO("mysql:host=localhost:3306; dbname=linkhub", "root", "");

$methode = filter_input(INPUT_SERVER, "REQUEST_METHOD");

if (isset($_GET['group_id'])) {
    $group_id = $_GET['group_id'];
    $requser = $pdo->prepare("SELECT * FROM `group` WHERE id = :group_id");
    $requser->execute([
        ":group_id" => $group_id
    ]);
    echo $group_id;
    $data_group = $requser->fetch(PDO::FETCH_ASSOC);
    
    $group_name = $data_group['group_name'];
    $group_description = $data_group['description'];
    $group_avatar = $data_group['avatar'];
    $group_banner = $data_group['banner'];

    if (isset($_POST['newname']) && $_POST['newname'] != $data_group['group_name']) {
        $newname = htmlspecialchars($_POST['newname']);
        $insertname = $pdo->prepare("UPDATE `group` SET group_name = ? WHERE id = ?");
        $insertname->execute(array($newname, $group_id));
        // header('Location: profil.php?id=' . $_SESSION['id']);
    }

    if (isset($_POST['newdescription'])  && $_POST['newdescription'] != $data_group['description']) {
        $newdescription = htmlspecialchars($_POST['newdescription']);
        $insertdescription = $pdo->prepare("UPDATE `group` SET description = ? WHERE id = ?");
        $insertdescription->execute(array($newdescription, $group_id));
        // header('Location: profil.php?id=' . $_SESSION['id']);
    }

    if (isset($_POST['submit'])) {
        $file_avatar_group = $_FILES['file_avatar_group'];
        $fileName_avatar_group = $_FILES['file_avatar_group']['name'];
        $fileTempName_avatar_group = $_FILES['file_avatar_group']['tmp_name'];
        $fileSize_avatar_group = $_FILES['file_avatar_group']['size'];
        $fileError_avatar_group = $_FILES['file_avatar_group']['error'];
        $fileType_avatar_group = $_FILES['file_avatar_group']['type'];

        $fileExt_avatar_group = explode('.', $fileName_avatar_group);
        $fileActualExt_avatar_group = strtolower(end($fileExt_avatar_group));

        $allowedExt_avatar_group = array("jpg", "jpeg", "png", "pdf");

        if (in_array($fileActualExt_avatar_group, $allowedExt_avatar_group)) {
            if ($fileError_avatar_group == 0) {
                if ($fileSize_avatar_group < 10000000) {
                    $fileNemeNew_avatar_group = uniqid('', true) . "." . $fileActualExt_avatar_group;
                    $fileDestination_avatar_group = 'assets/uploads/' . $fileNemeNew_avatar_group;
                    move_uploaded_file($fileTempName_avatar_group, $fileDestination_avatar_group);

                    echo "File Uploaded successfully";
                    $requete = $pdo->prepare("
                    UPDATE `group`
                    SET avatar = :image
                    WHERE id = :id
                ");

                    $requete->execute([
                        ":image" => $fileNemeNew_avatar_group,
                        ":id" => $group_id  
                 ]);
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
    }
}

if (isset($_POST['submit'])) {
    $file_banner_group = $_FILES['file_banner_group'];
    $fileName_banner_group = $_FILES['file_banner_group']['name'];
    $fileTempName_banner_group = $_FILES['file_banner_group']['tmp_name'];
    $fileSize_banner_group = $_FILES['file_banner_group']['size'];
    $fileError_banner_group = $_FILES['file_banner_group']['error'];
    $fileType_banner_group = $_FILES['file_banner_group']['type'];

    $fileExt_banner_group = explode('.', $fileName_banner_group);
    $fileActualExt_banner_group = strtolower(end($fileExt_banner_group));

    $allowedExt_banner_group = array("jpg", "jpeg", "png", "pdf");

    if (in_array($fileActualExt_banner_group, $allowedExt_banner_group)) {
        if ($fileError_banner_group == 0) {
            if ($fileSize_banner_group < 10000000) {
                $fileNemeNew_banner_group = uniqid('', true) . "." . $fileActualExt_banner_group;
                $fileDestination_banner_group = 'assets/uploads/' . $fileNemeNew_banner_group;
                move_uploaded_file($fileTempName_banner_group, $fileDestination_banner_group);

                echo "File Uploaded successfully";
                $requete = $pdo->prepare("
                UPDATE `group`
                SET banner = :image
                WHERE id = :id
            ");

                $requete->execute([
                    ":image" => $fileNemeNew_banner_group,
                    ":id" => $group_id  
             ]);
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

    header("Location: group_page.php?group_name={$newname}&group_id={$group_id}");

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/edition_profil.css">
    <link rel="stylesheet" href="./css/header.css">
    <link rel="stylesheet" href="./css/footer_shortpage.css">
    <title>Editer mon groupe</title>
</head>

<body>

    <?php include_once './pages/header.php' ?>
    <img src="./assets/uploads/<?= $group_avatar ?>" alt="Avatar du groupe">
    <img src="./assets/uploads/<?= $group_banner ?>" alt="Bannière du groupe">
    <?php echo $group_description ?>

    <form action="" method="POST" enctype="multipart/form-data">
        <label>Nom de groupe</label>
        <input type="text" name="newname" value='<?= $group_name ?>' />

        <label>Description du groupe</label>
        <input type="text" name="newdescription" value='<?= $group_description ?>' />

        <label>Avatar du groupe</label>
        <input type="file" name="file_avatar_group" id="file_avatar_group">

        <label>Bannière du groupe</label>
        <input type="file" name="file_banner_group" id="file_banner_group">

        <input type="submit" name="submit" value="Mettre à jour" />
    </form>

    <?php include_once './pages/footer.php' ?>
</body>

</html>
