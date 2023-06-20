    <?php

require './function.php';

// Affiche la page du groupe associé à son id
if (isset($_GET['group_name']) && isset($_GET['group_id'])) {
    $group_name = $_GET['group_name'];
    $group_id = $_GET['group_id'];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/header.css">
    <link rel="stylesheet" href="./css/footer.css">
    <link rel="stylesheet" href="./css/group_page.css">
    <title>Liste des membres</title>
</head>

<body>
    <?php require_once './pages/header.php' ?>
    <section id="list_member_page">
        <div class="list_member">
            <h2>Liste des membres</h2>
            <?php permission_admin($pdo, $group_id, $group_name) ?>
        </div>
        
    </section>

    <?php require_once './pages/footer.php' ?>

</body>

</html>