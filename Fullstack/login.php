<?php
    require_once './include/session_start.php';
    require_once './authentification/token.php';

    if(isset($_SESSION["validtoken"]) && $_SESSION["validtoken"] === true)
    {

        header('Location: dashboard.php');
        exit();

    }

    $methode = filter_input(INPUT_SERVER, "REQUEST_METHOD");


    if ($methode == "POST") {

        $_SESSION['status_error'] = "";
        $login = filter_input(INPUT_POST, "login");
        $password = filter_input(INPUT_POST, "password");

        $pdo = new PDO("mysql:host=localhost:3306; dbname=linkhub", "root", "");

        $requete = $pdo->prepare("

            SELECT * FROM users
            WHERE mail = :login 

        ");

        $requete->execute([

            ":login" => $login,
            

        ]);

        $utilisateur = $requete->fetch(PDO::FETCH_ASSOC);

        if(!empty($utilisateur['password'])){

            
            if (password_verify($password, $utilisateur["password"])){

            
                $_SESSION["login"] = $login;
                create_token();
                header('Location: dashboard.php');
                exit();


            } else {

                $_SESSION['status_error'] = "Votre identifiant ou votre mot de passe n'est pas correct, veuillez réessayez !";

            }

        }  else {

            $_SESSION['status_error'] = "Cette utilisateur n'existe pas";

        }

    }




?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/footer_shortpage.css">
    <link rel="stylesheet" href="./css/header.css">
    <link rel="stylesheet" href="./css/login.css">
    
    <title>Se connecter</title>
</head>
<body>

    <section>
        <h1>LinkHub</h1>
        <div class="container">
            <form action="" method="POST">
                <input type="email" name="login" placeholder="Email" required>

                <input type="password" name="password" placeholder="Mot de passe" required>
                <input type="submit" value="Connexion">
                
                <p class="text-center">Vous n'avez pas compte ? <a href="./register.php">Inscrivez-vous !</a> </p>
            </form>
        </div>
    </section>
    <?php require_once './pages/footer.php' ?>
</body>
</html>