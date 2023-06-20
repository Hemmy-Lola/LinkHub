<?php 
   
    require_once './include/verify_connection.php';
    require_once './function.php';
    

    $pdo = new PDO("mysql:host=localhost:3306;dbname=linkhub", "root", "");
    
    $requete = $pdo->prepare("
        SELECT id FROM users
        WHERE mail = :login
    
    ");

    $requete->execute([

        ":login" => $_SESSION['login']

    ]);

    $utilisateur = $requete->fetch(PDO::FETCH_ASSOC);

    $_SESSION['id'] = $utilisateur['id'];


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/header.css">
    <link rel="stylesheet" href="./css/publications.css">
    <link rel="stylesheet" href="./css/footer.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <title>Dashboard</title>
</head>
<body>
    
    <?php require_once './pages/header.php' ?>

	<section>
    
    
        <h3>Bonjour <?php echo "{$utilisateur['first_name']} {$utilisateur['name']}" ?></h3>
	    <div class="publication-form">
			<form method="POST" action="publication_function.php" enctype="multipart/form-data">
                <input type="text" id="title" name="title" placeholder="Titre de la publication" required>
				<textarea id="content" name="content" placeholder="Exprimez-vous"></textarea>
				<input type="file" id="image" name="image" accept="image/*">
				<button type="submit" name="publish_user"><i class="fas fa-paper-plane"></i> Publier</button>
			</form>
		</div>
        <div class='publication_list'>
            <?php showAllPublication($pdo) ?>
        </div>

    </section>
    <?php require_once './pages/footer.php' ?>


</body>

<script src="https://kit.fontawesome.com/1234567890.js" crossorigin="anonymous"></script>
</html>