<?php

    require_once './include/session_start.php';
    
    $pdo = new PDO("mysql:host=localhost:3306; dbname=linkhub", "root", "");

    $methode = filter_input(INPUT_SERVER, "REQUEST_METHOD");

    if($methode == "POST"){

        $nom = filter_input(INPUT_POST, "nom");
        $prenom = filter_input(INPUT_POST, "prenom");
        $email = filter_input(INPUT_POST, "email");
        $password = filter_input(INPUT_POST, "password");
        $telephone = filter_input(INPUT_POST, "telephone");
        $birthday = filter_input(INPUT_POST, "birthday");
        $gender = filter_input(INPUT_POST, "gender");
        $cpassword = filter_input(INPUT_POST, "cpassword");
        $avatar = filter_input(INPUT_POST, "avatar");
        $banner = filter_input(INPUT_POST, "banner");
        // $birthday_ref = DateTime::createFromFormat('Y-m-d', $birthday);
        // $birthday_limite = date('Y-m-d', strtotime('-18 years'));
        // $difference = $birthday_ref->diff(new DateTime($birthday_limite));
        // $age = $difference->y;

        if ($password === $cpassword) {

            $requete = $pdo->prepare("


                INSERT INTO users(name, first_name, mail, password, phone, birthday, gender, roles, avatar, banner)
                SELECT :nom, :prenom, :email, :password, :telephone, :birthday, :gender, :roles, :avatar, :banner
            
            ");

            $requete->execute([

                ":nom" => $nom,
                ":prenom" => $prenom,
                ":email" => $email,
                ":password" => password_hash($password, PASSWORD_DEFAULT),
                ":telephone" => $telephone,
                "birthday" => $birthday,
                ":gender" => $gender,
                ":roles"=> "Client",
                ":avatar" => $avatar,
                ":banner" => $banner

            ]);

            header("Location: login.php");
            exit();

        } else {

           $_SESSION['status_error'] = "Les mots de passe ne correspondent pas !";

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
    <link rel="stylesheet" href="./css/register.css">
    
    <title>S'enregistrer</title>

</head>
<body>
    
    <section id="header">
            <div id="navbar-left">
                <a href="./index.php"><div class="logo"></div></a>
            </div>
    </section>

    <section id ="formulaire">
        <form method="POST">

            <h2>Inscription</h2>
            <hr>
            
            <p>Veuillez remplir tous les champs : </p>

            <input type="radio" name="gender" id ="radio1" value="Homme" required>
            <label for="radio1">Homme</label>

            <input type="radio" name="gender" id ="radio2" value="Femme" required>
            <label for="radio2">Femme</label>
            <br>

            <input type="text" name ="nom" id="champ_nom" placeholder="Nom" required>

            <input type="text" name="prenom" id="champ_prenom" placeholder="Prénom" required>

            <input type="email" name ="email" id="email" placeholder="Adresse Mail" required pattern=".*@.*" title="L'adresse e-mail doit contenir le caractère '@' ">

            <input type="text" name="telephone" id="phone_number" placeholder="Numéro de téléphone" required minlenght="10" required maxlenght="10">

            <input type="date" name ="birthday" id="birthday">
            <input type="hidden" name="avatar" value="pp_predefini_linkhub.jpeg">
            <input type="hidden" name="banner" value="banniere_par_defaut.png">

            <input type="password" name="password" id="champ_passeword" placeholder="Mot de passe" required pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*\W).{8,}" title="Le mot de passe doit contenir au minimum 8 caractères, dont au moins une lettre majuscule, une lettre minuscule, un chiffre et un caractère spécial.">    
            <input type="password" name="cpassword" id="champ_passeword" placeholder="Confirmer votre mot de passe" required>    

            <input id="button" type="submit" value="S'inscrire">

        </form>
    
    </section>
    <?php require_once './pages/footer.php' ?>
</body>
</html>