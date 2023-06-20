<?php


require_once './include/verify_connection.php';
require_once './function.php';


// Affiche la page du groupe associé à son id
if (isset($_GET['group_name']) && isset($_GET['group_id'])) {
    $group_name = $_GET['group_name'];
    $group_id = $_GET['group_id'];
    $getCurrentProfileId = $_GET['group_id'];
}


$sql = "
    SELECT * 
    FROM publications 
    WHERE who_typed = 'group' AND who_typed_id = :group_id
";
$stmt = $pdo->prepare($sql);
$stmt->execute([':group_id' => $group_id]);
$publications = $stmt->fetchAll(PDO::FETCH_ASSOC);

$requser = $pdo->prepare("SELECT * FROM `group` WHERE id = :group_id");
$requser->execute([
    ":group_id" => $group_id
]);
$data_group = $requser->fetch(PDO::FETCH_ASSOC);
$group_avatar = $data_group['avatar'];
$group_banner = $data_group['banner'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <link rel="stylesheet" href="./css/style.css"> -->
    <link rel="stylesheet" href="./css/header.css">
    <link rel="stylesheet" href="./css/footer.css">
    <link rel="stylesheet" href="./css/group_page.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <title><?= $group_name ?> - Groupe</title>
</head>

<body>

    <?php require_once './pages/header.php' ?>

    <section id="group_main_page">
        <!-- <img src="./assets/genshin.webp" class="background-image_profil"></img> -->
        <img src="./assets/uploads/<?= $group_avatar ?>">
    <img src="./assets/uploads/<?= $group_banner ?>">
        <div id="group_follow">
            <div class="profil_image">
                <div class="logo"></div>
                <h2><?= $group_name ?></h2>
            </div>
           
            <?php adminEditionButton($pdo, $group_id, $group_name) ?>

            <?php if_joined(); ?>
            
        </div>
        <div class="admin_list">
            <a href="admin.php">
                <h2>Administrateurs</h2>
            </a>
            <?php admin_group($pdo, $group_id) ?>
        </div>
        <div class="description">
            <h2>Description</h2>
            <?php description_group($pdo, $group_id) ?>
        </div>
    </section>

    <section class="publication">
        <h2>Publication</h2>
        <?php echo "<a href='./publication_group.php?group_name=$group_name&group_id=$group_id'>Créer une publication....</a>"; ?>
        <div class="publication_list">
            <?php showProfilPublication($pdo, $getCurrentProfileId); ?>
        </div>

    </section>




</section>

    <section class="event">
        <h2>Evenement</h2>
        <p>Il n'y a pas d'événements actuellement....</p>
    </section>

    <section class="profil_list">
        <?php echo "<a href='members_group.php?group_name={$group_name}&group_id={$group_id}'>
                        <h2> Membre du groupe </h2>
                    </a>"
        ?>
        <?php group_user($pdo); ?>
    </section>


    <?php require_once './pages/footer.php' ?>

</body>

</html>